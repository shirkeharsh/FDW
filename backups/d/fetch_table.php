<?php
if (!isset($_GET['table'])) {
    die("Table not specified.");
}

$tableName = $_GET['table'];

$host = "13.200.73.247"; 
$username = "jerry"; 
$password = "admin"; 
$database = "hvh"; 

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Table: $tableName</h2>";

$columnsQuery = $conn->query("SHOW COLUMNS FROM `$tableName`");
echo "<table border='1' cellspacing='0' cellpadding='5'><tr>";
$columns = [];
while ($column = $columnsQuery->fetch_assoc()) {
    $columns[] = $column['Field'];
    echo "<th>{$column['Field']}</th>";
}
echo "</tr>";

$dataQuery = $conn->query("SELECT * FROM `$tableName`");
if ($dataQuery->num_rows > 0) {
    while ($row = $dataQuery->fetch_assoc()) {
        echo "<tr>";
        foreach ($columns as $col) {
            echo "<td>" . htmlspecialchars($row[$col]) . "</td>";
        }
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='" . count($columns) . "'>No data found</td></tr>";
}
echo "</table>";

$conn->close();
?>
