<?php
// Database connection (replace with your actual connection details)
$servername = "localhost";
$username = "root"; // or your DB username
$password = ""; // or your DB password
$dbname = "hungrymedb";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session to get the username
session_start();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if required fields are set
    if (isset($_POST['Addon']) && isset($_POST['btnID']) && isset($_POST['adminName'])) {
        $addon_price = $_POST['Addon'];
        $btnID = $_POST['btnID'];
        $adminName = $_POST['adminName'];
        
        // Retrieve AdminID based on the adminName
        $sql = "SELECT AdminID FROM admin WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $adminName);
        $stmt->execute();
        $stmt->bind_result($adminID);
        $stmt->fetch();
        $stmt->close();
        
        if ($adminID) {
            // Update the AddOn table
            if ($btnID == "Rice") {
                $sql = "UPDATE AddOn SET Price = ?, AdminID = ? WHERE AddOnID = 1";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("di", $addon_price, $adminID);
                $stmt->execute();
                
                if ($stmt->affected_rows > 0) {
                    header("Location: HUNGRYME-Admin.php");
                } else {
                    echo "Error updating record: " . $conn->error;
                }
                
                $stmt->close();
            } else {
                echo "Invalid btnID value.";
            }
        } else {
            echo "Admin name not found.";
        }
    } else {
        echo "Required form fields are missing.";
    }
} else {
    echo "Invalid request method.";
}

// Close the connection
$conn->close();
?>
