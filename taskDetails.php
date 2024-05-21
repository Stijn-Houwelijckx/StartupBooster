<?php
include_once (__DIR__ . "/classes/User.php");
include_once (__DIR__ . "/classes/Task.php");
include_once (__DIR__ . "/classes/Db.php");
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'error.log');

$task_question = isset($_GET['question']) ? $_GET['question'] : '';

if (empty($task_question)) {
    echo "Ongeldige tasknaam.";
    exit;
}

if (isset($_SESSION["user_id"])) {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);
    $task = Task::getTaskByQuestion($pdo, $task_question, $user["statute_id"]);
    if (!$task) {
        echo "task niet gevonden";
        exit;
    }
    try {
        $activeTask = Task::getActiveTask($pdo, $_SESSION["user_id"], $user["statute_id"]);
        $activeTaskString = '';

        if (is_array($activeTask)) {
            $activeTaskString = implode(', ', $activeTask);
        }

        if (isset($_POST['taskId'])) {
            $taskId = filter_input(INPUT_POST, 'taskId', FILTER_SANITIZE_NUMBER_INT);
            if ($taskId) {
                Task::updateRead($pdo, $taskId, $_SESSION["user_id"]);
                header("Location: roadmap.php");
                exit();
            } else {
                error_log('Invalid taskId received.');
            }
        }
    } catch (Exception $e) {
        error_log('Database error: ' . $e->getMessage());
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
    <title>Task Details</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <?php include_once ('inc/nav.inc.php'); ?>
    <div id="task-details">
        <div class="top">
            <a href="roadmap.php">
                <i class="fa fa-angle-left"></i>
            </a>
            <h1>
                Stap
                <?php echo htmlspecialchars($task['position']); ?>:
                <?php echo htmlspecialchars($task['question']); ?>
            </h1>
        </div>
        <div class="elements">
            <div class="text">
                <h2>Antwoord op de vraag</h2>
                <p>
                    <?php echo htmlspecialchars($task['answer']); ?>
                </p>
                <?php if ($task["id"] == $activeTaskString) { ?>
                    <form id='readForm<?php echo $task['id']; ?>' method='POST'
                        onsubmit='submitReadForm(event, <?php echo $task['id']; ?>)'>
                        <input type="hidden" name="taskId" value="<?php echo $task['id']; ?>">
                        <button type='submit' class='btn'>Gelezen</button>
                    </form>
                <?php } ?>
            </div>
            <div class="image"
                style="background-image: url('./assets/images/questions/question<?php echo htmlspecialchars($task['id']); ?>.jpg');">
            </div>
        </div>
    </div>
</body>

</html>