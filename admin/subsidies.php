<?php
include_once (__DIR__ . "../../classes/Db.php");
include_once (__DIR__ . "../../classes/User.php");
include_once (__DIR__ . "../../classes/Subsidie.php");
session_start();

if (isset($_SESSION["user_id"])) {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);
    $current_page = 'subsidies';
} else {
    header("Location: login.php?error=notLoggedIn");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["subsidie_name"])) {
        try {
            $pdo = Db::getInstance();
            $name = $_POST["subsidie_name"];
            $delete = Subsidie::deleteSubsidie($pdo, $name);
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
    <link rel="icon" type="image/x-icon" href="assets/images/Favicon.svg">
</head>

<body>
    <?php include_once ('../inc/navAdmin.inc.php'); ?>
    <div id="subsidies">
        <div class="top">
            <h1>Subsidies</h1>
            <a href="addSubsidie.php" class="btn"><i class="fa fa-plus" style="padding-right:8px"></i> Toevoegen</a>
        </div>

        <div class=" subsidies">
            <?php if (!empty($subsidies)): ?>
                <?php foreach ($subsidies as $subsidie): ?>
                    <div class="subsidie">
                        <a href="subsidieDetails.php?name=<?php echo urlencode($subsidie["name"]); ?>">
                            <div class="image"
                                style="background-image: url('.././assets/images/subsidies/tegel<?php echo htmlspecialchars($subsidie["id"]); ?>.jpg')">
                            </div>
                            <div class="text">
                                <h3>
                                    <?php echo htmlspecialchars($subsidie["name"]); ?>
                                </h3>
                                <p>Bekijk details</p>
                            </div>
                        </a>
                        <form method="post" action="addSubsidie.php">
                            <input type="hidden" name="edit_subsidie_name"
                                value="<?php echo htmlspecialchars($subsidie["name"]); ?>">
                            <button type="submit" class="edit"><i class="fa fa-edit"></i></button>
                        </form>
                        <form method="post">
                            <input type="hidden" name="subsidie_name"
                                value="<?php echo htmlspecialchars($subsidie["name"]); ?>">
                            <button type="submit" class="delete"><i class="fa fa-trash"></i></button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No subsidies found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>