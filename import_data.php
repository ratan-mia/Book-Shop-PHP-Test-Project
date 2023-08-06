<?php
require_once 'db_config.php';
require_once 'VersionComparison.php';

$jsonData = file_get_contents('sales_data.json');
$salesData = json_decode($jsonData, true);

foreach ($salesData as $sale) {
    $customerId = insertOrUpdateCustomer($db, $sale['customer_name'], $sale['customer_mail']);
    $productId = insertOrUpdateProduct($db, $sale['product_name'], $sale['product_price']);
    
    $versionComparisonResult = VersionComparison::compareVersions($sale['version'], '1.0.17+60');
    
    $saleDate = $versionComparisonResult
        ? $sale['sale_date']
        : convertToUTC($sale['sale_date'], 'Europe/Berlin', 'UTC');
    
    $sql = "INSERT INTO sales (customer_id, product_id, sale_date) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$customerId, $productId, $saleDate]);
}

function insertOrUpdateCustomer($db, $name, $email) {
    $sql = "SELECT id FROM customers WHERE email = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$email]);
    $customerId = $stmt->fetchColumn();

    if (!$customerId) {
        $sql = "INSERT INTO customers (name, email) VALUES (?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$name, $email]);
        $customerId = $db->lastInsertId();
    }

    return $customerId;
}

function insertOrUpdateProduct($db, $name, $price) {
    $sql = "SELECT id FROM products WHERE name = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$name]);
    $productId = $stmt->fetchColumn();

    if (!$productId) {
        $sql = "INSERT INTO products (name, price) VALUES (?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$name, $price]);
        $productId = $db->lastInsertId();
    }

    return $productId;
}

function convertToUTC($date, $fromTimezone, $toTimezone) {
    $dateTime = new DateTime($date, new DateTimeZone($fromTimezone));
    $dateTime->setTimezone(new DateTimeZone($toTimezone));
    return $dateTime->format('Y-m-d H:i:s');
}
?>
