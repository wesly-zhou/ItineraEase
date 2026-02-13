# ItineraEase

A road trip planning web application that helps users map routes, discover points of interest, find events, hotels, and restaurants along the way, and save their trips for later.

## Features

- **Route Planning** — Enter an origin and destination, add waypoints, and get driving directions via Google Maps
- **Points of Interest** — Discover nearby places (museums, restaurants, etc.) along your route
- **Event Search** — Browse upcoming events in any city using the Ticketmaster API
- **Hotel Search** — Find hotels near your destination with Google Places
- **Restaurant Search** — Look up restaurants by location and cuisine type
- **User Accounts** — Sign up, log in, and save your planned trips and stops
- **Saved Trips** — View and replay your previously saved routes on the map

## Tech Stack

- **Backend:** PHP, MySQL
- **Frontend:** HTML, CSS, JavaScript, jQuery
- **APIs:** Google Maps JavaScript API, Google Places API, Ticketmaster Discovery API

## Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/wesly-zhou/ItineraEase.git
   cd ItineraEase
   ```

2. Copy the sample config and fill in your credentials:
   ```bash
   cp config-sample.php config.php
   ```
   Edit `config.php` with your Google Maps API key and database credentials.

3. Set up a MySQL database and import the required tables (`users`, `waypoints`, saved events, etc.).

4. Serve the project with a PHP-capable server (e.g., Apache with XAMPP, or PHP's built-in server):
   ```bash
   php -S localhost:8000
   ```

5. Open `http://localhost:8000/index.php` in your browser.

## Project Structure

```
ItineraEase/
├── index.php            # Home page — route planning with Google Maps
├── search.php           # Search for events, hotels, and restaurants
├── contact.php          # Contact form
├── faq.php              # Frequently asked questions
├── login.php            # User login
├── signup.php           # User registration
├── logout.php           # Logout handler
├── savedtrips.php       # View and replay saved trip routes
├── savedstops.php       # View saved stops/events
├── header.php           # Shared navbar and page head
├── footer.php           # Shared footer and scripts
├── config.php           # Database and API credentials (gitignored)
├── config-sample.php    # Template config with placeholder values
├── app.js               # Main client-side JavaScript
└── styles/              # CSS stylesheets
```

## Authors

Nazario Campos-Chi, Anamol Kaspal, Prince Millidah, Wesly Zhou
