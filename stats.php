<?php
include_once (__DIR__ . "/classes/Db.php");
include_once (__DIR__ . "/classes/User.php");

session_start();
$current_page = 'stats';

// if (isset($_SESSION["user_id"])) {
//     $pdo = Db::getInstance();
//     $user = User::getUserById($pdo, $_SESSION["user_id"]);

//     try {
//         $pdo = Db::getInstance();
//     } catch (Exception $e) {
//         error_log('Database error: ' . $e->getMessage());
//     }
// } else {
//     header("Location: login.php?error=notLoggedIn");
//     exit();
// }
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
                    <h2>Overzicht</h2>
                    <div class="elements">
                        <div class="element">
                            <div class="row">
                                <img src="./assets/images/revenue.svg" alt="revenue">
                                <h3>Omzet</h3>
                            </div>
                            <p class="price">€1.206</p>
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
                            <p class="price">€89</p>
                            <div class="increaseRow">
                                <i class="fa fa-arrow-up"></i>
                                <p class="increase">+18%</p>
                            </div>
                        </div>
                        <div class="element">
                            <div class="row">
                                <img src="./assets/images/profit.svg" alt="profit">
                                <h3>Winst</h3>
                            </div>
                            <p class="price">€1.117</p>
                            <div class="increaseRow">
                                <i class="fa fa-arrow-down"></i>
                                <p class="increase">-2%</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rapport">
                    <div class="row">
                        <h2>Rapport</h2>
                        <div>
                            <select name="filter" id="filter">
                                <option value="Student-zelfstandigen">Student-zelfstandigen</option>
                                <option value="Zelfstandigen">Zelfstandigen</option>
                            </select>
                            <select name="filter" id="filter">
                                <option value="revenue">Omzet</option>
                                <option value="cost">Kosten</option>
                                <option value="profit">Winst</option>
                            </select>
                        </div>
                    </div>
                    <div class="figure">
                        <div id="curve_chart" style="width: 100%; height: 500px"></div>
                    </div>
                </div>



                <div class="rapport two">
                    <div class="row">
                        <h2>Overzicht sectoren</h2>
                        <select name="year" id="year">
                            <option value="year">2024</option>
                            <option value="year">2023</option>
                            <option value="year">2022</option>
                            <option value="year">2021</option>
                        </select>
                    </div>
                    <div class="figure">
                        <select name="filter" id="filter">
                            <option value="revenue">Omzet</option>
                            <option value="cost">Kosten</option>
                            <option value="profit">Winst</option>
                        </select>
                        <div class="column">
                            <canvas id="myChart" style="width:100%"></canvas>
                            <select name="filter" id="filter">
                                <option value="revenue">Omzet</option>
                                <option value="cost">Kosten</option>
                                <option value="profit">Winst</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right">
                <div class="background center">
                    <h2>Klanttevredenheidsscore</h2>
                    <div class="circle2">
                        <div class="circle1">
                            <h3>70%</h3>
                            <p>Tevreden klanten</p>
                        </div>
                    </div>
                    <div class="legenda">
                        <div class="item">
                            <p class="circle blue"></p>
                            <p>Tevreden</p>
                        </div>
                        <div class="item">
                            <p class="circle gray"></p>
                            <p>Ontevreden</p>
                        </div>
                    </div>
                </div>
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
    <script>
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
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', { 'packages': ['corechart'] });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Year', 'Mijn rapport', 'Rapport gemiddelde ondernemer'],
                ['2023', 1000, 400],
                ['2022', 1170, 460],
                ['2021', 660, 1120],
                ['2020', 1030, 540]
            ]);

            var options = {
                curveType: 'function',
                legend: { position: 'bottom' }
            };

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

            chart.draw(data, options);
        }
    </script>
</body>

</html>