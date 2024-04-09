<?php
include_once (__DIR__ . "/classes/Db.php");
include_once (__DIR__ . "/classes/User.php");
include_once (__DIR__ . "/classes/Statute.php");
include_once (__DIR__ . "/classes/Sector.php");
include_once (__DIR__ . "/classes/Message.php");
session_start();
$current_page = 'dashboard';

if (isset($_SESSION["user_id"])) {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);

    try {
        // $pdo = Db::getInstance();
        $statutes = Statute::getAll($pdo);
        $sectors = Sector::getAll($pdo);
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
    <title>StartupBooster</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/Favicon.svg">
</head>

<body>
    <?php include_once ('inc/nav.inc.php'); ?>
    <div id="dashboard">
        <h1>Mijn dashboard</h1>
        <div class="notifications">
            <h2>Meldingen</h2>
            <div class="notification">
                <p>U heeft 3 ongelezen berichten in ‘Chat’.</p>
                <i class="fa fa-angle-right"></i>
            </div>
            <div class="notification error">
                <p>U heeft 1 opmerking bij het aanvragen van uw statuut als zelfstandige.</p>
                <i class="fa fa-angle-right"></i>
            </div>
        </div>
    </div>
    <?php include_once ('inc/chat.inc.php'); ?>
</body>

</html>