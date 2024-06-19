<?php
include_once (__DIR__ . "/classes/Db.php");
include_once (__DIR__ . "/classes/User.php");
include_once (__DIR__ . "/classes/Statute.php");
include_once (__DIR__ . "/classes/Sector.php");
include_once (__DIR__ . "/classes/Message.php");
include_once (__DIR__ . "/classes/Task.php");

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('error_log', 'error.log');

session_start();

$current_page = 'dashboard';


if (isset($_SESSION["user_id"])) {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);

    try {
        // $pdo = Db::getInstance();
        $statutes = Statute::getAll($pdo);
        $sectors = Sector::getAll($pdo);

        $activeTaskID = Task::getActiveTask($pdo, $_SESSION["user_id"], $user["statute_id"]);

        if ($activeTaskID) {
            $activeTask = Task::getTaskById($pdo, $activeTaskID["task_id"]);
        } else {
            $activeTask = null;
        }

    } catch (Exception $e) {
        error_log('Database error: ' . $e->getMessage());
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
    <title>StartupBooster</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/Favicon.svg">
</head>

<body>
    <?php include_once ('inc/nav.inc.php'); ?>
    <div id="dashboard">
        <h1><?php echo $user["firstname"]?>'s dashboard</h1>
        <!-- <div class="notifications">
            <h2>Meldingen</h2>
            <div class="notification">
                <p>U heeft 3 ongelezen berichten in ‘Chat’.</p>
                <i class="fa fa-angle-right"></i>
            </div>
            <div class="notification error">
                <p>U heeft 1 opmerking bij het aanvragen van uw statuut als zelfstandige.</p>
                <i class="fa fa-angle-right"></i>
            </div>
        </div> -->
        <?php if($activeTask): ?>
            <div class="tasks">
                <div class="task">
                    <p class="label">
                        <?php /*echo htmlspecialchars($activeTask["label"]);*/ ?>
                        Huidige stap in stappenplan
                    </p>
                    <div class="row">
                        <p class="question">
                            <?php echo htmlspecialchars($activeTask["question"]); ?>
                        </p>
                    </div>
                    <p class="answer">
                        <?php echo htmlspecialchars($activeTask["answer"]); ?>
                    </p>
                    <div class="row display">
                        <a href="taskDetails.php?question=<?php echo urlencode($activeTask["question"]); ?>" class="readmore">Ga naar stap</a>
                    </div>
                </div>

                <div class="roadmap-link-container">
                    <i class="fa fa-arrow-right"></i>
                    <a class="roadmap-link" href="roadmap.php">Ga naar stappenplan</a>
                </div>
            </div>
        <?php else: ?>
            <div class="tasks">
                <div class="task">
                    <p class="label">
                        Geen actieve stap in stappenplan
                    </p>
                    <div class="row">
                        <p class="question">
                            U heeft alle stappen in het stappenplan doorlopen.
                        </p>
                    </div>
                    <div class="row display">
                        <a href="roadmap.php" class="readmore">Ga naar stappenplan</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php include_once ('inc/chat.inc.php'); ?>
</body>

</html>