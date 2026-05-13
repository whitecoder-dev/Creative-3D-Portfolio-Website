<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

if (request_method() !== 'GET') {
    json_error('Method not allowed.', 405);
}

try {
    $limit = isset($_GET['limit']) ? max(1, min(20, (int) $_GET['limit'])) : 12;
    $feedUrl = (string) config('medium.feed_url');

    if (!validate_url($feedUrl)) {
        json_success('Medium feed is not configured.', []);
    }

    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'PortfolioMediumFetcher/1.0',
        ],
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
        ],
    ]);

    $xmlString = @file_get_contents($feedUrl, false, $context);

    if ($xmlString === false || trim($xmlString) === '') {
        json_success('No Medium posts found.', []);
    }

    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NONET | LIBXML_NOCDATA);

    if ($xml === false || !isset($xml->channel->item)) {
        json_success('No Medium posts found.', []);
    }

    $items = $xml->channel->item;
    $posts = [];

    foreach ($items as $item) {
        if (count($posts) >= $limit) {
            break;
        }

        $title = clean_text((string) ($item->title ?? 'Untitled'), 255);
        $url = (string) ($item->link ?? '');
        if (!validate_url($url)) {
            continue;
        }

        $publishedAt = (string) ($item->pubDate ?? '');
        $descriptionRaw = (string) ($item->description ?? '');

        $encodedContent = '';
        $contentNamespace = $item->children('http://purl.org/rss/1.0/modules/content/');
        if (isset($contentNamespace->encoded)) {
            $encodedContent = (string) $contentNamespace->encoded;
        }

        $fullContent = $encodedContent !== '' ? $encodedContent : $descriptionRaw;

        $image = null;
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $fullContent, $matches)) {
            $candidate = trim((string) ($matches[1] ?? ''));
            if (validate_url($candidate)) {
                $image = $candidate;
            }
        }

        $excerptText = excerpt($fullContent, 170);

        $posts[] = [
            'title' => $title,
            'url' => $url,
            'image_url' => $image,
            'excerpt' => $excerptText,
            'published_at' => $publishedAt,
            'published_at_formatted' => format_date($publishedAt),
            'reading_time' => reading_time_minutes($fullContent),
        ];
    }

    json_success('Data loaded successfully.', $posts, ['count' => count($posts)]);
} catch (Throwable $exception) {
    error_log('get_medium_posts error: ' . $exception->getMessage());
    json_error('Unable to load Medium posts right now.', 500);
}
