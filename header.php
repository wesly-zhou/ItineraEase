<?php 
    session_start();
    $user = $_SESSION["user"] ?? null;
    $loginst = 0;
    if ($user) {
        $loginst = 1;
    }
?>

<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="styles/<?php echo $style; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&libraries=places"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <title><?php echo $title; ?></title>
</head>

<body> 
<?php if ($loginst == 1) { ?>
    <div class="navbar">
        <div class="navbar__container">
            <a class="logo" href="index.php" id="home">
                <img src="images/logo.jpg" alt="Travel Logo">
                <p class="logo__text">ItineraEase</p>
            </a>
            <div class="nav_menu">
                <a href="search.php" id="search">Search</a>
                <a href="faq.php" id="faq">FAQ</a>
                <a href="contact.php" id="contact">Contact</a>
                <div class="dropdown">
                    <a onclick="myFunction()" class="dropbtn">Account</a>
                    <div id="myDropdown" class="dropdown-content">
                        <a href="savedtrips.php">Saved Trips</a>
                        <a href="savedstops.php">Saved Stops</a>
                        <a href="logout.php">Log Out</a>
                    </div>
                </div>
            </div>
            <div class="hamburger_menu">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="navbar">
        <div class="navbar__container">
            <a class="logo" href="index.php" id="home">
                <img src="images/logo.jpg" alt="Travel Logo">
                <h1 class="logo__text">ItineraEase</h1>
            </a>
            <div class="nav_menu">
                <a href="search.php" id="search">Search</a>
                <a href="faq.php" id="faq">FAQ</a>
                <a href="contact.php" id="contact">Contact</a>
                <a href="login.php" id="login">Log In</a>
            </div>
            <div class="hamburger_menu">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
<?php } ?>
