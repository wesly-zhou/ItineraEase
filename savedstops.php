<?php
    session_start();
    $title = "ItineraEase | Saved Events";
    $style = "eventsstyles.css";
?>
<?php include 'header.php'; ?>
    <div class="page-header">
        <h1 class="page-header-title">Stops</h1>
        <p class="page-header-subtitle">Listed below are all of the stops that you saved for your trips</p>
    </div>
    <?php
        $user = $_SESSION["user"];
        $username = $user["username"];
        $server = $config['database']['server'];
        $dbusername = $config['database']['dbusername'];
        $dbpassword = $config['database']['dbpassword'];
        $db = $config['database']['events'];
        $conn_event = new mysqli($server, $dbusername, $dbpassword, $db);
        if ($conn_event->connect_error) {
            die("Connection failed: " . $conn_event->connect_error);
          }
        $sql = "SELECT * FROM events WHERE username = '$username'";
        $result = mysqli_query($conn_event, $sql);
        $events = mysqli_fetch_all($result, MYSQLI_ASSOC);

        if (count($events) > 0) {
            echo "<div class='events'>";
            foreach ($events as $event) {
                echo "<div class='event-container'>";
                echo "<div class='event-date'>";
                echo "</div>";
                echo "<div class='event-info'>";
                echo "<h2 class='event-name'>" . $event["name"] . "</h2>";
                echo "<h4 class='event-category'>" . $event["category"] . "</h4>";
                echo "</div>";
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<h2>No events saved</h2>";
        }
        $conn_event->close();
        ?>
    <?php include 'footer.php'; ?>
    </body>
</html>