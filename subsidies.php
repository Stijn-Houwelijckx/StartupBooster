<?php
include_once (__DIR__ . "/classes/Subsidie.php");
include_once (__DIR__ . "/classes/Db.php");

try {
    $pdo = Db::getInstance();
    $subsidies = Subsidie::getSubsidies($pdo);
} catch (Exception $e) {
    error_log('Database error: ' . $e->getMessage());
    $subsidies = [];
}

$current_page = 'subsidies';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StartupBooster - helpdesk</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/Favicon.svg">
</head>

<body>
    <?php include_once ('inc/nav.inc.php'); ?>
    <div id="subsidies">
        <h1>Subsidies</h1>
        <div class="subsidies">
            <?php if (!empty ($subsidies)): ?>
                <?php foreach ($subsidies as $subsidie): ?>
                    <a href="subsidieDetails.php?name=<?php echo urlencode($subsidie["name"]); ?>">
                        <div class="subsidie">
                            <div class="image"
                                style="background-image: url('./assets/images/subsidies/tegel<?php echo htmlspecialchars($subsidie["id"]); ?>.jpg')">
                            </div>
                            <div class="text">
                                <h3>
                                    <?php echo htmlspecialchars($subsidie["name"]); ?>
                                </h3>
                                <p>Bekijk details</p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No subsidies found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>