<?php
include_once (__DIR__ . "/classes/Task.php");
include_once (__DIR__ . "/classes/Db.php");

try {
    $pdo = Db::getInstance();
    $tasks = Task::getTasks($pdo);
    // Omkeren van de volgorde van de taken
    $tasks = array_reverse($tasks);
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
    <link rel="icon" type="image/x-icon" href="assets/images/Favicon.svg">
    <style>
        <?php foreach ($tasks as $task): ?>
            <?php $backgroundColor = ($task["done"] == 1) ? "var(--blue)" : "#DADADA"; ?>
            #roadmap .tasks .task-<?php echo $task['id']; ?>:before {
                content: '';
                position: absolute;
                left: -38px;
                top: 50%;
                transform: translateY(-50%);
                width: 24px;
                height: 24px;
                border-radius: 50%;
                background-color:
                    <?php echo $backgroundColor; ?>
                ;
            }

        <?php endforeach; ?>
    </style>
</head>

<body>
    <?php include_once ('inc/nav.inc.php'); ?>
    <div id="roadmap">
        <h1>Stappenplan</h1>
        <div class="elements">
            <div class="bar">
                <p class="border"></p>
            </div>
            <div class="tasks mobile">
                <?php if (!empty ($tasks)): ?>
                    <?php foreach ($tasks as $task): ?>
                        <?php
                        $taskClasses = "task task-" . $task['id'];
                        if ($task["done"] == 1) {
                            $taskClasses .= " active"; // Voeg de klassen toe voor de eerste blauwe taak
                        }
                        ?>
                        <div class="<?php echo $taskClasses; ?>">
                            <p class="label">
                                <?php echo htmlspecialchars($task["label"]); ?>
                            </p>
                            <div class="row">
                                <p class="question">
                                    <?php echo htmlspecialchars($task["question"]); ?>
                                </p>
                                <?php if ($task["done"] == 1): ?>
                                    <i class="fa fa-angle-up" data-task="<?php echo $task['id']; ?>"></i>
                                <?php else: ?>
                                    <i class="fa fa-angle-down" data-task="<?php echo $task['id']; ?>"></i>
                                <?php endif; ?>
                            </div>
                            <p class="answer">
                                <?php echo htmlspecialchars($task["answer"]); ?>
                            </p>
                            <div class="row display">
                                <a class="readmore">Lees meer</a>
                                <div>
                                    <p>Gelezen</p>
                                    <i class="fa fa-square-o"></i>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No tasks found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const activeTask = document.querySelector('.tasks .active');
            if (activeTask) {
                window.scrollTo({
                    top: activeTask.offsetTop,
                    behavior: 'smooth' // Optioneel, maakt het scrollen soepel
                });
            }


            document.querySelectorAll('.fa.fa-angle-down').forEach(icon => {
                icon.addEventListener('click', function () {
                    if (icon.classList.contains("fa-angle-down")) {
                        icon.classList.remove("fa-angle-down");
                        icon.classList.add("fa-angle-up");
                    } else {
                        icon.classList.remove("fa-angle-up");
                        icon.classList.add("fa-angle-down");
                    }

                    const taskId = this.getAttribute('data-task');
                    const answer = document.querySelector('.task.task-' + taskId + ' .answer');
                    const displayRow = document.querySelector('.task.task-' + taskId + ' .display'); // Selecteer de bijbehorende display-rij

                    if (answer.style.display === 'flex') {
                        answer.style.display = 'none';
                        displayRow.style.display = 'none'; // Verberg ook de bijbehorende display-rij
                    } else {
                        answer.style.display = 'flex';
                        answer.style.transition = "opacity 0.5s ease"; // Aangepaste overgangseffect voor het antwoord
                        displayRow.style.display = 'flex'; // Toon ook de bijbehorende display-rij
                    }
                });
            });

            document.querySelectorAll('.fa.fa-angle-up').forEach(icon => {
                icon.addEventListener('click', function () {
                    if (icon.classList.contains("fa-angle-up")) {
                        icon.classList.remove("fa-angle-up");
                        icon.classList.add("fa-angle-down");
                    } else {
                        icon.classList.remove("fa-angle-down");
                        icon.classList.add("fa-angle-up");
                    }

                    const taskId = this.getAttribute('data-task');
                    const answer = document.querySelector('.task.task-' + taskId + ' .answer');
                    const displayRow = document.querySelector('.task.task-' + taskId + ' .display'); // Selecteer de bijbehorende display-rij

                    if (answer.style.display === 'none') {
                        answer.style.display = 'flex';
                        displayRow.style.display = 'flex'; // Verberg ook de bijbehorende display-rij
                    } else {
                        answer.style.display = 'none';
                        answer.style.transition = "opacity 0.5s ease"; // Aangepaste overgangseffect voor het antwoord
                        displayRow.style.display = 'none'; // Toon ook de bijbehorende display-rij
                    }
                });
            });


            document.querySelectorAll(".task .display i").forEach(icon => {
                icon.addEventListener('click', function () {
                    if (icon.classList.contains("fa-square-o")) {
                        icon.classList.remove("fa-square-o");
                        icon.classList.add("fa-check-square-o");
                    } else {
                        icon.classList.remove("fa-check-square-o");
                        icon.classList.add("fa-square-o");
                    }
                });
            });

        });

    </script>
</body>

</html>