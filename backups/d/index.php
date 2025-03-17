<?php
$tables = ['coupons', 'dashboard', 'feedback', 'product', 'status', 'website'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Database Tables</title>
</head>
<body>
    <h1>Database Tables</h1>
    <ul>
        <li><a href="fetch_users_orders.php">Users & Orders</a></li>
        <?php foreach ($tables as $table): ?>
            <li><a href="fetch_table.php?table=<?php echo $table; ?>"><?php echo ucfirst($table); ?></a></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
