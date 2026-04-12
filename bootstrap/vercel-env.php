<?php

/**
 * Run before Laravel loads config so DATABASE_URL / POSTGRES_* are already normalized.
 *
 * - Strips accidental newlines (common when pasting secrets in the Vercel UI).
 * - Removes channel_binding from Postgres URIs (often breaks pdo_pgsql on Vercel’s runtime).
 */
if ((getenv('VERCEL') ?: ($_SERVER['VERCEL'] ?? $_ENV['VERCEL'] ?? '')) !== '1') {
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

foreach ($newlineStripKeys as $key) {
    $value = $_SERVER[$key] ?? $_ENV[$key] ?? getenv($key);
    if (! is_string($value) || $value === '') {
        continue;
    }

    $clean = preg_replace('/[\r\n]+/', '', $value) ?? $value;

    if (in_array($key, $urlKeys, true)) {
        $clean = $stripChannelBinding($clean);
    }

    if ($clean === $value) {
        continue;
    }

    $_ENV[$key] = $clean;
    $_SERVER[$key] = $clean;
    putenv($key.'='.$clean);
}
