<?php
include_once (__DIR__ . "../../classes/Db.php");
include_once (__DIR__ . "../../classes/User.php");
session_start();

$pdo = Db::getInstance();

if (isset($_SESSION["user_id"])) {
    $user = User::getUserById($pdo, $_SESSION["user_id"]);
    $current_page = 'users';
} else {
    header("Location: login.php?error=notLoggedIn");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

}

// $users = User::getAll($pdo);
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
    <div id="users">
        <h1>Gebruikers</h1>
    </div>
</body>

</html>