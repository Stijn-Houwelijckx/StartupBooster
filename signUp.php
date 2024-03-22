<?php
session_start();

include_once (__DIR__ . "/classes/User.php");
include_once (__DIR__ . "/classes/Db.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$currentStep = 1;
$error;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = new User();
    if (isset ($_POST['current_step'])) {
        $currentStep = $_POST['current_step'];
    }

    if ($currentStep == 1) {
        $_SESSION['firstname'] = filter_input(INPUT_POST, 'firstname');
        $_SESSION['lastname'] = filter_input(INPUT_POST, 'lastname');
        $_SESSION['email'] = filter_input(INPUT_POST, 'email');
        $_SESSION['nationalRegistryNumber'] = filter_input(INPUT_POST, 'nationalRegistryNumber');

        if ($_SESSION['firstname'] && $_SESSION['lastname'] && $_SESSION['email'] && $_SESSION['nationalRegistryNumber']) {
            $currentStep = 2;
        } else {
            $error = "Vul alle velden in.";
        }
    } elseif ($currentStep == 2) {
        $_SESSION['street'] = filter_input(INPUT_POST, 'street');
        $_SESSION['houseNumber'] = filter_input(INPUT_POST, 'houseNumber');
        $_SESSION['zipCode'] = filter_input(INPUT_POST, 'zipCode');
        $_SESSION['city'] = filter_input(INPUT_POST, 'city');

        if ($_SESSION['street'] && $_SESSION['houseNumber'] && $_SESSION['zipCode'] && $_SESSION['city']) {
            $currentStep = 3;
        } else {
            $error = "Vul alle velden in.";
        }
    } elseif ($currentStep == 3) {
        $password = filter_input(INPUT_POST, 'password');
        $password2 = filter_input(INPUT_POST, 'password2');
        if (!empty ($password) && !empty ($password2) && $password === $password2) {
            $user->setFirstname($_SESSION['firstname']);
            $user->setLastname($_SESSION['lastname']);
            $user->setEmail($_SESSION['email']);
            $user->setNationalRegistryNumber($_SESSION['nationalRegistryNumber']);
            $user->setStreet($_SESSION['street']);
            $user->setHouseNumber($_SESSION['houseNumber']);
            $user->setZipCode($_SESSION['zipCode']);
            $user->setCity($_SESSION['city']);
            $user->setPassword($password);

            $pdo = Db::getInstance();

            if ($user->addUser($pdo)) {
                $worked = "Registratie succesvol!";
                $user = User::getUserByEmail($pdo, $_SESSION["email"]);
                $_SESSION['user_id'] = $user["id"];
                header("Location: dashboard.php");
            } else {
                $error = "Er is iets misgegaan, probeer opnieuw.";
            }
        } else {
            $error = "Wachtwoorden komen niet overeen of zijn leeg.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>StartupBooster - Registreren</title>
    <link rel="stylesheet" href="https://use.typekit.net/kqy0ynu.css" />
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>" />
    <link rel="icon" type="image/x-icon" href="assets/images/Favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div id="signUp">
        <div class="text">
            <h1>Registreren</h1>
            <p>We hebben alleen wat meer informatie nodig om je account aan te maken.</p>
            <div class="partners">
                <img src="assets/images/Itsme.svg" alt="Itsme">
            </div>
            <div class="row">
                <p class="border"></p>
                <p>of</p>
                <p class="border"></p>
            </div>

            <?php if (isset ($error)): ?>
                <p class="alert">
                    <?php echo $error ?>
                </p>
            <?php endif; ?>

            <?php if (isset ($worked)): ?>
                <p class="alert success">
                    <?php echo $worked ?>
                </p>
            <?php endif; ?>

            <div class="step" id="step1" <?php echo $currentStep !== 1 ? 'style="display: none;"' : ''; ?>>
                <form class="form form--login" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="current_step" value="1">
                    <div class="row bar">
                        <a class="number active">1</a>
                        <p class="border"></p>
                        <a class="number">2</a>
                        <p class="border"></p>
                        <a class="number">3</a>
                    </div>
                    <div class="row">
                        <div>
                            <label for="firstname">Voornaam</label>
                            <input type="text" id="firstname" name="firstname" placeholder="John">
                        </div>

                        <div>
                            <label for="lastname">Achternaam</label>
                            <input type="text" id="lastname" name="lastname" placeholder="Doe">
                        </div>
                    </div>
                    <div class="row">

                        <div>
                            <label for="email">E-mail</label>
                            <input type="text" id="email" name="email" placeholder="JohnDoe@gmail.com">
                        </div>

                        <div>
                            <label for="nationalRegistryNumber">Rijksregisternummer</label>
                            <input type="text" id="nationalRegistryNumber" name="nationalRegistryNumber"
                                placeholder="00.00.00-000.00">
                        </div>
                    </div>

                    <button type="submit" class="btn" id="btnsignup">Ga verder</button>
                </form>
            </div>

            <div class="step" id="step2" <?php echo $currentStep !== 2 ? 'style="display: none;"' : ''; ?>>
                <form class="form form--login" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="current_step" value="2">
                    <div class="row bar">
                        <a class="number">1</a>
                        <p class="border"></p>
                        <a class="number active">2</a>
                        <p class="border"></p>
                        <a class="number">3</a>
                    </div>
                    <div class="row">
                        <div>
                            <label for="street">Straat</label>
                            <input type="text" id="street" name="street" placeholder="Grote markt">
                        </div>

                        <div>
                            <label for="houseNumber">Huisnr.</label>
                            <input type="text" id="houseNumber" name="houseNumber" placeholder="1">
                        </div>
                    </div>
                    <div class="row">
                        <div>
                            <label for="zipCode">Postcode</label>
                            <input type="text" id="zipCode" name="zipCode" placeholder="2800">
                        </div>
                        <div>
                            <label for="city">Stad</label>
                            <input type="text" id="city" name="city" placeholder="Mechelen">
                        </div>
                    </div>
                    <button type="submit" class="btn" id="btnsignup">Ga verder</button>
                </form>
            </div>

            <div class="step" id="step3" <?php echo $currentStep !== 3 ? 'style="display: none;"' : ''; ?>>
                <form class="form form--login" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="current_step" value="3">
                    <div class="row bar">
                        <a class="number">1</a>
                        <p class="border"></p>
                        <a class="number">2</a>
                        <p class="border"></p>
                        <a class="number active">3</a>
                    </div>
                    <div class="column passwordInput">
                        <label for="password">Wachtwoord</label>
                        <div class="row">
                            <input type="password" id="password" name="password" placeholder="Wachtwoord">
                            <i class="fa fa-eye-slash"></i>
                        </div>
                    </div>
                    <div class="column passwordInput">
                        <label for="password2">Bevestig wachtwoord</label>
                        <div class="row">
                            <input type="password" id="password2" name="password2" placeholder="Herhaal wachtwoord">
                            <i class="fa fa-eye-slash"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn" id="btnsignup">Ga verder</button>
                </form>
            </div>

            <div class="row">
                <p>Heb je al een account?</p>
                <a href="login.php" class="active">Meld je aan</a>
            </div>
        </div>
        <div class="image"></div>
    </div>
    <script>
        var eyeIcons = document.querySelectorAll('.fa-eye-slash');
        eyeIcons.forEach(function (eyeIcon) {
            eyeIcon.addEventListener('click', function () {
                var passwordInput = this.parentElement.querySelector('input[type="password"], input[type="text"]');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                } else {
                    passwordInput.type = 'password';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                }
            });
        });
    </script>

</body>

</html>