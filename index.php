<!DOCTYPE html>
<html>

<head>
    <title>Book Shop Sales</title>
</head>

<body>
    <form action="filtered_results.php" method="post">
        <label for="customer">Customer:</label>
        <input type="text" id="customer" name="customer">

        <label for="product">Product:</label>
        <input type="text" id="product" name="product">

        <label for="min_price">Min Price:</label>
        <input type="text" id="min_price" name="min_price">

        <label for="max_price">Max Price:</label>
        <input type="text" id="max_price" name="max_price">

        <input type="submit" value="Filter">
    </form>
</body>

</html>