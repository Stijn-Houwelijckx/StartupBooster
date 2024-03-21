<?php
include_once (__DIR__ . "/classes/Task.php");
include_once (__DIR__ . "/classes/Db.php");

try {
    $pdo = Db::getInstance();
    $tasks = Task::getTasks($pdo);
} catch (Exception $e) {
    error_log('Database error: ' . $e->getMessage());
    $tasks = [];
}

$current_page = 'tasks';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StartupBooster - helpdesk</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <?php include_once ('inc/nav.inc.php'); ?>
    <div id="roadmap">
        <h1>Stappenplan</h1>
        <div class="tasks">
            <?php if (!empty ($tasks)): ?>
                <?php foreach ($tasks as $task): ?>
                    <p>
                        <?php echo htmlspecialchars($task["label"]); ?>
                    </p>
                    <p>
                        <?php echo htmlspecialchars($task["question"]); ?>
                    </p>
                    <p>
                        <?php echo htmlspecialchars($task["answer"]); ?>
                    </p>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No tasks found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>