<?php
require_once __DIR__ . '/config/database.php';
$cfg = require __DIR__ . '/config/database.php';

try {
    $pdo = new PDO("mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['dbname']};charset={$cfg['charset']}", $cfg['username'], $cfg['password'], $cfg['options']);
    
    echo "=== DANH SACH BRANDS ===\n";
    $brands = $pdo->query("SELECT id, name FROM brands")->fetchAll(PDO::FETCH_ASSOC);
    print_r($brands);

    echo "=== DANH SACH PRODUCTS ===\n";
    $products = $pdo->query("SELECT id, name, brand_id, status FROM products")->fetchAll(PDO::FETCH_ASSOC);
    print_r($products);

    echo "=== PRODUCT VARIANTS ===\n";
    $variants = $pdo->query("SELECT id, product_id, sku, size, color, price, stock_quantity, status FROM product_variants LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    print_r($variants);

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
