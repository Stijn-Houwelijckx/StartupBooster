<?php
include_once (__DIR__ . "/../classes/Db.php");
include_once (__DIR__ . "/../classes/User.php");
include_once (__DIR__ . "/../classes/Statute.php");
include_once (__DIR__ . "/../classes/Sector.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'error.log');

session_start();
$current_page = 'settings';

$currentStep = "persoonlijkeGegevens";

$pages = array(
    "persoonlijkeGegevens" => "Persoonlijke gegevens",
    "account" => "Account"
);

$error;
$success;

if (isset($_GET['page'])) {
    $currentStep = $_GET['page'];
}

if (isset($_SESSION["user_id"])) {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);

    if ($user["isAdmin"] == "on") {
        $userIsAdmin = "on";
    } else {
        $userIsAdmin = "off";
    }

    try {
        $pdo = Db::getInstance();
        $statutes = Statute::getAll($pdo);
        $sectors = Sector::getAll($pdo);
    } catch (Exception $e) {
        error_log('Database error: ' . $e->getMessage());
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $user = new User();

        try {
            if (isset($_POST["firstname"])) {
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

                $user->setisAdmin($userIsAdmin);

                $user->setStatute($statute);
                $user->setSector($sector);
                $user->setPhoneNumber($phone);
                $user->setStreet($street);
                $user->setHouseNumber($houseNumber);
                $user->setZipCode($zipCode);
                $user->setCity($city);

                $user->updateUser($pdo, $_SESSION["user_id"]);
                $success = "Uw gegevens zijn successvol aangepast.";
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        try {
            if (isset($_POST["email"])) {
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


        if (isset($_POST["old-password"])) {
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

        if (isset($_POST["password"])) {
            $password = $_POST["password"];
            $user = User::getUserById($pdo, $_SESSION["user_id"]);
            $hashedPassword = $user["password"];
            if (password_verify($password, $hashedPassword)) {

                if ($newPassword === $confirmNewPassword) {
                    try {
                        User::deleteUser($pdo, $_SESSION["user_id"]);
                        session_unset();
                        session_destroy();
                        header("Location: login.php?delete=success");
                        exit();
                    } catch (Exception $e) {
                        $error = $e->getMessage();
                    }
                } else {
                    $error = "Error";
                }
            } else {
                $error = "Wachtwoord is onjuist.";
            }
        }

        if (isset($_FILES["profileImg"])) {
            $user = new User();

            $file = $_FILES["profileImg"];

            // Set rule variables
            $maxFileSize = 10 * 1000 * 1000; // 10MB
            $allowedExt = array("jpg", "jpeg", "png");

            $user->setProfileImg($file, $allowedExt, $maxFileSize);

            if ($user->updateProfileImg($pdo, $_SESSION["user_id"])) {
                header("Location: settings.php?profileImgUpload=success");
                exit();
            } else {
                header("Location: settings.php?profileImgUpload=error");
                exit();
            }
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
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="../assets/images/Favicon.svg">
</head>

<body>
    <?php include_once ('../inc/navAdmin.inc.php'); ?>
    <div id="settings">
        <h1>Instellingen</h1>
        <div class="elements">
            <div class="navigation">
                <a href='settings.php?page=persoonlijkeGegevens' class='<?php if ($currentStep === "persoonlijkeGegevens") {
                    echo "active";
                } ?>'>Persoonlijke
                    Gegevens</a>
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
                        <div class="profilePicture"
                            style="background-image: url('../<?php $user = User::getUserById($pdo, $_SESSION["user_id"]); echo $user["profileImg"] !== null ? $user["profileImg"] : "../assets/images/Tom.jpg"; ?>');">
                        </div>
                        <form id="profileImgForm" action="" method="post" enctype="multipart/form-data">
                            <label for="profileImg" id="profileImgInput">
                                <i class="fa fa-edit"></i>
                            </label>
                            <input type="file" name="profileImg" id="profileImg" style="display: none">
                        </form>
                        <div class="text">
                            <h3>
                                <?php $user = User::getUserById($pdo, $_SESSION["user_id"]);
                                echo htmlspecialchars($user["firstname"]) . " " . htmlspecialchars($user["lastname"]); ?>
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
                    <?php if (isset($success)): ?>
                        <p class="alert success">
                            <?php echo $success ?>
                        </p>
                    <?php endif; ?>
                    <form action="" method="post" class="elements">
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
                        <button type="submit" class="btn" id="btnSaveInfo">Bewaren</button>
                    </form>
                    <?php if (isset($error)): ?>
                        <p class="alert">
                            <?php echo $error ?>
                        </p>
                    <?php endif; ?>
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
                    <?php if (isset($success)): ?>
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
                    <?php if (isset($error)): ?>
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
                    <?php if (isset($success)): ?>
                        <p class="alert success">
                            <?php echo $success ?>
                        </p>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <div class="field">
                            <label for="old-password">Uw oud wachtwoord</label>
                            <div class="passwordInput">
                                <div class="row">
                                    <input type="password" id="old-password" name="old-password" placeholder="••••••••">
                                    <i class="fa fa-eye-slash" id="toggle-old-password"
                                        onclick="togglePasswordVisibility('old-password', 'toggle-old-password')"></i>
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label for="new-password">Nieuw wachtwoord</label>
                            <div class="passwordInput">
                                <div class="row">
                                    <input type="password" id="new-password" name="new-password" placeholder="••••••••">
                                    <i class="fa fa-eye-slash" id="toggle-new-password"
                                        onclick="togglePasswordVisibility('new-password', 'toggle-new-password')"></i>
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label for="confirm-new-password">Herhaal nieuw wachtwoord</label>
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
                    <?php if (isset($error)): ?>
                        <p class="alert">
                            <?php echo $error ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($currentStep == "AccountVerwijderen"): ?>
                <div class="option">
                    <h2>Account verwijderen</h2>
                    <p class="border"></p>
                    <?php if (isset($success)): ?>
                        <p class="alert success">
                            <?php echo $success ?>
                        </p>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <div class="field">
                            <label for="password">Uw wachtwoord</label>
                            <div class="passwordInput">
                                <div class="row">
                                    <input type="password" id="password" name="password" placeholder="••••••••">
                                    <i class="fa fa-eye-slash" id="toggle-password"
                                        onclick="togglePasswordVisibility('password', 'toggle-password')"></i>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn red">Verwijderen</button>
                    </form>
                    <?php if (isset($error)): ?>
                        <p class="alert">
                            <?php echo $error ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        function toggleCheckbox(id) {
            var checkboxes = document.querySelectorAll('.' + id);

            // Iterate through all checkboxes with the given class
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = !checkbox.checked;
            });
        }

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

        // Function to submit the profileImg form when a file is selected
        document.getElementById('profileImg').addEventListener('change', function () {
            document.getElementById('profileImgForm').submit();
        });
    </script>
</body>

</html>