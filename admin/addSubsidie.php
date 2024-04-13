<?php
include_once (__DIR__ . "/../classes/Subsidie.php");
include_once (__DIR__ . "/../classes/Db.php");
include_once (__DIR__ . "/../classes/User.php");
session_start();
$subsidie = new Subsidie();

if (isset($_SESSION["user_id"]) && $user["isAdmin"] == "on") {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["name"])) {
            try {
                $pdo = Db::getInstance();
                $subsidie->setName($_POST["name"]);
                $subsidie->setDescription($_POST["description"]);
                $subsidie->setWho($_POST["who"]);
                $subsidie->setWhat($_POST["what"]);
                $subsidie->setAmount($_POST["amount"]);
                $subsidie->setLink($_POST["link"]);
                Subsidie::addSubsidie($pdo, $_POST["name"], $_POST["description"], $_POST["who"], $_POST["what"], $_POST["amount"], $_POST["link"]);
                header("Location: subsidies.php");
                exit;
            } catch (PDOException $e) {
                error_log('Database error: ' . $e->getMessage());
            }
        }
    }
    $current_page = 'subsidies';
} else {
    header("Location: ../login.php?error=notLoggedIn");
    exit();
}

$subsidies = Subsidie::getSubsidies($pdo);

$nameValue = $subsidie->getName();
$whoValue = $subsidie->getWho();
$descriptionValue = $subsidie->getDescription();
$whatValue = $subsidie->getWhat();
$amountValue = $subsidie->getAmount();
$linkValue = $subsidie->getLink();
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
    <div id="addSubsidie">
        <h2>Voeg een subsidie toe</h2>
        <form action="" method="POST">
            <div class="row">
                <div class="column">
                    <label for="name">Naam van subsidie</label>
                    <input type="text" name="name" id="name" placeholder="<?php echo htmlspecialchars($nameValue); ?>">
                </div>
                <div class="column">
                    <label for="who">Voor wie is de subsidie?</label>
                    <input type="text" name="who" id="who" placeholder="<?php echo htmlspecialchars($whoValue); ?>">
                </div>
            </div>
            <div class="row">
                <div class="column">
                    <label for="description">Beschrijving van subsidie</label>
                    <textarea name="description" id="description" cols="30" rows="10"
                        placeholder="<?php echo htmlspecialchars($descriptionValue); ?>"></textarea>
                </div>
                <div class="column">
                    <label for="what">Vat de subsidie kort samen</label>
                    <textarea name="what" id="what" cols="30" rows="10"
                        placeholder="<?php echo htmlspecialchars($whatValue); ?>"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="column">
                    <label for="amount">Hoeveel geld krijg ik met de subsidie?</label>
                    <input type="text" name="amount" id="amount"
                        placeholder="<?php echo htmlspecialchars($amountValue); ?>">
                </div>
                <div class="column">
                    <label for="link">Link naar aanvraag subsidie</label>
                    <input type="text" name="link" id="link" placeholder="<?php echo htmlspecialchars($linkValue); ?>">
                </div>
            </div>
            <button type="submit" class="btn">Toevoegen</button>
        </form>
    </div>
</body>

</html>