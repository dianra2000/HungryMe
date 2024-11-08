<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hungrymedb";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $riderName = isset($_POST['rider-name']) ? $conn->real_escape_string($_POST['rider-name']) : '';
    
    if (empty($riderName)) {
        echo "Rider Name is required.";
    } else {
        // Use the entered rider name as the username
        $username = $riderName;
        
        // Generate a unique password in the format DEL001, DEL002, etc.
        $usernamePrefix = "DEL";
        $latestIdQuery = "SELECT MAX(SUBSTRING(password, 4)) AS latest_id FROM deliveryboy WHERE password LIKE 'DEL%'";
        $result = $conn->query($latestIdQuery);
        
        if ($result) {
            $latestId = $result->fetch_assoc()['latest_id'];
            $newId = str_pad(($latestId ? intval($latestId) + 1 : 1), 3, '0', STR_PAD_LEFT);
            $password = $usernamePrefix . $newId;

            // Prepare and execute SQL statement
            $sql = "INSERT INTO deliveryboy (Name, password, role) VALUES (?, ?, 'delivery_boy')";
            if ($stmt = $conn->prepare($sql)) {
                // Bind parameters (s for string)
                $stmt->bind_param("ss", $username, $password);
                if ($stmt->execute()) {
                    header("Location: HUNGRYME-Admin.php");
                    exit(); // Make sure to exit after redirection
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        } else {
            echo "Error fetching latest ID: " . $conn->error;
        }
    }
}

// Close the connection
$conn->close();
?>
