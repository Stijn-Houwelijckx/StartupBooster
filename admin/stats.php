<?php
session_start();

include_once (__DIR__ . "/../classes/Db.php");
include_once (__DIR__ . "/../classes/User.php");
include_once (__DIR__ . "/../classes/Sector.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'error.log');

if (!isset($_SESSION["user_id"]) && $user["isAdmin"] == "on") {
    header("Location: ../login.php?error=notLoggedIn");
    exit();
}

$pdo = Db::getInstance();
$user = User::getUserById($pdo, $_SESSION["user_id"]);
$current_page = 'stats';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["delete"])) {
        try {
            foreach ($_POST["delete"] as $id => $value) {
                Sector::deleteSector($pdo, $id);
            }
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
        }
    }

    if (isset($_POST['sectors'])) {
        $updatedSectors = $_POST['sectors'];
        foreach ($updatedSectors as $sector) {
            try {
                Sector::updateSectors($pdo, $sector['id'], $sector['title']);
            } catch (Exception $e) {
                error_log('Database error: ' . $e->getMessage());
            }
        }
    }
}

$sectors = Sector::getAll($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StartupBooster - tasks</title>
    <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="../assets/images/Favicon.svg">
</head>

<body>
    <?php include_once ('../inc/navAdmin.inc.php'); ?>
    <div id="stats">
        <div class="top">
            <h1>Statistieken</h1>
        </div>
        <div class="top">
            <h2>Sectoren</h2>
            <a href="addSector.php" class="btn"><i class="fa fa-plus" style="padding-right:8px"></i> Toevoegen</a>
        </div>
        <form action="" method="post">
            <div class="sectors">
                <?php foreach ($sectors as $sector): ?>
                    <div class="sector">
                        <input type="text" name="sectors[<?php echo $sector["id"] ?>][title]"
                            value="<?php echo $sector["title"] ?>">
                        <input type="hidden" name="sectors[<?php echo $sector["id"]; ?>][id]"
                            value="<?php echo $sector["id"] ?>">
                        <div class="icons">
                            <label for="delete[<?php echo $sector["id"] ?>]"><i class="fa fa-trash"></i></label>
                            <input hidden type="submit" name="delete[<?php echo $sector["id"] ?>]"
                                id="delete[<?php echo $sector["id"] ?>]">
                        </div>
                    </div>

                <?php endforeach ?>
                <button type="submit" class="btn" name="saveChanges">Opslaan</button>
            </div>
        </form>

    </div>

    <script>
        document.querySelectorAll(".sectors .delete").forEach(function (deleteBtn) {
            deleteBtn.addEventListener("click", function (e) {
                e.preventDefault(); // Voorkom standaardgedrag van formulier
                document.querySelector(".popup").style.display = "flex";
                document.querySelector(".popup .close").addEventListener("click", function (e) {
                    document.querySelector(".popup").style.display = "none";
                });
            });
        });
    </script>
</body>

</html>