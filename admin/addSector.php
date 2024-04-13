<?php
include_once (__DIR__ . "/../classes/Db.php");
include_once (__DIR__ . "/../classes/User.php");
include_once (__DIR__ . "/../classes/Sector.php");
session_start();
$sector = new Sector();

$pdo = Db::getInstance();
$user = User::getUserById($pdo, $_SESSION["user_id"]);

if (isset($_SESSION["user_id"]) && $user["isAdmin"] == "on") {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["title"])) {
            try {
                $pdo = Db::getInstance();
                $sector->setTitle($_POST["name"]);
                Sector::addSector($pdo, $_POST["title"]);
                header("Location: stats.php");
                exit;
            } catch (PDOException $e) {
                error_log('Database error: ' . $e->getMessage());
            }
        }
    }
    $current_page = 'sectors';
} else {
    header("Location: ../login.php?error=notLoggedIn");
    exit();
}

$sectors = Sector::getAll($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StartupBooster - helpdesk</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/Favicon.svg">
</head>

<body>
    <?php include_once ('../inc/navAdmin.inc.php'); ?>
    <div id="addSector">
        <h2>Voeg een sector toe</h2>
        <form action="" method="POST">
            <div class="row">
                <div class="column">
                    <label for="title">Naam van sector</label>
                    <input type="text" name="title" id="title">
                </div>
            </div>
            <button type="submit" class="btn">Toevoegen</button>
        </form>
    </div>
</body>

</html>