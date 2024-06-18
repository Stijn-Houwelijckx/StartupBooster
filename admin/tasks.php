<?php
session_start();

include_once (__DIR__ . "/../classes/Db.php");
include_once (__DIR__ . "/../classes/User.php");
include_once (__DIR__ . "/../classes/Task.php");
include_once (__DIR__ . "/../classes/Statute.php");

$current_page = 'roadmap';

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'error.log');

$pdo = Db::getInstance();
$user = User::getUserById($pdo, $_SESSION["user_id"]);
$selectedStatute = 1;

if (!isset($_SESSION["user_id"]) && $user["isAdmin"] == "on") {
    header("Location: ../login.php?error=notLoggedIn");
    exit();
}

$pdo = Db::getInstance();
$user = User::getUserById($pdo, $_SESSION["user_id"]);
$statutes = Statute::getAll($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["statute"])) {
        $selectedStatute = $_POST["statute"];
    }

    if (isset($_POST["delete"])) {
        try {
            foreach ($_POST["delete"] as $id => $value) {
                $taskId = Task::getTaskById($pdo, $id);
                $position = $taskId["position"];

                Task::updatePositionOnDelete($pdo, $position, $selectedStatute);

                Task::deleteTask($pdo, $id);
            }
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
        }
    }
    
    if (isset($_POST['steps'])) {
        $tasks = $_POST['steps'];
        foreach ($tasks as $task) {
            try {
                Task::updateTasks($pdo, $task['id'], $task['label'], $task['question'], $task['answer'], $task['position'], $selectedStatute);
            } catch (Exception $e) {
                error_log('Database error: ' . $e->getMessage());
            }
        }
    }
}

$tasks = Task::getAllTasks($pdo);
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
    <div id="roadmapAdmin">
        <div class="top">
            <h1>Stappenplan</h1>
            <a href="#" class="btn" id="changePositionBtn"><i class="fa fa-sort" style="padding-right:8px"></i> Volgorde aanpassen</a>
            <a href="addTask.php" class="btn" id="appendTaskBtn"><i class="fa fa-plus" style="padding-right:8px"></i> Toevoegen</a>
        </div>
        <div class="steps">
            <h2>Stappen</h2>
            <form action="" method="post" id="statuteForm">
                <select name="statute" id="statute" style="width: max-content; margin-left: 24px;">
                    <option value="0" disabled <?php if ($selectedStatute == null) {echo "selected";} ?>>=== Selecteer statuut ===</option>
                    <?php foreach ($statutes as $statute): ?>
                        <option value="<?php echo $statute["id"] ?>" <?php if ($selectedStatute == $statute["id"]) {echo "selected";} ?>><?php echo $statute["title"] ?></option>
                    <?php endforeach ?>
                </select>
            </form>
            <div class="nav">
                <h3 class="topStepID">Stap</h3>
                <h3 class="topLabel">Label</h3>
                <h3 class="topQuestion">Vraag</h3>
                <h3 class="topAnswer">Antwoord</h3>
            </div>
            <form action="" method="post" id="tasksForm">
                <div id="dropzone">
                    <?php foreach ($tasks as $index => $task): ?>
                        <?php if ($task["statute_id"] != $selectedStatute) {
                            continue;
                        } ?>
                        <div class="step" draggable="false">
                            <a href="addTask.php?position=<?php echo $task["position"] ?>" class="addTaskBtn" data-taskid="<?php echo $task["position"] ?>">
                                <i class="fa fa-plus"></i>
                            </a>
                            <div class="stepContent">
                                <div class="text">
                                    <p class="stepID">Stap <?php echo $task["position"] ?> </p>
                                    <select name="steps[<?php echo $task["id"] ?>][label]" class="label">
                                        <option value="Start" <?php if ($task["label"] == "Start") {
                                            echo "selected";
                                        } ?>>Start</option>
                                        <option value="Aanvragen" <?php if ($task["label"] == "Aanvragen") {
                                            echo "selected";
                                        } ?>>Aanvragen</option>
                                    </select>
                                    <input type="text" name="steps[<?php echo $task["id"] ?>][question]" value="<?php echo $task["question"] ?>" class="question">
                                    <input type="text" name="steps[<?php echo $task["id"] ?>][answer]" value="<?php echo $task["answer"] ?>" class="answer">
                                    <input type="text" name="steps[<?php echo $task["id"] ?>][id]" value="<?php echo $task["id"] ?>" hidden>
                                    <input type="hidden" name="steps[<?php echo $task["id"] ?>][position]" class="task-position" value="<?php echo $index + 1; ?>">
                                </div>
                                <div class="icons">
                                    <span class="handle" style="display:none"><i class="fa fa-bars"></i></span>
                                    <label for="delete[<?php echo $task["id"] ?>]"><i class="fa fa-trash"></i></label>
                                    <input hidden type="submit" name="delete[<?php echo $task["id"] ?>]"
                                        id="delete[<?php echo $task["id"] ?>]">
                                </div>
                            </div>
                        </div>
    
                    <?php endforeach ?>
                </div>
                <button type="submit" class="btn" name="saveChanges" id="saveChangesBtn">Opslaan</button>
            </form>
        </div>
    </div>

    <script>

        const statute = document.getElementById('statute');
        const tasksForm = document.getElementById('tasksForm');

        statute.addEventListener('change', (e) => {
            document.getElementById('statuteForm').submit();
        });

    </script>
    
    <script src="../javascript/adminTask.js"></script>
</body>

</html>