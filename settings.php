<?php
include_once (__DIR__ . "/classes/Db.php");
include_once (__DIR__ . "/classes/User.php");
include_once (__DIR__ . "/classes/Statute.php");
include_once (__DIR__ . "/classes/Sector.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'error.log');

session_start();
$current_page = 'settings';

$currentStep = "persoonlijkeGegevens";

$pages = array(
    "persoonlijkeGegevens" => "Persoonlijke gegevens",
    "veiligheid" => "Veiligheid",
    "meldingen" => "Meldingen",
    "account" => "Account"
);

$error;
$success;
if (isset ($_GET['page'])) {
    $currentStep = $_GET['page'];
}

if (isset ($_SESSION["user_id"])) {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);

    try {
        $pdo = Db::getInstance();
        $statutes = Statute::getAll($pdo);
        $sectors = Sector::getAll($pdo);
    } catch (Exception $e) {
        error_log('Database error: ' . $e->getMessage());
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $user = new User();

        if (isset ($_POST["firstname"])) {
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
            $user->setSector($sector);
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

        try {
            if (isset ($_POST["email"])) {
                if (User::getUserByEmail((Db::getInstance()), $_POST["email"])) {
                    throw new Exception("Dit e-mailadres is al in gebruik.");
                } else {
                    $user->setEmail($_POST["email"]);
                    $user->updateEmail($pdo, $_SESSION["user_id"]);
                    $success = "Uw emailadres is succesvol veranderd.";
                }
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }


        if (isset ($_POST["old-password"])) {
            $oldPassword = $_POST["old-password"];
            $user = User::getUserById($pdo, $_SESSION["user_id"]);
            $hashedPassword = $user["password"];
            if (password_verify($oldPassword, $hashedPassword)) {
                $newPassword = $_POST["new-password"];
                $confirmNewPassword = $_POST["confirm-new-password"];
                if ($newPassword === $confirmNewPassword) {
                    try {
                        $user = new User();
                        $user->setPassword($newPassword);
                        $user->updatePassword($pdo, $_SESSION["user_id"]);
                        $success = "Uw wachtwoord is successvol veranderd.";
                    } catch (Exception $e) {
                        $error = $e->getMessage();
                    }
                } else {
                    $error = "Wachtwoorden komen niet overeen.";
                }
            } else {
                $error = "Oud wachtwoord is onjuist.";
            }
        }
    }

    // Ensure that $two_step_verification and $sms_set are initialized before calling updateSecurity
    // if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //     if ($currentStep == "veiligheid") {
    //         $two_step_verification = isset ($_POST["two_step_verification"]) ? (bool) $_POST["two_step_verification"] : false;
    //         $sms_set = isset ($_POST["sms_set"]) ? (bool) $_POST["sms_set"] : false;

    //         if ($two_step_verification || $sms_set) {
    //             if ($user instanceof User && $user->updateSecurity($pdo, $_SESSION["user_id"], $two_step_verification, $sms_set)) {
    //                 $success = true;
    //                 header("Location: settings.php?profileUpdate=success");
    //                 exit();
    //             } else {
    //                 header("Location: settings.php?profileUpdate=error");
    //                 exit();
    //             }
    //         }
    //     }


    // }

    // if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //     if ($currentStep == "EmailWijzigen") {
    //         if (User::getUserByEmail((Db::getInstance()), $_POST["email"])) {
    //             throw new Exception("Dit e-mailadres is al in gebruik.");
    //         } else {
    //             $user->setEmail($_POST["email"]);
    //             var_dump($user->getEmail());
    //         }
    //     }
    // }

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
                <a href='settings.php?page=persoonlijkeGegevens' class='<?php if ($currentStep === "persoonlijkeGegevens") {
                    echo "active";
                } ?>'>Persoonlijke
                    Gegevens</a>
                <a href='settings.php?page=veiligheid' class='<?php if ($currentStep === "veiligheid") {
                    echo "active";
                } ?>'>Veiligheid</a>
                <a href='settings.php?page=meldingen' class='<?php if ($currentStep === "meldingen") {
                    echo "active";
                } ?>'>Meldingen</a>
                <a href='settings.php?page=account' class='<?php if ($currentStep === "account" || $currentStep === "EmailWijzigen" || $currentStep === "WachtwoordWijzigen" || $currentStep === "AccountVerwijderen") {
                    echo "active";
                } ?>'>Account</a>
            </div>
            <p class="border"></p>
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
                                echo htmlspecialchars(Statute::getStatuteByUser($pdo, $_SESSION["user_id"], $user["statute_id"])["title"]);
                                ?>

                            </p>
                            <p>
                                <?php echo htmlspecialchars($user["city"]); ?>
                            </p>
                        </div>
                    </div>
                    <form action="" method="post">
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
                                    <input type="text" name="phone" id="phone" placeholder="+32476 75 67 36" value="<?php if ($user["phoneNumber"] != null) {
                                        echo htmlspecialchars($user["phoneNumber"]);
                                    } ?>">
                                </div>
                                <div class="field">
                                    <label for="statute">Statuut</label>
                                    <select name="statute" id="statute">
                                        <?php foreach ($statutes as $statute): ?>
                                            <option value="<?php echo $statute["id"] ?>" <?php echo ($user["statute_id"] == $statute["id"]) ? 'selected' : '' ?>>
                                                <?php echo htmlspecialchars($statute["title"]) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="field">
                                    <label for="sector">Sector</label>
                                    <select name="sector" id="sector">
                                        <?php foreach ($sectors as $sector): ?>
                                            <option value="<?php echo $sector["id"] ?>" <?php echo ($user["sector_id"] == $sector["id"]) ? 'selected' : '' ?>>
                                                <?php echo htmlspecialchars($sector["title"]) ?>
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
                            <p class="success">Uw gegevens zijn successvol aangepast.</p>
                        <?php endif ?>
                        <button type="submit" class="btn" id="btnSave">Bewaren</button>
                    </form>
                </div>
            <?php endif; ?>
            <?php if ($currentStep == "veiligheid"): ?>
                <div class="option">
                    <h2>Veiligheid</h2>
                    <p class="border"></p>
                    <div class="verification">
                        <h3>Verificatie</h3>
                        <form action="" method="POST">
                            <!-- Other form fields -->
                            <div class="row">
                                <p>Tweestapsverificatie</p>
                                <label for="two_step_verification">
                                    <div class="toggle <?php if (is_array($user) && isset ($user['two_step_verification']) && $user['two_step_verification'] == 1) {
                                        echo "active";
                                    } ?>" id="toggle1">
                                        <div class="toggle-handle"></div>
                                    </div>
                                </label>
                                <input hidden class="input_security_two_step" type="checkbox" name="two_step_verification"
                                    id="two_step_verification" <?php if (is_object($user) && $user->getTwo_step_verification() === true) {
                                        echo "checked";
                                    } ?>>
                            </div>
                            <div class="row">
                                <p>SMS instellen</p>
                                <label for="sms_set">
                                    <div class="toggle <?php if (is_array($user) && isset ($user['sms_set']) && $user['sms_set'] == 1) {
                                        echo "active";
                                    } ?>" id="toggle2">
                                        <div class="toggle-handle"></div>
                                    </div>
                                </label>
                                <input hidden class="input_security_sms" type="checkbox" name="sms_set" id="sms_set" <?php if (is_object($user) && $user->getSms_set() === true) {
                                    echo "checked";
                                } ?>>
                            </div>
                            <button type="submit" class="btn" id="btnSave">Bewaren</button>
                        </form>

                    </div>
                </div>
            <?php endif; ?>

            <?php if ($currentStep == "meldingen"): ?>
                <div class="option">
                    <h2>Meldingen</h2>
                    <p class="border"></p>
                    <div class="notifications">
                        <!-- <form action="" method="POST"> -->
                        <div class="row">
                            <p>Beveiligingswaarschuwingen</p>
                            <div class="toggle" id="toggle3">
                                <div class="toggle-handle"></div>
                            </div>
                        </div>
                        <div class="row">
                            <p>Email meldingen</p>
                            <div class="toggle" id="toggle4">
                                <div class="toggle-handle"></div>
                            </div>
                        </div>
                        <div class="row">
                            <p>SMS-melding</p>
                            <div class="toggle" id="toggle5">
                                <div class="toggle-handle"></div>
                            </div>
                        </div>
                        <div class="row">
                            <p>Waarschuwingen apparaat aanmelden</p>
                            <div class="toggle" id="toggle6">
                                <div class="toggle-handle"></div>
                            </div>
                        </div>
                        <!-- </form> -->
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($currentStep == "account"): ?>
                <div class="option">
                    <h2>Account</h2>
                    <p class="border"></p>
                    <div class="account">
                        <h3>Email wijzigen</h3>
                        <div class="row">
                            <p>Email wijzigen</p>
                            <a href="settings.php?page=EmailWijzigen" class="btn">Email wijzigen</a>
                        </div>
                        <h3>Wachtwoord wijzigen</h3>
                        <div class="row">
                            <p>Wachtwoord wijzigen</p>
                            <a href="settings.php?page=WachtwoordWijzigen" class="btn">Wachtwoord wijzigen</a>
                        </div>
                        <h3>Account verwijderen</h3>
                        <div class="row">
                            <p>Account verwijderen</p>
                            <a href="settings.php?page=AccountVerwijderen" class="btn red">Account verwijderen</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($currentStep == "EmailWijzigen"): ?>
                <div class="option">
                    <h2>E-mailadres wijzigen</h2>
                    <p class="border"></p>
                    <?php if (isset ($success)): ?>
                        <p class="alert success">
                            <?php echo $success ?>
                        </p>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <div class="field">
                            <label for="email">Uw nieuwe e-mailadres</label>
                            <input type="text" id="email" name="email" placeholder="your@email.com" value="<?php $user = User::getUserById($pdo, $_SESSION["user_id"]);
                            ;
                            echo htmlspecialchars($user["email"]); ?>">
                        </div>
                        <button type="submit" class="btn">Bewaren</button>
                    </form>
                    <?php if (isset ($error)): ?>
                        <p class="alert">
                            <?php echo $error ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($currentStep == "WachtwoordWijzigen"): ?>
                <div class="option">
                    <h2>Wachtwoord wijzigen</h2>
                    <p class="border"></p>
                    <?php if (isset ($success)): ?>
                        <p class="alert success">
                            <?php echo $success ?>
                        </p>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <div class="field">
                            <label for="old_password">Uw oud wachtwoord</label>
                            <div class="passwordInput">
                                <div class="row">
                                    <input type="password" id="old-password" name="old-password" placeholder="••••••••">
                                    <i class="fa fa-eye-slash" id="toggle-old-password"
                                        onclick="togglePasswordVisibility('old-password', 'toggle-old-password')"></i>
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label for="old_password">Nieuw wachtwoord</label>
                            <div class="passwordInput">
                                <div class="row">
                                    <input type="password" id="new-password" name="new-password" placeholder="••••••••">
                                    <i class="fa fa-eye-slash" id="toggle-new-password"
                                        onclick="togglePasswordVisibility('new-password', 'toggle-new-password')"></i>
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label for="old_password">Herhaal nieuw wachtwoord</label>
                            <div class="passwordInput">
                                <div class="row">
                                    <input type="password" id="confirm-new-password" name="confirm-new-password"
                                        placeholder="••••••••">
                                    <i class="fa fa-eye-slash" id="toggle-confirm-new-password"
                                        onclick="togglePasswordVisibility('confirm-new-password', 'toggle-confirm-new-password')"></i>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn">Bewaren</button>
                    </form>
                    <?php if (isset ($error)): ?>
                        <p class="alert">
                            <?php echo $error ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggles = document.querySelectorAll('.toggle');

            toggles.forEach(function (toggle) {
                toggle.addEventListener("click", function (e) {
                    this.classList.toggle('active');
                });
            });
        });

        function togglePasswordVisibility(inputId, iconId) {
            var input = document.getElementById(inputId);
            var icon = document.getElementById(iconId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        }


    </script>
</body>

</html>