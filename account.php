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
    <title>StartupBooster - Account</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/Favicon.svg">
</head>

<body>
    <?php include_once ('inc/nav.inc.php'); ?>
    <div id="account">
        <div class="info">
            <div class="image">
                <a href="#">
                    <img src="assets/images/Camera.svg" alt="Camera">
                    <p>Omslagfoto bewerken</p>
                </a>
            </div>
            <div class="profilePicture"></div>
            <i class="fa fa-edit"></i>
            <h1>
                <?php echo htmlspecialchars($user["firstname"]) . " " . htmlspecialchars($user["lastname"]); ?>
            </h1>
            <div class="row">
                <div>
                    <img src="assets/images/Building.svg" alt="accountIcon">
                    <p>
                        <?php echo htmlspecialchars($user["function"]); ?>
                    </p>
                </div>
                <div>
                    <img src="assets/images/Location.svg" alt="accountIcon">
                    <p>
                        <?php echo htmlspecialchars($user["city"]); ?>
                    </p>
                </div>
                <div>
                    <img src="assets/images/CalendarBlue.svg" alt="accountIcon">
                    <p>Aangesloten sinds
                        <?php
                        $maanden = array(
                            "januari",
                            "februari",
                            "maart",
                            "april",
                            "mei",
                            "juni",
                            "juli",
                            "augustus",
                            "september",
                            "oktober",
                            "november",
                            "december"
                        );

                        $maandNummer = date("n", strtotime($user["signupDate"]));
                        $jaar = date("Y", strtotime($user["signupDate"]));

                        $maand = $maanden[$maandNummer - 1];

                        echo $maand . " " . $jaar;
                        ?>

                    </p>
                </div>
            </div>
        </div>
        <form class="gegevens" action="account.php" method="post">
            <h2>Mijn gegevens</h2>
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
                    <label for="function">Functie</label>
                    <select name="function" id="function">
                        <option value="Student-zelfstandige" <?php if ($user["function"] == "Student-zelfstandige")
                            echo "selected"; ?>>Student-zelfstandige</option>
                        <option value="Zelfstandige" <?php if ($user["function"] == "Zelfstandige")
                            echo "selected"; ?>>
                            Zelfstandige</option>
                    </select>
                </div>
                <div class="field">
                    <label for="phone">Telefoonnummer</label>
                    <input type="text" name="phone" id="phone" placeholder="+32476 75 67 36"
                        value="<?php echo htmlspecialchars($user["phoneNumber"]); ?>">
                </div>
                <div class="field">
                    <label for="email">E-mail</label>
                    <input type="text" name="email" id="email" placeholder="info@tomjansen.com"
                        value="<?php echo htmlspecialchars($user["email"]); ?>">
                </div>
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
            <button type="submit" class="btn" id="btnSave">Bewaren</button>
        </form>
    </div>
</body>

</html>