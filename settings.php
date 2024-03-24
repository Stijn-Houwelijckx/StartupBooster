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
                <div class="option">
                    <h2>Veiligheid</h2>
                    <p class="border"></p>
                    <div class="verification">
                        <h3>Verificaie</h3>
                        <div class="row">
                            <p>Tweestapsverificatie</p>
                            <svg xmlns="http://www.w3.org/2000/svg" width="57" height="43" viewBox="0 0 57 43" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M41.143 33.9078C39.403 34.0078 37.665 33.9998 35.925 33.9998C35.913 33.9998 27.108 33.9998 27.108 33.9998C25.334 33.9998 23.596 34.0078 21.857 33.9078C20.276 33.8178 18.736 33.6258 17.203 33.1968C13.976 32.2948 11.158 30.4108 9.121 27.7398C7.096 25.0858 6 21.8368 6 18.5008C6 15.1608 7.096 11.9138 9.121 9.25985C11.158 6.58985 13.976 4.70485 17.203 3.80285C18.736 3.37385 20.276 3.18285 21.857 3.09185C23.596 2.99185 25.334 3.00085 27.074 3.00085C27.086 3.00085 35.893 2.99985 35.893 2.99985C37.665 3.00085 39.403 2.99185 41.143 3.09185C42.723 3.18285 44.263 3.37385 45.796 3.80285C49.023 4.70485 51.841 6.58985 53.878 9.25985C55.903 11.9138 57 15.1608 57 18.4998C57 21.8368 55.903 25.0858 53.878 27.7398C51.841 30.4108 49.023 32.2948 45.796 33.1968C44.263 33.6258 42.723 33.8178 41.143 33.9078Z"
                                    fill="#F6F6F6" />
                                <g filter="url(#filter0_dd_597_1525)">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M21.5 5C14.0442 5 8 11.0442 8 18.5C8 25.9558 14.0442 32 21.5 32C28.9558 32 35 25.9558 35 18.5C35 11.0442 28.9558 5 21.5 5Z"
                                        fill="white" />
                                </g>
                                <defs>
                                    <filter id="filter0_dd_597_1525" x="0" y="0" width="43" height="43"
                                        filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="3" />
                                        <feGaussianBlur stdDeviation="0.5" />
                                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.06 0" />
                                        <feBlend mode="normal" in2="BackgroundImageFix"
                                            result="effect1_dropShadow_597_1525" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="3" />
                                        <feGaussianBlur stdDeviation="4" />
                                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.15 0" />
                                        <feBlend mode="normal" in2="effect1_dropShadow_597_1525"
                                            result="effect2_dropShadow_597_1525" />
                                        <feBlend mode="normal" in="SourceGraphic" in2="effect2_dropShadow_597_1525"
                                            result="shape" />
                                    </filter>
                                </defs>
                            </svg>
                        </div>
                        <div class="row">
                            <p>SMS instellen</p>
                            <svg xmlns="http://www.w3.org/2000/svg" width="57" height="43" viewBox="0 0 57 43" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M41.143 33.9078C39.403 34.0078 37.665 33.9998 35.925 33.9998C35.913 33.9998 27.108 33.9998 27.108 33.9998C25.334 33.9998 23.596 34.0078 21.857 33.9078C20.276 33.8178 18.736 33.6258 17.203 33.1968C13.976 32.2948 11.158 30.4108 9.121 27.7398C7.096 25.0858 6 21.8368 6 18.5008C6 15.1608 7.096 11.9138 9.121 9.25985C11.158 6.58985 13.976 4.70485 17.203 3.80285C18.736 3.37385 20.276 3.18285 21.857 3.09185C23.596 2.99185 25.334 3.00085 27.074 3.00085C27.086 3.00085 35.893 2.99985 35.893 2.99985C37.665 3.00085 39.403 2.99185 41.143 3.09185C42.723 3.18285 44.263 3.37385 45.796 3.80285C49.023 4.70485 51.841 6.58985 53.878 9.25985C55.903 11.9138 57 15.1608 57 18.4998C57 21.8368 55.903 25.0858 53.878 27.7398C51.841 30.4108 49.023 32.2948 45.796 33.1968C44.263 33.6258 42.723 33.8178 41.143 33.9078Z"
                                    fill="#F6F6F6" />
                                <g filter="url(#filter0_dd_597_1525)">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M21.5 5C14.0442 5 8 11.0442 8 18.5C8 25.9558 14.0442 32 21.5 32C28.9558 32 35 25.9558 35 18.5C35 11.0442 28.9558 5 21.5 5Z"
                                        fill="white" />
                                </g>
                                <defs>
                                    <filter id="filter0_dd_597_1525" x="0" y="0" width="43" height="43"
                                        filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="3" />
                                        <feGaussianBlur stdDeviation="0.5" />
                                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.06 0" />
                                        <feBlend mode="normal" in2="BackgroundImageFix"
                                            result="effect1_dropShadow_597_1525" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="3" />
                                        <feGaussianBlur stdDeviation="4" />
                                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.15 0" />
                                        <feBlend mode="normal" in2="effect1_dropShadow_597_1525"
                                            result="effect2_dropShadow_597_1525" />
                                        <feBlend mode="normal" in="SourceGraphic" in2="effect2_dropShadow_597_1525"
                                            result="shape" />
                                    </filter>
                                </defs>
                            </svg>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($currentStep == "meldingen"): ?>
                <div class="option">
                    <h2>Meldingen</h2>
                    <p class="border"></p>
                    <div class="notifications">
                        <div class="row">
                            <p>Beveiligingswaarschuwingen</p>
                            <svg xmlns="http://www.w3.org/2000/svg" width="57" height="43" viewBox="0 0 57 43" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M41.143 33.9078C39.403 34.0078 37.665 33.9998 35.925 33.9998C35.913 33.9998 27.108 33.9998 27.108 33.9998C25.334 33.9998 23.596 34.0078 21.857 33.9078C20.276 33.8178 18.736 33.6258 17.203 33.1968C13.976 32.2948 11.158 30.4108 9.121 27.7398C7.096 25.0858 6 21.8368 6 18.5008C6 15.1608 7.096 11.9138 9.121 9.25985C11.158 6.58985 13.976 4.70485 17.203 3.80285C18.736 3.37385 20.276 3.18285 21.857 3.09185C23.596 2.99185 25.334 3.00085 27.074 3.00085C27.086 3.00085 35.893 2.99985 35.893 2.99985C37.665 3.00085 39.403 2.99185 41.143 3.09185C42.723 3.18285 44.263 3.37385 45.796 3.80285C49.023 4.70485 51.841 6.58985 53.878 9.25985C55.903 11.9138 57 15.1608 57 18.4998C57 21.8368 55.903 25.0858 53.878 27.7398C51.841 30.4108 49.023 32.2948 45.796 33.1968C44.263 33.6258 42.723 33.8178 41.143 33.9078Z"
                                    fill="#F6F6F6" />
                                <g filter="url(#filter0_dd_597_1525)">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M21.5 5C14.0442 5 8 11.0442 8 18.5C8 25.9558 14.0442 32 21.5 32C28.9558 32 35 25.9558 35 18.5C35 11.0442 28.9558 5 21.5 5Z"
                                        fill="white" />
                                </g>
                                <defs>
                                    <filter id="filter0_dd_597_1525" x="0" y="0" width="43" height="43"
                                        filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="3" />
                                        <feGaussianBlur stdDeviation="0.5" />
                                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.06 0" />
                                        <feBlend mode="normal" in2="BackgroundImageFix"
                                            result="effect1_dropShadow_597_1525" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="3" />
                                        <feGaussianBlur stdDeviation="4" />
                                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.15 0" />
                                        <feBlend mode="normal" in2="effect1_dropShadow_597_1525"
                                            result="effect2_dropShadow_597_1525" />
                                        <feBlend mode="normal" in="SourceGraphic" in2="effect2_dropShadow_597_1525"
                                            result="shape" />
                                    </filter>
                                </defs>
                            </svg>
                        </div>
                        <div class="row">
                            <p>Email meldingen</p>
                            <svg xmlns="http://www.w3.org/2000/svg" width="57" height="43" viewBox="0 0 57 43" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M41.143 33.9078C39.403 34.0078 37.665 33.9998 35.925 33.9998C35.913 33.9998 27.108 33.9998 27.108 33.9998C25.334 33.9998 23.596 34.0078 21.857 33.9078C20.276 33.8178 18.736 33.6258 17.203 33.1968C13.976 32.2948 11.158 30.4108 9.121 27.7398C7.096 25.0858 6 21.8368 6 18.5008C6 15.1608 7.096 11.9138 9.121 9.25985C11.158 6.58985 13.976 4.70485 17.203 3.80285C18.736 3.37385 20.276 3.18285 21.857 3.09185C23.596 2.99185 25.334 3.00085 27.074 3.00085C27.086 3.00085 35.893 2.99985 35.893 2.99985C37.665 3.00085 39.403 2.99185 41.143 3.09185C42.723 3.18285 44.263 3.37385 45.796 3.80285C49.023 4.70485 51.841 6.58985 53.878 9.25985C55.903 11.9138 57 15.1608 57 18.4998C57 21.8368 55.903 25.0858 53.878 27.7398C51.841 30.4108 49.023 32.2948 45.796 33.1968C44.263 33.6258 42.723 33.8178 41.143 33.9078Z"
                                    fill="#F6F6F6" />
                                <g filter="url(#filter0_dd_597_1525)">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M21.5 5C14.0442 5 8 11.0442 8 18.5C8 25.9558 14.0442 32 21.5 32C28.9558 32 35 25.9558 35 18.5C35 11.0442 28.9558 5 21.5 5Z"
                                        fill="white" />
                                </g>
                                <defs>
                                    <filter id="filter0_dd_597_1525" x="0" y="0" width="43" height="43"
                                        filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="3" />
                                        <feGaussianBlur stdDeviation="0.5" />
                                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.06 0" />
                                        <feBlend mode="normal" in2="BackgroundImageFix"
                                            result="effect1_dropShadow_597_1525" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="3" />
                                        <feGaussianBlur stdDeviation="4" />
                                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.15 0" />
                                        <feBlend mode="normal" in2="effect1_dropShadow_597_1525"
                                            result="effect2_dropShadow_597_1525" />
                                        <feBlend mode="normal" in="SourceGraphic" in2="effect2_dropShadow_597_1525"
                                            result="shape" />
                                    </filter>
                                </defs>
                            </svg>
                        </div>
                        <div class="row">
                            <p>SMS-melding</p>
                            <svg xmlns="http://www.w3.org/2000/svg" width="57" height="43" viewBox="0 0 57 43" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M41.143 33.9078C39.403 34.0078 37.665 33.9998 35.925 33.9998C35.913 33.9998 27.108 33.9998 27.108 33.9998C25.334 33.9998 23.596 34.0078 21.857 33.9078C20.276 33.8178 18.736 33.6258 17.203 33.1968C13.976 32.2948 11.158 30.4108 9.121 27.7398C7.096 25.0858 6 21.8368 6 18.5008C6 15.1608 7.096 11.9138 9.121 9.25985C11.158 6.58985 13.976 4.70485 17.203 3.80285C18.736 3.37385 20.276 3.18285 21.857 3.09185C23.596 2.99185 25.334 3.00085 27.074 3.00085C27.086 3.00085 35.893 2.99985 35.893 2.99985C37.665 3.00085 39.403 2.99185 41.143 3.09185C42.723 3.18285 44.263 3.37385 45.796 3.80285C49.023 4.70485 51.841 6.58985 53.878 9.25985C55.903 11.9138 57 15.1608 57 18.4998C57 21.8368 55.903 25.0858 53.878 27.7398C51.841 30.4108 49.023 32.2948 45.796 33.1968C44.263 33.6258 42.723 33.8178 41.143 33.9078Z"
                                    fill="#F6F6F6" />
                                <g filter="url(#filter0_dd_597_1525)">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M21.5 5C14.0442 5 8 11.0442 8 18.5C8 25.9558 14.0442 32 21.5 32C28.9558 32 35 25.9558 35 18.5C35 11.0442 28.9558 5 21.5 5Z"
                                        fill="white" />
                                </g>
                                <defs>
                                    <filter id="filter0_dd_597_1525" x="0" y="0" width="43" height="43"
                                        filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="3" />
                                        <feGaussianBlur stdDeviation="0.5" />
                                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.06 0" />
                                        <feBlend mode="normal" in2="BackgroundImageFix"
                                            result="effect1_dropShadow_597_1525" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="3" />
                                        <feGaussianBlur stdDeviation="4" />
                                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.15 0" />
                                        <feBlend mode="normal" in2="effect1_dropShadow_597_1525"
                                            result="effect2_dropShadow_597_1525" />
                                        <feBlend mode="normal" in="SourceGraphic" in2="effect2_dropShadow_597_1525"
                                            result="shape" />
                                    </filter>
                                </defs>
                            </svg>
                        </div>
                        <div class="row">
                            <p>Waarschuwingen apparaat aanmelden</p>
                            <svg xmlns="http://www.w3.org/2000/svg" width="57" height="43" viewBox="0 0 57 43" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M41.143 33.9078C39.403 34.0078 37.665 33.9998 35.925 33.9998C35.913 33.9998 27.108 33.9998 27.108 33.9998C25.334 33.9998 23.596 34.0078 21.857 33.9078C20.276 33.8178 18.736 33.6258 17.203 33.1968C13.976 32.2948 11.158 30.4108 9.121 27.7398C7.096 25.0858 6 21.8368 6 18.5008C6 15.1608 7.096 11.9138 9.121 9.25985C11.158 6.58985 13.976 4.70485 17.203 3.80285C18.736 3.37385 20.276 3.18285 21.857 3.09185C23.596 2.99185 25.334 3.00085 27.074 3.00085C27.086 3.00085 35.893 2.99985 35.893 2.99985C37.665 3.00085 39.403 2.99185 41.143 3.09185C42.723 3.18285 44.263 3.37385 45.796 3.80285C49.023 4.70485 51.841 6.58985 53.878 9.25985C55.903 11.9138 57 15.1608 57 18.4998C57 21.8368 55.903 25.0858 53.878 27.7398C51.841 30.4108 49.023 32.2948 45.796 33.1968C44.263 33.6258 42.723 33.8178 41.143 33.9078Z"
                                    fill="#F6F6F6" />
                                <g filter="url(#filter0_dd_597_1525)">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M21.5 5C14.0442 5 8 11.0442 8 18.5C8 25.9558 14.0442 32 21.5 32C28.9558 32 35 25.9558 35 18.5C35 11.0442 28.9558 5 21.5 5Z"
                                        fill="white" />
                                </g>
                                <defs>
                                    <filter id="filter0_dd_597_1525" x="0" y="0" width="43" height="43"
                                        filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="3" />
                                        <feGaussianBlur stdDeviation="0.5" />
                                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.06 0" />
                                        <feBlend mode="normal" in2="BackgroundImageFix"
                                            result="effect1_dropShadow_597_1525" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="3" />
                                        <feGaussianBlur stdDeviation="4" />
                                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.15 0" />
                                        <feBlend mode="normal" in2="effect1_dropShadow_597_1525"
                                            result="effect2_dropShadow_597_1525" />
                                        <feBlend mode="normal" in="SourceGraphic" in2="effect2_dropShadow_597_1525"
                                            result="shape" />
                                    </filter>
                                </defs>
                            </svg>
                        </div>
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
                            <a href="#" class="btn">Email wijzigen</a>
                        </div>
                        <h3>Wachtwoord wijzigen</h3>
                        <div class="row">
                            <p>Wachtwoord wijzigen</p>
                            <a href="#" class="btn">Wachtwoord wijzigen</a>
                        </div>
                        <h3>Account verwijderen</h3>
                        <div class="row">
                            <p>Account verwijderen</p>
                            <a href="#" class="btn red">Account verwijderen</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>