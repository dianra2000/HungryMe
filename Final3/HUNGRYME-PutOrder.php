<?php
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve customer ID using the username from cookies
    $customerUsername = $_COOKIE['username'];
    $query = "SELECT cusID FROM customer WHERE name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $customerUsername);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();
    $cusID = $customer['cusID'];

    // Retrieve cart ID using customer ID
    $query = "SELECT cartID FROM cart WHERE cusID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cusID);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart = $result->fetch_assoc();
    $cartID = $cart['cartID'];

    // Insert order details into the order table
    $totalAmount = $_POST['totalAmount'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phoneNo = $_POST['phonenum'];
    $email = $_POST['email'];
    $landmark = $_POST['landmark'];
    $paymentMethod = $_POST['paymentMethod'];
    $orderDate = date("Y-m-d H:i:s");  //current date and time
    $orderStatus = "Pending";

    $query = "INSERT INTO `order` (OrderDate, OrderStatus, TotAmount, Name, Address, PhoneNo, Email, Landmarks, PaymentMethod, CartID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdssssssi", $orderDate, $orderStatus, $totalAmount, $name, $address, $phoneNo, $email, $landmark, $paymentMethod, $cartID);

    if ($stmt->execute()) {
        $orderID = $conn->insert_id; 

        // shop names and item names from cookies
        $shopNames = isset($_COOKIE['shop_names']) ? json_decode($_COOKIE['shop_names'], true) : [];
        $itemNames = isset($_COOKIE['item_names']) ? json_decode($_COOKIE['item_names'], true) : [];
        $itemQuantity = isset($_COOKIE['item_quantities']) ? json_decode($_COOKIE['item_quantities'], true) : [];
        $addons = isset($_COOKIE['addons']) ? json_decode($_COOKIE['addons'], true) : [];
        $itemdescription = isset($_COOKIE['item_Description']) ? json_decode($_COOKIE['item_Description'], true) : [];



        // Initialize an array to hold all ShopID values
        $shopIDList = [];

        // Insert each item into the orderitem table
        foreach ($shopNames as $index => $shopName) {
            $itemDescriptions = $itemdescription[$index];

            // Retrieve ShopID from the shop table
            $query = "SELECT ShopID FROM shop WHERE ShopName = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $shopName);
            $stmt->execute();
            $result = $stmt->get_result();
            $shop = $result->fetch_assoc();
            $ShopID = $shop['ShopID'];

            // Add ShopID to the list
            $shopIDList[] = $ShopID;

            // Retrieve MenuItemID from the menuitem table
            $query = "SELECT MenuItemID FROM menuitem WHERE Description = ? AND ShopID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $itemDescriptions, $ShopID);
            $stmt->execute();
            $result = $stmt->get_result();
            $menuItem = $result->fetch_assoc();
            $MenuItemID = $menuItem['MenuItemID'];

            // Insert into orderitem table
            $query = "INSERT INTO orderitem (orderID, MenuItemID) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $orderID, $MenuItemID);
            $stmt->execute();
        }

        // Convert ShopID array to a comma-separated
        $shopIDString = implode(",", $shopIDList);

        // Insert the OrderID and comma-separated ShopID s
        $query = "INSERT INTO ordershop (orderID, ShopID) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $orderID, $shopIDString);
        $stmt->execute();

         // Retrieve the AddonID from the addon table
         $query = "SELECT AddonID FROM addon LIMIT 1";
         $result = $conn->query($query);
         $addon = $result->fetch_assoc();
         $AddonID = $addon['AddonID'];
 
         // If addon is selected, update the orderitem table with AddonID
         if (isset($_COOKIE['addAddon']) && $_COOKIE['addAddon'] === 'true') {
             $query = "UPDATE orderitem SET AddonID = ? WHERE orderID = ?";
             $stmt = $conn->prepare($query);
             $stmt->bind_param("ii", $AddonID, $orderID);
             $stmt->execute();
         }

        // Close the statement and connection
        $stmt->close();
        $conn->close();

        echo "<script>
        alert('Order Success');
        window.location.href = 'HUNGRYME-PutOrder.php';
        </script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection if there's an error
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="title.jpg">
    <title>HUNGRYME Order</title>
    <link rel="stylesheet" type="text/css" href="Bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="H_Style.css">
    <script type="text/javascript" src="Bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="jquery-3.7.1.js"></script>
    <script type="text/javascript" src="Bootstrap/js/bootstrap.min.js"></script>

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

            // Show modal on login button click
            $('#show-popup1').click(function () {
                $('#login').modal('show');
            });

            // Show modal on sign-up button click
            $('#btnsignup').click(function () {
                $('#login').modal('show');
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
                </li>
                <li> <button id="btnbars"><i class="fa-solid fa-bars fa-2xl"></i></button>
                </li>
            </ul>
        </div>
    </nav>

    <script>
        function DarkMode() {
            var element = document.body;
            element.classList.toggle("dark-mode");
        }
    </script>
    
    <div class="Topic">
        <h1>M A K E O R D E R</h1>
    </div>
    <div id="typewriter">
        <script>
            var i = 0;
            var firstText = "Hey there...! Welcome to HUNGRYME...";
            var secondText = "Join us to quench your hunger...";
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
    <br><br>
    <label for="CustomerName" style="display: inline-block; font-weight: bold; padding-left: 20px;">Hello</label>
    <input type="text" id="CustomerName" name="CustomerName" readonly value="<?php echo $_COOKIE['username']; ?>" style="display: inline-block; border: none; font-weight: bold;">

    
    <div class="container" id="containerPayment">
        <button onclick="window.location.href='HUNGRYME-Cart'"  id="navcart" class="cart"><i class="fa-solid fa-cart-shopping fa-xl"></i></button>
        <h2 class="text-center">Enter your information</h2>
        <form method="POST" action="#">
            <div class="form-group">
                <label for="tot"><b>Total</b></label>
                <input type="text" class="form-control" id="tot" name="totalAmount" readonly value="<?php echo $_COOKIE['totalPrice']; ?>" style="display: inline-block; border: none; font-weight: bold;">
            </div>
            <div class="form-group">
                <?php
                // Retrieve and decode cookies
                $shopNames = isset($_COOKIE['shop_names']) ? json_decode($_COOKIE['shop_names'], true) : [];
                $itemNames = isset($_COOKIE['item_names']) ? json_decode($_COOKIE['item_names'], true) : [];
                $itemdescription = isset($_COOKIE['item_Description']) ? json_decode($_COOKIE['item_Description'], true) : [];
                ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Shop Name</th>
                            <th>Items</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Combine shop names and item names into rows
                        $maxItems = max(count($shopNames), count($itemNames));
                        for ($i = 0; $i < $maxItems; $i++):
                        ?>
                            <tr>
                                <td><?php echo isset($shopNames[$i]) ? $shopNames[$i] : ''; ?></td>
                                <td><?php echo isset($itemNames[$i]) ? $itemNames[$i] : ''; ?></td>
                                <td><?php echo isset($itemdescription[$i]) ? $itemdescription[$i] : ''; ?></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                <label for="addonStatus"><b>Add Addon items:</b></label>
                <p id="addonStatus">Cookie status will be displayed here.</p>
            </div>
            <script>
                // Function to get a cookie by name
                function getCookie(name) {
                    let nameEQ = name + "=";
                    let ca = document.cookie.split(';');
                    for (let i = 0; i < ca.length; i++) {
                        let c = ca[i];
                        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
                    }
                    return null;
                }

                // Function to display the checkbox status
                function displayCheckboxStatus() {
                    let isChecked = getCookie("addAddon") === "true";
                    let statusText = isChecked ? "Addon is selected." : "Addon is not selected.";
                    document.getElementById("addonStatus").textContent = statusText;
                }

                // Display the checkbox status on page load
                displayCheckboxStatus();
            </script>

            <div class="form-group">
                <label for="name"><b>Name:</b></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
            </div>
            <div class="form-group">
                <label for="address"><b>Address:</b></label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Enter your address" required>
            </div>
            <div class="form-group">
                <label for="phonenum"><b>Phone Number:</b></label>
                <input type="text" class="form-control" id="phonenum" name="phonenum" placeholder="Enter your phone number" required>
            </div>
            <div class="form-group">
                <label for="email"><b>Email:</b></label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="landmark"><b>Landmark:</b></label>
                <textarea class="form-control" id="landmark" name="landmark" placeholder="e.g:- in front of Train Station"></textarea>
            </div>

            <h2 class="text-center"><b>Payment Options</b></h2>
            <div class="form-group">
                <label for="paymentMethod"><b>Select Payment Method:</b></label>
                <select class="form-control" id="paymentMethod" name="paymentMethod" onchange="handlePaymentChange()">
                    <option value="cod">Cash on Delivery</option>
                    <option value="online">Online Payment</option>
                </select>
            </div>
            <center><button type="submit" class="btn btn-primary">Place Order</button></center>
        </form> 
    </div>

    <br><br>

    <!-- Online Payment Modal -->
    <div class="modal fade" id="onlinePaymentModal" tabindex="-1" aria-labelledby="onlinePaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="onlinePaymentModalLabel">Online Payment Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="cardNumber">Card Number</label>
                            <input type="text" class="form-control" id="cardNumber" placeholder="Enter card number">
                        </div>
                        <div class="form-group">
                            <label for="expiryDate">Expiry Date</label>
                            <input type="text" class="form-control" id="expiryDate" placeholder="MM/YY">
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" class="form-control" id="cvv" placeholder="Enter CVV">
                        </div>
                        <div class="form-group">
                            <label for="TotalAmount">Total Amount</label>
                            <input type="text" class="form-control" id="TotalAmount" placeholder="RS.">
                        </div>
                        <center><button type="submit" class="btn btn-primary">Place Order</button></center>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function handlePaymentChange() {
            var paymentMethod = document.getElementById('paymentMethod').value;
            if (paymentMethod === 'online') {
                // Show the online payment modal
                var onlinePaymentModal = new bootstrap.Modal(document.getElementById('onlinePaymentModal'));
                onlinePaymentModal.show();
            }
        }
    </script>

    <center>
    <p style="font-weight: bold; font-size:16px">If you choose an addon,<br> the following will be added to your meal <br> an additional fee will be charged</p><br>
    <table style="border-collapse: collapse; width: auto; border: 1px solid black;">
        <thead>
            <tr>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: center;">Item</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: center;">Details</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid black; padding: 8px;">Rice</td>
                <td style="border: 1px solid black; padding: 8px;">Meat, Egg, Sausages</td>
            </tr>
            <tr>
                <td style="border: 1px solid black; padding: 8px;">Kottu</td>
                <td style="border: 1px solid black; padding: 8px;">Meat, Egg, Sausages, Spice</td>
            </tr>
            <tr>
                <td style="border: 1px solid black; padding: 8px;">Noodle</td>
                <td style="border: 1px solid black; padding: 8px;">Meat, Egg, Sausages, Vegetable</td>
            </tr>
            <tr>
                <td style="border: 1px solid black; padding: 8px;">Pizza</td>
                <td style="border: 1px solid black; padding: 8px;">Cheese, Meat, Vegetable</td>
            </tr>
        </tbody>
    </table>
    </center>
    <br><br><br>
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
        </div>
    </footer>
</body>

</html>