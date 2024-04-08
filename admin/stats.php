<?php
session_start();

include_once (__DIR__ . "/../classes/Db.php");
include_once (__DIR__ . "/../classes/User.php");
include_once (__DIR__ . "/../classes/Sector.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=notLoggedIn");
    exit();
}

$pdo = Db::getInstance();
$user = User::getUserById($pdo, $_SESSION["user_id"]);
$current_page = 'stats';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['title'])) {
        try {
            $title = $_POST["title"];
            $delete = Sector::deleteSector($pdo, $title);
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
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
        <div class="sectors">
            <div class="top">
                <h2>Sectoren</h2>
                <a href="addSector.php" class="btn"><i class="fa fa-plus" style="padding-right:8px"></i> Toevoegen</a>
            </div>
            <?php foreach ($sectors as $sector): ?>
                <div class="sector">
                    <p>
                        <?php echo $sector["title"] ?>
                    </p>
                    <div class="icons">
                        <form method="post" action="addSector.php">
                            <input type="hidden" name="edit_task_question"
                                value="<?php echo htmlspecialchars($sector["title"]); ?>">
                            <button type="submit" class="edit"><i class="fa fa-edit"></i></button>
                        </form>
                        <form method="post">
                            <input type="hidden" name="title" value="<?php echo $sector["title"]; ?>">
                            <button type="submit" class="delete"><i class="fa fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</body>

</html>