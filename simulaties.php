<?php
include_once (__DIR__ . "/classes/Db.php");
include_once (__DIR__ . "/classes/User.php");
include_once (__DIR__ . "/classes/Stat.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'error.log');

session_start();

$current_page = 'simulaties';


if (isset($_SESSION["user_id"])) {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);
    
    try {
        // $allUsers = User::getAll($pdo);
        $allUsersBySector = User::getAllByUserSector($pdo, $user["id"]);
        $locations = [];

        foreach ($allUsersBySector as $user) {
            $stats = Stat::getStats($pdo, date('Y') - 1, $user["id"]);

            $locations[] = [
                'name' => $user["street"] . " " . $user["houseNumber"] . ", " . $user["city"],
                'number' => $stats[0]["price"]
            ];
        }

        // var_dump($locations);

        $locationsJson = json_encode($locations);
        
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
        <h1>Pand prijzen & Simulaties</h1>

        <input id="city-input" type="text" placeholder="Enter city name">
        <button id="search-button" class="btn">Search</button>

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
        var locations = <?php echo $locationsJson; ?>;

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