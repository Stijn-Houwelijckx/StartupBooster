<?php
include_once (__DIR__ . "../../classes/Db.php");
include_once (__DIR__ . "../../classes/User.php");
include_once (__DIR__ . "../../classes/Task.php");
session_start();

if (isset($_SESSION["user_id"])) {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);
    $current_page = 'roadmap';
} else {
    header("Location: login.php?error=notLoggedIn");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["question"])) {
        try {
            $pdo = Db::getInstance();
            $question = $_POST["question"];
            var_dump($question);
            $delete = Task::deleteTask($pdo, $question);

            var_dump($delete);

        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
        }
    }
}

$steps = Task::getTasks($pdo, $_SESSION["user_id"]);
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
        <h1>Stappenplan</h1>
        <div class="steps">
            <h2>Stappen</h2>
            <div class="top">
                <h3 class="topStepID">Stap</h3>
                <h3 class="topLabel">Label</h3>
                <h3 class="topQuestion">Vraag</h3>
                <h3 class="topAnswer">Antwoord</h3>
            </div>
            <?php foreach ($steps as $step): ?>
                <div class="step">
                    <div class="text">
                        <p class="stepID">Stap
                            <?php echo $step["id"] ?>
                        </p>
                        <p class="label">
                            <?php echo $step["label"] ?>
                        </p>
                        <p class="question">
                            <?php echo $step["question"] ?>
                        </p>
                        <p class="answer">
                            <?php echo $step["answer"] ?>
                        </p>
                    </div>
                    <div class="icons">
                        <form method="post" action="addSubsidie.php">
                            <input type="hidden" name="edit_task_question"
                                value="<?php echo htmlspecialchars($step["question"]); ?>">
                            <button type="submit" class="edit"><i class="fa fa-edit"></i></button>
                        </form>
                        <form method="post">
                            <input type="hidden" name="question" value="<?php echo htmlspecialchars($step["question"]); ?>">
                            <button type="submit" class="delete"><i class="fa fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</body>

</html>