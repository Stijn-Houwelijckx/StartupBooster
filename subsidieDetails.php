<?php
include_once (__DIR__ . "/classes/Subsidie.php");
include_once (__DIR__ . "/classes/Db.php");

$subsidie_name = isset ($_GET['name']) ? $_GET['name'] : '';

if (empty ($subsidie_name)) {
    echo "Ongeldige subsidienaam.";
    exit;
}

try {
    // Verbinding maken met de database
    $pdo = Db::getInstance();

    // Subsidiegegevens ophalen op basis van de naam uit de URL
    $subsidie = Subsidie::getSubsidieByName($pdo, $subsidie_name);

    if (!$subsidie) {
        echo "Subsidie niet gevonden";
        exit;
    }
} catch (Exception $e) {
    // Foutmelding weergeven en loggen als er een databasefout optreedt
    error_log('Database error: ' . $e->getMessage());
    echo "Er is een probleem opgetreden bij het ophalen van de subsidiegegevens. Probeer het later opnieuw.";
    exit;
}

$current_page = 'subsidie_details';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subsidie Details</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include_once ('inc/nav.inc.php'); ?>
    <div id="subsidie-details">
        <h1>Subsidie Details</h1>
        <div class="text">
            <div class="description">
                <h2>Beschrijving van subsidie -
                    <?php echo htmlspecialchars($subsidie['name']); ?>
                </h2>
                <p>
                    <?php echo htmlspecialchars($subsidie['description']); ?>
                </p>
                <a href="<?php echo htmlspecialchars($subsidie['link']); ?>" target="_blank" class="btn">
                    Aanvragen
                </a>
            </div>
            <div class="summary">
                <h2>Samengevat </h2>
                <h3>Voor wie?</h3>
                <p>
                    <?php echo htmlspecialchars($subsidie['who']); ?>
                </p>
                <h3>Voor wat?</h3>
                <p>
                    <?php echo htmlspecialchars($subsidie['what']); ?>
                </p>
                <h3>Bedrag</h3>
                <p>
                    <?php echo htmlspecialchars($subsidie['amount']); ?>
                </p>
            </div>
        </div>
        <div class="image"
            style="background-image: url('./assets/images/subsidies/tegel<?php echo htmlspecialchars($subsidie["id"]); ?>.jpg')">
            ></div>
    </div>
</body>

</html>