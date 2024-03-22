<?php
session_start();

include_once (__DIR__ . "/classes/User.php");
include_once (__DIR__ . "/classes/Db.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'error.log');

$currentStep = -1;
$error;

if(isset($_GET['step'])) {

    $currentStep = intval($_GET['step']);

    if($currentStep < $_GET['step']) {
        header("Location: signUp.php?step=" . $currentStep);
    }

    var_dump("currentStep = " . $currentStep);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST)) {
    $user = new User();

    if(isset($_POST["firstname"])) {
        try {
            $user->setFirstname($_POST["firstname"]);
            $user->setLastname($_POST["lastname"]);
            $user->setEmail($_POST["email"]);
            $user->setNationalRegistryNumber($_POST["nationalRegistryNumber"]);

            $currentStep = 2;
            
            header("Location: signUp.php?step=2");
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
    else if (isset($_POST["street"])) {
        try {
            $user->setStreet($_POST["street"]);
            $user->setHouseNumber($_POST["houseNumber"]);
            $user->setZipCode($_POST["zipCode"]);
            $user->setCity($_POST["city"]);

            $currentStep = 3;

            header("Location: signUp.php?step=3");
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
    else if (isset($_POST["password"])) {
        $password = $_POST["password"];
        $password2 = $_POST["password2"];
        if ($password === $password2) {
            try {
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
                    $user = User::getUserByEmail($pdo, $_SESSION["email"]);
                    $_SESSION['user_id'] = $user["id"];

                    session_unset("firstname");
                    session_unset("lastname");
                    session_unset("email");
                    session_unset("nationalRegistryNumber");
                    session_unset("street");
                    session_unset("houseNumber");
                    session_unset("zipCode");
                    session_unset("city");

                    header("Location: dashboard.php");
                } else {
                    $error = "Er is iets misgegaan, probeer opnieuw.";
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        } else {
            $error = "Wachtwoorden komen niet overeen.";
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

            <div class="step" id="step1">
                <form class="form form--login" method="post" action="">
                    <div class="row bar">
                        <a class="number <?php if($currentStep === 1){echo "active";} ?>">1</a>
                        <p class="border"></p>
                        <a class="number" <?php if($currentStep === 2){echo "active";} ?>>2</a>
                        <p class="border"></p>
                        <a class="number" <?php if($currentStep === 3){echo "active";} ?>>3</a>
                    </div>
                    
                    <!-- Step 1 -->
                    <?php if($currentStep === 1): ?>
                        
                        <div class="row">
                            <div>
                                <label for="firstname">Voornaam</label>
                                <input type="text" id="firstname" name="firstname" placeholder="John" value="<?php if(isset($_SESSION["firstname"])){echo $_SESSION["firstname"];} ?>">
                            </div>

                            <div>
                                <label for="lastname">Achternaam</label>
                                <input type="text" id="lastname" name="lastname" placeholder="Doe" value="<?php if(isset($_SESSION["lastname"])){echo $_SESSION["lastname"];}  ?>">
                            </div>
                        </div>
                        <div class="row">

                            <div>
                                <label for="email">E-mail</label>
                                <input type="text" id="email" name="email" placeholder="JohnDoe@gmail.com" value="<?php if(isset($_SESSION["email"])){echo $_SESSION["email"];} ?>">
                            </div>

                            <div>
                                <label for="nationalRegistryNumber">Rijksregisternummer</label>
                                <input type="text" id="nationalRegistryNumber" name="nationalRegistryNumber" placeholder="00.00.00-000.00" value="<?php if(isset($_SESSION["nationalRegistryNumber"])){echo $_SESSION["nationalRegistryNumber"];} ?>">
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Step 2 -->
                    <?php if($currentStep === 2): ?>
                        <div class="row">
                            <div>
                                <label for="street">Straat</label>
                                <input type="text" id="street" name="street" placeholder="Grote markt" value="<?php if(isset($_SESSION["street"])){echo $_SESSION["street"];} ?>">
                            </div>

                            <div>
                                <label for="houseNumber">Huisnr.</label>
                                <input type="text" id="houseNumber" name="houseNumber" placeholder="1" value="<?php if(isset($_SESSION["houseNumber"])){echo $_SESSION["houseNumber"];} ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div>
                                <label for="zipCode">Postcode</label>
                                <input type="text" id="zipCode" name="zipCode" placeholder="2800" value="<?php if(isset($_SESSION["zipCode"])){echo $_SESSION["zipCode"];} ?>">
                            </div>
                            <div>
                                <label for="city">Stad</label>
                                <input type="text" id="city" name="city" placeholder="Mechelen" value="<?php if(isset($_SESSION["city"])){echo $_SESSION["city"];} ?>">
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Step 3 -->
                    <?php if($currentStep === 3): ?>
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
                    <?php endif; ?>

                    <button type="submit" class="btn">Ga verder</button>
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