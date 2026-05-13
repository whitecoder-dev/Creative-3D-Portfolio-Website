<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$pages = [
    ['path' => 'index.php', 'priority' => '1.0', 'changefreq' => 'weekly'],
    ['path' => 'about.php', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ['path' => 'education.php', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ['path' => 'works.php', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['path' => 'blogs.php', 'priority' => '0.8', 'changefreq' => 'daily'],
    ['path' => 'courses.php', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ['path' => 'contact.php', 'priority' => '0.7', 'changefreq' => 'monthly'],
];

$xml = new DOMDocument('1.0', 'UTF-8');
$xml->formatOutput = true;

$urlSet = $xml->createElement('urlset');
$urlSet->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
$xml->appendChild($urlSet);

foreach ($pages as $page) {
    $url = $xml->createElement('url');

    $loc = $xml->createElement('loc', site_url($page['path']));
    $changefreq = $xml->createElement('changefreq', $page['changefreq']);
    $priority = $xml->createElement('priority', $page['priority']);
    $lastmod = $xml->createElement('lastmod', date('Y-m-d'));

    $url->appendChild($loc);
    $url->appendChild($lastmod);
    $url->appendChild($changefreq);
    $url->appendChild($priority);

    $urlSet->appendChild($url);
}

$targetPath = __DIR__ . '/sitemap.xml';
$xml->save($targetPath);

echo 'Sitemap generated at: ' . $targetPath . PHP_EOL;
