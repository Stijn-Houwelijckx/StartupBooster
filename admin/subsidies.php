<?php
include_once (__DIR__ . "../../classes/Db.php");
include_once (__DIR__ . "../../classes/User.php");
include_once (__DIR__ . "../../classes/Subsidie.php");
session_start();

$pdo = Db::getInstance();

if (isset($_SESSION["user_id"])) {
    $user = User::getUserById($pdo, $_SESSION["user_id"]);
    $current_page = 'subsidies';
} else {
    header("Location: login.php?error=notLoggedIn");
    exit();
}

$selectedSubsidie = Subsidie::getSubsidieById($pdo, 0);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // if (isset($_POST["subsidie_name"])) {
    //     try {
    //         $name = $_POST["subsidie_name"];
    //         $delete = Subsidie::deleteSubsidie($pdo, $name);
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
    <div id="subsidies" class="admin">
        <div class="top">
            <h1>Subsidies</h1>
            <a href="addSubsidie.php" class="btn"><i class="fa fa-plus" style="padding-right:8px"></i> Toevoegen</a>
        </div>
        <form action="" id="subsidieSelector" onchange="submitSubsidieForm()" method="post">
            <select name="subsidie" id="subsidie">
                <?php foreach ($subsidies as $subsidie): ?>
                    <option value="<?php echo $subsidie["id"] ?>">
                        <?php echo htmlspecialchars($subsidie["name"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <div class="subsidies">
            <?php if (!empty($subsidies)): ?>
                <div class="subsidie">
                    <div class="text">
                        <input type="text" name="name" value="<?php echo htmlspecialchars($selectedSubsidie["name"]); ?>">
                        </input>
                        <textarea name="description" cols="30"
                            rows="10"><?php echo htmlspecialchars($selectedSubsidie["description"]); ?></textarea>
                        <input type="text" value=" <?php echo htmlspecialchars($selectedSubsidie["who"]); ?>">
                        </input>
                        <input type="text" value="<?php echo htmlspecialchars($selectedSubsidie["what"]); ?>">
                        </input>
                        <input type="text" value="<?php echo htmlspecialchars($selectedSubsidie["amount"]); ?>">
                        </input>
                        <input type="text" value="<?php echo htmlspecialchars($selectedSubsidie["link"]); ?>">
                        </input>
                        <input type="text" hidden value="<?php echo htmlspecialchars($selectedSubsidie["id"]); ?>">
                    </div>
                </div>
            <?php else: ?>
                <p>No subsidies found.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function submitSubsidieForm() {
            document.getElementById("subsidieSelector").submit();
        }
    </script>
</body>

</html>