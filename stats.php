<?php
include_once (__DIR__ . "/classes/Db.php");
include_once (__DIR__ . "/classes/User.php");

session_start();
$current_page = 'stats';

if (isset ($_SESSION["user_id"])) {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);

    try {
        $pdo = Db::getInstance();
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
                        <select name="filter" id="filter">
                            <option value="revenue">Omzet</option>
                            <option value="cost">Kosten</option>
                            <option value="profit">Winst</option>
                        </select>
                    </div>
                    <div class="figure">
                        <div class="yAs">
                            <p>1K</p>
                            <p>800</p>
                            <p>600</p>
                            <p>400</p>
                            <p>200</p>
                            <p>0</p>
                            <p style="visibility: hidden">0</p>
                        </div>
                        <div class="figureRight">
                            <div class="graphic"></div>
                            <div class="xAs">
                                <p>Ma</p>
                                <p>Di</p>
                                <p>Woe</p>
                                <p>Do</p>
                                <p>Vr</p>
                                <p>Za</p>
                                <p>Zo</p>
                            </div>
                        </div>
                    </div>
                    <div class=" legenda">
                        <div class="item">
                            <p class="circle blue"></p>
                            <p>Mijn rapport</p>
                        </div>
                        <div class="item">
                            <p class="circle green"></p>
                            <p>Rapport gemiddelde ondernemer binnen mijn sector</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right">
                <div class="customerSatisfactionScore"></div>
            </div>
        </div>
    </div>

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
    </script>
</body>

</html>