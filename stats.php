<?php
include_once (__DIR__ . "/classes/Db.php");
$current_page = 'stats';

if (isset ($_SESSION["user_id"])) {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);

    try {
        $pdo = Db::getInstance();
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
    <title>StartupBooster - statistieken</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/Favicon.svg">
</head>

<body>
    <?php include_once ('inc/nav.inc.php'); ?>
    <div id="stats">
        <h1>Statistieken</h1>
    </div>
</body>

</html>