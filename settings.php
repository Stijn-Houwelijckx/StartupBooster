<?php
include_once (__DIR__ . "/classes/Db.php");
include_once (__DIR__ . "/classes/User.php");
include_once (__DIR__ . "/classes/Statute.php");
include_once (__DIR__ . "/classes/Sector.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'error.log');

session_start();
$current_page = 'account';

$currentStep = "persoonlijkeGegevens";
$error;

if (isset ($_GET['page'])) {
    $currentStep = $_GET['page'];
}



$success = false; // Initialize $success variable

if (isset ($_SESSION["user_id"])) {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);
    // var_dump(Statute::getStatuteByUser($pdo, $_SESSION["user_id"], 2));

    try {
        $pdo = Db::getInstance();
        $statutes = Statute::getAll($pdo);
        $sectors = Sector::getAll($pdo);
    } catch (Exception $e) {
        error_log('Database error: ' . $e->getMessage());
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $user = new User();

        $firstName = filter_input(INPUT_POST, 'firstname');
        $lastName = filter_input(INPUT_POST, 'lastname');
        $statute = filter_input(INPUT_POST, 'statute');
        $sector = filter_input(INPUT_POST, 'sector');
        $phone = filter_input(INPUT_POST, 'phone');
        $street = filter_input(INPUT_POST, 'street');
        $houseNumber = filter_input(INPUT_POST, 'houseNumber');
        $zipCode = filter_input(INPUT_POST, 'zipCode');
        $city = filter_input(INPUT_POST, 'city');

        $user->setFirstname($firstName);
        $user->setLastname($lastName);
        $user->setStatute($statute);
        $user->setStatute($sector);
        $user->setPhoneNumber($phone);
        $user->setStreet($street);
        $user->setHouseNumber($houseNumber);
        $user->setZipCode($zipCode);
        $user->setCity($city);

        if ($user->updateUser($pdo, $_SESSION["user_id"])) {
            $success = true;
            header("Location: settings.php?profileUpdate=success");
            exit();
        } else {
            header("Location: settings.php?profileUpdate=error");
            exit();
        }
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
    <title>StartupBooster - instellingen</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/Favicon.svg">
</head>

<body>
    <?php include_once ('inc/nav.inc.php'); ?>
    <div id="settings">
        <h1>Instellingen</h1>
        <div class="elements">
            <div class="navigation">
                <a href='settings.php?page=persoonlijkeGegevens' class="active">Persoonlijke gegevens</a>
                <a href='settings.php?page=veiligheid'>Veiligheid</a>
                <a href='settings.php?page=meldingen'>Meldingen</a>
                <a href='settings.php?page=account'>Account</a>
            </div>
            <?php if ($currentStep == "persoonlijkeGegevens"): ?>
                <div class="option">
                    <h2>Persoonlijke gegevens</h2>
                    <p class="border"></p>
                    <div class="info">
                        <div class="profilePicture"></div>
                        <div class="text">
                            <h3>
                                <?php echo htmlspecialchars($user["firstname"]) . " " . htmlspecialchars($user["lastname"]); ?>
                            </h3>
                            <p>
                                <?php
                                // var_dump($user->getStatute($_SESSION["user_id"]));
                                // var_dump(Statute::getStatuteByUser($pdo, $_SESSION["user_id"], 2));
                                echo Statute::getStatuteByUser($pdo, $_SESSION["user_id"], 2)["title"];
                                // echo Statute::getStatuteByUser($pdo, $_SESSION["user_id"], $user->getStatute())["title"];
                            
                                ?>

                            </p>
                            <p>
                                <?php echo htmlspecialchars($user["city"]); ?>
                            </p>
                        </div>
                    </div>
                    <form action="settings.php" method="post">
                        <div class="extraInfo">
                            <h3>Persoonlijke gegevens</h3>
                            <div class="fields">
                                <div class="field">
                                    <label for="firstname">Voornaam</label>
                                    <input type="text" name="firstname" id="firstname" placeholder="Tom"
                                        value="<?php echo htmlspecialchars($user["firstname"]); ?>">
                                </div>
                                <div class="field">
                                    <label for="lastname">Achternaam</label>
                                    <input type="text" name="lastname" id="lastname" placeholder="Jansen"
                                        value="<?php echo htmlspecialchars($user["lastname"]); ?>">
                                </div>
                                <div class="field">
                                    <label for="phone">Telefoonnummer</label>
                                    <input type="text" name="phone" id="phone" placeholder="+32476 75 67 36"
                                        value="<?php echo htmlspecialchars($user["phoneNumber"]); ?>">
                                </div>
                                <div class="field">
                                    <label for="statute">Statuut</label>
                                    <select name="statute" id="statute">
                                        <?php foreach ($statutes as $statute): ?>
                                            <option value="<?php echo $statute["id"] ?>">
                                                <?php echo $statute["title"] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="field">
                                    <label for="sector">Sector</label>
                                    <select name="sector" id="sector">
                                        <?php foreach ($sectors as $sector): ?>
                                            <option value="<?php echo $sector["id"] ?>">
                                                <?php echo $sector["title"] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="extraInfo">
                            <h3>Adres</h3>
                            <div class="fields">
                                <div class="field">
                                    <label for="street">Straat</label>
                                    <input type="text" name="street" id="street" placeholder="Grote markt"
                                        value="<?php echo htmlspecialchars($user["street"]); ?>">
                                </div>
                                <div class="field">
                                    <label for="houseNumber">Huisnr.</label>
                                    <input type="text" name="houseNumber" id="houseNumber" placeholder="1"
                                        value="<?php echo htmlspecialchars($user["houseNumber"]); ?>">
                                </div>
                                <div class="field">
                                    <label for="zipCode">Postcode.</label>
                                    <input type="text" name="zipCode" id="zipCode" placeholder="2800"
                                        value="<?php echo htmlspecialchars($user["zipCode"]); ?>">
                                </div>
                                <div class="field">
                                    <label for="city">Stad</label>
                                    <input type="text" name="city" id="city" placeholder="Mechelen"
                                        value="<?php echo htmlspecialchars($user["city"]); ?>">
                                </div>
                            </div>
                        </div>
                        <?php if ($success): ?>
                            <p class="success">Uw gegevens zijn succesvol aangepast.</p>
                        <?php endif ?>
                        <button type="submit" class="btn" id="btnSave">Bewaren</button>
                    </form>
                </div>
            <?php endif; ?>
            <?php if ($currentStep == "veiligheid"): ?>

            <?php endif; ?>

            <?php if ($currentStep == "meldingen"): ?>
            <?php endif; ?>

            <?php if ($currentStep == "account"): ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>