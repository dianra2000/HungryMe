<?php
// Start session to access session variables
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fetchOrderDetails'])) {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hungrymedb";

    // Create connection
    $conn = new mysqli($servername, $username, $password, "", $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch data from the database
    $sql = "SELECT Name, Address, PhoneNo, Landmarks, OrderID AS OID, PaymentMethod AS PaymentMethod, Email, OrderDate, shopname, menuitem
            FROM order";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo "Name: " . $row["Name"]. " - Address: " . $row["Address"]. " - Phone No: " . $row["PhoneNo"]. 
                 " - Landmarks: " . $row["Landmarks"]. " - Order ID: " . $row["OID"]. " - Payment Method: " . $row["PaymentMethod"]. 
                 " - Email: " . $row["Email"]. " - Order Date: " . $row["OrderDate"]. 
                 " - Shop Name: " . $row["shopname"]. " - Menu Item: " . $row["menuitem"]. "<br>";
        }
    } else {
        echo "No records found.";
    }

    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addMenuItem'])) {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hungrymedb";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Set parameters and execute
    $shopID = $_POST['ShopOwnerID'];
    $shopname = $_POST['ShopOwnerName'];
    $menuitemName = $_POST['menuitemName'];
    $menuitemLocation = $_POST['menuitemLocation'];
    $menuitemPrice = $_POST['menuitemPrice'];
    $menuitemDescription = $_POST['menuitemDescription'];
    $district = $_POST['menuitemDistrict'];

    $menuitemImage = "";
    if (isset($_FILES['menuitemImage']) && $_FILES['menuitemImage']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["menuitemImage"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true); 
        }

        $check = getimagesize($_FILES["menuitemImage"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["menuitemImage"]["size"] > 500000) { // 500KB limit
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // If everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["menuitemImage"]["tmp_name"], $target_file)) {
                $menuitemImage = $target_file; // Store the file path
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "File not uploaded or upload error.";
    }

    // Step 1: Get the ShopID from the shop table using the logged-in username
    $userName = $_COOKIE['username']; 
    $sqlShopID = "SELECT ShopID FROM shop WHERE ShopName = ?";
    $stmtShopID = $conn->prepare($sqlShopID);
    $stmtShopID->bind_param("s", $userName);
    $stmtShopID->execute();
    $resultShopID = $stmtShopID->get_result();
    $shopID = null;

    if ($resultShopID->num_rows > 0) {
        $rowShopID = $resultShopID->fetch_assoc();
        $shopID = $rowShopID['ShopID'];
    } else {
        echo "No shop found for the user.";
        exit;
    }
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO menuitem (Description, Price, MenuName, District, Location, ImagePath, ShopID) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsssss", $menuitemDescription, $menuitemPrice, $menuitemName, $district, $menuitemLocation, $menuitemImage, $shopID);

    if ($stmt->execute()) {
        echo "New menu item added successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['removeMenuItem'])) {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hungrymedb";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Set parameters and execute
    $menuItemID = $_POST['menuItemID'];

    // Prepare and bind
    $stmt = $conn->prepare("DELETE FROM menuitem WHERE MenuItemID = ?");
    $stmt->bind_param("i", $menuItemID);

    if ($stmt->execute()) {
        echo "Menu item removed successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

?>

<!-- Order Details -->

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

// Fetch the username from the cookie
$userName = htmlspecialchars($_COOKIE['username']);

// Initialize an empty array for order details
$orderDetails = [];

// Step 1: Get ShopID related to the username
$sqlShop = "SELECT ShopID FROM shop WHERE ShopName = ?";
$stmtShop = $conn->prepare($sqlShop);
$stmtShop->bind_param("s", $userName);
$stmtShop->execute();
$resultShop = $stmtShop->get_result();

if ($resultShop->num_rows > 0) {
    $rowShop = $resultShop->fetch_assoc();
    $shopID = $rowShop['ShopID'];

    // Step 2: Get OrderID related to the ShopID (use FIND_IN_SET to check for ShopID in comma-separated values)
    $sqlOrderShop = "SELECT OrderID FROM ordershop WHERE FIND_IN_SET(?, ShopID) > 0";
    $stmtOrderShop = $conn->prepare($sqlOrderShop);
    $stmtOrderShop->bind_param("s", $shopID);
    $stmtOrderShop->execute();
    $resultOrderShop = $stmtOrderShop->get_result();

    if ($resultOrderShop->num_rows > 0) {
        while ($rowOrderShop = $resultOrderShop->fetch_assoc()) {
            $orderID = $rowOrderShop['OrderID'];

            // Step 3: Get MenuItemID and AddonID related to the OrderID
            $sqlOrderItem = "SELECT MenuItemID, AddonID FROM orderitem WHERE OrderID = ?";
            $stmtOrderItem = $conn->prepare($sqlOrderItem);
            $stmtOrderItem->bind_param("i", $orderID);
            $stmtOrderItem->execute();
            $resultOrderItem = $stmtOrderItem->get_result();

            if ($resultOrderItem->num_rows > 0) {
                while ($rowOrderItem = $resultOrderItem->fetch_assoc()) {
                    $menuItemID = $rowOrderItem['MenuItemID'];
                    $addonID = $rowOrderItem['AddonID'];

                    // Step 4: Get MenuName and Description from menuitem table
                    $sqlMenuItem = "SELECT MenuName, Description FROM menuitem WHERE MenuItemID = ?";
                    $stmtMenuItem = $conn->prepare($sqlMenuItem);
                    $stmtMenuItem->bind_param("i", $menuItemID);
                    $stmtMenuItem->execute();
                    $resultMenuItem = $stmtMenuItem->get_result();

                    if ($resultMenuItem->num_rows > 0) {
                        $rowMenuItem = $resultMenuItem->fetch_assoc();
                        $menuName = $rowMenuItem['MenuName'];
                        $description = $rowMenuItem['Description'];

                        // Step 5: Get DeliveryBoyID from the order table
                        $sqlOrder = "SELECT DeliveryBoyID, CartID FROM `order` WHERE OrderID = ?";
                        $stmtOrder = $conn->prepare($sqlOrder);
                        $stmtOrder->bind_param("i", $orderID);
                        $stmtOrder->execute();
                        $resultOrder = $stmtOrder->get_result();

                        if ($resultOrder->num_rows > 0) {
                            $rowOrder = $resultOrder->fetch_assoc();
                            $deliveryBoyID = $rowOrder['DeliveryBoyID'];
                            $cartID = $rowOrder['CartID'];

                            // Step 6: Get Quantity from the cartmenuitem table based on CartID and MenuItemID
                            $sqlCartMenuItem = "SELECT Quantity FROM cartmenuitem WHERE CartID = ? AND MenuItemID = ?";
                            $stmtCartMenuItem = $conn->prepare($sqlCartMenuItem);
                            $stmtCartMenuItem->bind_param("ii", $cartID, $menuItemID);
                            $stmtCartMenuItem->execute();
                            $resultCartMenuItem = $stmtCartMenuItem->get_result();

                            if ($resultCartMenuItem->num_rows > 0) {
                                $rowCartMenuItem = $resultCartMenuItem->fetch_assoc();
                                $quantity = $rowCartMenuItem['Quantity'];

                                // Store the details in an array
                                $orderDetails[] = [
                                    'OrderID' => $orderID,
                                    'MenuName' => $menuName,
                                    'Description' => $description,
                                    'AddonID' => $addonID,
                                    'DeliveryBoyID' => $deliveryBoyID,
                                    'Quantity' => $quantity
                                ];
                            }
                        }
                    }
                }
            }
        }
    }
} 

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="title.jpg">
    <link rel="C:\wamp64\www\Final\Images">
    <title>HUNGRYME_Shop_Owner</title>
    <link rel="stylesheet" type="text/css" href="Bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="H_Style.css">
    <script type="text/javascript" src="Bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="jquery-3.7.1.js"></script>
    <style>
        /* The following styles are not relevant to the toggle button or list items */
        #l1,
        #l2,
        #l3,
        #l4,
        #l5,
        #l6,
        #l7 {
            display: none;
            /* Initially hide the list items */
        }
    </style>
</head>

<body>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#btnbars").click(function () {
                $("#l1").toggle("slow");
                $("#l2").toggle("slow");
                $("#l3").toggle("slow");
                $("#l4").toggle("slow");
                $("#l5").toggle("slow");
                $("#l6").toggle("slow");
                $("#l7").toggle("slow");
            });

            // Show modal on shop owner request button click
            $('#showShopOwnerRequestModal').click(function () {
                $('#shopOwnerRequestModal').modal('show');
            });

            // Add menu item to the list
            $('#btnAddMenuItem').click(function () {
                var itemName = $('#menu-item-name').val();
                var itemPrice = $('#menu-item-price').val();
                if (itemName && itemPrice) {
                    $('#menuList').append('<li>' + itemName + ' - ' + itemPrice + '</li>');
                    $('#menu-item-name').val('');
                    $('#menu-item-price').val('');
                }
            });
        });
    </script>

    <nav class="navbar navbar-expand-sm bg-warning">
        <div class="container-fluid">
            <div class="logo textual pull-left">
                <img src="HUNGRYME(txt).png" height="40px" alt="Logo">
            </div>
            <ul class="navbar-nav ml-auto">
                <li id="l1"><a class="nav-link" href="#home">Home</a></li>
                <li id="l2"><a class="nav-link" href="#footer">About us</a></li>
                <li id="l3"><a class="nav-link" href="https://wa.me/94722714507">Contact us</a></li>
                <li id="l4"><a class="nav-link" href="https://maps.app.goo.gl/5sHYmUQesEMHQfWNA">Main Branch</a></li>
                <li id="l7">
                    <div class="buttonDark"> <button onclick="DarkMode()">
                            <i class="fa-solid fa-moon fa-xl"></i>
                        </button>
                    </div>
                    <script>
                        function DarkMode() {
                            var element = document.body;
                            element.classList.toggle("dark-mode");
                        }
                    </script>
                </li>
                <li> <button id="btnbars"><i class="fa-solid fa-bars fa-2xl"></i></button>
                </li>
            </ul>
        </div>
    </nav>

    <div class="Topic">
        <h1> 
            <!-- $username = "shop_owner_username"; -->
        </h1>
    </div>
    <div id="typewriter">
        <script>
            var i = 0;
            var firstText = "Hey there...! Welcome to HUNGRYME...";
            var secondText = "Join us to grow your shop...";
            var speed = 50;
            var phase = 1;

            function typeWriter() {
                if (phase === 1) {
                    if (i < firstText.length) {
                        document.getElementById("typewriter").innerHTML += firstText.charAt(i);
                        i++;
                        setTimeout(typeWriter, speed);
                    } else {
                        setTimeout(clearText, 0); // Clear immediately
                    }
                } else if (phase === 2) {
                    if (i < secondText.length) {
                        document.getElementById("typewriter").innerHTML += secondText.charAt(i);
                        i++;
                        setTimeout(typeWriter, speed);
                    } else {
                        setTimeout(clearText, 0); // Clear immediately
                    }
                }
            }

            function clearText() {
                i = 0;
                document.getElementById("typewriter").innerHTML = "";
                phase = phase === 1 ? 2 : 1; // Switch between phase 1 and phase 2
                setTimeout(typeWriter, speed);
            }

            window.onload = function () {
                typeWriter();
            };
        </script>
    </div>
    <br>

    <label for="CustomerName" style="display: inline-block; font-weight: bold; padding-left: 20px;">Hello</label>
    <input type="text" id="CustomerName" name="CustomerName" readonly value="<?php echo isset($_COOKIE['username']) ? $_COOKIE['username'] : 'ABC'; ?>" style="display: inline-block; border: none; font-weight: bold; background-color: transparent; color: black;">

    <br><br>

    <div class="container" id="menuList">
    <h2 class="text-center" style="color: red;">Order Details</h2>
        <table class="table table-striped" style="box-shadow: 0 0 0 2px red;">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Menu Items</th>
                    <th>Description</th>
                    <th>Addon</th>
                    <th>DeliveryBoy ID</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orderDetails)): ?>
                    <?php foreach ($orderDetails as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['OrderID']); ?></td>
                            <td><?php echo htmlspecialchars($order['MenuName']); ?></td>
                            <td><?php echo htmlspecialchars($order['Description']); ?></td>
                            <td><?php echo isset($order['AddonID']) && $order['AddonID'] !== null ? "selected" : "not selected"; ?></td>
                            <td><?php echo htmlspecialchars(!empty($order['DeliveryBoyID']) ? $order['DeliveryBoyID'] : "Not Accepted"); ?></td>
                            <td><?php echo htmlspecialchars($order['Quantity']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No records found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <br><br>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hungrymedb";
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $userName = $_COOKIE['username']; 
    $sqlShopID = "SELECT ShopID FROM shop WHERE ShopName = ?";
    $stmtShopID = $conn->prepare($sqlShopID);
    $stmtShopID->bind_param("s", $userName);
    $stmtShopID->execute();
    $resultShopID = $stmtShopID->get_result();
    $shopID = null;

    if ($resultShopID->num_rows > 0) {
        $rowShopID = $resultShopID->fetch_assoc();
        $shopID = $rowShopID['ShopID'];
    } else {
        echo "No shop found for the user.";
        exit;
    }

    $sqlMenuItems = "SELECT MenuItemID, MenuName, Description, Price FROM menuitem WHERE ShopID = ?";
    $stmtMenuItems = $conn->prepare($sqlMenuItems);
    $stmtMenuItems->bind_param("s", $shopID);
    $stmtMenuItems->execute();
    $resultMenuItems = $stmtMenuItems->get_result();

    $menuItems = [];
    if ($resultMenuItems->num_rows > 0) {
        while ($rowMenuItem = $resultMenuItems->fetch_assoc()) {
            $menuItems[] = $rowMenuItem;
        }
    } else {
        echo "No menu items found for the shop.";
    }
    ?>

    <!-- Display the menu list -->
    <div class="container" id="menuList">
    <h3 style="text-align: center;">Menu List</h3>
        <table class="table table-striped" style="box-shadow: 0 0 0 2px blue;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($menuItems)) : ?>
                    <?php foreach ($menuItems as $item) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['MenuItemID']); ?></td>
                            <td><?php echo htmlspecialchars($item['MenuName']); ?></td>
                            <td><?php echo htmlspecialchars($item['Description']); ?></td>
                            <td><?php echo htmlspecialchars($item['Price']); ?></td>
                            <td>
                                <form method="POST" action="#" style="display:inline;">
                                    <input type="hidden" name="menuItemID" value="<?php echo htmlspecialchars($item['MenuItemID']); ?>">
                                    <input type="hidden" name="removeMenuItem" value="1">
                                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No menu items found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>



    <br><br>
    <!-- Trigger Button -->
    <form class="form" id="ShopOwnerForm" method="POST" action="#" enctype="multipart/form-data">
    <!-- Add Item Button -->
    <button id="openAddModal">Add Item</button>

    <!-- Modal Structure for Adding -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close-add">&times;</span>
            <div class="container" id="ShopOwnerCon">
                <h2>Add New Item to Menu</h2> <br><br>
                <form class="form" id="ShopOwnerForm" method="POST" action="#" enctype="multipart/form-data">
                    <input type="hidden" name="addMenuItem" value="1">
                    <label for="ShopOwnerID">Shop Owner ID</label><br>
                    <input type="text" id="ShopOwnerID" name="ShopOwnerID" readonly value="<?php echo $_COOKIE['password'] ; ?>"><br><br>

                    <label for="ShopOwnerName">Shop Owner Name</label><br>
                    <input type="text" id="ShopOwnerName" name="ShopOwnerName" readonly value="<?php echo $_COOKIE['username'] ; ?>"><br><br>

                    <select id="menuitemName" name="menuitemName" required>
                    <option value="" disabled selected>Select food 🔽</option>
                        <option value="Kottu">Kottu</option>
                        <option value="Rice">Rice</option>
                        <option value="Pizza">Pizza</option>
                        <option value="Beverage">Beverage</option>
                        <option value="Noodles">Noodles</option>
                    </select> <br><br>

                    <label for="menuitemLocation" id="label">Location</label> <br>
                    <input type="text" id="menuitemLocation" name="menuitemLocation" placeholder="Enter Location" required><br><br>

                    <label for="menuitemDistrict" id="label">District</label> <br>
                    <input type="text" id="menuitemDistrict" name="menuitemDistrict" placeholder="Enter District" required><br><br>

                    <label for="menuitemPrice" id="label">Menu Item Price</label> <br>
                    <input type="number" id="menuitemPrice" name="menuitemPrice" placeholder="Enter Menu Item Price" required><br><br>

                    <label for="menuitemDescription" id="label">Menu Item Description</label> <br>
                    <textarea id="menuitemDescription" name="menuitemDescription" placeholder="Ingredients are - eggs, Chicken, ..." ></textarea><br><br>

                    <label for="menuitemImage" id="label">Menu Item Image</label> <br>
                    <input type="file" id="menuitemImage" name="menuitemImage" accept="image/*">
                    <div class="checkbox-container">
                        <input type="checkbox" id="confirmAdd" name="confirmAdd" required>
                        <label for="confirmAdd">Confirm to Add</label> <br><br>
                    </div>
                    <button id="btnAddMenuItem" type="submit">Add Menu Item</button> <br><br>
                </form>
            </div>
        </div>
    </div>

    <br>
    <!-- Edit Item Button -->
    <button id="openEditModal" style="background-color: yellow; width: 100%; border-radius: 10px; border: none; padding: 10px; cursor: pointer; font-weight: bold;">Edit Item</button>

    <!-- Modal Structure for Editing -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-edit">&times;</span>
            <div class="container" id="EditItemCon">
                <h2>Edit Menu Item</h2> <br><br>
                <form class="form" id="EditItemForm" method="POST" action="update_item.php" enctype="multipart/form-data">
                    <input type="hidden" name="updateMenuItem" value="1">
                    <input type="hidden" id="itemId" name="itemId" value="">

                    <label for="MenuItemID">Menu Item ID</label><br>
                    <input type="text" id="MenuItemID" name="MenuItemID" placeholder="Enter Menu Item ID" required><br><br>

                    <label for="ShopOwnerID">Shop Owner ID</label><br>
                    <input type="text" id="ShopOwnerID" name="ShopOwnerID" readonly value="<?php echo $_COOKIE['password']; ?>"><br><br>

                    <label for="ShopOwnerName">Shop Owner Name</label><br>
                    <input type="text" id="ShopOwnerName" name="ShopOwnerName" readonly value="<?php echo $_COOKIE['username']; ?>"><br><br>

                    <label for="menuitemName" id="label">Menu Item Name</label> <br>
                    <input type="text" id="menuitemName" name="menuitemName" placeholder="Enter Menu Item Name" required><br><br>

                    <label for="menuitemLocation" id="label">Location</label> <br>
                    <input type="text" id="menuitemLocation" name="menuitemLocation" placeholder="Enter Location" required><br><br>

                    <label for="menuitemDistrict" id="label">District</label> <br>
                    <input type="text" id="menuitemDistrict" name="menuitemDistrict" placeholder="Enter District" required><br><br>

                    <label for="menuitemPrice" id="label">Menu Item Price</label> <br>
                    <input type="number" id="menuitemPrice" name="menuitemPrice" placeholder="Enter Menu Item Price" required><br><br>

                    <label for="menuitemDescription" id="label">Menu Item Description</label> <br>
                    <textarea id="menuitemDescription" name="menuitemDescription" placeholder="Ingredients are - eggs, Chicken, ..."></textarea><br><br>

                    <label for="menuitemImage" id="label">Menu Item Image</label> <br>
                    <input type="file" id="menuitemImage" name="menuitemImage" accept="image/*">
                    
                    <div class="checkbox-container">
                        <input type="checkbox" id="confirmEdit" name="confirmEdit" required>
                        <label for="confirmEdit">Confirm to Update</label> <br><br>
                    </div>
                    <button id="btnUpdateMenuItem" type="submit">Update Menu Item</button> <br><br>
                </form>
            </div>
        </div>
    </div>



    <script>
        // Get the Add Item modal element
        var addModal = document.getElementById("addModal");
        var openAddModalBtn = document.getElementById("openAddModal");
        var closeAddSpan = document.getElementsByClassName("close-add")[0];

        // Get the Edit Item modal element
        var editModal = document.getElementById("editModal");
        var openEditModalBtn = document.getElementById("openEditModal");
        var closeEditSpan = document.getElementsByClassName("close-edit")[0];

        // Open Add Item modal
        openAddModalBtn.onclick = function() {
            addModal.style.display = "block";
        }

        // Close Add Item modal
        closeAddSpan.onclick = function() {
            addModal.style.display = "none";
        }

        // Open Edit Item modal
        openEditModalBtn.onclick = function() {
            editModal.style.display = "block";
        }

        // Close Edit Item modal
        closeEditSpan.onclick = function() {
            editModal.style.display = "none";
        }

        // Close modal if clicked outside of modal content
        window.onclick = function(event) {
            if (event.target == addModal) {
                addModal.style.display = "none";
            }
            if (event.target == editModal) {
                editModal.style.display = "none";
            }
        }
    </script>

    <br><br>
    <!-- Footer -->
    <footer>
        <div class="text-center text-lg-start" id="footer">

            <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
                <!-- Left -->
                <div class="me-5 d-none d-lg-block">
                    <span>Check with us on social networks:</span>
                </div>
                <!-- Right -->
                <div>
                    <a href="" class="me-4 text-reset"><i class="fab fa-facebook-f"></i></a>
                    <a href="" class="me-4 text-reset"><i class="fab fa-google"></i></a>
                    <a href="" class="me-4 text-reset"><i class="fab fa-instagram"></i></a>
                </div>
            </section>

            <div class="row mt-3">
                <div class="col-md-6 col-lg-4 col-xl-8 mx-auto mb-4">
                    <h4 class="text-uppercase fw-bold mb-4"><i class="fas fa-burger me-3"></i>HungryMe</h4>
                    <p>Join us to quench your hunger</p>
                </div>
                <!-- Grid column -->
                <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                    <h6 class="text-uppercase fw-bold mb-4">Contact</h6>
                    <p><i class="fas fa-home me-3"></i> No.46, Matara RD, Galle</p>
                    <p><i class="fas fa-envelope me-3"></i>hungryme@gmail.com</p>
                    <p><i class="fas fa-phone me-3"></i> + 94 915 628 313</p>
                    <p><i class="fas fa-phone me-3"></i> + 94 915 628 314</p>
                </div>
            </div>
            <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">© 2024 Copyright:<a
                    class="text-reset fw-bold" href="http://localhost/Final/HungryMe/HUNGRYME.php">Hungryme.com</a>
            </div>
    </footer>
</body>

</html>