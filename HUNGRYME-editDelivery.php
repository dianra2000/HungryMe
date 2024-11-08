<?php
$servername = "localhost";
$dbUsername = "root";  // Change this according to your server's username
$password = "";
$dbname = "hungrymedb";

// Create a connection to the database
$conn = new mysqli($servername, $dbUsername, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the order ID and username from the POST request
    $orderID = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $username = isset($_POST['username']) ? $_POST['username'] : '';

    if ($orderID > 0 && !empty($username)) {
        // Fetch the DeliveryBoyID based on the entered username
        $sqlDeliveryBoy = "SELECT DeliveryBoyID FROM deliveryboy WHERE name = ?";
        if ($stmtDeliveryBoy = $conn->prepare($sqlDeliveryBoy)) {
            $stmtDeliveryBoy->bind_param("s", $username);
            $stmtDeliveryBoy->execute();
            $stmtDeliveryBoy->bind_result($deliveryBoyID);
            $stmtDeliveryBoy->fetch();
            $stmtDeliveryBoy->close();

            if ($deliveryBoyID) {
                // Now, update the order with the status and DeliveryBoyID
                $sqlOrder = "UPDATE `order` SET OrderStatus = 'On the Way', DeliveryBoyID = ? WHERE OrderID = ?";
                if ($stmtOrder = $conn->prepare($sqlOrder)) {
                    $stmtOrder->bind_param("ii", $deliveryBoyID, $orderID);
                    if ($stmtOrder->execute()) {
                        header("Location: HUNGRYME-DeliveryBoy.php"); // Redirect after success
                        exit;
                    } else {
                        echo "Error: " . $stmtOrder->error;
                    }
                    $stmtOrder->close();
                } else {
                    echo "Error preparing statement: " . $conn->error;
                }
            } else {
                echo "No delivery boy found with the username: $username";
            }
        } else {
            echo "Error preparing statement to fetch DeliveryBoyID: " . $conn->error;
        }
    } else {
        echo "Invalid Order ID or username.";
    }
}

// Close the database connection
$conn->close();
?>
