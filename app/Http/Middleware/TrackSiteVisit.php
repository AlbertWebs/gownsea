<?php

namespace App\Http\Middleware;

use App\Models\SiteVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackSiteVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethod('GET')
            || $response->getStatusCode() !== Response::HTTP_OK
            || ! str_contains(strtolower((string) $response->headers->get('content-type')), 'text/html')
            || $request->is('admin', 'admin/*', 'up', 'sanctum/*')
            || $this->isAutomated($request->userAgent())) {
            return $response;
        }

        $sessionId = $request->hasSession() ? $request->session()->getId() : null;
        if (! $sessionId) {
            return $response;
        }

        [$source, $referrerHost, $referrerPath, $hasAcquisitionSource] = $this->referrer($request);
        $attribution = [
            'utm_source' => $source,
            'utm_medium' => $this->utmValue($request, 'utm_medium'),
            'utm_campaign' => $this->utmValue($request, 'utm_campaign'),
            'utm_content' => $this->utmValue($request, 'utm_content'),
            'utm_term' => $this->utmValue($request, 'utm_term'),
        ];
        if ($hasAcquisitionSource) {
            $request->session()->put('site_analytics.source', $source);
            $request->session()->put('site_analytics.referrer_host', $referrerHost);
            $request->session()->put('site_analytics.referrer_path', $referrerPath);
            $request->session()->put('site_analytics.attribution', $attribution);
        } else {
            $source = (string) $request->session()->get('site_analytics.source', 'Direct');
            $referrerHost = $request->session()->get('site_analytics.referrer_host');
            $referrerPath = $request->session()->get('site_analytics.referrer_path');
            $attribution = (array) $request->session()->get('site_analytics.attribution', $attribution);
        }
        $userAgent = strtolower((string) $request->userAgent());
        $device = preg_match('/ipad|tablet|kindle|silk/', $userAgent) === 1
            ? 'tablet'
            : (preg_match('/mobile|iphone|android/', $userAgent) === 1 ? 'mobile' : 'desktop');

        SiteVisit::query()->create([
            'visitor_hash' => hash_hmac('sha256', $sessionId, (string) config('app.key')),
            'path' => Str::limit('/'.ltrim($request->path(), '/'), 500, ''),
            'source' => $source,
            'referrer_host' => $referrerHost,
            'referrer_path' => $referrerPath,
            'device' => $device,
            'utm_medium' => $attribution['utm_medium'] ?? null,
            'utm_campaign' => $attribution['utm_campaign'] ?? null,
            'utm_content' => $attribution['utm_content'] ?? null,
            'utm_term' => $attribution['utm_term'] ?? null,
            'created_at' => now(),
        ]);

        return $response;
    }

    /** @return array{string, ?string, ?string, bool} */
    private function referrer(Request $request): array
    {
        $raw = (string) $request->headers->get('referer', '');
        $parts = $raw !== '' ? parse_url($raw) : false;
        $host = is_array($parts) ? strtolower((string) ($parts['host'] ?? '')) : '';
        $ownHost = strtolower($request->getHost());
        $isInternal = $host !== '' && ($host === $ownHost || $host === 'gownsea.com' || str_ends_with($host, '.gownsea.com'));
        $utmSource = $request->query('utm_source');

        if (is_string($utmSource) && trim($utmSource) !== '') {
            $source = Str::limit(trim(preg_replace('/[^a-zA-Z0-9._ -]/', '', $utmSource) ?? ''), 120, '');
            $path = is_array($parts) ? (string) ($parts['path'] ?? '') : '';

            return [$source, $host !== '' && ! $isInternal ? Str::limit($host, 190, '') : null, $path !== '' ? Str::limit($path, 500, '') : null, true];
        }

        if ($host === '' || $isInternal) {
            return ['Direct', null, null, false];
        }

        $source = $host;
        $path = is_array($parts) ? (string) ($parts['path'] ?? '') : '';

        return [$source, Str::limit($host, 190, ''), $path !== '' ? Str::limit($path, 500, '') : null, true];
    }

    private function isAutomated(?string $userAgent): bool
    {
        return preg_match('/bot|crawler|spider|preview|headless|lighthouse|pagespeed/i', (string) $userAgent) === 1;
    }

    private function utmValue(Request $request, string $key): ?string
    {
        $value = $request->query($key);
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $clean = trim(preg_replace('/[\x00-\x1F\x7F]/u', '', $value) ?? '');

        return $clean !== '' ? Str::limit($clean, 190, '') : null;
    }
}
