<?php
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

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shopName = $conn->real_escape_string($_POST['shop-name']);
    $shopEmail = $conn->real_escape_string($_POST['shop-email']);
    $shopPhone = $conn->real_escape_string($_POST['shop-phone']); 

    // Determine the next ShopID
    $result = $conn->query("SELECT MAX(CAST(SUBSTRING(ShopID, 4) AS UNSIGNED)) AS max_id FROM shop");
    if ($result === FALSE) {
        die("Error: " . $conn->error);
    }
    $row = $result->fetch_assoc();
    $nextId = $row['max_id'] + 1;
    $shopID = 'SHP' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

    // Debugging output
    //echo "Generated ShopID: " . $shopID . "<br>";

    // Insert into Shop table
    $sql = "INSERT INTO shop (ShopID, ShopName, ShopEmail,password,role) VALUES ('$shopID', '$shopName', '$shopEmail','$shopID','shop_owner')";
    if ($conn->query($sql) === TRUE) {
        $phoneNumbers = array_map('trim', explode(',', $shopPhone));

        // Insert each phone number into ShopPhone table
        foreach ($phoneNumbers as $phone) {
            $phone = $conn->real_escape_string($phone);
            $sqlPhone = "INSERT INTO shopphone (ShopID, ShopPhone) VALUES ('$shopID', '$phone')";
            if ($conn->query($sqlPhone) !== TRUE) {
                echo "Error inserting phone number: " . $conn->error;
            }
        

            // // Insert login information for the shop owner
            // $shopOwnerUsername = $conn->real_escape_string($shopName); // Use ShopName as username
            // // $shopOwnerPassword = password_hash($shopID, PASSWORD_DEFAULT); // Use ShopID as password, hashed
            // $shopOwnerRole = 'shop_owner';
            // $sqlLogin = "INSERT INTO tbllogin (username, password, role) VALUES ('$shopOwnerUsername', '$shopID', '$shopOwnerRole')";

            // // Debugging output
            // echo "Username: " . $shopOwnerUsername . "<br>";
            // echo "Password (hashed): " . $shopOwnerPassword . "<br>";
            // echo "Role: " . $shopOwnerRole . "<br>";
            // echo "SQL: " . $sqlLogin . "<br>";

            // if ($conn->query($sqlLogin) !== TRUE) {
            //     echo "Error inserting login information: " . $conn->error;
            // } else {
            //     echo "New shop added successfully.";
            //     // Redirect to admin page after successful insertion
            //     if (headers_sent()) {
            //         echo "<script>window.location.href='HUNGRYME-Admin.php';</script>";
            //     } else {
            //         header("Location: HUNGRYME-Admin.php");
            //     }
            //     exit(); // Ensure no further code execution after redirection
            // }
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="title.jpg">
    <link rel="C:\wamp64\www\Final\Images">
    <title>HUNGRYME_Admin</title>
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
            <!-- $username = "admin_username"; -->
        </h1>
    </div>
    <div id="typewriter">
        <script>
            var i = 0;
            var firstText = "Hey Admin...! Welcome to HUNGRYME...";
            var secondText = "Manage your deliveries and payments efficiently...";
            var speed = 50;
            var phase = 1;

            function typeWriter() {
                if (phase === 1) {
                    if (i < firstText.length) {
                        document.getElementById("typewriter").innerHTML += firstText.charAt(i);
                        i++;
                        setTimeout(typeWriter, speed);
                    } else {
                        setTimeout(clearText, 0);
                    }
                } else if (phase === 2) {
                    if (i < secondText.length) {
                        document.getElementById("typewriter").innerHTML += secondText.charAt(i);
                        i++;
                        setTimeout(typeWriter, speed);
                    } else {
                        setTimeout(clearText, 0); 
                    }
                }
            }

            function clearText() {
                i = 0;
                document.getElementById("typewriter").innerHTML = "";
                phase = phase === 1 ? 2 : 1; // Switch between 1 and 2
                setTimeout(typeWriter, speed);
            }

            window.onload = function () {
                typeWriter();
            };
        </script>
    </div>

    <label for="CustomerName" style="display: inline-block; font-weight: bold; padding-left: 20px;">Hello</label>
    <input type="text" id="CustomerName" name="CustomerName" readonly value="<?php echo isset($_COOKIE['username']) ? $_COOKIE['username'] : 'ABC'; ?>" style="display: inline-block; border: none; font-weight: bold; background-color: transparent; color: black;">

    <br><br>

    <center>
    <div class="form-container" id="Adminf">
        <h2>Add Shop</h2>
        <form action="HUNGRYME-Admin.php" method="POST">
            <label for="shop-name">Shop Name</label>
            <input type="text" id="shop-name" name="shop-name" required>
    
            <label for="shop-email">Shop Email</label>
            <input type="email" id="shop-email" name="shop-email" required>
    
            <label for="shop-phone">Shop Phone Number</label>
            <input type="text" id="shop-phone" name="shop-phone" pattern="[0-9]{10}" required>
            <input type="submit" value="Add Shop" class="submit-button">
        </form>
    </div>
    <br><br>
    <center>
    <div class="form-container" id="Adminf">
    <h2>Add Delivery Boy</h2>
        <form action="HUNGRYME-deliveryAdd.php" method="POST">
            <label for="rider-name">Rider Name</label>
            <input type="text" id="rider-name" name="rider-name" required>
            <input type="submit" value="Add Rider" class="submit-button">
        </form>
    </div>


    <br><br>

    
    <div class="form-pricechange" id="priceChange">
        <h4>Customization Price Change</h4>
        <!-- Buttons to trigger modals -->
        <button style="width: 100%; background-color: yellow; font-weight: 700; border: none; padding: 10px; border-radius: 10px; margin-bottom: 10px; cursor: pointer;" id="showHandleRice" data-toggle="modal" data-target="#RiceModal">Customize Extra Price</button>

        <!-- Handle Rice Payment Modal -->
        <div class="modal fade" id="RiceModal" tabindex="-1" role="dialog" aria-labelledby="RiceModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="Addon">Add on Price Customization</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="addonForm" method="post" action="customize.php">
                        <input type="hidden" name="btnID" value="Rice">
                        <div class="modal-body">
                            <label for="Addon">New Add on Price</label>
                            <input type="number" step="0.01" class="form-control" id="Addon" name="Addon" required>
                            
                            <input type="text" id="CustomerName" name="CustomerName" readonly value="<?php echo isset($_COOKIE['username']) ? $_COOKIE['username'] : 'ABC'; ?>" style="display: inline-block; border: none; font-weight: bold; background-color: transparent; color: black;">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" style="width: 100%;" name="update" value="Rice" onclick="submitForm()">Update</button>
                        </div>
                    </form>

                    <script>
                    function submitForm() {
                        // Get the username from the read-only input field
                        var adminName = document.getElementById('CustomerName').value;

                        // Set the admin name as a hidden field value
                        var form = document.getElementById('addonForm');
                        var hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'adminName';
                        hiddenInput.value = adminName;
                        form.appendChild(hiddenInput);

                        // Submit the form
                        form.submit();
                    }
                    </script>
                </div>
            </div>
        </div>
    </div>


    </center>

    <br><br>
    <!-- jQuery, Popper.js, and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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