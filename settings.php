<?php
include_once (__DIR__ . "/classes/Db.php");
include_once (__DIR__ . "/classes/User.php");

session_start();
$current_page = 'account';

if (isset ($_SESSION["user_id"])) {

    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $user = new User();

        $firstName = filter_input(INPUT_POST, 'firstname');
        $lastName = filter_input(INPUT_POST, 'lastname');
        $function = filter_input(INPUT_POST, 'function');
        $email = filter_input(INPUT_POST, 'email');
        $phone = filter_input(INPUT_POST, 'phone');
        $street = filter_input(INPUT_POST, 'street');
        $houseNumber = filter_input(INPUT_POST, 'houseNumber');
        $zipCode = filter_input(INPUT_POST, 'zipCode');
        $city = filter_input(INPUT_POST, 'city');

        $user->setFirstname($firstName);
        $user->setLastname($lastName);
        $user->setFunction($function);
        $user->setEmail($email);
        $user->setPhoneNumber($phone);
        $user->setStreet($street);
        $user->setHouseNumber($houseNumber);
        $user->setZipCode($zipCode);
        $user->setCity($city);

        if ($user->updateUser($pdo, $_SESSION["user_id"])) {
            header("Location: account.php?profileUpdate=success");
            exit();
        } else {
            header("Location: account.php?profileUpdate=error");
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
                <a href="#" class="active">Persoonlijke gegevens</a>
                <a href="#">Veiligheid</a>
                <a href="#">Meldingen</a>
                <a href="#">Account</a>
            </div>
            <div class="option">
                <h2>Persoonlijke gegevens</h2>
                <p class="border"></p>
                <div class="info">
                    <div class="profilePicture"></div>
                    <div class="text">
                        <h3>Tom Jansen</h3>
                        <p>Student-zelfstandige</p>
                        <p>Mechelen</p>
                    </div>
                </div>
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
                            <label for="function">Functie</label>
                            <select name="function" id="function">
                                <option value="Student-zelfstandige" <?php if ($user["function"] == "Student-zelfstandige")
                                    echo "selected"; ?>>Student-zelfstandige
                                </option>
                                <option value="Zelfstandige" <?php if ($user["function"] == "Zelfstandige")
                                    echo "selected"; ?>>
                                    Zelfstandige</option>
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
                <a href="#" class="btn">Bewaren</a>
            </div>
        </div>
    </div>
</body>

</html>