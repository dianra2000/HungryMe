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

if (isset($_POST['AddonID'])) {
    $addonID = $_POST['AddonID'];

    // Query to get the addon price where AddonID = 1
    $query = "SELECT Price FROM addon WHERE AddonID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $addonID);
    $stmt->execute();
    $stmt->bind_result($price);
    $stmt->fetch();
    $stmt->close();

    // Return the price as a response
    echo $price;
}
?>