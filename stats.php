<?php
include_once (__DIR__ . "/classes/Db.php");

try {
    $pdo = Db::getInstance();

} catch (Exception $e) {
    error_log('Database error: ' . $e->getMessage());
}

$current_page = 'statistieken';
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

<body></body>

</html>