<?php

declare(strict_types=1);

namespace Pccurico\HostingPanel\Services;

final class ApacheService
{
    private const SITES_ENABLED = '/etc/apache2/sites-enabled';

    public function getSites(): array
    {
        $files = glob(self::SITES_ENABLED . '/*.conf') ?: [];
        sort($files);

        $sites = [];

        foreach ($files as $file) {
            foreach ($this->parseConfigFile($file) as $site) {
                $site['config_file'] = basename($file);
                $site['enabled'] = true;
                $site['document_root_exists'] =
                    $site['document_root'] !== ''
                    && is_dir($site['document_root']);

                $site['document_root_writable'] =
                    $site['document_root'] !== ''
                    && is_dir($site['document_root'])
                    && is_writable($site['document_root']);

                $sites[] = $site;
            }
        }

        return $sites;
    }

    public function findSite(string $configFile, string $serverName): ?array
    {
        $configFile = basename($configFile);

        if (
            $configFile === '' ||
            !preg_match('/^[a-zA-Z0-9._-]+\.conf$/', $configFile)
        ) {
            return null;
        }

        $file = self::SITES_ENABLED . '/' . $configFile;

        if (!is_file($file)) {
            return null;
        }

        foreach ($this->parseConfigFile($file) as $site) {
            if (($site['server_name'] ?? '') === $serverName) {
                $site['config_file'] = $configFile;
                $site['enabled'] = true;
                $site['config_path'] = $file;
                $site['document_root_exists'] =
                    $site['document_root'] !== ''
                    && is_dir($site['document_root']);

                $site['document_root_writable'] =
                    $site['document_root'] !== ''
                    && is_dir($site['document_root'])
                    && is_writable($site['document_root']);

                $site['raw_config'] = $this->extractVirtualHost(
                    $file,
                    $serverName
                );

                return $site;
            }
        }

        return null;
    }

    public function getApacheStatus(): string
    {
        $output = [];
        exec('systemctl is-active apache2 2>/dev/null', $output);

        return trim($output[0] ?? '') ?: 'unknown';
    }

    public function getApacheVersion(): string
    {
        $output = [];
        exec('apache2ctl -v 2>/dev/null', $output);

        foreach ($output as $line) {
            if (preg_match('/Server version:\s*(.+)$/', $line, $m)) {
                return trim($m[1]);
            }
        }

        return 'unknown';
    }

    private function parseConfigFile(string $file): array
    {
        $content = @file_get_contents($file);

        if ($content === false) {
            return [];
        }

        preg_match_all(
            '/<VirtualHost\s+([^>]+)>(.*?)<\/VirtualHost>/is',
            $content,
            $blocks,
            PREG_SET_ORDER
        );

        $sites = [];

        foreach ($blocks as $block) {
            $endpoint = trim($block[1]);
            $body = $block[2];

            $serverName = '';

            if (
                preg_match(
                    '/^[\t ]*ServerName[\t ]+([^\s#]+)/mi',
                    $body,
                    $match
                )
            ) {
                $serverName = trim($match[1]);
            }

            if ($serverName === '') {
                continue;
            }

            $aliases = [];

            if (
                preg_match_all(
                    '/^[\t ]*ServerAlias[\t ]+(.+)$/mi',
                    $body,
                    $match
                )
            ) {
                foreach ($match[1] as $line) {
                    $line = preg_replace('/#.*/', '', $line);

                    foreach (preg_split('/\s+/', trim($line)) as $alias) {
                        if ($alias !== '') {
                            $aliases[] = $alias;
                        }
                    }
                }
            }

            $documentRoot = '';

            if (
                preg_match(
                    '/^[\t ]*DocumentRoot[\t ]+["\']?([^"\'>\s]+)["\']?/mi',
                    $body,
                    $match
                )
            ) {
                $documentRoot = trim($match[1]);
            }

            $phpHandler = 'No detectado';

            if (
                preg_match(
                    '/SetHandler\s+"?proxy:unix:([^|"]+)\|fcgi:\/\/localhost\/"?/i',
                    $body,
                    $match
                )
            ) {
                $socket = basename(trim($match[1]));

                if (preg_match('/php(\d+\.\d+)-fpm\.sock/', $socket, $php)) {
                    $phpHandler = 'PHP-FPM ' . $php[1];
                } else {
                    $phpHandler = $socket;
                }
            }

            $ssl = false;

            if (
                preg_match('/SSLEngine\s+on/i', $body) ||
                preg_match('/<VirtualHost\s+[^>]*:443>/i', $block[0])
            ) {
                $ssl = true;
            }

            $sites[] = [
                'server_name' => $serverName,
                'aliases' => array_values(array_unique($aliases)),
                'document_root' => $documentRoot,
                'endpoint' => $endpoint,
                'php_handler' => $phpHandler,
                'ssl' => $ssl,
            ];
        }

        return $sites;
    }

    private function extractVirtualHost(
        string $file,
        string $serverName
    ): string {
        $content = @file_get_contents($file);

        if ($content === false) {
            return '';
        }

        preg_match_all(
            '/<VirtualHost\s+([^>]+)>(.*?)<\/VirtualHost>/is',
            $content,
            $blocks,
            PREG_SET_ORDER
        );

        foreach ($blocks as $block) {
            $body = $block[2];

            if (
                preg_match(
                    '/^[\t ]*ServerName[\t ]+' .
                    preg_quote($serverName, '/') .
                    '[\t ]*$/mi',
                    $body
                )
            ) {
                return trim($block[0]);
            }
        }

        return '';
    }
}
