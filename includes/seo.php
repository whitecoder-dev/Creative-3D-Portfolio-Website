<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

function render_seo(array $seo = []): void
{
    $title = (string) ($seo['title'] ?? (config('site.name') . ' | ' . config('site.brand_title')));
    $description = (string) ($seo['description'] ?? config('site.short_bio'));
    $keywords = (string) ($seo['keywords'] ?? 'Oshanda Geethanjana, Creative Developer, AI Engineer, WhiteCoder, HND Study Hub');
    $slug = (string) ($seo['slug'] ?? current_page_slug());
    $canonical = (string) ($seo['canonical'] ?? site_url($slug === 'home' ? 'index.php' : ($slug . '.php')));
    $ogImage = (string) ($seo['og_image'] ?? asset_url((string) config('site.default_og_image')));
    $robots = (string) ($seo['robots'] ?? 'index,follow,max-image-preview:large');

    $breadcrumbs = $seo['breadcrumbs'] ?? [];

    $personSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => config('site.name'),
        'jobTitle' => config('site.brand_title'),
        'description' => config('site.short_bio'),
        'url' => site_url(),
        'sameAs' => [
            config('social.github'),
            config('social.linkedin'),
            config('social.instagram'),
        ],
    ];

    $websiteSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => config('site.name'),
        'url' => site_url(),
        'inLanguage' => 'en',
    ];

    $breadcrumbSchema = null;
    if (is_array($breadcrumbs) && !empty($breadcrumbs)) {
        $itemList = [];
        $position = 1;

        foreach ($breadcrumbs as $label => $url) {
            $itemList[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $label,
                'item' => $url,
            ];
            $position++;
        }

        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemList,
        ];
    }

    echo '<title>' . e($title) . '</title>';
    echo '<meta name="description" content="' . e($description) . '">';
    echo '<meta name="keywords" content="' . e($keywords) . '">';
    echo '<meta name="robots" content="' . e($robots) . '">';
    echo '<meta name="author" content="' . e((string) config('site.name')) . '">';
    echo '<link rel="canonical" href="' . e($canonical) . '">';

    echo '<meta property="og:type" content="website">';
    echo '<meta property="og:site_name" content="' . e((string) config('site.name')) . '">';
    echo '<meta property="og:title" content="' . e($title) . '">';
    echo '<meta property="og:description" content="' . e($description) . '">';
    echo '<meta property="og:url" content="' . e($canonical) . '">';
    echo '<meta property="og:image" content="' . e($ogImage) . '">';

    echo '<meta name="twitter:card" content="summary_large_image">';
    echo '<meta name="twitter:title" content="' . e($title) . '">';
    echo '<meta name="twitter:description" content="' . e($description) . '">';
    echo '<meta name="twitter:image" content="' . e($ogImage) . '">';

    echo '<script type="application/ld+json">' . json_encode($personSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    echo '<script type="application/ld+json">' . json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';

    if ($breadcrumbSchema !== null) {
        echo '<script type="application/ld+json">' . json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }
}
