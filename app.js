let map;
let directionsService;
let directionsRenderer;
let markers = [];
let waypointCount = 0;
let restaurantItinerary = [];
let hotelItinerary = [];
let eventItinerary = [];
let placeItinerary= [];

// Hotel search variables
let places;
let autocomplete;
const MARKER_PATH =
  "https://developers.google.com/maps/documentation/javascript/images/marker_green";
const hostnameRegexp = new RegExp("^https?://.+?/");

let restaurantMap;
let restaurantAutocomplete;
let restaurantMarkers = [];

window.onload = function() {
  initMaps();
};

function initMaps() {
  initMap();
  initRestaurantMap();
}

function initMap() {
  map = new google.maps.Map(document.getElementById("map"), {
    zoom: 4,
    center: { lat: 37.1, lng: -95.7 },
    mapTypeControl: true,
    panControl: true,
    zoomControl: true,
    streetViewControl: false,
  });

  directionsService = new google.maps.DirectionsService();
  directionsRenderer = new google.maps.DirectionsRenderer({ map: map });

  setupAutocomplete('origin');
  setupAutocomplete('destination');

  autocomplete = new google.maps.places.Autocomplete(
    document.getElementById("autocomplete")
  );

  places = new google.maps.places.PlacesService(map);

  if (autocomplete) {
    autocomplete.addListener("place_changed", onPlaceChanged);
  }
  
  const searchButton = document.getElementById("search-button");
  if (searchButton) {
    searchButton.addEventListener("click", search);
  }
}

function initRestaurantMap() {
  restaurantMap = new google.maps.Map(document.getElementById("restaurant-map"), {
    zoom: 12,
    center: { lat: 37.7749, lng: -122.4194 },
    mapTypeControl: true,
    panControl: true,
    zoomControl: true,
    streetViewControl: false,
  });

  restaurantAutocomplete = new google.maps.places.Autocomplete(
    document.getElementById("restaurant-location")
  );

  if (restaurantAutocomplete) {
    restaurantAutocomplete.addListener("place_changed", onRestaurantPlaceChanged);
  }
  
  const restaurantSearchButton = document.getElementById("restaurant-search-button");
  if (restaurantSearchButton) {
    restaurantSearchButton.addEventListener("click", searchRestaurants);
  }
}

function setupAutocomplete(id) {
  const inputElement = document.getElementById(id);
  if (inputElement) {
    new google.maps.places.Autocomplete(inputElement, {
      types: ['geocode']
    });
  }
}

function addWaypoint() {
  const container = document.getElementById('dynamicWaypointsContainer');
  if (container) {
    const input = document.createElement('input');
    input.type = 'text';
    input.placeholder = 'Enter waypoint';
    input.className = 'waypoint';
    input.id = `waypoint-${waypointCount}`;
    container.appendChild(input);
    setupAutocomplete(input.id);
    waypointCount++;
  }
}

let originCoords, destinationCoords, waypointCoords = [];
let coordinatesArray = [];

function calculateAndDisplayRoute() {
  clearPreviousResults();

  const origin = document.getElementById("origin").value;
  const destination = document.getElementById("destination").value;
  const poiType = document.getElementById("poiType").value;
  const waypoints = Array.from(document.getElementsByClassName('waypoint'))
    .map(input => ({ location: input.value, stopover: true }))
    .filter(wp => wp.location !== "");

  const geocoder = new google.maps.Geocoder();

  // Geocode origin
  geocoder.geocode({ address: origin }, (results, status) => {
    if (status === 'OK') {
      originCoords = {
        lat: results[0].geometry.location.lat(),
        lng: results[0].geometry.location.lng()
      };
      geocodeDestination();
    } else {
      console.error('Geocode was not successful for the following reason: ' + status);
    }
  });

  function geocodeDestination() {
    // Geocode destination
    geocoder.geocode({ address: destination }, (results, status) => {
      if (status === 'OK') {
        destinationCoords = {
          lat: results[0].geometry.location.lat(),
          lng: results[0].geometry.location.lng()
        };
        geocodeWaypoints(0);
      } else {
        console.error('Geocode was not successful for the following reason: ' + status);
      }
    });
  }
   
  function geocodeWaypoints(index) {
    if (index === waypoints.length) {
      // All waypoints geocoded, display coordinates
      displayCoordinates();
      return;
    }

    // Geocode waypoint
    geocoder.geocode({ address: waypoints[index].location }, (results, status) => {
      if (status === 'OK') {
        waypointCoords[index] = {
          lat: results[0].geometry.location.lat(),
          lng: results[0].geometry.location.lng()
        };
        geocodeWaypoints(index + 1);
      } else {
        console.error('Geocode was not successful for the following reason: ' + status);
      }
    });
  }

  function displayCoordinates() {
    console.log('Origin:', originCoords);
    console.log('Destination:', destinationCoords);
    console.log('Waypoints:', waypointCoords);

    // Create an array to store the coordinates in the logical order
    coordinatesArray = [waypointCoords];
    document.getElementById('coordinatesArrayInput').value = JSON.stringify(coordinatesArray);
    updateLatLng(coordinatesArray);

    // Continue with the rest of the route calculation and display logic
    const routeRequest = {
      origin: origin,
      destination: destination,
      waypoints: waypoints,
      travelMode: google.maps.TravelMode.DRIVING,
      optimizeWaypoints: waypoints.length > 0
    };

    directionsService.route(routeRequest, (response, status) => {
      if (status === 'OK') {
        directionsRenderer.setDirections(response);
        displayTravelTimesAndFindPOIs(response, poiType);
      } else {
        window.alert('Directions request failed due to ' + status);
      }
    });
  }
}

function updateLatLng(coordinatesArray) {
  $.ajax({
    url:"index.php",
    method:"post",
    data: { coordinatesArray : coordinatesArray },
    success: function (res) {
      console.log(coordinatesArray);
    }
  });
}

function displayTravelTimesAndFindPOIs(directionsResult, poiType) {
  const route = directionsResult.routes[0];
  let totalTime = 0;
  let totalDistance = 0;
  let nextPOISearchTime = 3600;
  let accumulatedTime = 0;
  
  route.legs.forEach((leg, index) => {
    totalTime += leg.duration.value;
    totalDistance += leg.distance.value;
    leg.steps.forEach(step => {
      accumulatedTime += step.duration.value;
      if (accumulatedTime >= nextPOISearchTime) {
        searchNearbyPOIs(step.end_location, poiType);
        nextPOISearchTime += 3600;
      }
    });

    if (index === route.legs.length - 1) {
      displayEndOfRouteInfo(leg, totalTime, totalDistance);
    }
  });
}

function searchNearbyPOIs(location, poiType) {
  const service = new google.maps.places.PlacesService(map);
  const types = poiType.split(',').map(type => type.trim());
  service.nearbySearch({
    location: location,
    radius: 40000,
    type: types,
    keyword: poiType
  }, (results, status) => {
    if (status === google.maps.places.PlacesServiceStatus.OK && results.length) {
      displayPOIs(results);
    } else {
      console.log('No points of interest found or API error:', status);
    }
  });
}

function displayPOIs(places) {
  places.slice(0, 5).forEach(place => {
    const marker = new google.maps.Marker({
      position: place.geometry.location,
      map: map,
      title: place.name,
      icon: {
        url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
      }
    });

    const infowindow = new google.maps.InfoWindow({
      content: `
        <div>
          <strong>${place.name}</strong><br>
          Rating: ${place.rating || 'N/A'}<br>
          </div>
      ` // Removed the button element here
    });

    marker.addListener('click', () => {
      infowindow.open(map, marker);
    });

    markers.push(marker);
  });
}
function displayEndOfRouteInfo(leg, totalTime, totalDistance) {
  const marker = new google.maps.Marker({
    position: leg.end_location,
    map: map,
    title: "Route End"
  });

  const totalHours = Math.floor(totalTime / 3600);
  const totalMinutes = Math.floor((totalTime % 3600) / 60);
  const totalDistanceKm = (totalDistance / 1000).toFixed(2);
  
  const infowindow = new google.maps.InfoWindow({
    content: `<div><strong>Total Distance:</strong> ${totalDistanceKm} km<br><strong>Total Time:</strong> ${totalHours}h ${totalMinutes}m</div>`
  });

  infowindow.open(map, marker);
  markers.push(marker);
}

function clearPreviousResults() {
  markers.forEach(marker => marker.setMap(null));
  markers = [];
  directionsRenderer.setDirections({ routes: [] });
}

function onPlaceChanged() {
  const place = autocomplete.getPlace();

  if (place.geometry) {
    map.panTo(place.geometry.location);
    map.setZoom(15);
    search();
  } else {
    document.getElementById("autocomplete").placeholder = "Enter a city";
  }
}

let globalMarkerIndex = 0; 

function search() {
  const city = document.getElementById("autocomplete").value;

  if (city) {
    const search = {
      bounds: map.getBounds(),
      types: ["lodging"],
    };

    places.nearbySearch(search, (results, status, pagination) => {
      if (status === google.maps.places.PlacesServiceStatus.OK && results) {
        clearResults();
        clearMarkers();

        for (let i = 0; i < results.length; i++, globalMarkerIndex++) {
          const markerLetter = String.fromCharCode("A".charCodeAt(0) + (globalMarkerIndex % 26));
          const markerIcon = MARKER_PATH + markerLetter + ".png";
          const marker = new google.maps.Marker({
            position: results[i].geometry.location,
            map: map,
            icon: markerIcon,
          });
          markers.push(marker);

          const tr = document.createElement("tr");
          tr.style.backgroundColor = i % 2 === 0 ? "#F0F0F0" : "#FFFFFF";
          tr.onclick = function () {
            google.maps.event.trigger(marker, "click");
          };
          const iconTd = document.createElement("td");
          const nameTd = document.createElement("td");
          const icon = document.createElement("img");
          icon.src = markerIcon;
          icon.setAttribute("class", "placeIcon");
          const name = document.createTextNode(results[i].name);
          iconTd.appendChild(icon);
          nameTd.appendChild(name);
          tr.appendChild(iconTd);
          tr.appendChild(nameTd);

          const addButton = document.createElement("button");
          addButton.textContent = "Add to Itinerary";
          addButton.classList.add("add-to-itinerary");
          addButton.name = "itinerary";
          addButton.onclick = function() {
          const hotelName = results[i].name;
          const hotelLocation = document.getElementById("City").value;
          const state = document.getElementById("state").value;
          const hotelType = "hotel";
          hotelItinerary.push({
            name: hotelName,
            type: hotelType
          });
            updateItinerary(); 
            console.log(`Added ${hotelName} to the itinerary`);
            // add logic
          };
          tr.appendChild(addButton);

          document.getElementById("results").appendChild(tr);
        }
      }
    });
  }
}

function clearResults() {
  const results = document.getElementById("results");
  while (results.childNodes[0]) {
    results.removeChild(results.childNodes[0]);
  }
}

function clearMarkers() {
  for (let i = 0; i < markers.length; i++) {
    if (markers[i]) {
      markers[i].setMap(null);
    }
  }
  markers = [];
}

function onRestaurantPlaceChanged() {
  const place = restaurantAutocomplete.getPlace();

  if (place.geometry) {
    restaurantMap.panTo(place.geometry.location);
    restaurantMap.setZoom(15);
    searchRestaurants();
  } else {
    document.getElementById("restaurant-location").placeholder = "Enter a location";
  }
}
function updateItinerary() {
  const itineraryData = {
    restaurants: restaurantItinerary,
    events: eventItinerary,
    hotels: hotelItinerary
  };

  $.ajax({
    url: "search.php",
    method: "POST",
    data: { itinerary: JSON.stringify(itineraryData) },
    success: function(response) {
      console.log("Itinerary data sent to server:", response);
    },
    error: function(error) {
      console.error("Error sending itinerary data:", error);
    }
  });
}
function searchRestaurants() {
  const location = document.getElementById("restaurant-location").value;
  const query = document.getElementById("restaurant-query").value;

  if (location) {
    const request = {
      location: restaurantMap.getCenter(),
      radius: 5000,
      type: ["restaurant"],
      keyword: query,
    };

    places.nearbySearch(request, (results, status) => {
      if (status === google.maps.places.PlacesServiceStatus.OK && results) {
        clearRestaurantResults();
        clearRestaurantMarkers();

        for (let i = 0; i < results.length; i++) {
          const markerLetter = String.fromCharCode("A".charCodeAt(0) + (i % 26));
          const markerIcon = MARKER_PATH + markerLetter + ".png";
          const marker = new google.maps.Marker({
            position: results[i].geometry.location,
            map: restaurantMap,
            icon: markerIcon,
          });
          restaurantMarkers.push(marker);

          const tr = document.createElement("tr");
          tr.style.backgroundColor = i % 2 === 0 ? "#F0F0F0" : "#FFFFFF";
          tr.onclick = function () {
            google.maps.event.trigger(marker, "click");
          };
          const iconTd = document.createElement("td");
          const nameTd = document.createElement("td");
          const icon = document.createElement("img");
          icon.src = markerIcon;
          icon.setAttribute("class", "placeIcon");
          const name = document.createTextNode(results[i].name);
          iconTd.appendChild(icon);
          nameTd.appendChild(name);
          tr.appendChild(iconTd);
          tr.appendChild(nameTd);

          const addButton = document.createElement("button");
          addButton.textContent = "Add to Itinerary";
          addButton.classList.add("add-to-itinerary");
          addButton.onclick = function() {
                      const restaurantName = results[i].name;
          const restaurantLocation = results[i].geometry.location;
          
          // Add to restaurantItinerary array
          restaurantItinerary.push({
            name: restaurantName,
            type: "restaurant"
          });
          updateItinerary(); 
          console.log(`Added ${restaurantName} to the itinerary`);
          };
          tr.appendChild(addButton);

          document.getElementById("restaurant-table").appendChild(tr);
        }
      }
    });
  }
}

function clearRestaurantResults() {
  const results = document.getElementById("restaurant-table");
  while (results.childNodes[0]) {
    results.removeChild(results.childNodes[0]);
  }
}

function clearRestaurantMarkers() {
  for (let i = 0; i < restaurantMarkers.length; i++) {
    if (restaurantMarkers[i]) {
      restaurantMarkers[i].setMap(null);
    }
  }
  restaurantMarkers = [];
}

var page = 0;
var ticketMasterWidgetTemplate = document.getElementById('Ticketmaster-widget');
var searchButton = $(".button");
var cityI = "Chicago";
var stateI = "IL";
var Today = moment().format('YYYY-MM-DD');
var dateI = Today;
var TktAPIKey = window.TKT_API_KEY || "";

searchButton.on("click", function() {
  selectQuery();
  getEvents(page);
  reloadTicketmasterWidget();
});

function selectQuery() {
  cityI = $('#City').val();
  stateI = $('#state').val(); 
  dateI = $('#eventDate').val();
  selectedCity = cityI;
  console.log(' City entered: ' + cityI);
  console.log(' State entered: ' + stateI);
  console.log(' Date entered: ' + dateI);
}

function getEvents(page) {
  $('#events-panel').show();
  $('#attraction-panel').hide();

  if (page < 0) {
    page = 0;
    return;
  }
  if (page > 0) {
    if (page > getEvents.json.page.totalPages - 1) {
      page = 0;
      return;
    }
  }
 
  $.ajax({
    type: "GET",
    url: "https://app.ticketmaster.com/discovery/v2/events.json?apikey=" + TktAPIKey + "&sort=date,asc" + "&city=" + cityI + "&countryCode=US" + "&startedatetime=" + dateI + "&size=4&page=" + page,
    async: true,
    dataType: "json",
    success: function(json) {
      getEvents.json = json;
      showEvents(json);
      console.log(json);
    },
    error: function(xhr, status, err) {
      console.log(err);
    }
  });
}

function showEvents(json) {
  var items = $('#events .list-group-item');
  items.hide();

  if (json && json._embedded && json._embedded.events) {
    var events = json._embedded.events;
    var item = items.first();
    for (var i = 0; i < events.length; i++) {
      item.children('.list-group-item-heading').text(events[i].name);
      item.children('.list-group-item-text').text(events[i].dates.start.localDate);
      try {
        item.children('.venue').text(events[i]._embedded.venues[0].name + " in " + events[i]._embedded.venues[0].city.name);
      } catch (err) {
        console.log(err);
      }
      item.off("click");

      item.click(events[i], function(eventObject) {
        console.log(eventObject.data);
        try {
          getAttraction(eventObject.data._embedded.attractions[0].id);
        } catch (err) {
          console.log(err);
        }
      });

      if (item.find('.add-to-itinerary').length === 0) {
        var addButton = $('<button>').addClass('add-to-itinerary').text('Add to Itinerary');
        addButton.click(function(e) {
          e.stopPropagation();
          const eventName = $(this).parent().find('.list-group-item-heading').text();
          const eventDate = $(this).parent().find('.list-group-item-text').text();
          eventItinerary.push({
            name: eventName,
            type: "event"
          });
          updateItinerary(); 
          console.log('Add to itinerary:', eventName);
          // Add function to handle adding to itinerary here
        });
        item.append(addButton);
      }

      item.show();
      item = item.next(); // move to the next item
    }
  }
}

var prevButton = $('#prev');
var nextButton = $('#next');

$('#prev').click(function() { 
  getEvents(--page);
});

$('#next').click(function() {
  getEvents(++page);
});

function getAttraction(id) {
  $.ajax({
    type: "GET",
    url: "https://app.ticketmaster.com/discovery/v2/attractions/" + id + ".json?apikey=" + TktAPIKey,
    async: true,
    dataType: "json",
    success: function(json) {
      showAttraction(json);
    },
    error: function(xhr, status, err) {
      console.log(err);
    }
  });
}

function showAttraction(json) {
  $('#events-panel').show(); 
  $('#attraction-panel').show();
  
  if (json && json.name && json.images && json.images.length > 0 && json.classifications && json.classifications.length > 0) {
    $('#attraction .list-group-item-heading').first().text(json.name);
    $('#attraction img').first().attr('src', json.images[0].url);
    $('#attraction img').first().css({'width': '80%', 'height': '80%'});
    $('#classification').text(json.classifications[0].segment.name + " - " + json.classifications[0].genre.name + " - " + json.classifications[0].subGenre.name);
    console.log(json.classifications[0].genre.name);
  }
}

function reloadTicketmasterWidget() {
  if (ticketMasterWidgetTemplate) {
    $('#Ticketmaster-widget').fadeOut(400, function() {
      var newTemplate = $(ticketMasterWidgetTemplate.outerHTML);
      newTemplate.attr('w-city', cityI);
      newTemplate.attr('w-state', stateI);
      $('#Ticketmaster-widget').html(newTemplate);
      var s = document.createElement('script');
      s.src = 'https://ticketmaster-api-staging.github.io/products-and-docs/widgets/event-discovery/1.0.0/lib/main-widget.js';
      document.body.appendChild(s);
      $('#Ticketmaster-widget').fadeIn(400);
    });
  }
}

getEvents(page);
