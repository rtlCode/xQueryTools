<?php

namespace xQuery\Tools;

class URI
{
    /**
     * @param array $url
     * @return string
     */
    public static function Builder(array $url = [
        'scheme' => 'vless',
        'host' => 'example.org',
        'user' => 'user',
        'port' => 443,
        'path' => null,
        'query' => [
            'type' => 'ws'
        ],
        'fragment' => 'xQueryAPI'
    ]): string
    {
        // Check Scheme 'vmess' / 'vless' / 'trojan' / 'https' / 'http' / ...
        $build = isset($url['scheme']) ? "{$url['scheme']}://" : '';
        // Check User 'uuid' / ...
        $build .= isset($url['user']) ? "{$url['user']}@" : '';
        // Check Host '1.1.1.1' / 'example.org'
        $build .= isset($url['host']) ? "{$url['host']}" : '';
        // Check Port 443 / ...
        $build .= isset($url['port']) ? ":{$url['port']}" : '';
        // Check Path 'myPath'
        $build .= isset($url['path']) ? "/{$url['path']}" : '';
        // Check Query ['type' => 'ws']
        $build .= is_array($url['query'] ?? '') ? '?' . http_build_query($url['query']) : '';
        // Check Fragment 'xQuery'
        $build .= isset($url['fragment']) ? "#{$url['fragment']}" : '';

        return $build;
    }

    /**
     * @param string $url
     * @return array
     */
    public static function Reader(string $url = 'vless://user@example.org:443?query#remark'): array
    {
        // Parse URL
        $url = parse_url($url) ?? [];
        // Protocol 'vmess' / 'vless' / 'trojan'
        $protocol = $url['scheme'] ?? '';
        // Return Data
        $host = '';
        $port = 0;
        $user = '';
        $queryHost = '';

        switch ($protocol) {
            case 'vmess':
                if ($url = base64_decode($url['host'] ?? '')) {
                    if ($url = json_decode($url, true)) {
                        $host = $url['add'] ?? '';
                        $port = $url['port'] ?? 0;
                        $user = $url['id'] ?? '';
                        $queryHost = $url['host'] ?? '';
                    }
                }
                break;

            case 'vless':
            case 'trojan':
                parse_str($url['query'] ?? '', $query);
                $host = $url['host'] ?? '';
                $port = $url['port'] ?? 0;
                $user = $url['user'] ?? '';
                $queryHost = $query['host'] ?? '';
                break;
        }

        return ($host && $port && $user) ? [
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'queryHost' => $queryHost,
        ] : [];
    }
}