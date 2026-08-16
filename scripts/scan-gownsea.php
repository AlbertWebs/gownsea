<?php

declare(strict_types=1);

$pages = [
    'https://gownsea.com/',
    'https://gownsea.com/gown-for-hire',
    'https://gownsea.com/church-wear',
    'https://gownsea.com/legal-attire',
    'https://gownsea.com/about-us',
    'https://gownsea.com/shop-attire/graduation-attire',
    'https://gownsea.com/shop-attire/legal-attire',
    'https://gownsea.com/shop-attire/church-wear',
    'https://gownsea.com/our-products/graduation-tassels',
    'https://gownsea.com/our-products/undergraduate-academic-hoods',
    'https://gownsea.com/our-products/graduation-stoles',
    'https://gownsea.com/our-products/preschool-graduation',
    'https://gownsea.com/our-products/phd-graduation-gown',
    'https://gownsea.com/our-products/degree-graduation-gowns',
    'https://gownsea.com/our-products/certificate-gowns',
    'https://gownsea.com/our-products/diploma-graduation-gowns',
    'https://gownsea.com/our-products/masters-gown',
    'https://gownsea.com/our-products/phd-caps',
    'https://gownsea.com/shop-attire-collection/graduation-attire/graduation-cap',
    'https://gownsea.com/shop-attire-collection/graduation-attire/graduation-hoods',
    'https://gownsea.com/shop-attire-collection/graduation-attire/phd-caps',
    'https://gownsea.com/shop-attire-collection/graduation-attire/pre-school-gowns',
    'https://gownsea.com/shop-attire-collection/graduation-attire/certificate-gowns',
    'https://gownsea.com/shop-attire-collection/graduation-attire/diploma-gowns',
    'https://gownsea.com/shop-attire-collection/graduation-attire/masters-gowns',
    'https://gownsea.com/shop-attire-collection/graduation-attire/phd-gowns',
    'https://gownsea.com/shop-attire-collection/graduation-attire/degree-gown',
];

$htmlDir = __DIR__.'/storage/app/gownsea-html';
if (! is_dir($htmlDir)) {
    mkdir($htmlDir, 0777, true);
}

$images = [];
$links = [];

foreach ($pages as $url) {
    echo "FETCH {$url}\n";
    $html = fetch($url);
    $slug = preg_replace('/[^a-z0-9]+/i', '-', parse_url($url, PHP_URL_PATH) ?: 'home');
    file_put_contents($htmlDir.'/'.trim($slug, '-').'.html', $html);

    if (preg_match_all('/https?:\/\/[^"\'\s<>]+/i', $html, $matches)) {
        foreach ($matches[0] as $found) {
            $found = html_entity_decode($found, ENT_QUOTES);
            $found = rtrim($found, '.,);');
            if (preg_match('/\.(jpe?g|png|webp|svg|gif)(\?|$)/i', $found) || str_contains($found, 'images.squarespace') || str_contains($found, 'cdn.prod.website') || str_contains($found, 'images.unsplash') || str_contains($found, 'static1.squarespace') || str_contains($found, 'wp-content/uploads')) {
                $images[] = $found;
            }
            if (str_starts_with($found, 'https://gownsea.com/')) {
                $links[] = strtok($found, '?#');
            }
        }
    }
}

$images = array_values(array_unique($images));
$links = array_values(array_unique($links));
file_put_contents($htmlDir.'/images.json', json_encode($images, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
file_put_contents($htmlDir.'/links.json', json_encode($links, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "\nIMAGES ".count($images)."\n";
foreach ($images as $image) {
    echo $image."\n";
}

function fetch(string $url): string
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 45,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
        CURLOPT_HTTPHEADER => ['Accept: text/html'],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "  status {$code} bytes ".strlen((string) $body)."\n";

    return is_string($body) ? $body : '';
}
