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
                    $username = $user['username'];
                    $server = $config['database']['server'];
                    $dbusername = $config['database']['dbusername'];
                    $dbpassword = $config['database']['dbpassword'];
                    $db = $config['database']['route'];
                    $conn = new mysqli($server, $dbusername, $dbpassword, $db);
                    if ($conn->connect_error) {
                        die('Connection failed: ' . $conn->connect_error);
                    }
                    $sql = "SELECT MAX(tripID) AS NumTrips FROM waypoints WHERE username = '$username'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $tripID = $row["NumTrips"] + 1;
                    }
                    else {
                        $tripID = 1;
                    }
                    $coord= json_decode($_POST['coordinatesArray'], true);
                    $coordinatesArray = $coord[0];
                    foreach ($coordinatesArray as $coordinates) {
                        $latitude = $coordinates['lat'];
                        $longitude = $coordinates['lng'];
                        $sql2 = "INSERT INTO waypoints (tripID, latitude, longitude, username)
                            VALUES ('$tripID', '$latitude', '$longitude', '$username')";
                        if ($conn->query($sql2) === TRUE) {
                            console.log("New records created successfully");
                        } else {
                            console.log("Error: " . $sql2 . "<br>" . $conn->error);
                        }
                    }
                    $conn->close();
                    $db2 = $config['database']['users'];
                    $conn2 = new mysqli($server, $dbusername, $dbpassword, $db2);
                    if ($conn2->connect_error) {
                        die('Connection failed: ' . $conn2->connect_error);
                    }
                    $sql2 = "UPDATE users SET numTrips = numTrips + 1 WHERE username = '$username'";
                    if ($conn2->query($sql2) === TRUE) {
                        console.log("Record updated successfully");
                    } else {
                        console.log("Error updating record: " . $conn2->error);
                    }
                    $conn2->close();
                }
                else {
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

<?php include 'footer.php'; ?>
<script src="app.js"></script>
<script>
let map, geocoder, directionsService, directionsRenderer;

function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    zoom: 8,
    center: { lat: 37.7749, lng: -122.4194 } 
  });

  geocoder = new google.maps.Geocoder();
  directionsService = new google.maps.DirectionsService();
  directionsRenderer = new google.maps.DirectionsRenderer();
  directionsRenderer.setMap(map);


  const originInput = document.getElementById('origin');
  const destinationInput = document.getElementById('destination');

  originInput.addEventListener('input', geocodeOrigin);
  destinationInput.addEventListener('input', geocodeDestination);
}

function geocodeOrigin() {
  const address = this.value;
  geocoder.geocode({ 'address': address }, function(results, status) {
    if (status === 'OK') {
      const originCoords = results[0].geometry.location;

    } else {
      console.log('Geocode was not successful for the following reason: ' + status);
    }
  });
}

function geocodeDestination() {
  const address = this.value;
  geocoder.geocode({ 'address': address }, function(results, status) {
    if (status === 'OK') {
      const destinationCoords = results[0].geometry.location;

    } else {
      console.log('Geocode was not successful for the following reason: ' + status);
    }
  });
}


window.onload = initMap;
</script>

</body>
</html>
