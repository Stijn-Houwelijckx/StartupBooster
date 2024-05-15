<?php
include_once (__DIR__ . "/classes/Db.php");
include_once (__DIR__ . "/classes/User.php");
include_once (__DIR__ . "/classes/Stat.php");
include_once (__DIR__ . "/classes/Sector.php");

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'error.log');

$current_page = 'stats';

if (isset($_SESSION["user_id"])) {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);
    try {
        $pdo = Db::getInstance();
        $userYears = Stat::getUserYears($pdo, $_SESSION["user_id"]);
        $citys = User::getAllCitys($pdo);

        // Standaardlocatie
        $defaultLocation = isset($citys[0]['city']) ? $citys[0]['city'] : "";

        // Haal de geschatte kosten op voor de standaardlocatie
        $defaultEstimatedCost = User::priceByCity($pdo, $defaultLocation);
        $defaultEstimatedCost = (float)$defaultEstimatedCost['AVG(price)'];

        if (isset($_GET["location"]) && isset($_GET["budget"])) {
            $location = $_GET["location"];
            $budget = $_GET["budget"];
            $estimatedCost = User::priceByCity($pdo, $_GET["location"]);
            $estimatedCost = (float)$estimatedCost['AVG(price)'];
            $response = "De geschatte kosten voor een pand in $location met een budget van €$budget zijn €$estimatedCost per maand.";
            var_dump($estimatedCost);
        }

        if (!empty($userYears)) {
           
            // eerste grafiek
            $years = array_column($userYears, 'year');
            $lowestYear = min($years);
            $highestYear = max($years);
            $allStatsByStatuteSector = Stat::getAllStatsByStatuteSector($pdo, $lowestYear, $highestYear, $user['statute_id'], $user['sector_id']);
            $wantedStat = "revenue";
            $wantedStatCalc = "median";
            $wantedYear = 2023;

            $year = isset($_POST['year']) ? $_POST['year'] : date("Y", strtotime("-1 year"));

            $differenceToLastYear = [];
            $currentYearStats = Stat::getStats($pdo, $year, $_SESSION["user_id"]);
            $previousYearStats = Stat::getStats($pdo, $year - 1, $_SESSION["user_id"]);

            unset($currentYearStats[0]["id"]);
            unset($currentYearStats[0]["user_id"]);
            unset($previousYearStats[0]["id"]);
            unset($previousYearStats[0]["user_id"]);

            $statNamesDutch = [
                "revenue" => "Omzet",
                "costs" => "Kosten",
                "profit_loss" => "Winst/verlies",
                "personnel" => "Personeel",
                "equityCapital" => "Eigen vermogen",
                "grossMargin" => "Bruto marge"
            ];

            if (isset($previousYearStats[0]["year"]) && $previousYearStats[0]["year"] >= $lowestYear) {
                unset($previousYearStats[0]["year"]);
                unset($currentYearStats[0]["year"]);
                foreach ($currentYearStats[0] as $key => $value) {
                    if (isset($previousYearStats[0][$key])) {
                        $differenceToLastYear[$key] = (($value - $previousYearStats[0][$key]) / $previousYearStats[0][$key]) * 100;
                    }
                }
                foreach ($differenceToLastYear as $key => $value) {
                    $differenceToLastYear[$key] = number_format($value);
                }
            } else {
                $differenceToLastYear = [
                    "revenue" => 0,
                    "costs" => 0,
                    "profit_loss" => 0,
                    "personnel" => 0,
                    "equityCapital" => 0,
                    "grossMargin" => 0
                ];

                unset($currentYearStats[0]["year"]);
            }

            if (isset($_POST["statsFilter"])) {
                $wantedStat = $_POST["statsFilter"];
                $wantedStatCalc = $_POST["calcFilter"];
            }

            if (isset($_POST["year"])) {
                $wantedYear = $_POST["year"];
            }

            $legenda = $wantedStatCalc == "median" ? "mediaan" : "gemiddelde";

            $data = [
                ['Year', 'Mijn rapport', 'Rapport ' . $legenda . ' ondernemer'],
            ];

            $filteredStats = [];
            foreach ($allStatsByStatuteSector as $stat) {
                if (in_array($stat['year'], $years)) {
                    $filteredStats[] = $stat;
                }
            }

            $calculatedStats = [];
            foreach ($filteredStats as $stat) {
                $year = $stat['year'];
                $values = [];
                foreach ($filteredStats as $filteredStat) {
                    if ($filteredStat['year'] == $year) {
                        $values[] = $filteredStat[$wantedStat];
                    }
                }

                if ($wantedStatCalc == "median") {
                    $median = Stat::calculateMedian($values);
                } else {
                    $median = Stat::calculateAverage($values);
                }

                $calculatedStats[$year] = intval($median);
            }

            $i = 1;
            foreach ($allStatsByStatuteSector as $key => $value) {
                if ($value['user_id'] == $_SESSION["user_id"]) {
                    $data[$i][0] = strval($value['year']);
                    $data[$i][1] = intval($value[$wantedStat]);
                    $data[$i][2] = intval($calculatedStats[$value['year']]);
                    $i++;
                }
            }

            $userSector = Sector::getSectorByUserId($pdo, $_SESSION["user_id"]);

            $json_sector_UID = json_encode($userSector["UID"]);

            $json_data = json_encode($data);

            // tweede grafiek
            $wantedSectorStatX = "revenue";
            $wantedSectorStatY = "costs";
            $wantedSectorStatsYear = 2023;
            $sectors = Sector::getPopulatedSector($pdo);
            $sectorYears = array_column(Stat::getSectorYears($pdo), "year");
            $minSectorYear = min($sectorYears);
            $maxSectorYear = max($sectorYears);

            if (isset($_POST["sectorStatsFilterX"])) {
                $wantedSectorStatX = $_POST["sectorStatsFilterX"];
                $wantedSectorStatY = $_POST["sectorStatsFilterY"];
                $wantedSectorStatsYear = $_POST["sectorStatsYear"];
            }

            $sectorData = [];

            $highestXValue = 0;
            $highestYValue = 0;

            foreach ($sectors as $key => $sector) {
                $userCount = Sector::getUserCountBySectorId($pdo, $sector['id']);

                $sectorDataX = Stat::getAllStatsByType($pdo, $wantedSectorStatsYear, $wantedSectorStatX, $sector['id']);
                $sectorDataY = Stat::getAllStatsByType($pdo, $wantedSectorStatsYear, $wantedSectorStatY, $sector['id']);

                if (empty($sectorDataX) || empty($sectorDataY)) {
                    continue;
                }

                $sectorDataXAvg = Stat::calculateAverage(array_column($sectorDataX, $wantedSectorStatX));
                $sectorDataYAvg = Stat::calculateAverage(array_column($sectorDataY, $wantedSectorStatY));

                if ($sectorDataXAvg > $highestXValue) {
                    $highestXValue = intval($sectorDataXAvg);
                }

                if ($sectorDataYAvg > $highestYValue) {
                    $highestYValue = intval($sectorDataYAvg);
                }

                $sectorData[$key]['pointRadius'] = $userCount * 10;
                $red = rand(0, 255);
                $green = rand(0, 255);
                $blue = rand(0, 255);
                $alpha = 0.6;
                $sectorData[$key]['backgroundColor'] = "rgba($red, $green, $blue, $alpha)";
                $sectorData[$key]['label'] = $sector['title'];
                $sectorData[$key]['x'] = intval($sectorDataXAvg);
                $sectorData[$key]['y'] = intval($sectorDataYAvg);
            }

            $jsSectorValuesJSON = json_encode($sectorData);

        } else {
            $error = "Geen statistieken gevonden.";
        }
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
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/Favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>


<body>
    <?php include_once ('inc/nav.inc.php'); ?>
    <div id="stats">
        <h1>Statistieken</h1>
        <?php if (isset($error)): ?>
            <p>
                <?php echo $error; ?>
            </p>
        <?php else: ?>
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
                                    onchange="submitYearForm()">
                                    <?php foreach (array_reverse($years) as $year): ?>
                                        <option value="<?php echo $year; ?>" <?php if ($year == $wantedYear) {echo "selected";} ?>>
                                            <?php echo $year; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                        <?php if (!empty($currentYearStats)): ?>
                            <div class="btnsElements">
                                <button id="prevBtn"><i class="fa fa-angle-left"></i></button>
                                <div class="elements tegels">
                                    <?php foreach ($currentYearStats[0] as $key => $stat): ?>
                                        <div class="element">
                                            <div class="row">
                                                <img src="./assets/images/<?php echo $key ?>.svg" alt="icon">
                                                <h3>
                                                    <?php echo $statNamesDutch[$key] ?>
                                                </h3>
                                            </div>
                                            <p class="price">
                                                <?php echo $key == "personnel" ? htmlspecialchars($stat) : "€ " . htmlspecialchars($stat); ?>
                                            </p>
                                            <div class="increaseRow">
                                                <i class="fa fa-arrow-down increaseIcon"></i>
                                                <p class="increase">
                                                    <?php echo htmlspecialchars($differenceToLastYear[$key]); ?>%
                                                </p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
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
                                <form action="" method="POST" id="statsFilter" onchange="submitStatsForm()">
                                    <!-- <select name="statuteFilter">
                                        <option value="Student-zelfstandigen">Student-zelfstandigen</option>
                                        <option value="Zelfstandigen">Zelfstandigen</option>
                                    </select> -->
                                    <select name="calcFilter">
                                        <option value="median" <?php if ($wantedStatCalc == "median") {echo "selected";} ?>>Mediaan</option>
                                        <option value="average" <?php if ($wantedStatCalc == "average") {echo "selected";} ?>>Gemiddelde</option>
                                    </select>
                                    <select name="statsFilter" id="statsFilterChart">
                                        <option value="revenue" <?php if ($wantedStat == "revenue") {echo "selected";} ?>>Omzet</option>
                                        <option value="costs" <?php if ($wantedStat == "costs") {echo "selected";} ?>>Kosten</option>
                                        <option value="profit_loss" <?php if ($wantedStat == "profit_loss") {echo "selected";} ?>>Winst</option>
                                        <option value="personnel" <?php if ($wantedStat == "personnel") {echo "selected";} ?>>Personeel</option>
                                        <option value="equityCapital" <?php if ($wantedStat == "equityCapital") {echo "selected";} ?>>Eigen vermogen</option>
                                        <option value="grossMargin" <?php if ($wantedStat == "grossMargin") {echo "selected";} ?>>Bruto marge</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                        <div class="figure">
                            <div id="curve_chart" style="width: 100%; height: 500px"></div>
                        </div>
                    </div>

                    <div class="rapport two">
                        <form action="" id="sectorStastFilter" method="post" onchange="submitSectorStatsForm()">
                            <div class="row">
                                <h2>Overzicht sectoren</h2>
                                    <select name="sectorStatsYear">
                                        <?php foreach ($sectorYears as $year): ?>
                                            <option value="<?php echo $year; ?>" <?php if ($year == $wantedSectorStatsYear) {echo "selected";} ?>>
                                                <?php echo $year; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                            </div>
                        <div class="figure">
                            <select name="sectorStatsFilterY" id="sectorStatsFilterY">
                                <option value="revenue" <?php if ($wantedSectorStatY == "revenue") {echo "selected";} ?>>Omzet</option>
                                <option value="costs" <?php if ($wantedSectorStatY == "costs") {echo "selected";} ?>>Kosten</option>
                                <option value="profit_loss" <?php if ($wantedSectorStatY == "profit_loss") {echo "selected";} ?>>Winst</option>
                                <option value="personnel" <?php if ($wantedSectorStatY == "personnel") {echo "selected";} ?>>Personeel</option>
                                <option value="equityCapital" <?php if ($wantedSectorStatY == "equityCapital") {echo "selected";} ?>>Eigen vermogen</option>
                                <option value="grossMargin" <?php if ($wantedSectorStatY == "grossMargin") {echo "selected";} ?>>Bruto marge</option>
                            </select>
                            <div class="column">
                                <canvas id="myChart" style="width:100%"></canvas>
                                <select name="sectorStatsFilterX" id="sectorStatsFilterX">
                                    <option value="revenue" <?php if ($wantedSectorStatX == "revenue") {echo "selected";} ?>>Omzet</option>
                                    <option value="costs" <?php if ($wantedSectorStatX == "costs") {echo "selected";} ?>>Kosten</option>
                                    <option value="profit_loss" <?php if ($wantedSectorStatX == "profit_loss") {echo "selected";} ?>>Winst</option>
                                    <option value="personnel" <?php if ($wantedSectorStatX == "personnel") {echo "selected";} ?>>Personeel</option>
                                    <option value="equityCapital" <?php if ($wantedSectorStatX == "equityCapital") {echo "selected";} ?>>Eigen vermogen</option>
                                    <option value="grossMargin" <?php if ($wantedSectorStatX == "grossMargin") {echo "selected";} ?>>Bruto marge</option>
                                </select>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
                <div class="right">
                    <div class="background">
                        <h2>Simulator</h2>
                        <div class="questions">
                            <div class="question first">
                                <p>Is het voordelig om iemand extra aan te nemen in mijn bedrijf?</p>
                                <i class="fa fa-angle-down"></i>
                            </div>
                            <form action="" method="GET" id="employee_form">
                                <div class="row">
                                    <div class="column">
                                        <label for="employee_count">Aantal werknemers</label>
                                        <input type="text" name="employee_count" id="employee_count">
                                    </div>
                                    <div class="column">
                                        <label for="employee_hours">Aantal werkuren per dag</label>
                                        <input type="text" name="employee_hours" id="employee_hours">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="column">
                                        <label for="employee_wage">Uurloon (&euro;)</label>
                                        <input type="text" name="employee_wage" id="employee_wage" placeholder="10">
                                    </div>
                                </div>
                                <button type="submit" class="btn" id="employee_simulate">Simuleren</button>
                            </form>
                            <div class="question second">
                                <p>Hoeveel kost een pand in mijn buurt?</p>
                                <i class="fa fa-angle-down"></i>
                            </div>
                            <form action="" method="GET" id="premises_form">
                                <div class="row">
                                    <div class="column">
                                        <label for="premises_location">Locatie</label>
                                        <select name="premises_location" id="premises_location" onchange="updateEstimatedCost(this.value)">
                                            <?php foreach ($citys as $city): ?>
                                                <option value="<?php echo $city['city']; ?>"><?php echo $city['city']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p id="estimated_cost"><?php echo $defaultEstimatedCost; ?></p>
                                    </div>
                                    <div class="column">
                                        <p id="estimated_cost"></p>
                                        <label for="premises_price">Gemiddeld bedrag</label>
                                        <p id="default_estimated_cost"><?php echo $defaultEstimatedCost?></p>
                                    </div>
                                </div>
                                <button type="button" class="btn" id="premises_simulate">Simuleren</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    <?php endif; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    
    <script>
function updateEstimatedCost() {
    var location = document.getElementById("premises_location").value;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var estimatedCost = parseFloat(this.responseText);
            document.getElementById("estimated_cost").innerText = "Geschatte kosten: €" + estimatedCost.toFixed(2);
            console.log(estimatedCost);
        }
    };
    xhttp.open("GET", "stats.php?location=" + location, true);
    xhttp.send();
}
    
    const json_sector_UID = <?php echo $json_sector_UID; ?>;
    </script>
    <script>
        function submitYearForm() {
            document.getElementById("filter_year_form").submit();
        }

        function submitStatsForm() {
            document.getElementById("statsFilter").submit();
        }

        function submitSectorStatsForm() {
            document.getElementById("sectorStastFilter").submit();
        }

        document.addEventListener("DOMContentLoaded", function () {
            const prevBtn = document.getElementById("prevBtn");
            const nextBtn = document.getElementById("nextBtn");
            const elementsContainer = document.querySelector(".tegels");
            let currentPosition = 0;

            function scrollLeft() {
                const elementsContainer = document.querySelector(".tegels");
                const currentPosition = elementsContainer.scrollLeft;
                const newPosition = currentPosition - 200;
                elementsContainer.scrollTo({
                    left: newPosition,
                    behavior: 'smooth'
                });
            }

            function scrollRight() {
                const elementsContainer = document.querySelector(".tegels");
                const currentPosition = elementsContainer.scrollLeft;
                const newPosition = currentPosition + 200;
                elementsContainer.scrollTo({
                    left: newPosition,
                    behavior: 'smooth'
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
                if (increaseText.includes('-')) {
                    item.parentElement.classList.add('red');
                } else if (increaseText === '0%') {
                    item.parentElement.firstElementChild.style.display = 'none';
                    item.parentElement.classList.add('green');
                }
                else {
                    item.parentElement.classList.add('green');
                    item.parentElement.firstElementChild.classList.remove('fa-arrow-down');
                    item.parentElement.firstElementChild.classList.add('fa-arrow-up');
                }
            });
        };

        /* ---- eerste grafiek, vergelijken met gemiddelde ondernemer binnen sector ---- */
        google.charts.load('current', { 'packages': ['corechart'] });
        google.charts.setOnLoadCallback(drawChart);
        
        var jsonData = <?php echo $json_data; ?>; // Haal de JSON-data op die door PHP is gegenereerd
        function drawChart() {
            var options = {
                curveType: 'function',
                legend: { position: 'bottom' },
                // colors: ['red', 'blue', 'green'],
                series: {
                            0: {color: 'blue', lineWidth: 1},
                            1: {color: 'red', lineWidth: 1},
                            2: {lineDashStyle: [8, 8], color : 'green', lineWidth: 4},
                        },
            };
            var data = google.visualization.arrayToDataTable(jsonData);
            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

            chart.draw(data, options);
        }

        /* ---- 2de grafiek, vergelijken op sector ---- */
        const xyValues = <?php echo $jsSectorValuesJSON; ?>;

        const modifiedXYValues = xyValues.map(value => ({
            backgroundColor: value.backgroundColor,
            label: value.label,
            data: [{ x: value.x, y: value.y, r: value.pointRadius / 2 }]
        }));

        new Chart("myChart", {
            type: "bubble",
            data: {
                datasets: modifiedXYValues
            },
            options: {
                legend: { display: true },
                scales: {
                    xAxes: [{ ticks: { min: 0, max: <?php echo round($highestXValue * 1.5) ?> } }],
                    yAxes: [{ ticks: { min: 0, max: <?php echo round($highestYValue * 1.5) ?> } }]
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                            return 'X: ' + tooltipItem.xLabel + ', Y: ' + tooltipItem.yLabel;
                        }
                    }
                }
            }
        });

        document.querySelectorAll(".question").forEach(function(question) {
            question.addEventListener("click", function(e) {
                var angleDownIcon = question.querySelector(".fa-angle-down");
                var form = question.nextElementSibling; // Assuming the form is the next sibling of the question element

                if (form.style.display === "flex") {
                    form.style.display = "none";
                    angleDownIcon.style.transform = "rotate(0deg)";
                } else {
                    form.style.display = "flex";
                    angleDownIcon.style.transform = "rotate(180deg)";
                }
            });
        });
    </script>

    <script src="javascript/simulator.js"></script>
</body>

</html>