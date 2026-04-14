<?php

/**
 * Run before Laravel loads config so DATABASE_URL / POSTGRES_* are already normalized.
 *
 * - Strips accidental newlines (common when pasting secrets in the Vercel UI).
 * - Removes channel_binding from Postgres URIs (often breaks pdo_pgsql on Vercel’s runtime).
 * - If session/cache would use the database but migrations were not run on Neon, requests 500; we
 *   fall back to cookie sessions and array cache unless you explicitly set another non-database driver.
 */
$vercel = $_SERVER['VERCEL'] ?? $_ENV['VERCEL'] ?? getenv('VERCEL');
$vercelEnv = $_SERVER['VERCEL_ENV'] ?? $_ENV['VERCEL_ENV'] ?? getenv('VERCEL_ENV');
$onVercel = (string) $vercel === '1' || (is_string($vercelEnv) && $vercelEnv !== '');

if (! $onVercel) {
    return;
}

$urlKeys = [
    'DB_URL',
    'DATABASE_URL',
    'DATABASE_URL_UNPOOLED',
    'POSTGRES_URL',
    'POSTGRES_PRISMA_URL',
    'POSTGRES_URL_NON_POOLING',
    'POSTGRES_URL_NO_SSL',
    'SUPABASE_DB_URL',
];

$newlineStripKeys = array_merge($urlKeys, [
    'APP_KEY',
    'DB_HOST',
    'POSTGRES_USER',
    'POSTGRES_HOST',
    'POSTGRES_PASSWORD',
    'POSTGRES_DATABASE',
    'PGPASSWORD',
    'PGHOST',
    'PGHOST_UNPOOLED',
    'PGDATABASE',
]);

$stripChannelBinding = static function (string $url): string {
    if (! str_contains($url, 'channel_binding')) {
        return $url;
    }

    $url = preg_replace('/&channel_binding=[^&]*/', '', $url) ?? $url;
    $url = preg_replace('/\?channel_binding=[^&]*(&|$)/', '?', $url) ?? $url;
    $url = str_replace('?&', '?', $url);
    $url = rtrim($url, '?&');

    return $url;
};

$ensureNeonEndpointOption = static function (string $url): string {
    $parts = parse_url($url);
    if (! is_array($parts) || empty($parts['host'])) {
        return $url;
    }

    $host = (string) $parts['host'];
    if (! str_contains($host, '.neon.tech')) {
        return $url;
    }

    $endpointId = explode('.', $host)[0] ?? '';
    if ($endpointId === '') {
        return $url;
    }

    parse_str($parts['query'] ?? '', $query);
    $options = isset($query['options']) ? (string) $query['options'] : '';

    if (str_contains($options, 'endpoint=')) {
        return $url;
    }

    $query['options'] = trim($options === '' ? "endpoint={$endpointId}" : "{$options} endpoint={$endpointId}");

    $scheme = isset($parts['scheme']) ? $parts['scheme'].'://' : '';
    $user = $parts['user'] ?? '';
    $pass = isset($parts['pass']) ? ':'.$parts['pass'] : '';
    $auth = $user !== '' ? $user.$pass.'@' : '';
    $port = isset($parts['port']) ? ':'.$parts['port'] : '';
    $path = $parts['path'] ?? '';
    $fragment = isset($parts['fragment']) ? '#'.$parts['fragment'] : '';
    $queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

    return $scheme.$auth.$host.$port.$path.($queryString !== '' ? '?'.$queryString : '').$fragment;
};

foreach ($newlineStripKeys as $key) {
    $value = $_SERVER[$key] ?? $_ENV[$key] ?? getenv($key);
    if (! is_string($value) || $value === '') {
        continue;
    }

    $clean = preg_replace('/[\r\n]+/', '', $value) ?? $value;

    if (in_array($key, $urlKeys, true)) {
        $clean = $stripChannelBinding($clean);
        $clean = $ensureNeonEndpointOption($clean);
    }

    if ($clean === $value) {
        continue;
    }

    $_ENV[$key] = $clean;
    $_SERVER[$key] = $clean;
    putenv($key.'='.$clean);
}

$forceEnv = static function (string $key, string $value): void {
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
    putenv($key.'='.$value);
};

$sessionDriver = strtolower((string) ($_SERVER['SESSION_DRIVER'] ?? $_ENV['SESSION_DRIVER'] ?? getenv('SESSION_DRIVER') ?: ''));
if ($sessionDriver === '' || $sessionDriver === 'database') {
    $forceEnv('SESSION_DRIVER', 'cookie');
}

$cacheStore = strtolower((string) ($_SERVER['CACHE_STORE'] ?? $_ENV['CACHE_STORE'] ?? getenv('CACHE_STORE') ?: ''));
if ($cacheStore === '' || $cacheStore === 'database') {
    $forceEnv('CACHE_STORE', 'array');
}
