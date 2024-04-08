<?php
include_once (__DIR__ . "/../classes/Db.php");
include_once (__DIR__ . "/../classes/User.php");
include_once (__DIR__ . "/../classes/Task.php");
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=notLoggedIn");
    exit();
}

$pdo = Db::getInstance();
$user = User::getUserById($pdo, $_SESSION["user_id"]);
$current_page = 'roadmap';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST["label"]) && !empty($_POST["question"]) && !empty($_POST["answer"])) {
        try {
            $task = new Task();
            $task->setLabel($_POST["label"]);
            $task->setQuestion($_POST["question"]);
            $task->setAnswer($_POST["answer"]);
            $task->setStatus("1");
            Task::addTask($pdo, $_POST["label"], $_POST["question"], $_POST["answer"], "1");
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
    <div id="addSubsidie">
        <h2>Voeg een subsidie toe</h2>
        <form action="" method="POST">
            <div class="row">
                <div class="column">
                    <label for="label">Label</label>
                    <select name="label">
                        <option value="Start">Start</option>
                        <option value="Aanvragen">Aanvragen
                        </option>
                    </select>
                </div>
                <div class="column">
                    <label for="question">Vraag</label>
                    <input type="text" name="question" id="question">
                </div>
            </div>
            <div class="row">
                <div class="column">
                    <label for="answer">Antwoord op vraag</label>
                    <textarea name="answer" id="answer" cols="30" rows="10"></textarea>
                </div>
            </div>
            <button type="submit" class="btn">Toevoegen</button>
        </form>
    </div>
</body>

</html>