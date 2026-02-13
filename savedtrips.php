<?php
    session_start();
    $title = "ItineraEase | Saved Trips";
    $style = "profilestyles.css";
?>
<?php include 'header.php'; ?>
<div class="page-header">
    <h1 class="page-header-title">Saved Trips</h1>
    <p class="page-header-subtitle">Click on the "Route" buttons below to see each of your individual trips</p>
</div>
<div id="map" style="height: 500px; width: 100%;"></div>
<?php
    $user = $_SESSION['user'];
    $username = $user['username'];
    $server = server;
    $dbusername = dbusername;
    $dbpassword = dbpassword;
    $db = db;
    $conn = new mysqli($server, $dbusername, $dbpassword, $db);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $tripWaypoints = array();
    $stmt = $conn->prepare("SELECT numTrips FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            for ($i = 1; $i <= $row['numTrips']; $i++) {
                $_SESSION['numTrips'] = $row['numTrips'];
                echo "<div class='container'><div class=trip_container>";
                echo "<h2>Trip $i</h2>";
                echo "<div class='buttons_container'";
                echo "<form method='post'>";
                echo "<input type='hidden' name='trip_route' id='trip_route'>";
                echo "<input type='submit' class='trip_btn' name='btn-route' id='route_$i' value='Route'>";
                echo "</form></div></div></div>";
            }
        }
    }
    $stmt2 = $conn->prepare("SELECT latitude, longitude FROM waypoints WHERE username = ? AND tripID = ?");
    for ($i = 1; $i <= $_SESSION['numTrips']; $i++) {
        $stmt2->bind_param("si", $username, $i);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $waypoints = array();
        if ($result2->num_rows > 0) {
            while($row2 = $result2->fetch_assoc()) {
                $waypoints[] = array(
                    'lat' => $row2['latitude'],
                    'lng' => $row2['longitude']
                );
            }
        }
        $tripWaypoints[$i] = $waypoints;
    }
    $conn->close();
?>
<?php include 'footer.php'; ?>
<script>
var map;
var directionsService;
var directionsRenderer;

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 0, lng: 0},
        zoom: 8
    });
    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer();
    directionsRenderer.setMap(map);
}

function showTrip(x) {
    var tripIndex = x + 1;
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 0, lng: 0},
        zoom: 8
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer();
    directionsRenderer.setMap(map);

    var tripWaypoints = <?php echo json_encode($tripWaypoints); ?>;
    console.log(tripIndex);
    console.log(tripWaypoints);
    var waypoints = tripWaypoints[tripIndex];
    if (Object.keys(waypoints).length > 1) {
        var origin = waypoints[0];
        var destination = waypoints[1];
        var midpoints = waypoints.slice(2);

        var request = {
            origin: new google.maps.LatLng(origin.lat, origin.lng),
            destination: new google.maps.LatLng(destination.lat, destination.lng),
            waypoints: midpoints.map(function(location) {
                return {
                    location: new google.maps.LatLng(location.lat, location.lng),
                    stopover: true
                };
            }),
            optimizeWaypoints: true,
            travelMode: 'DRIVING'
        };

        directionsService.route(request, function(result, status) {
            if (status === 'OK') {
                directionsRenderer.setDirections(result);
            }
        });
    }
}

google.maps.event.addDomListener(window, 'load', initMap);
document.addEventListener('DOMContentLoaded', function() {
    var buttons = document.getElementsByClassName('trip_btn');
    for (var x = 0; x < buttons.length; x++) {
        (function(index) {
            buttons[index].addEventListener('click', function() { showTrip(index); });
        })(x);
    }
});
</script>
</body>
</html>
