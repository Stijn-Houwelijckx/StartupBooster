<?php
include_once (__DIR__ . "/classes/Db.php");
include_once (__DIR__ . "/classes/User.php");
include_once (__DIR__ . "/classes/Stat.php");

session_start();
$current_page = 'stats';

if (isset($_SESSION["user_id"])) {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);
    try {
        $year = isset($_POST['year']) ? $_POST['year'] : date("Y", strtotime("-1 year"));
        $pdo = Db::getInstance();
        $stats = Stat::getStats($pdo, $year, $_SESSION["user_id"]);
        $allStats = Stat::getAllStats($pdo);
        $userYears = Stat::getUserYears($pdo, $_SESSION["user_id"]);
        // var_dump($userYears);

        function calculateMedian($values)
        {
            sort($values);
            $count = count($values);
            $middle = floor($count / 2);
            if ($count % 2 == 0) {
                $median = ($values[$middle - 1] + $values[$middle]) / 2;
            } else {
                $median = $values[$middle];
            }
            return $median;
        }

        // Array to store medians
        $medians = [];

        // Loop through the data
        foreach ($allStats as $item) {
            // Skip user_id and id
            unset($item['id']);
            unset($item['user_id']);

            // Extract year
            $year = $item['year'];
            unset($item['year']);

            // Initialize median array for the year if not exists
            if (!isset($medians[$year])) {
                $medians[$year] = [];
            }

            // Calculate median for each type of financial data
            foreach ($item as $key => $value) {
                // Convert to float for calculation
                $value = floatval($value);

                // Initialize array for the key if not exists
                if (!isset($medians[$year][$key])) {
                    $medians[$year][$key] = [];
                }

                // Add value to array
                $medians[$year][$key][] = $value;
            }
        }

        $data = [
            ['Year', 'Mijn rapport', 'Rapport mediaan ondernemer'],

        ];

        // Calculate medians
        foreach ($medians as $year => $financialData) {
            // echo "Year: $year\n";
            foreach ($financialData as $key => $values) {
                $median = intval(calculateMedian($values));
                // echo "$key Median: $median\n";
            }
            // $data[$year + 1][2] = $median;
        }

        foreach ($userYears as $key => $userYearArray) {
            $userYear = $userYearArray["year"];
            $data[$key + 1][0] = strval($userYear);

        }

        $data[1][1] = rand(100, 100000);
        $data[1][2] = rand(100, 100000);
        $data[2][1] = rand(100, 100000);
        $data[2][2] = rand(100, 100000);
        $data[3][1] = rand(100, 100000);
        $data[3][2] = rand(100, 100000);
        $data[4][1] = rand(100, 100000);
        $data[4][2] = rand(100, 100000);
        $data[5][1] = rand(100, 100000);
        $data[5][2] = rand(100, 100000);

        // var_dump($data);

        $json_data = json_encode($data);
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
    <title>StartupBooster - statistieken</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/Favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
</head>


<body>
    <?php include_once ('inc/nav.inc.php'); ?>
    <div id="stats">
        <h1>Statistieken</h1>
        <div class="top">
            <h2>De gezondheid van uw bedrijf bijhouden</h2>
            <p>Controleer en analyseer je gegevens op de handigste manier</p>
        </div>
        <div class="elements">
            <div class="left">
                <div class="overview">
                    <div class="row">
                        <h2>Overzicht</h2>
                        <form action="" method="POST" id="filter_year_form"> <select name="year" id="filter_year"
                                onchange="submitForm()">
                                <option value="2023" <?php if ($year == "2023")
                                    echo "selected"; ?>>2023</option>
                                <option value="2022" <?php if ($year == "2022")
                                    echo "selected"; ?>>2022</option>
                                <option value="2021" <?php if ($year == "2021")
                                    echo "selected"; ?>>2021</option>
                                <option value="2020" <?php if ($year == "2020")
                                    echo "selected"; ?>>2020</option>
                            </select>
                        </form>
                    </div>
                    <?php if (!empty($stats)): ?>
                        <div class="btnsElements">
                            <button id="prevBtn"><i class="fa fa-angle-left"></i></button>
                            <?php foreach ($stats as $c): ?>
                                <div class="elements tegels">
                                    <div class="element">
                                        <div class="row">
                                            <img src="./assets/images/profit.svg" alt="profit">
                                            <h3>Winst/verlies</h3>
                                        </div>
                                        <p class="price">€
                                            <?php echo htmlspecialchars($c["profit_loss"]); ?>
                                        </p>
                                        <div class="increaseRow">
                                            <i class="fa fa-arrow-down"></i>
                                            <p class="increase">-980%</p>
                                        </div>
                                    </div>
                                    <div class="element">
                                        <div class="row">
                                            <img src="./assets/images/profit.svg" alt="profit">
                                            <h3>Eigen vermogen</h3>
                                        </div>
                                        <p class="price">€
                                            <?php echo htmlspecialchars($c["equityCapital"]); ?>
                                        </p>
                                        <div class="increaseRow">
                                            <i class="fa fa-arrow-down"></i>
                                            <p class="increase">-25%</p>
                                        </div>
                                    </div>
                                    <div class="element">
                                        <div class="row">
                                            <img src="./assets/images/profit.svg" alt="profit">
                                            <h3>Brutomarge</h3>
                                        </div>
                                        <p class="price">€
                                            <?php echo htmlspecialchars($c["grossMargin"]); ?>
                                        </p>
                                        <div class="increaseRow">
                                            <i class="fa fa-arrow-down"></i>
                                            <p class="increase">-2%</p>
                                        </div>
                                    </div>
                                    <div class="element">
                                        <div class="row">
                                            <img src="./assets/images/revenue.svg" alt="revenue">
                                            <h3>Omzet</h3>
                                        </div>
                                        <p class="price">€
                                            <?php echo htmlspecialchars($c["revenue"]); ?>
                                        </p>
                                        <div class="increaseRow">
                                            <i class="fa fa-arrow-up"></i>
                                            <p class="increase">+12%</p>
                                        </div>
                                    </div>
                                    <div class="element">
                                        <div class="row">
                                            <img src="./assets/images/cost.svg" alt="cost">
                                            <h3>Kosten</h3>
                                        </div>
                                        <p class="price">€
                                            <?php echo htmlspecialchars($c["costs"]); ?>
                                        </p>
                                        <div class="increaseRow">
                                            <i class="fa fa-arrow-up"></i>
                                            <p class="increase">+18%</p>
                                        </div>
                                    </div>
                                    <div class="element">
                                        <div class="row">
                                            <img src="./assets/images/profit.svg" alt="profit">
                                            <h3>Personeel <span>FTE</span></h3>
                                        </div>
                                        <p class="price">
                                            <?php echo htmlspecialchars($c["personnel"]); ?>
                                        </p>
                                        <div class="increaseRow">
                                            <i class="fa fa-arrow-down"></i>
                                            <p class="increase">-2%</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <button id="nextBtn"><i class="fa fa-angle-right"></i></button>
                        </div>
                    <?php else: ?>
                        <p>Geen statistieken gevonden.</p>
                    <?php endif; ?>
                </div>
                <div class="rapport">
                    <div class="row">
                        <h2>Rapport</h2>
                        <div>
                            <form action="" method="POST">
                                <select name="filter">
                                    <option value="Student-zelfstandigen">Student-zelfstandigen</option>
                                    <option value="Zelfstandigen">Zelfstandigen</option>
                                </select>
                                <select name="filter">
                                    <option value="revenue">Omzet</option>
                                    <option value="cost">Kosten</option>
                                    <option value="profit">Winst</option>
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="figure">
                        <div id="curve_chart" style="width: 100%; height: 500px"></div>
                    </div>
                </div>



                <div class="rapport two">
                    <div class="row">
                        <h2>Overzicht sectoren</h2>
                        <select name="year">
                            <option value="year">2024</option>
                            <option value="year">2023</option>
                            <option value="year">2022</option>
                            <option value="year">2021</option>
                        </select>
                    </div>
                    <div class="figure">
                        <select name="filter">
                            <option value="revenue">Omzet</option>
                            <option value="cost">Kosten</option>
                            <option value="profit">Winst</option>
                        </select>
                        <div class="column">
                            <canvas id="myChart" style="width:100%"></canvas>
                            <select name="filter">
                                <option value="revenue">Omzet</option>
                                <option value="cost">Kosten</option>
                                <option value="profit">Winst</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right">
                <div class="background">
                    <h2>Meest gestelde vragen</h2>
                    <div class="questions">
                        <div class="question">
                            <p>Is het voordelig om iemand extra aan te nemen in mijn bedrijf?</p>
                            <i class="fa fa-angle-down"></i>
                        </div>
                        <div class="question">
                            <p>Is het voordelig om iemand extra aan te nemen in mijn bedrijf?</p>
                            <i class="fa fa-angle-down"></i>
                        </div>
                        <div class="question">
                            <p>Is het voordelig om iemand extra aan te nemen in mijn bedrijf?</p>
                            <i class="fa fa-angle-down"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        function submitForm() {
            document.getElementById("filter_year_form").submit();
        }

        document.addEventListener("DOMContentLoaded", function () {
            const prevBtn = document.getElementById("prevBtn");
            const nextBtn = document.getElementById("nextBtn");
            const elementsContainer = document.querySelector(".tegels");
            let currentPosition = 0;

            function scrollLeft() {
                const elementsContainer = document.querySelector(".tegels");
                const currentPosition = elementsContainer.scrollLeft;
                const newPosition = currentPosition - 200; // Adjust scroll amount as needed
                elementsContainer.scrollTo({
                    left: newPosition,
                    behavior: 'smooth' // Add smooth scrolling behavior
                });
            }

            function scrollRight() {
                const elementsContainer = document.querySelector(".tegels");
                const currentPosition = elementsContainer.scrollLeft;
                const newPosition = currentPosition + 200; // Adjust scroll amount as needed
                elementsContainer.scrollTo({
                    left: newPosition,
                    behavior: 'smooth' // Add smooth scrolling behavior
                });
            }

            prevBtn.addEventListener("click", scrollLeft);
            nextBtn.addEventListener("click", scrollRight);
        });

        /* ---- cijfers postief of negatief maken overzicht ---- */
        window.onload = function () {
            var increases = document.querySelectorAll('.increase');

            increases.forEach(function (item) {
                var increaseText = item.textContent.trim();
                if (increaseText.includes('+')) {
                    item.parentElement.classList.add('green');
                } else if (increaseText.includes('-')) {
                    item.parentElement.classList.add('red');
                }
            });
        };

        /* ---- eerste grafiek, vergelijken met gemiddelde ondernemer binnen sector ---- */
        google.charts.load('current', { 'packages': ['corechart'] });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var jsonData = <?php echo $json_data; ?>; // Haal de JSON-data op die door PHP is gegenereerd

            var data = google.visualization.arrayToDataTable(jsonData);

            var options = {
                curveType: 'function',
                legend: { position: 'bottom' }
            };

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

            chart.draw(data, options);
        }

        /* ---- 2de grafiek, vergelijken op sector ---- */
        const xyValues = [
            { name: "Gezondheidszorg en sociale diensten", x: 200, y: 80, pointRadius: 25, backgroundColor: "rgba(255, 0, 0, 0.6)" }, // Rood met 80% opacity
            { name: "Detailhandel", x: 340, y: 160, pointRadius: 10, backgroundColor: "rgba(0, 0, 255, 0.6)" }, // Blauw met 80% opacity
            { name: "Industrie", x: 450, y: 580, pointRadius: 40, backgroundColor: "rgba(0, 255, 0, 0.6)" }, // Groen met 80% opacity
            { name: "Onderwijs", x: 280, y: 480, pointRadius: 55, backgroundColor: "rgba(255, 255, 0, 0.6)" }, // Geel met 80% opacity
            { name: "ICT en technologie  ", x: 430, y: 120, pointRadius: 30, backgroundColor: "rgba(255, 165, 0, 0.6)" }, // Oranje met 80% opacity
            { name: "Bouw en vastgoed", x: 320, y: 820, pointRadius: 20, backgroundColor: "rgba(128, 0, 128, 0.6)" }, // Paars met 80% opacity
            { name: "Horeca en toerisme  ", x: 120, y: 100, pointRadius: 50, backgroundColor: "rgba(0, 255, 255, 0.6)" }, // Cyaan met 80% opacity
            { name: "Transport en logistiek  ", x: 630, y: 360, pointRadius: 15, backgroundColor: "rgba(255, 0, 255, 0.6)" }, // Magenta met 80% opacity
            { name: "Consultancy en professionele dienstverlening", x: 520, y: 420, pointRadius: 45, backgroundColor: "rgba(0, 128, 128, 0.6)" }, // Teal met 80% opacity
            { name: "Landbouw en voedingsindustrie", x: 830, y: 630, pointRadius: 14, backgroundColor: "rgba(0, 0, 128, 0.6)" }, // Navy met 80% opacity
            { name: "Landbouw en voedingsindustrie", x: 420, y: 630, pointRadius: 24, backgroundColor: "rgba(100, 100, 0, 0.6)" }, // Aangepaste kleur
            { name: "Landbouw en voedingsindustrie", x: 510, y: 630, pointRadius: 40, backgroundColor: "rgba(200, 200, 0, 0.6)" }, // Aangepaste kleur
            { name: "Energie en milieu", x: 630, y: 630, pointRadius: 25, backgroundColor: "rgba(0, 128, 128, 0.6)" }, // Teal met 80% opacity
            { name: "Landbouw en voedingsindustrie", x: 310, y: 630, pointRadius: 35, backgroundColor: "rgba(0, 100, 128, 0.6)" }, // Aangepaste kleur
            { name: "Media en communicatie", x: 600, y: 630, pointRadius: 44, backgroundColor: "rgba(100, 0, 128, 0.6)" }, // Aangepaste kleur
            { name: "Automotive sector", x: 100, y: 630, pointRadius: 32, backgroundColor: "rgba(0, 200, 128, 0.6)" }, // Aangepaste kleur
            { name: "Farmaceutische industrie", x: 780, y: 630, pointRadius: 52, backgroundColor: "rgba(200, 128, 0, 0.6)" }, // Aangepaste kleur
            { name: "Creatieve industrieën", x: 220, y: 630, pointRadius: 59, backgroundColor: "rgba(128, 200, 0, 0.6)" }, // Aangepaste kleur
            { name: "Telecommunicatie", x: 180, y: 630, pointRadius: 22, backgroundColor: "rgba(128, 0, 200, 0.6)" }, // Aangepaste kleur
            { name: "Juridische dienstverlening", x: 800, y: 630, pointRadius: 31, backgroundColor: "rgba(128, 0, 0, 0.6)" }, // Donkerrood met 80% opacity
        ];

        const datasets = xyValues.map(value => ({
            pointRadius: value.pointRadius,
            backgroundColor: value.backgroundColor,
            label: value.name, // Gebruik de naam uit xyValues als label voor elke dataset
            data: [value]
        }));

        new Chart("myChart", {
            type: "scatter",
            data: {
                datasets: datasets
            },
            options: {
                legend: { display: true },
                scales: {
                    xAxes: [{ ticks: { min: 0, max: 1000 } }],
                    yAxes: [{ ticks: { min: 0, max: 1000 } }],
                }
            }
        });
    </script>
</body>

</html>