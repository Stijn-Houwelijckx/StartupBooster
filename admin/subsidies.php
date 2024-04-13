<?php
include_once (__DIR__ . "../../classes/Db.php");
include_once (__DIR__ . "../../classes/User.php");
include_once (__DIR__ . "../../classes/Subsidie.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'error.log');

session_start();

$pdo = Db::getInstance();

if (isset($_SESSION["user_id"]) && $user["isAdmin"] == "on") {
    $user = User::getUserById($pdo, $_SESSION["user_id"]);
    $current_page = 'subsidies';
} else {
    header("Location: ../login.php?error=notLoggedIn");
    exit();
}

$selectedSubsidie = Subsidie::getSubsidieById($pdo, 0);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // if (isset($_POST["id"])) {
    //     try {
    //         Subsidie::deleteSubsidie($pdo, $_POST["id"]);
    //         $selectedSubsidie = Subsidie::getSubsidieById($pdo, $_POST["id"]);
    //     } catch (Exception $e) {
    //         error_log('Database error: ' . $e->getMessage());
    //     }
    // }

    if (isset($_POST["subsidie"])) {
        try {
            $subsidie_id = $_POST["subsidie"];
            $selectedSubsidie = Subsidie::getSubsidieById($pdo, $subsidie_id);

        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
        }
    }

    if (isset($_POST["name"])) {
        try {
            Subsidie::updateSubsidie($pdo, $_POST['name'], $_POST['description'], $_POST['who'], $_POST['what'], $_POST['amount'], $_POST['link'], $_POST['id']);
            $selectedSubsidie = Subsidie::getSubsidieById($pdo, $_POST["id"]);
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
        }
    }
}

$subsidies = Subsidie::getSubsidies($pdo);
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
    <div id="subsidies" class="admin">
        <div class="top">
            <h1>Subsidies</h1>
            <a href="addSubsidie.php" class="btn"><i class="fa fa-plus" style="padding-right:8px"></i> Toevoegen</a>
        </div>
        <form action="" id="subsidieSelector" onchange="submitSubsidieForm()" method="post">
            <select name="subsidie">
                <?php foreach ($subsidies as $subsidie): ?>
                    <option value="<?php echo $subsidie["id"] ?>">
                        <?php echo htmlspecialchars($subsidie["name"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <div class="subsidies">
            <?php if (!empty($subsidies)): ?>
                <form action="" method="post" id="subsidieForm">
                    <div class="subsidie">
                        <div class="text">
                            <input type="text" name="name"
                                value="<?php echo htmlspecialchars($selectedSubsidie["name"]); ?>">
                            <textarea name="description" cols="30"
                                rows="10"><?php echo htmlspecialchars($selectedSubsidie["description"]); ?></textarea>
                            <input type="text" name="who" value="<?php echo htmlspecialchars($selectedSubsidie["who"]); ?>">
                            <input type="text" name="what"
                                value="<?php echo htmlspecialchars($selectedSubsidie["what"]); ?>">
                            <input type="text" name="amount"
                                value="<?php echo htmlspecialchars($selectedSubsidie["amount"]); ?>">
                            <input type="text" name="link"
                                value="<?php echo htmlspecialchars($selectedSubsidie["link"]); ?>">
                            <input type="text" name="id" hidden
                                value="<?php echo htmlspecialchars($selectedSubsidie["id"]); ?>">
                        </div>
                    </div>
                    <div class="buttons">
                        <button type="submit" class="btn">Opslaan</button>
                        <!-- <button type="submit" class="btn remove">Verwijderen</button> -->
                    </div>
                </form>
            <?php else: ?>
                <p>No subsidies found.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="popup">
        <p>Weet je zeker dat je deze subsidie wilt verwijderen?</p>
        <div class="btns">
            <a href="#" class="close">Nee</a>
            <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo $selectedSubsidie["id"]; ?>">
                <button type="submit" class="btn remove">Ja</button>
            </form>
        </div>
    </div>

    <script>
        function submitSubsidieForm() {
            document.getElementById("subsidieSelector").submit();
        }


        document.querySelector(".subsidies .remove").addEventListener("click", function (e) {
            document.querySelector(".popup").style.display = "flex";
            e.preventDefault();
            document.querySelector(".popup .close").addEventListener("click", function (e) {
                document.querySelector(".popup").style.display = "none";
            });
            document.querySelector(".popup .close").addEventListener("click", function (e) {
                document.getElementById("subsidieForm").submit();

            });
        });
    </script>
</body>

</html>