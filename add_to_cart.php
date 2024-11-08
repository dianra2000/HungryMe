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

if (isset($_POST['add_to_cart'])) {
    // Get the customer name from the POST data
    $customerName = $_POST['username'];
    $menuName = $_POST['MenuName'];
    $description = $_POST['Description'];

    // Retrieve the CusID for this customer name
    $sql = "SELECT CusID FROM customer WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $customerName);
    $stmt->execute();
    $result = $stmt->get_result();         

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $cusID = $row['CusID'];

        // Get the CartID for this customer from the cart table
        $cartSql = "SELECT CartID FROM cart WHERE CusID = ?";
        $cartStmt = $conn->prepare($cartSql);
        $cartStmt->bind_param("i", $cusID);
        $cartStmt->execute();
        $cartResult = $cartStmt->get_result();

        if ($cartResult->num_rows > 0) {
            $cartRow = $cartResult->fetch_assoc();
            $cartID = $cartRow['CartID'];

            // Fetch the MenuItemID for the selected menu item from the menuitem table
            $fetchMenuItemIDSql = "SELECT MenuItemID FROM menuitem WHERE MenuName = ? AND Description = ?";
            $fetchMenuItemIDStmt = $conn->prepare($fetchMenuItemIDSql);
            $fetchMenuItemIDStmt->bind_param("ss", $menuName, $description);
            $fetchMenuItemIDStmt->execute();
            $menuItemResult = $fetchMenuItemIDStmt->get_result();

            if ($menuItemResult->num_rows > 0) {
                $menuItemRow = $menuItemResult->fetch_assoc();
                $menuItemID = $menuItemRow['MenuItemID'];

                // Debug: print CartID and MenuItemID
                echo "CartID: $cartID, MenuItemID: $menuItemID<br>";

                // Insert the CartID and MenuItemID into the cartmenuitem table
                $insertCartMenuItemSql = "INSERT INTO cartmenuitem (CartID, MenuItemID) VALUES (?, ?)";
                $insertCartMenuItemStmt = $conn->prepare($insertCartMenuItemSql);
                $insertCartMenuItemStmt->bind_param("ii", $cartID, $menuItemID);

                if ($insertCartMenuItemStmt->execute()) {
                    echo "Item added to cart successfully.";
                } else {
                    // Debug: print error if execution fails
                    echo "Error: " . $insertCartMenuItemStmt->error;
                }
                $insertCartMenuItemStmt->close();
            } else {
                echo "Menu item not found.";
            }
            $fetchMenuItemIDStmt->close();
        } else {
            echo "No cart found for the customer.";
        }
        $cartStmt->close();
    } else {
        echo "No customer found with the name " . $customerName;
    }

    // Close connections
    $stmt->close();
}

$conn->close();
?>