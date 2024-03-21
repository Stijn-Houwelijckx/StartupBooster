<?php
include_once (__DIR__ . "/classes/Task.php");
include_once (__DIR__ . "/classes/Db.php");

$current_page = 'roadmap';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StartupBooster - roadmap</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <?php include_once ('inc/nav.inc.php'); ?>
    <div id="roadmap">
        <h1>Stappenplan</h1>
        <div class="tasks">
            <div class="task"></div>
        </div>
    </div>
</body>

</html>