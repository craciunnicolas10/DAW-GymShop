<?php

/*
 * Creates config/config.local.php for the production server from environment variables
 * (GitHub Actions secrets). Used by the deploy workflow; the file is never committed.
 */

declare(strict_types=1);

$requiredVariables = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD'];

foreach ($requiredVariables as $variable) {
    if (getenv($variable) === false || getenv($variable) === '') {
        fwrite(STDERR, "Missing environment variable: {$variable}\n");
        exit(1);
    }
}

$database = [
    'host' => getenv('DB_HOST'),
    'port' => (int) (getenv('DB_PORT') ?: 3306),
    'name' => getenv('DB_NAME'),
    'user' => getenv('DB_USER'),
    'password' => getenv('DB_PASSWORD'),
];

$content = "<?php\n\n"
    . "// Generated automatically by the deploy workflow. Do not commit this file.\n"
    . "\$config = require __DIR__ . '/config.example.php';\n"
    . "\$config['app']['debug'] = false;\n"
    . "\$config['database'] = array_merge(\$config['database'], " . var_export($database, true) . ");\n\n"
    . "return \$config;\n";

$targetFile = dirname(__DIR__, 2) . '/config/config.local.php';
file_put_contents($targetFile, $content);

echo "Production configuration written to config/config.local.php\n";
