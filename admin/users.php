<?php
include_once (__DIR__ . "../../classes/Db.php");
include_once (__DIR__ . "../../classes/User.php");
include_once (__DIR__ . "../../classes/Statute.php");
include_once (__DIR__ . "../../classes/Sector.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'error.log');

session_start();

$pdo = Db::getInstance();
$user = User::getUserById($pdo, $_SESSION["user_id"]);
// $popop = false;

if (isset($_SESSION["user_id"]) && $user["isAdmin"] == "on") {
    $user = User::getUserById($pdo, $_SESSION["user_id"]);
    $current_page = 'users';

    $statutes = Statute::getAll($pdo);
    $sectors = Sector::getAll($pdo);
} else {
    header("Location: ../login.php?error=notLoggedIn");
    exit();
}

$selectedUser = User::getUserById($pdo, 0);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["id"])) {
        try {
            User::deleteUser($pdo, $_POST["id"]);
            $selectedUser = User::getUserById($pdo, 0);
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
        }
    }

    if (isset($_POST["user_id"])) {
        try {
            $user_id = $_POST["user_id"];
            $selectedUser = User::getUserById($pdo, $user_id);

        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
        }
    }

    if (isset($_POST["firstname"])) {
        try {
            $user = new User();
            $user->setFirstname($_POST['firstname']);
            $user->setLastname($_POST['lastname']);
            $user->setIsAdmin($_POST['isAdmin']);
            $user->setEmail($_POST['email']);
            $user->setStatute($_POST['statute']);
            $user->setSector($_POST['sector']);
            $user->setStreet($_POST['street']);
            $user->setHouseNumber($_POST['houseNumber']);
            $user->setZipCode($_POST['zipCode']);
            $user->setCity($_POST['city']);
            $user->updateUser($pdo, $_POST["user_id"]);

            unset($_SESSION["firstname"]);
            unset($_SESSION["lastname"]);
            unset($_SESSION["statute"]);
            unset($_SESSION["sector"]);
            unset($_SESSION["email"]);
            unset($_SESSION["street"]);
            unset($_SESSION["houseNumber"]);
            unset($_SESSION["zipCode"]);
            unset($_SESSION["city"]);

            $selectedUser = User::getUserById($pdo, $_POST["user_id"]);
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
        }
    }
}

$users = User::getAll($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StartupBooster - helpdesk</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="../assets/images/Favicon.svg">
</head>

<body>
    <?php include_once ('../inc/navAdmin.inc.php'); ?>
    <div id="users">
        <h1>Gebruikers</h1>
        <form action="" id="userSelector" onchange="submitUserForm()" method="post">
            <select name="user_id">
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user["id"] ?>">
                        <?php echo htmlspecialchars($user["firstname"]) . " " . htmlspecialchars($user["lastname"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <div class="users">
            <?php if (!empty($users)): ?>
                <form action="" method="post" id="userForm">
                    <div class="user">
                        <div class="text">
                            <label for="firstname">Voornaam:</label>
                            <input type="text" name="firstname" id="firstname" value="<?php echo htmlspecialchars($selectedUser["firstname"]); ?>">
                            
                            <label for="lastname">Achternaam:</label>
                            <input type="text" name="lastname" id="lastname" value="<?php echo htmlspecialchars($selectedUser["lastname"]); ?>">
                            
                            <label for="statute">Statuut:</label>
                            <select name="statute" id="statute">
                                <?php foreach ($statutes as $statute): ?>
                                    <option value="<?php echo $statute["id"] ?>" <?php echo $selectedUser["statute_id"] == $statute["id"] ? "selected" : "" ?>>
                                        <?php echo $statute["title"] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <label for="sector">Sector:</label>
                            <select name="sector" id="sector">
                                <?php foreach ($sectors as $sector): ?>
                                    <option value="<?php echo $sector["id"] ?>" <?php echo $selectedUser["sector_id"] == $sector["id"] ? "selected" : "" ?>>
                                        <?php echo $sector["title"] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <label for="email">E-mail:</label>
                            <input type="text" name="email" id="email" value="<?php echo htmlspecialchars($selectedUser["email"]); ?>">
                            
                            <label for="phoneNumber">Telefoonnummer:</label>
                            <input type="text" name="phoneNumber" id="phoneNumber" value="<?php echo htmlspecialchars($selectedUser["phoneNumber"]); ?>">
                            
                            <label for="street">Straat:</label>
                            <input type="text" name="street" id="street" value="<?php echo htmlspecialchars($selectedUser["street"]); ?>">
                            
                            <label for="houseNumber">Huisnummer:</label>
                            <input type="text" name="houseNumber" id="houseNumber" value="<?php echo htmlspecialchars($selectedUser["houseNumber"]); ?>">
                            
                            <label for="zipCode">Postcode:</label>
                            <input type="text" name="zipCode" id="zipCode" value="<?php echo htmlspecialchars($selectedUser["zipCode"]); ?>">
                            
                            <label for="city">Stad:</label>
                            <input type="text" name="city" id="city" value="<?php echo htmlspecialchars($selectedUser["city"]); ?>">
                            
                            <input type="hidden" name="user_id" id="user_id" value="<?php echo htmlspecialchars($selectedUser["id"]); ?>">
                            
                            <div class="row">
                                <input type="hidden" name="isAdmin" value="off">
                                <label for="checkboxIsAdmin">isAdmin:</label>
                                <input type="checkbox" name="isAdmin" id="checkboxIsAdmin" <?php if ($selectedUser["isAdmin"] == "on") echo "checked"; ?>>
                            </div>
                        </div>
                    </div>
                    <div class="buttons">
                        <button type="submit" class="btn">Opslaan</button>
                    </div>
                </form>
                <div class="popup">
                    <p>Weet je zeker dat je deze gebruiker wilt verwijderen?</p>
                    <div class="btns">
                        <a href="#" class="close">Nee</a>
                        <form action="" method="POST">
                            <input type="text" name="id" hidden value="<?php echo $selectedUser["id"] ?>>">
                            <button type="submit" class="btn">Ja</button>
                        </form>
                    </div>
                </div>


                <div class="popupIsAdmin">
                    <p>Weet je zeker dat je deze gebruiker admin wilt maken?</p>
                    <div class="btns">
                        <a href="#" class="close">Nee</a>
                        <form action="" method="POST">
                            <input type="text" name="user_admin_id" hidden value="<?php echo $selectedUser["id"] ?>>">
                            <button type="button" class="btn confirm-admin">Ja</button>
                        </form>
                    </div>
                </div>
                <button class="btn remove">Verwijderen</button>
            <?php endif; ?>
        </div>
    </div>


    <script>
        function submitUserForm() {
            document.getElementById("userSelector").submit();
        }

        document.querySelector(".users .remove").addEventListener("click", function (e) {
            document.querySelector(".popup").style.display = "flex";
            document.querySelector(".popup .close").addEventListener("click", function (e) {
                document.querySelector(".popup").style.display = "none";
            });
        });

        document.querySelector("#checkboxIsAdmin").addEventListener("change", function (e) {
            if (this.checked) {
                document.querySelector(".popupIsAdmin").style.display = "flex";
                document.querySelector(".popupIsAdmin .close").addEventListener("click", function (e) {
                    document.querySelector(".popupIsAdmin").style.display = "none";
                    document.querySelector("#checkboxIsAdmin").checked = false;
                });

                e.preventDefault();
            }
        });

        document.querySelector(".confirm-admin").addEventListener("click", function (e) {
            document.querySelector("#userForm").submit();
        });

    </script>

</body>

</html>