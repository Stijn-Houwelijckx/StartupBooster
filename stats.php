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
    $users = User::getAllUser($pdo);
    $user = User::getUserById($pdo, $_SESSION["user_id"]);
    try {
        $pdo = Db::getInstance();
        $userYears = Stat::getUserYears($pdo, $_SESSION["user_id"]);
        if (!empty($userYears)) {
            $years = array_column($userYears, 'year');
            $lowestYear = min($years);
            $highestYear = max($years);
            $allStats = Stat::getAllStats($pdo, $lowestYear, $highestYear, $user['statute_id'], $user['sector_id']);
            $wantedStat = "revenue";
            $wantedStatCalc = "median";
            $wantedYear = 2023;

            $sectors = Sector::getAll($pdo);
            $cleanedData = [];

            foreach ($sectors as $sector) {
                $userCount = Sector::getUserCountBySectorId($pdo, $sector['id']);
                $cleanedSector = [];

                foreach ($sector as $key => $value) {
                    if (is_string($value)) {
                        $cleanedValue = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                        $cleanedSector[$key] = $cleanedValue;
                    } else {
                        $cleanedSector[$key] = $value;
                    }
                }

                $cleanedSector['pointRadius'] = $userCount;
                $red = rand(0, 255);
                $green = rand(0, 255);
                $blue = rand(0, 255);
                $alpha = 0.6;
                $cleanedSector['backgroundColor'] = "rgba($red, $green, $blue, $alpha)";
                $cleanedSector['label'] = $cleanedSector['title'];
                $cleanedSector['x'] = rand(100, 1000);
                $cleanedSector['y'] = rand(100, 1000);

                $cleanedData[] = $cleanedSector;
            }

            $jsSectorValuesJSON = json_encode($cleanedData);

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

            if (isset($previousYearStats[0]["year"]) && $previousYearStats[0]["year"] > $lowestYear) {
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

            function calculateAverage($values)
            {
                $sum = array_sum($values);
                $count = count($values);
                $average = $sum / $count;
                return $average;
            }

            $filteredStats = [];
            foreach ($allStats as $stat) {
                if (in_array($stat['year'], $years)) {
                    $filteredStats[] = $stat;
                }
            }

            $calculatedStats = [];
            foreach ($filteredStats as $stat) {
                $year = $stat['year'];
                $revenues = [];
                foreach ($filteredStats as $filteredStat) {
                    if ($filteredStat['year'] == $year) {
                        $revenues[] = $filteredStat[$wantedStat];
                    }
                }

                if ($wantedStatCalc == "median") {
                    $median = calculateMedian($revenues);
                } else {
                    $median = calculateAverage($revenues);
                }

                $calculatedStats[$year] = intval($median);
            }

            $i = 1;
            foreach ($allStats as $key => $value) {
                if ($value['user_id'] == $_SESSION["user_id"]) {
                    $data[$i][0] = strval($value['year']);
                    $data[$i][1] = intval($value[$wantedStat]);
                    $data[$i][2] = intval($calculatedStats[$value['year']]);
                    $i++;
                }
            }

            $json_data = json_encode($data);
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
                                        <option value="<?php echo $year; ?>" <?php if ($year == $wantedYear) {
                                               echo "selected";
                                           } ?>>
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
                                        <option value="median" <?php if ($wantedStatCalc == "median") {
                                            echo "selected";
                                        } ?>>
                                            Mediaan</option>
                                        <option value="average" <?php if ($wantedStatCalc == "average") {
                                            echo "selected";
                                        } ?>>
                                            Gemiddelde</option>
                                    </select>
                                    <select name="statsFilter">
                                        <option value="revenue" <?php if ($wantedStat == "revenue") {
                                            echo "selected";
                                        } ?>>Omzet
                                        </option>
                                        <option value="costs" <?php if ($wantedStat == "costs") {
                                            echo "selected";
                                        } ?>>Kosten
                                        </option>
                                        <option value="profit_loss" <?php if ($wantedStat == "profit_loss") {
                                            echo "selected";
                                        } ?>>Winst</option>
                                        <option value="personnel" <?php if ($wantedStat == "personnel") {
                                            echo "selected";
                                        } ?>>
                                            Personeel</option>
                                        <option value="equityCapital" <?php if ($wantedStat == "equityCapital") {
                                            echo "selected";
                                        } ?>>Eigen vermogen</option>
                                        <option value="grossMargin" <?php if ($wantedStat == "grossMargin") {
                                            echo "selected";
                                        } ?>>Bruto marge</option>
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


    <?php endif; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        function submitYearForm() {
            document.getElementById("filter_year_form").submit();
        }

        function submitStatsForm() {
            document.getElementById("statsFilter").submit();
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
        const xyValues = <?php echo $jsSectorValuesJSON; ?>;

        const modifiedXYValues = xyValues.map(value => ({
            pointRadius: value.pointRadius,
            backgroundColor: value.backgroundColor,
            label: value.label,
            data: [{ x: value.x, y: value.y }]
        }));

        new Chart("myChart", {
            type: "scatter",
            data: {
                datasets: modifiedXYValues
            },
            options: {
                legend: { display: true },
                scales: {
                    xAxes: [{ ticks: { min: 0, max: 1000 } }],
                    yAxes: [{ ticks: { min: 0, max: 1000 } }]
                }
            }
        });

    </script>
</body>

</html>