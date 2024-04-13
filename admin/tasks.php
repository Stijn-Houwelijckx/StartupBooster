<?php
session_start();

include_once (__DIR__ . "/../classes/Db.php");
include_once (__DIR__ . "/../classes/User.php");
include_once (__DIR__ . "/../classes/Task.php");

$current_page = 'roadmap';

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'error.log');

$pdo = Db::getInstance();
$user = User::getUserById($pdo, $_SESSION["user_id"]);

if (!isset($_SESSION["user_id"]) && $user["isAdmin"] == "on") {
    header("Location: ../login.php?error=notLoggedIn");
    exit();
}

$pdo = Db::getInstance();
$user = User::getUserById($pdo, $_SESSION["user_id"]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["delete"])) {
        try {
            foreach ($_POST["delete"] as $id => $value) {
                Task::deleteTask($pdo, $id);
            }
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
        }
    }

    if (isset($_POST['steps'])) {
        $steps = $_POST['steps'];
        foreach ($steps as $step) {
            try {
                Task::updateTasks($pdo, $step['id'], $step['label'], $step['question'], $step['answer']);
            } catch (Exception $e) {
                error_log('Database error: ' . $e->getMessage());
            }
        }
    }
}

$steps = Task::getAllTasks($pdo);
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
            <a href="addTask.php" class="btn"><i class="fa fa-plus" style="padding-right:8px"></i> Toevoegen</a>
        </div>
        <div class="steps">
            <h2>Stappen</h2>
            <div class="nav">
                <h3 class="topStepID">Stap</h3>
                <h3 class="topLabel">Label</h3>
                <h3 class="topQuestion">Vraag</h3>
                <h3 class="topAnswer">Antwoord</h3>
            </div>
            <form action="" method="post">
                <?php foreach ($steps as $step): ?>
                    <div class="step">
                        <a href="addTask.php?position=<?php echo $step["position"] ?>" class="addTaskBtn" data-taskid="<?php echo $step["position"] ?>">
                            <i class="fa fa-plus"></i>
                        </a>
                        <div class="stepContent">
                            <div class="text">
                                <p class="stepID">Stap <?php echo $step["position"] ?> </p>
                                <select name="steps[<?php echo $step["id"] ?>][label]" class="label">
                                    <option value="Start" <?php if ($step["label"] == "Start") {
                                        echo "selected";
                                    } ?>>Start</option>
                                    <option value="Aanvragen" <?php if ($step["label"] == "Aanvragen") {
                                        echo "selected";
                                    } ?>>Aanvragen</option>
                                </select>
                                <input type="text" name="steps[<?php echo $step["id"] ?>][question]"
                                    value="<?php echo $step["question"] ?>" class="question">
                                <input type="text" name="steps[<?php echo $step["id"] ?>][answer]"
                                    value="<?php echo $step["answer"] ?>" class="answer">
    
                                <input type="text" name="steps[<?php echo $step["id"] ?>][id]" value="<?php echo $step["id"] ?>"
                                    hidden>
                            </div>
                            <div class="icons">
                                <label for="delete[<?php echo $step["id"] ?>]"><i class="fa fa-trash"></i></label>
                                <input hidden type="submit" name="delete[<?php echo $step["id"] ?>]"
                                    id="delete[<?php echo $step["id"] ?>]">
                            </div>
                        </div>
                    </div>

                <?php endforeach ?>
                <button type="submit" class="btn" name="saveChanges">Opslaan</button>
            </form>
        </div>
    </div>

    <script>

    </script>
    
    <script src="../javascript/adminTask.js"></script>
</body>

</html>