<?php
include_once (__DIR__ . "/classes/Db.php");
include_once (__DIR__ . "/classes/User.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'error.log');

session_start();

$current_page = 'simulaties';


if (isset($_SESSION["user_id"])) {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);

    try {
        
    } catch (Exception $e) {
        error_log('Database error: ' . $e->getMessage());
    }
} else {
    header("Location: login.php?error=notLoggedIn");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StartupBooster - simulaties</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/Favicon.svg">

    <link href='https://api.mapbox.com/mapbox-gl-js/v2.6.1/mapbox-gl.css' rel='stylesheet' />
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.6.1/mapbox-gl.js'></script>

    <style>
        #map {
            width: 100%;
            height: 600px;
        }

        .marker {
            background-color: blue;
            border-radius: 360px;
            /* width: 30px;
            height: 20px; */
            padding: 4px 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #ffffff;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include_once ('inc/nav.inc.php'); ?>
    <div id="simulaties">
        <h1>Simulaties</h1>

        <input id="city-input" type="text" placeholder="Enter city name">
        <button id="search-button">Search</button>

        <div id="map"></div>
        
    </div>

    <script>
        mapboxgl.accessToken = 'pk.eyJ1IjoidmFtcGkxIiwiYSI6ImNsdzU4NWJrNjFjdXoya3BobGg1M2hnMnEifQ.uHXReRj7ibvLuFQpxtgK6w';

        var map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [4.3517, 50.8503], // Brussels coordinates
            zoom: 8
        });

        // Sample array of locations with numbers
        var locations = [
            { name: "Antwerp", number: 718 },
            { name: "Ghent", number: 635 },
            { name: "Charleroi", number: 821 },
            { name: "Liège", number: 569 },
            { name: "Brussels", number: 914 },
            { name: "Bruges", number: 726 },
            { name: "Namur", number: 567 },
            { name: "Leuven", number: 824 },
            { name: "Mons", number: 690 },
            { name: "Aalst", number: 554 },
            { name: "Mechelen", number: 728 },
            { name: "La Louvière", number: 625 },
            { name: "Kortrijk", number: 581 },
            { name: "Hasselt", number: 903 },
            { name: "Ostend", number: 640 },
            { name: "Sint-Niklaas", number: 546 },
            { name: "Tournai", number: 809 },
            { name: "Genk", number: 725 },
            { name: "Roeselare", number: 582 },
            { name: "Verviers", number: 768 },
            { name: "Mouscron", number: 667 },
            { name: "Beveren", number: 772 },
            { name: "Dendermonde", number: 687 },
            { name: "Beringen", number: 519 },
            { name: "Turnhout", number: 698 },
            { name: "Dilbeek", number: 631 },
            { name: "Heist-op-den-Berg", number: 578 },
            { name: "Sint-Truiden", number: 621 },
            { name: "Lokeren", number: 914 },
            { name: "Braine-l'Alleud", number: 750 },
            { name: "Brasschaat", number: 543 },
            { name: "Grimbergen", number: 873 },
            { name: "Halle", number: 661 },
            { name: "Waregem", number: 802 },
            { name: "Lier", number: 945 },
            { name: "Schoten", number: 700 },
            { name: "Ieper", number: 891 },
            { name: "Tienen", number: 546 },
            { name: "Herentals", number: 939 },
            { name: "Waver", number: 624 },
            { name: "Aarschot", number: 738 },
            { name: "Bilzen", number: 579 },
            { name: "Hoboken", number: 607 },
            { name: "Mol", number: 876 },
            { name: "Lommel", number: 554 },
            { name: "Geraardsbergen", number: 750 },
            { name: "Houthalen", number: 645 },
            { name: "Maasmechelen", number: 807 },
            { name: "Zaventem", number: 698 },
        ];

        // Add markers for each location
        locations.forEach(location => {
            // Use MapBox Geocoding API to get coordinates for the location
            fetch('https://api.mapbox.com/geocoding/v5/mapbox.places/' + encodeURIComponent(location.name) + '.json?access_token=' + mapboxgl.accessToken)
            .then(response => response.json())
            .then(data => {
                var coordinates = data.features[0].center;
                // Create custom marker element with number
                var markerElement = createCustomMarkerElement(location.number);
                var marker = new mapboxgl.Marker({
                    element: markerElement
                })
                .setLngLat(coordinates)
                .addTo(map);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        // Function to create custom marker element with number
        function createCustomMarkerElement(number) {
            var el = document.createElement('div');
            el.className = 'marker';
            el.textContent = '€ ' + number;
            return el;
        }

        // Event listener for search button click
        document.getElementById('search-button').addEventListener('click', function() {
            var location = document.getElementById('city-input').value;

            // Use MapBox Geocoding API to get coordinates for the entered location
            fetch('https://api.mapbox.com/geocoding/v5/mapbox.places/' + encodeURIComponent(location) + '.json?access_token=' + mapboxgl.accessToken)
            .then(response => response.json())
            .then(data => {
                var coordinates = data.features[0].center;
                // Fly to the coordinates
                map.flyTo({ center: coordinates, zoom: 18 });
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

    </script>
</body>



</html>