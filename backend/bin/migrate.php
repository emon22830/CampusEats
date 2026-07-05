<?php

declare(strict_types=1);

use App\Database\Connection;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../.env')) {
    Dotenv::createImmutable(__DIR__ . '/..')->load();
}

$settings = require __DIR__ . '/../src/Config/settings.php';
$db = Connection::get($settings);

$db->exec(
    'CREATE TABLE IF NOT EXISTS schema_migrations (
        migration VARCHAR(180) NOT NULL PRIMARY KEY,
        applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);

$applied = $db->query('SELECT migration FROM schema_migrations')->fetchAll(PDO::FETCH_COLUMN);

$runSeed = in_array('--seed', $argv, true);

$batches = [
    'migrations' => glob(__DIR__ . '/../migrations/*.sql'),
];

if ($runSeed) {
    $batches['seed'] = glob(__DIR__ . '/../seed/*.sql');
}

foreach ($batches as $label => $files) {
    sort($files);

    foreach ($files as $file) {
        $name = $label . '/' . basename($file);

        if (in_array($name, $applied, true)) {
            echo "Skipping already-applied: {$name}\n";
            continue;
        }

        echo "Applying: {$name}\n";

        // MySQL DDL (CREATE TABLE, etc.) implicitly commits, so these files
        // aren't wrapped in a transaction - that's not something PDO/MySQL supports reliably.
        try {
            $db->exec((string) file_get_contents($file));

            $stmt = $db->prepare('INSERT INTO schema_migrations (migration) VALUES (:migration)');
            $stmt->execute(['migration' => $name]);
        } catch (Throwable $e) {
            fwrite(STDERR, "Failed applying {$name}: {$e->getMessage()}\n");
            exit(1);
        }
    }
}

echo "Done.\n";
