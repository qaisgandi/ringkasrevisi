<?php

namespace App\Support;

class NeonConnectionUrl
{
    public static function addEndpointForLegacyLibpq(?string $url): ?string
    {
        if (! is_string($url) || ($parts = parse_url($url)) === false) {
            return $url;
        }

        $host = $parts['host'] ?? '';
        $hostLabel = explode('.', $host, 2)[0];
        $endpoint = preg_replace('/-pooler$/', '', $hostLabel);
        $queryOptions = [];
        parse_str($parts['query'] ?? '', $queryOptions);
        $password = isset($parts['pass']) ? rawurldecode($parts['pass']) : null;

        $hasEndpointOption = is_string($queryOptions['options'] ?? null)
            && str_contains($queryOptions['options'], 'endpoint=');

        if (! str_ends_with($host, 'neon.tech')
            || ! str_starts_with($endpoint, 'ep-')
            || ! isset($parts['user'], $password)
            || $hasEndpointOption
            || str_starts_with($password, 'endpoint=')) {
            return $url;
        }

        $authority = rawurlencode(rawurldecode($parts['user']))
            . ':' . rawurlencode("endpoint={$endpoint}$" . $password)
            . '@' . $host;

        if (isset($parts['port'])) {
            $authority .= ':' . $parts['port'];
        }

        return ($parts['scheme'] ?? 'postgresql') . '://'
            . $authority
            . ($parts['path'] ?? '')
            . (isset($parts['query']) ? '?' . $parts['query'] : '')
            . (isset($parts['fragment']) ? '#' . $parts['fragment'] : '');
    }
}
