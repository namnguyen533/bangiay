<?php

declare(strict_types=1);

$config = require __DIR__ . '/../config/database.php';
$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
    $config['host'],
    $config['port'],
    $config['dbname'],
    $config['charset']
);
$db = new PDO($dsn, $config['username'], $config['password'], $config['options']);

$columns = [
    'brands' => ['name'],
    'categories' => ['name'],
    'products' => ['name', 'description'],
    'product_variants' => ['size', 'color'],
    'product_images' => ['alt_text'],
    'users' => ['full_name'],
    'user_addresses' => ['recipient_name', 'province', 'district', 'ward', 'address_line'],
    'orders' => ['recipient_name', 'shipping_address', 'note'],
    'order_items' => ['product_name', 'size', 'color'],
    'settings' => ['setting_value'],
];
$apply = in_array('--apply', $argv, true);
$changed = [];

if ($apply) {
    $db->beginTransaction();
}

try {
    foreach ($columns as $table => $fields) {
        foreach ($fields as $field) {
            $select = $db->query("SELECT id, `{$field}` AS value FROM `{$table}` WHERE `{$field}` IS NOT NULL");
            $update = $db->prepare("UPDATE `{$table}` SET `{$field}` = :value WHERE id = :id");

            while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
                $original = $row['value'];
                if (!preg_match('/[\x{2500}-\x{257F}]/u', $original)) {
                    continue;
                }

                $repaired = @iconv('UTF-8', 'CP850', $original);
                if ($repaired === false || $repaired === $original || preg_match('//u', $repaired) !== 1) {
                    continue;
                }

                $changed[$table . '.' . $field] = ($changed[$table . '.' . $field] ?? 0) + 1;
                if ($apply) {
                    $update->execute(['value' => $repaired, 'id' => $row['id']]);
                }
            }
        }
    }

    if ($apply) {
        $db->commit();
    }
} catch (Throwable $exception) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    throw $exception;
}

if (!$changed) {
    echo "No CP850-corrupted text found.\n";
    exit(0);
}

foreach ($changed as $column => $count) {
    echo $column . ': ' . $count . " row(s)\n";
}
echo $apply ? "Repair applied.\n" : "Dry run only. Re-run with --apply to repair.\n";
