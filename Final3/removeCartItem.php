<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Database connection
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "hungrymedb"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cartID']) && isset($_POST['menuItemID'])) {
    $cartID = $_POST['cartID'];
    $menuItemID = $_POST['menuItemID'];

    // Prepare and execute the SQL query to delete the item
    $sql = "DELETE FROM cartmenuitem WHERE CartID = ? AND MenuItemID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cartID, $menuItemID);

    if ($stmt->execute()) {
        echo "Item removed successfully";
    } else {
        echo "Error: Could not remove the item.";
    }

    $stmt->close();
}
?>