<?php
    session_start();
    ob_start();
    $style = "defaultstyles.css";
    $title = "ItineraEase";
?>

<?php include 'header.php'; ?>
<div class="page-header">
        <h1 class="page-header-title">ItineraEase</h1>
        <p class="page-header-subtitle">Start here to plan your next great roadtrip</p>
    </div>
<div class="container">
    <div id="controlPanel">
        <input id="origin" type="text" placeholder="Enter origin" class="waypoint input-fixed-width">
        <input id="destination" type="text" placeholder="Enter destination" class="waypoint input-fixed-width">
        <input id="poiType" type="text" placeholder="Enter types of places (e.g., museum, restaurant)" class="input-fixed-width">
    </div>

    <div id="dynamicWaypointsContainer"></div>

    <div id="waypointsContainer">
        <button onclick="addWaypoint()" id="waypoint">Add Waypoint</button>
        <button onclick="calculateAndDisplayRoute()" id="submit_button">Get Directions</button>
    </div>
</div>

<div id="mapContainer">
    <div id="map"></div>
    <div id="routeButtonContainer">
        <?php
            if (isset($_POST['btn-route'])) {
                if (isset($_SESSION["user"])) {
                    $username = $_SESSION["user"]["username"];
                    $server = server;
                    $dbusername = dbusername;
                    $dbpassword = dbpassword;
                    $db = db;
                    $conn = new mysqli($server, $dbusername, $dbpassword, $db);
                    if ($conn->connect_error) {
                        die('Connection failed: ' . $conn->connect_error);
                    }

                    $stmt = $conn->prepare("SELECT MAX(tripID) AS NumTrips FROM waypoints WHERE username = ?");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $tripID = $row["NumTrips"] + 1;
                    } else {
                        $tripID = 1;
                    }

                    $coord = json_decode($_POST['coordinatesArray'], true);
                    $coordinatesArray = $coord[0];

                    $stmt2 = $conn->prepare("INSERT INTO waypoints (tripID, latitude, longitude, username) VALUES (?, ?, ?, ?)");
                    foreach ($coordinatesArray as $coordinates) {
                        $latitude = $coordinates['lat'];
                        $longitude = $coordinates['lng'];
                        $stmt2->bind_param("idds", $tripID, $latitude, $longitude, $username);
                        if (!$stmt2->execute()) {
                            error_log("Insert error: " . $stmt2->error);
                        }
                    }

                    $stmt3 = $conn->prepare("UPDATE users SET numTrips = numTrips + 1 WHERE username = ?");
                    $stmt3->bind_param("s", $username);
                    if (!$stmt3->execute()) {
                        error_log("Update error: " . $stmt3->error);
                    }

                    $conn->close();
                } else {
                    header("Location: login.php");
                }
            }
        ?>
        <form method="post">
            <input type="hidden" name="coordinatesArray" id="coordinatesArrayInput"> 
            <input type="submit" class="btn1" name="btn-route" value="Add Route">
        </form>
    </div>
</div>

<?php include __DIR__.'/footer.php'; ?>
<script>window.TKT_API_KEY = "<?php echo TKT_API_KEY; ?>";</script>
<script src="app.js"></script>

</body>
</html>
