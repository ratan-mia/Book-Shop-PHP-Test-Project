<?php
require_once 'db_config.php';

// Retrieve filter criteria from $_POST
$customer = $_POST['customer'] ?? '';
$product = $_POST['product'] ?? '';
$minPrice = $_POST['min_price'] ?? '';
$maxPrice = $_POST['max_price'] ?? '';

// Construct SQL query with filters
$sql = "SELECT s.sale_id, c.name AS customer_name, p.name AS product_name, p.price AS product_price, s.sale_date 
        FROM sales s
        JOIN customers c ON s.customer_id = c.id
        JOIN products p ON s.product_id = p.id
        WHERE c.name LIKE :customer
        AND p.name LIKE :product
        AND p.price >= :minPrice
        AND p.price <= :maxPrice";

$stmt = $db->prepare($sql);
$stmt->bindValue(':customer', '%' . $customer . '%');
$stmt->bindValue(':product', '%' . $product . '%');
$stmt->bindValue(':minPrice', $minPrice);
$stmt->bindValue(':maxPrice', $maxPrice);

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total price
$totalPrice = array_sum(array_column($results, 'product_price'));

// Display filtered results in a table
echo "<table border='1'>
        <tr>
            <th>Sale ID</th>
            <th>Customer Name</th>
            <th>Product Name</th>
            <th>Product Price</th>
            <th>Sale Date</th>
        </tr>";

foreach ($results as $row) {
    echo "<tr>
            <td>{$row['sale_id']}</td>
            <td>{$row['customer_name']}</td>
            <td>{$row['product_name']}</td>
            <td>{$row['product_price']}</td>
            <td>{$row['sale_date']}</td>
        </tr>";
}

echo "</table>";

// Display total price
echo "<p>Total Price: $totalPrice</p>";
