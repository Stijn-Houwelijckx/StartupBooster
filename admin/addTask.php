<?php
include_once (__DIR__ . "/../classes/Db.php");
include_once (__DIR__ . "/../classes/User.php");
include_once (__DIR__ . "/../classes/Task.php");

session_start();

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('error_log', 'error.log');

$current_page = 'roadmap';

$pdo = Db::getInstance();
$user = User::getUserById($pdo, $_SESSION["user_id"]);

if (!isset($_SESSION["user_id"]) && $user["isAdmin"] == "on") {
    header("Location: ../login.php?error=notLoggedIn");
    exit();
}

$pdo = Db::getInstance();
$user = User::getUserById($pdo, $_SESSION["user_id"]);

// Retreive position of last task
$allTasks = Task::getAllTasks($pdo);
$position = end($allTasks)["position"] + 1;

if (isset($_GET["position"]) && !empty($_GET["position"])) {
    $position = intval($_GET["position"]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST["label"]) && !empty($_POST["question"]) && !empty($_POST["answer"])) {
        try {
            // Update positions of the tasks first before adding a new task
            Task::updateTaskPositions($pdo, $position);

            // Create new task
            $task = new Task();
            $task->setPosition($position);
            $task->setLabel($_POST["label"]);
            $task->setQuestion($_POST["question"]);
            $task->setAnswer($_POST["answer"]);
            $insertedTaskId = $task->addTask($pdo, $_SESSION["selectedStatute"]);

            // Insert new task to all users
            if ($insertedTaskId) {
                $users = User::getAll($pdo);
                foreach ($users as $user) {
                    Task::addTaskToUser($pdo, $user["id"], $insertedTaskId);
                }
            } else {
                error_log('Failed to insert task to all users.');
            }

            header("Location: tasks.php");
            exit;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
        }
    }
} ?>

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
    <div id="addTask">
        <h2>Voeg een subsidie toe op positie <?php echo $position ?></h2>
        <form action="" method="POST">
            <div class="column">
                <label for="label">Label</label>
                <select name="label">
                    <option value="Start">Start</option>
                    <option value="Aanvragen">Aanvragen
                    </option>
                </select>
                <div class="column">
                    <label for="question">Vraag</label>
                    <input type="text" name="question" id="question">
                </div>
            </div>
            <div class="column">
                <label for="answer">Antwoord op vraag</label>
                <textarea name="answer" id="answer" cols="30" rows="10"></textarea>
            </div>
            <button type="submit" class="btn">Toevoegen</button>
        </form>
    </div>
</body>

</html>