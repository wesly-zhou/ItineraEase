<?php
    session_start();
    $title = "ItineraEase | Search";
    $style = "searchstyles.css";
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["itinerary"])) {
        $itineraryData = json_decode($_POST["itinerary"], true);
        $restaurants = $itineraryData["restaurants"];
        $events = $itineraryData["events"];
        $hotels = $itineraryData["hotels"];
    }
?>
    <?php include 'header.php'; ?>
    <div class="page-header">
        <h1 class="page-header-title">Search</h1>
        <p class="page-header-subtitle">Find the next unforgettable part of your trip</p>
    </div>
    <!---------- events page page selector ------------->
    <div id="events__page">
        <div class="container">
            <div class="row">
                <div class="events-col-1">
                    <img src="images/Road_Trip_Illustration.jpg" alt="car facing away from the user and towards the sun">
                </div>
                <div class="events-col-2">
                    <h2 class="sub-title">What would <strong>YOU</strong> like to look at?</h2>
                    <p>Choose from either three to view listings!</p>
                    <div class="tab-titles">
                        <p class="tab-links active-link" onclick="opentab('events')">Events</p>
                        <p class="tab-links" onclick="opentab('hotels')" >Hotel</p>
                        <p class="tab-links" onclick="opentab('restaurants')">Restaurant</p>
                    </div>
    
                    <!---------- ticketmaster ------------->
                    <div class="tab-contents active-tab" id="events">
                        <section class="events-container">
                            <div class="small--container">
                                <h2>Events</h2>
                                <div class="search-form">
                                    <input id="City" type="text" placeholder="Enter city">
                                    <input type="date" id="eventDate">
                                    <button class="button">Search</button>
                                </div>
                                <div id="events-panel">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Events</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div id="events" class="list-group">
                                                <a href="#" class="list-group-item">
                                                    <h4 class="list-group-item-heading"></h4>
                                                    <p class="list-group-item-text"></p>
                                                    <p class="venue"></p>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pagination">
                                    <button id="prev">Previous</button>
                                    <button id="next">Next</button>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="small--container">
                        <div id="attraction-panel">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h3 class="panel-title" id="attraction">Attraction</h3>
                                </div>
                                <div id="attraction" class="panel-body">
                                    <h4 class="list-group-item-heading">Attraction title</h4>
                                        <img class="col-xs-12" src="">
                                        <p id="classification" style="margin-bottom=5px;"></p>
                                </div>
                            </div>
                        </div>
                    <div id="Ticketmaster-widget"></div>
    
                    <!---------- hotels ------------->
                    <div class="tab-contents" id="hotels">
                        <section class="hotel-search-container">
                                <div class="small--container">
                                    <h2>Hotel Search</h2>
                                    <div class="search-form">
                                        <input id="autocomplete" type="text" placeholder="Enter a city">
                                        <button id="search-button">Search</button>
                                    </div>
                                    <div id="map"></div>
                                    <div id="hotel-results">
                                        <table id="results"></table>
                                    </div>
                                </div>
                        </section>
                    </div>
                    
                    <!---------- restaurants ------------->
                    <div class="tab-contents" id="restaurants">
                        <section class="restaurant-search-container">
                            <div class="small--container">
                                <h2>Restaurant Search</h2>
                                <div class="search-form">
                                    <input id="restaurant-location" type="text" placeholder="Enter a location">
                                    <input id="restaurant-query" type="text" placeholder="Enter a cuisine type (optional)">
                                    <button id="restaurant-search-button">Search</button>
                                </div>
                                <div id="restaurant-map"></div>
                                <div id="restaurant-results">
                                    <table id="restaurant-table"></table>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script>window.TKT_API_KEY = "<?php echo TKT_API_KEY; ?>";</script>
    <script src="app.js"></script>
</body>
</html>
