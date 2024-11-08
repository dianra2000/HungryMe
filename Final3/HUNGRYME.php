<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Database connection
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "hungrymedb"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['signup'])) {
        // Sign-Up Logic
        $signup_name = $conn->real_escape_string($_POST['signup-name']); 
        $signup_password = $_POST['signup-password'];
        $signup_role = $conn->real_escape_string($_POST['signup-role']); // Sanitize input

        // Save plain text password (not recommended)
        $plain_password = $signup_password;

        // Insert into customer table
        $sql = "INSERT INTO customer (name, password, role) VALUES ('$signup_name', '$plain_password', '$signup_role')";

        if ($conn->query($sql) === TRUE) {
            // Get the last inserted CusID
            $cusID = $conn->insert_id;

            // Insert a new record into the cart table with the new CusID
            $cartSql = "INSERT INTO cart (CusID) VALUES ('$cusID')";
            
            if ($conn->query($cartSql) === TRUE) {
                $_SESSION['username'] = $signup_name;
                $_SESSION['role'] = $signup_role;
                header("Location: HUNGRYME.php");
                exit();
            } else {
                echo "Error: " . $cartSql . "<br>" . $conn->error;
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } 
}


    if (isset($_POST['login'])) {
        // Sanitize input
        $login_name = $conn->real_escape_string($_POST['login-name']);
        $login_pass = $_POST['login-pass'];
        $login_role = $conn->real_escape_string($_POST['login-role']);

        // Check role and query the appropriate table
        if ($login_role == 'Admin') {
            $sql = "SELECT * FROM admin WHERE name='$login_name' AND role='$login_role'";
        } elseif ($login_role == 'delivery_boy') {
            $sql = "SELECT * FROM deliveryboy WHERE name='$login_name' AND role='$login_role'";
        } elseif ($login_role == 'shop_owner') {
            $sql = "SELECT * FROM shop WHERE ShopName='$login_name' AND role='$login_role'";
        } else {
            $sql = "SELECT * FROM customer WHERE name='$login_name' AND role='$login_role'";
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if ($row['password'] === $login_pass) { 
                $_SESSION['username'] = $login_name;
                $_SESSION['role'] = $row['role'];
                $_SESSION['customer_id'] = $row['id']; 

                setcookie("password", "$login_pass", time()+3600, "/","", 0); 
                setcookie("username", "$login_name", time()+3600, "/","", 0);

                // Redirect based on role
                if ($row['role'] == 'shop_owner') {
                    header("Location: HUNGRYME-ShopOwner.php");
                } elseif ($row['role'] == 'delivery_boy') {
                    header("Location: HUNGRYME-DeliveryBoy.php");
                } elseif ($row['role'] == 'Admin') {
                    header("Location: HUNGRYME-Admin.php");
                } elseif ($row['role'] == 'user') {
                    header("Location: HUNGRYME.php");
                }
                exit(); 
            } else {
                echo "Invalid password!";
            }
        } else {
            echo "No user found with that username and role!";
        }
    }

    // Search food items with shop names
if (isset($_POST['search'])) {
    $MenuName = isset($_POST['food']) ? $_POST['food'] : '';
    $District = isset($_POST['district']) ? $_POST['district'] : '';

    // Updated SQL query with JOIN
    $stmt = $conn->prepare("
        SELECT menuitem.*, shop.ShopName 
        FROM menuitem 
        LEFT JOIN shop ON menuitem.ShopID = shop.ShopID 
        WHERE menuitem.MenuName = ? AND menuitem.District = ?
    ");
    $stmt->bind_param("ss", $MenuName, $District);
    $stmt->execute();
    $result = $stmt->get_result();

    $shops = [];
    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $shops[] = $row;
            }
        }
        $result->close();
    } else {
        echo "Error: " . $conn->error;
    }
    
    $stmt->close();
    
    echo json_encode($shops);
    exit();
}



$conn->close();
?>


<!--html-->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="title.jpg">
    <title>HUNGRYME</title>
    <link rel="stylesheet" type="text/css" href="Bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="H_Style.css">
    <script type="text/javascript" src="Bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="jquery-3.7.1.js"></script>
    <script type="text/javascript" src="Bootstrap/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
            $("#l1, #l2, #l3, #l4, #l5, #l6, #l7").toggle("slow");
        });

        // Show modal on login button click
        $('#show-popup1').click(function () {
            $('#loginModal').modal('show');
            $('#loginForm').show();
            $('#signUpForm').hide();
        });

        // Show modal on sign-up button click
        $('#btnsignup').click(function () {
            $('#loginModal').modal('show');
            $('#loginForm').hide();
            $('#signUpForm').show();
        });

        // Toggle to sign-up form
        $('#showSignUp').click(function () {
            $('#loginForm').hide();
            $('#signUpForm').show();
        });

        // Toggle to login form
        $('#showLogin').click(function () {
            $('#loginForm').show();
            $('#signUpForm').hide();
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
            <li id="l6"><a class="nav-link" href="https://forms.gle/NmuEtnQbhLHnqMNN8" target="_blank">Add Shop</a></li>
            <li id="l5"><button id="show-popup1" class="login-button"><i class="fa-solid fa-user fa-xl"></i></button></li>
            <li id="l7">
                <div class="buttonDark">
                    <button onclick="DarkMode()">
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

    <!-- Login / SignUp Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form">
                        <form action="HUNGRYME.php" method="POST" id="loginForm">
                            <h2>Log in</h2>
                            <div class="form-element">
                            <label for="login-role">Role</label>
                            <select id="login-role" name="login-role" class="form-control" required>
                                <option value="" disabled selected>Select your role</option>
                                <option value="user">User</option>
                                <option value="shop_owner">ShopOwner</option>
                                <option value="delivery_boy">delivery</option>
                                <option value="Admin">Admin</option>
                            </select>
                            </div>
                            <div class="form-element">
                                <label for="login-name">User Name</label>
                                <input type="text" name="login-name" id="login-name" placeholder="Enter User Name" required>
                            </div>
                            <div class="form-element">
                                <label for="login-pass">Password</label>
                                <input type="password" name="login-pass" id="login-pass" placeholder="Enter Password" required>
                            </div>
                            <div class="form-element">
                            <button type="submit" id="btnlogin" name="login">Log in</button>
                            </div>
                            <div class="form-element">
                                <button type="button" id="showSignUp" >Sign Up</button>
                            </div>
                        </form>
                        <!-- Sign Up -->
                        <form action="HUNGRYME.php" method="POST" id="signUpForm" style="display: none;">
                            <h2>Sign Up</h2>
                            <div class="form-element">
                            <label for="signup-role">Role</label>
                            <select id="signup-role" name="signup-role" class="form-control" required>
                                <option value="" disabled selected>Select your role</option>
                                <option value="user">Customer</option>
                               
                            </select>
                            </div>
                            <div class="form-element">
                                <label for="signup-name">User Name</label>
                                <input type="text" name="signup-name" id="signup-name" placeholder="Enter User Name" required>
                            </div>
                            <div class="form-element">
                                <label for="signup-password">Password</label>
                                <input type="password" name="signup-password" id="signup-password" placeholder="Enter new Password" required>
                            </div>
                            <div class="form-element">
                                <button type="submit" name="signup" id="btnsignup">Sign Up</button>
                            </div>
                            <div class="form-element">
                                <button type="button" id="showLogin">Log in</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function DarkMode() {
            var element = document.body;
            element.classList.toggle("dark-mode");
        }
    </script>
    <div class="Topic" id="home">
        <div class="row">
            <h1>H U N G R Y M E</h1>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <div class="topic">
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
                </div>
            </div>
            <div class="col-sm-4">
                <div class="image">
                    <img src="HUNGRYME logo.png" alt="Circle Image" class="rotate-image">
                </div>
            </div>
        </div>
    </div>

    <br>
    <label for="CustomerName" style="display: inline-block; font-weight: bold; padding-left: 20px;">Hello</label>
    <input type="text" id="CustomerName" name="CustomerName" readonly value="<?php echo isset($_COOKIE['username']) ? $_COOKIE['username'] : 'Foodiezz'; ?>" style="display: inline-block; border: none; font-weight: bold; background-color: transparent; color: black;">

    <div class="container">
        <table class="table table-bordered table-hover" style="width: 100%; border-collapse: collapse; table-layout: auto;">
            <thead>
                <tr>  
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">Food</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">Shop</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">Price</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">Details</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">Image</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody id="foodTableBody">
                <!-- Food items will be appended here -->
            </tbody>
        </table>
    </div>
    <div class="search-bar" style="margin: 20px;" class="d-flex">
        <form id="searchForm">
            <select id="food" name="food" required>
            <option value="" disabled selected>Select food 🔽</option>
                <option value="Kottu">Kottu</option>
                <option value="Rice">Rice</option>
                <option value="Pizza">Pizza</option>
                <option value="Beverage">Beverage</option>
                <option value="Noodles">Noodles</option>
            </select>
            <select id="district" name="district" required>
                <option value="" disabled selected>Select a district 🔽</option>
                <option value="Ampara">Ampara</option>
                <option value="Anuradhapura">Anuradhapura</option>
                <option value="Badulla">Badulla</option>
                <option value="Batticaloa">Batticaloa</option>
                <option value="Colombo">Colombo</option>
                <option value="Galle">Galle</option>
                <option value="Gampaha">Gampaha</option>
                <option value="Hambantota">Hambantota</option>
                <option value="Jaffna">Jaffna</option>
                <option value="Kalutara">Kalutara</option>
                <option value="Kandy">Kandy</option>
                <option value="Kegalle">Kegalle</option>
                <option value="Kilinochchi">Kilinochchi</option>
                <option value="Kurunegala">Kurunegala</option>
                <option value="Mannar">Mannar</option>
                <option value="Matale">Matale</option>
                <option value="Matara">Matara</option>
                <option value="Monaragala">Monaragala</option>
                <option value="Mullaitivu">Mullaitivu</option>
                <option value="Nuwara Eliya">Nuwara Eliya</option>
                <option value="Polonnaruwa">Polonnaruwa</option>
                <option value="Puttalam">Puttalam</option>
                <option value="Ratnapura">Ratnapura</option>
                <option value="Trincomalee">Trincomalee</option>
                <option value="Vavuniya">Vavuniya</option>
            </select>
            <button type="submit" class="btn btn-blue">Search</button>
            <button onclick="goToCart()" id="ShopTablebtn">Go to Cart</button>
        </form>
    </div>

    
    <style>
    <style>
        .container {
            width: 100%;
            overflow-x: auto;
            color: red;
        }
        
    </style>
    
    <script type="text/javascript">
    $(document).ready(function () {
        $("#searchForm").submit(function (event) {
            event.preventDefault();

            var food = $("#food").val();
            var district = $("#district").val();

            $.ajax({
                type: "POST",
                url: "HUNGRYME.php",
                data: { search: true, food: food, district: district },
                success: function (response) {
                    var foodItems = JSON.parse(response);
                    var tableBody = $("#foodTableBody");

                    tableBody.empty(); // Clear the existing table rows

                    if (foodItems.length > 0) {
                        foodItems.forEach(function (item) {
                            var row = `<tr>
                                <td>${item.MenuName}</td>
                                <td>${item.ShopName}</td>
                                <td>${item.Price}</td>
                                <td>${item.Description}</td>
                                <td><img src="${item.ImagePath}" alt="Image" style="width: 100px; height: auto;"></td>
                                <td>
                                    <button class="btn btn-warning add-to-cart"
                                        data-menu-name="${item.MenuName}" data-description="${item.Description}">
                                        Add to Cart
                                    </button>
                                </td>
                            </tr>`;
                            tableBody.append(row);
                        });

                        // Add to cart button click event
                        $(".add-to-cart").click(function () {
                            var username = $("#CustomerName").val(); 
                            var menuName = $(this).data('menu-name');
                            var description = $(this).data('description');

                            $.ajax({
                                type: "POST",
                                url: "add_to_cart.php",
                                data: { add_to_cart: true, username: username, MenuName: menuName, Description: description },
                                success: function (response) {
                                    alert("Are you sure to add to cart");
                                }
                            });
                        });
                    } else {
                        tableBody.append("<tr><td colspan='6'>No items found</td></tr>");
                    }
                }
            });
        });
    });

    function goToCart() {
        window.location.href = 'HUNGRYME-Cart.php';
    }

    </script>
    <!-- methanata ara pictures wala code danna-->
    <br>
    <center>
        <div class="container" id="c">
            <div class="row">
                <div class="col-12" id="img">
                    <div class="horizontal-scroll-wrapper">
                        <div class="row flex-nowrap">
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card" style="width: 100%;">
                                    <a class="nav-link" href="####"><img class="card-img-top popup-img"
                                            src="mixrice.jpg" id="image"></a>
                                    <div class="overlay-text"> Fried Rice</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card" style="width: 100%;">
                                    <a class="nav-link" href="####"><img class="card-img-top popup-img" src="kottu.jpg"
                                            id="image"></a>
                                    <div class="overlay-text"> Kottu</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card" style="width: 100%;">
                                    <a class="nav-link" href="####"><img class="card-img-top popup-img" src="noodle.jpg"
                                            id="image"></a>
                                    <div class="overlay-text"> Noodle</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card" style="width: 100%;">
                                    <a class="nav-link" href="####"><img class="card-img-top popup-img" src="Pizza.jpg"
                                            id="image"></a>
                                    <div class="overlay-text"> Pizza</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </center>

    <br>
    <div class="container" id="D">
        <div class="row">
            <div class="image-container">
                <div class="text-container">
                    <h3 id="dh3">How it works</h3>
                    <h4 id="dh4">What we serve</h4>
                        <p id="dp">Product Quality Is Our Priority, And Always Guarantees <br> Freshness And Safety Until It Is In Your Hands.</p>
                    <div class="horizontal-scroll-wrapper">
                        <div style="display: flex; justify-content: space-between;" id="hh">
                            <h3><img src="phone.png"><br> Easy To Order</h3>
                            <h3><img src="bike.png"><br>Fastest Delivery</h3>
                            <h3><img src="man.png"><br>Best Quality</h3>
                        </div>
                        <div style="display: flex; justify-content: space-between;" id="pp">
                            <p>You only order through the app</p>
                            <p>Delivery will be on time</p>
                            <p>The best quality of food for you</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br>
    <!--QR-->
    <center>
        <h6 class="text-uppercase fw-bold">Add Your Shop to HungryMe</h6>
        <img src="QR.png" alt="QR Code for Add Shop" width="100px">
        <p>(Scan Me)</p>
    </center>
    
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