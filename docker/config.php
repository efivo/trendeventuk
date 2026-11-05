<?php
// ============================================================
//  Trend Events API — configuration for Docker / Coolify.
//  Every value comes from an environment variable (set in Coolify → Environment Variables).
// ============================================================

function env_str(string $name, string $default = ''): string
{
    $v = getenv($name);
    return ($v === false || $v === '') ? $default : $v;
}
function env_bool(string $name, bool $default): bool
{
    $v = getenv($name);
    return ($v === false || $v === '') ? $default : filter_var($v, FILTER_VALIDATE_BOOLEAN);
}

define('DB_HOST', env_str('DB_HOST', 'db'));
define('DB_PORT', (int) env_str('DB_PORT', '3306'));
define('DB_NAME', env_str('DB_NAME', 'trendevents'));
define('DB_USER', env_str('DB_USER', 'trenduser'));
define('DB_PASS', env_str('DB_PASSWORD'));

define('NOTIFY_EMAIL', env_str('NOTIFY_EMAIL', 'Trendeventsuk@gmail.com'));
define('MAIL_FROM', env_str('MAIL_FROM', 'no-reply@trendevents.uk'));

// The container has no local mail server: set SMTP_PASS so emails go out through SMTP
define('SMTP_HOST', env_str('SMTP_HOST', 'mail.trendevents.uk'));
define('SMTP_PORT', (int) env_str('SMTP_PORT', '465'));
define('SMTP_USER', env_str('SMTP_USER', MAIL_FROM));
define('SMTP_PASS', env_str('SMTP_PASS'));
define('SMTP_VERIFY_SSL', env_bool('SMTP_VERIFY_SSL', true));

define('SITE_URL', env_str('SITE_URL', 'https://trendevents.uk'));
define('ALLOWED_ORIGINS', array_values(array_filter(array_map('trim', explode(',', env_str('ALLOWED_ORIGINS'))))));
define('DEBUG', env_bool('DEBUG', false));
define('SESSION_DIR', env_str('SESSION_DIR', '/var/www/trend_sessions'));

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }
    return $pdo;
}
