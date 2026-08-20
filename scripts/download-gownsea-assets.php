<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$imageList = json_decode((string) file_get_contents($root.'/scripts/storage/app/gownsea-html/images.json'), true) ?: [];

$extra = [
    'https://gownsea.com/uploads/products/academic gradduation hoods  hoods  (18)-Photoroom.jpg',
    'https://gownsea.com/uploads/products/academic gradduation hoods  hoods  (17)-Photoroom.jpg',
    'https://gownsea.com/uploads/products/academic gradduation hoods  hoods  (16)-Photoroom.jpg',
    'https://gownsea.com/uploads/products/academic gradduation hoods  hoods  (15)-Photoroom.jpg',
    'https://gownsea.com/theme/images/clients-logos/client-16.png',
];

$urls = array_values(array_unique(array_merge($imageList, $extra)));

foreach ($urls as $url) {
    if (! str_contains($url, 'gownsea.com') && ! str_contains($url, 'corpusinvestmentsltd.com')) {
        continue;
    }

    $path = parse_url($url, PHP_URL_PATH) ?: '';
    $path = urldecode($path);
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION)) ?: 'bin';

    if (str_contains($path, 'clients-logos')) {
        $destRel = 'images/clients/'.basename($path);
    } elseif (str_contains($path, '/products/')) {
        $destRel = 'images/products/'.sanitize(basename($path));
    } elseif (str_contains($path, '/categories/')) {
        $destRel = 'images/categories/'.sanitize(basename($path));
    } elseif (str_contains($path, '/blogs/')) {
        $destRel = 'images/blogs/'.sanitize(basename($path));
    } elseif (str_contains($path, 'favicon')) {
        $destRel = 'images/favicon/'.sanitize(basename($path));
    } elseif (str_contains($path, 'theme/images')) {
        $destRel = 'images/brand/'.sanitize(basename($path));
    } else {
        $destRel = 'images/site/'.sanitize(basename($path));
    }

    $dest = $root.'/public/'.$destRel;
    if (! is_dir(dirname($dest))) {
        mkdir(dirname($dest), 0777, true);
    }

    echo "GET {$url}\n";
    $ok = download($url, $dest);
    if (! $ok) {
        echo "  FAIL\n";
        continue;
    }
    echo "  -> /{$destRel} (".filesize($dest)." bytes)\n";
}

function sanitize(string $name): string
{
    $name = str_replace([' ', ','], ['-', ''], $name);
    $name = preg_replace('/-+/', '-', $name) ?: $name;

    return $name;
}

function download(string $url, string $dest): bool
{
    $encoded = preg_replace_callback('/[^:\/]+(?=\/|$)/', function ($m) {
        return rawurlencode(urldecode($m[0]));
    }, $url) ?: $url;

    $ch = curl_init($encoded);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_USERAGENT => 'Mozilla/5.0',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $type = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    if ($code !== 200 || ! is_string($body) || $body === '' || str_contains($type, 'text/html')) {
        return false;
    }

    return file_put_contents($dest, $body) !== false;
}
