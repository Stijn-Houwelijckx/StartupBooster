<?php
include_once (__DIR__ . "/classes/Task.php");
include_once (__DIR__ . "/classes/Db.php");
session_start();

$task_question = isset ($_GET['question']) ? $_GET['question'] : '';

if (empty ($task_question)) {
    echo "Ongeldige tasknaam.";
    exit;
}

if (isset ($_SESSION["user_id"])) {
    $pdo = Db::getInstance();

    $task = Task::getTaskByQuestion($pdo, $task_question);

    if (!$task) {
        echo "task niet gevonden";
        exit;
    }
} else {
    header("Location: login.php?error=notLoggedIn");
    exit();
}

$current_page = 'task_details';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>task Details</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include_once ('inc/nav.inc.php'); ?>
    <div id="task-details">
        <h1>
            <?php echo htmlspecialchars($task['question']); ?>
        </h1>
        <div class="text">
            <div class="description">
                <h2>Antwoord op de vraag</h2>
                <p>
                    <?php echo htmlspecialchars($task['answer']); ?>
                </p>
            </div>
        </div>
    </div>
</body>

</html>