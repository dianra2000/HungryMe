<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hungrymedb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT Name, Address, PhoneNo, Landmarks, OrderID AS OID, PaymentMethod AS PaymentMethod, Email, OrderDate
        FROM `order`";
        
$result = $conn->query($sql);
?>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hungrymedb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $delivery_boy_username = $_POST['delivery_boy_username'];

    // Fetch the DeliveryBoyID for the entered username
    $stmt = $conn->prepare("SELECT DeliveryBoyID FROM deliveryboy WHERE Name = ?");
    $stmt->bind_param("s", $delivery_boy_username);
    $stmt->execute();
    $stmt->bind_result($delivery_boy_id);
    $stmt->fetch();
    $stmt->close();

    if ($delivery_boy_id) {
        // Check if the DeliveryBoyID matches the one in the order table
        $stmt = $conn->prepare("SELECT DeliveryBoyID FROM `order` WHERE OrderID = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->bind_result($order_delivery_boy_id);
        $stmt->fetch();
        $stmt->close();

        if ($order_delivery_boy_id == $delivery_boy_id) {
            // Update the order status to 'delivered'
            $stmt = $conn->prepare("UPDATE `order` SET OrderStatus = 'delivered' WHERE OrderID = ?");
            $stmt->bind_param("i", $order_id);

            if ($stmt->execute()) {
                echo "<script>alert('Order Delivery Success'); window.location.href = 'HUNGRYME-DeliveryBoy.php';</script>";
                exit();
            } else {
                echo "Error updating record: " . $stmt->error;
            }
        } else {
            echo "<script>alert('Delivery Boy ID does not match with the order!'); window.location.href = 'HUNGRYME-DeliveryBoy.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid Delivery Boy Username!'); window.location.href = 'HUNGRYME-DeliveryBoy.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>



<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hungrymedb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderID = $_POST['order_id'];
    $deliveryDate = date('Y-m-d H:i:s');

    $sql = "INSERT INTO deliveryboy (DeliveryDate, Status, DeliveryAddress, OrderID) 
            VALUES (?, 'Delivered', 'Address', 'OID')";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param('si', $deliveryDate, $orderID);

    if ($stmt->execute()) {
        echo "Order marked as delivered successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

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

// Query to get the order details with menu items grouped by OrderID and filtering by OrderStatus
$sql = "SELECT o.OrderID, o.OrderDate, o.Landmarks, o.Address, o.PhoneNo, o.Name, o.OrderStatus,
        GROUP_CONCAT(mi.MenuName SEPARATOR ', ') as MenuItems, s.ShopName, o.TotAmount, o.PaymentMethod
        FROM `order` o 
        JOIN orderitem oi ON o.OrderID = oi.OrderID
        JOIN menuitem mi ON oi.MenuItemID = mi.MenuItemID
        JOIN shop s ON mi.ShopID = s.ShopID
        WHERE o.OrderStatus IN ('On the Way', 'Pending') 
        GROUP BY o.OrderID";

$result = $conn->query($sql);

// Array to hold the order details
$orderDetails = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Store the order details with the concatenated menu items
        $orderDetails[] = [
            'Name' => $row['Name'],
            'Address' => $row['Address'],
            'PhoneNo' => $row['PhoneNo'],
            'OrderDate' => $row['OrderDate'],
            'Landmarks' => $row['Landmarks'],
            'OID' => $row['OrderID'],
            'OrderStatus' => $row['OrderStatus'],
            'shopname' => $row['ShopName'], 
            'menuitem' => $row['MenuItems'], 
            'TotAmount' => $row['TotAmount'], 
            'PaymentMethod' => $row['PaymentMethod'] 
        ];
    }
}

?>

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
$orderAssigned = false;
// Check if order_id and username are posted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['username'])) {
    $orderID = $_POST['order_id'];
    $deliveryUsername = $_POST['username'];

    // Step 1: Get DeliveryBoyID from deliveryboy table based on username
    $sqlDeliveryBoy = "SELECT DeliveryBoyID FROM deliveryboy WHERE name = ?";
    $stmtDeliveryBoy = $conn->prepare($sqlDeliveryBoy);
    $stmtDeliveryBoy->bind_param("s", $deliveryUsername);
    $stmtDeliveryBoy->execute();
    $resultDeliveryBoy = $stmtDeliveryBoy->get_result();

    if ($resultDeliveryBoy->num_rows > 0) {
        $rowDeliveryBoy = $resultDeliveryBoy->fetch_assoc();
        $deliveryBoyID = $rowDeliveryBoy['DeliveryBoyID'];

        // Step 2: Update the order with DeliveryBoyID
        $sqlUpdateOrder = "UPDATE `order` SET DeliveryBoyID = ? WHERE OrderID = ?";
        $stmtUpdateOrder = $conn->prepare($sqlUpdateOrder);
        $stmtUpdateOrder->bind_param("ii", $deliveryBoyID, $orderID);

        if ($stmtUpdateOrder->execute()) {
            // Redirect back to the same page to reflect the button color change
            header("Location: HUNGRYME-DeliveryBoy.php"); 
            exit();
        } else {
            echo "Error updating order: " . $conn->error;
        }
    } else {
        echo "Delivery boy not found.";
    }

    // Close the statement
    $stmtDeliveryBoy->close();
    $stmtUpdateOrder->close();
}

// Close the connection
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="title.jpg">
    <link rel="C:\wamp64\www\Final\Images">
    <title>HUNGRYME_Delivery_Boy</title>
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

            // Show modal on handle delivery button click
            $('#showHandleDeliveryModal').click(function () {
                $('#handleDeliveryModal').modal('show');
            });

            // Show modal on handle payment button click
            $('#showHandlePaymentModal').click(function () {
                $('#handlePaymentModal').modal('show');
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
                    <div class="buttonDark">
                        <button onclick="toggleDarkMode()">
                            <i class="fa-solid fa-moon fa-xl"></i>
                        </button>
                    </div>
                </li>
                <li>
                    <button id="btnbars"><i class="fa-solid fa-bars fa-2xl"></i></button>
                </li>
            </ul>
        </div>
    </nav>

    <br><br>
    <label for="CustomerName" style="display: inline-block; font-weight: bold; padding-left: 20px;">Hello</label>
    <input type="text" id="CustomerName" name="CustomerName" readonly value="<?php echo isset($_COOKIE['username']) ? $_COOKIE['username'] : 'ABC'; ?>" style="display: inline-block; border: none; font-weight: bold; background-color: transparent; color: black;">

    <br><br>

    <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
        <h2 class="text-center">Delivery Details</h2>
        <center>
        <table class="table table-bordered" style="max-width: 1500px;">
            <thead>
                <tr>
                    <th>OID</th>
                    <th>Customer Name</th>
                    <th>Address</th>
                    <th>Phone Number</th>
                    <th>Order Date</th>
                    <th>Land Mark</th>
                    <th>Shop Names</th>
                    <th>Item Names</th>
                    <th>Total Price</th>
                    <th>Payment method</th>
                    <th>Order Status </th>
                    <th>Click the Order</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orderDetails)): ?>
                    <?php foreach ($orderDetails as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['OID']); ?></td>
                            <td><?php echo htmlspecialchars($order['Name']); ?></td>
                            <td><?php echo htmlspecialchars($order['Address']); ?></td>
                            <td><?php echo htmlspecialchars($order['PhoneNo']); ?></td>
                            <td><?php echo htmlspecialchars($order['OrderDate']); ?></td>
                            <td><?php echo htmlspecialchars($order['Landmarks']); ?></td>
                            <td><?php echo htmlspecialchars($order['shopname']); ?></td> 
                            <td><?php echo htmlspecialchars($order['menuitem']); ?></td>
                            <td><?php echo htmlspecialchars($order['TotAmount']); ?></td>
                            <td><?php echo htmlspecialchars($order['PaymentMethod']); ?></td>
                            <td><?php echo htmlspecialchars($order['OrderStatus']); ?></td>
                            <td>
                                <!-- Click Order button -->
                                <form id="orderForm<?php echo htmlspecialchars($order['OID']); ?>" method="POST" action="HUNGRYME-editDelivery.php">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['OID']); ?>">
                                    <input type="hidden" id="usernameField<?php echo htmlspecialchars($order['OID']); ?>" name="username" value="">

                                    <button type="button" 
                                            class="btn <?php echo $orderAssigned ? 'btn-danger' : 'btn-warning'; ?>" 
                                            style="<?php echo $orderAssigned ? 'background-color: red; color: white;' : 'background-color: yellow; color: black;'; ?>"
                                            onclick="promptUsername('<?php echo htmlspecialchars($order['OID']); ?>')">
                                        <?php echo $orderAssigned ? 'Order Assigned' : 'Click Order'; ?>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form id="completeOrderForm" method="POST" action="HUNGRYME-DeliveryBoy.php">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['OID']); ?>">
                                    <button type="button" class="btn btn-primary" onclick="openUsernamePrompt()">Complete Order</button>
                                </form>
                            </td>
                            <script>
                                function openUsernamePrompt() {
                                    const username = prompt("Enter Delivery Boy Username:");
                                    if (username) {
                                        // Set the username into a hidden input and submit the form
                                        const form = document.getElementById('completeOrderForm');
                                        const input = document.createElement('input');
                                        input.type = 'hidden';
                                        input.name = 'delivery_boy_username';
                                        input.value = username;
                                        form.appendChild(input);
                                        form.submit();
                                    }
                                }
                            </script>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">No records found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </center>
    </div>

    <br><br>

<!-- Add JavaScript function -->
<script>
    function promptUsername(orderID) {
        // Prompt the user to enter their username
        var username = prompt("Enter your username:");

        // Check if the user entered a valid username
        if (username !== null && username.trim() !== "") {
            // Set the value of the hidden username input field
            document.getElementById('usernameField' + orderID).value = username;
            
            // Submit the form after setting the username
            document.getElementById('orderForm' + orderID).submit();
        } else {
            alert("Username cannot be empty. Please enter a valid username.");
        }
    }
</script>

    <script>
        $(document).ready(function () {
            $(".btn-delivered").click(function () {
                alert("Delivery marked as Delivered");
            });

            $(".btn-nondelivered").click(function () {
                alert("Delivery marked as Not Delivered");
            });
        });

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }
    </script>

    <footer>
        <div class="text-center text-lg-start" id="footer">
            <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
                <div class="me-5 d-none d-lg-block">
                    <span>Check with us on social networks:</span>
                </div>
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
        </div>
    </footer>
</body>

</html>