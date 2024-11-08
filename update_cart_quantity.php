<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hungrymedb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the CartID, MenuItemID, and Quantity from the AJAX request
$cartID = $_POST['CartID'];
$menuItemID = $_POST['MenuItemID'];
$quantity = $_POST['Quantity'];

// Update the quantity in the cartmenuitem table
$sql = "UPDATE cartmenuitem SET Quantity = ? WHERE CartID = ? AND MenuItemID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $quantity, $cartID, $menuItemID);

if ($stmt->execute()) {
    echo "Quantity updated successfully";
} else {
    echo "Error updating quantity: " . $conn->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>