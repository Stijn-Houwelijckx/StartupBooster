<?php
include_once (__DIR__ . "/classes/Task.php");
include_once (__DIR__ . "/classes/Db.php");

try {
    $pdo = Db::getInstance();
    $finished_steps = Task::getProgress($pdo);
    $tasks = Task::getTasks($pdo);
    $activeTask = Task::getActiveTask($pdo);
    $activeTaskString = implode(', ', $activeTask); // Convert array to string

    $progress = ($finished_steps["finished_steps"] / $finished_steps["total_steps"]) * 100;
    $progressRounded = number_format($progress, 0);
    $tasks = array_reverse($tasks);

    if (isset ($_POST['taskId'])) {
        $taskId = filter_input(INPUT_POST, 'taskId', FILTER_SANITIZE_NUMBER_INT);
        if ($taskId) {
            Task::updateRead($pdo, $taskId);
        } else {
            error_log('Invalid taskId received.');
        }
    }
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
</head>

<body>
    <?php include_once ('inc/nav.inc.php'); ?>
    <div id="roadmap">
        <h1>Stappenplan</h1>
        <div class="elements">
            <div class="bar">
                <p class="border"></p>
            </div>
            <div class="tasks">
                <?php if (!empty ($tasks)): ?>
                    <?php foreach ($tasks as $task): ?>
                        <?php
                        if ($task["id"] == $activeTaskString) {
                            $taskClasses = "task active";
                        } else if ($task["done"] == 1) {
                            $taskClasses = "task inactive";
                        } else {
                            $taskClasses = "task";
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
                                    <?php
                                    if ($task["id"] == $activeTaskString) {
                                        echo '<p>Gelezen</p>';
                                        echo '<form id="readForm' . $task['id'] . '" method="POST" onsubmit="submitReadForm(event, ' . $task['id'] . ')">';
                                        if ($task["done"] == 0) {
                                            // If task is active and not done
                                            echo '<i id="icon' . $task['id'] . '" class="fa fa-square-o" onclick="submitReadForm(event, ' . $task['id'] . ')"></i>';
                                        } else {
                                            // If task is active and done
                                            echo '<i id="icon' . $task['id'] . '" class="fa fa-check-square-o" onclick="submitReadForm(event, ' . $task['id'] . ')"></i>';
                                        }
                                        echo '<input type="hidden" name="taskId" value="' . $task['id'] . '">';
                                        echo '</form>';
                                    }
                                    ?>
                                </div>
                            </div>


                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No tasks found.</p>
                <?php endif; ?>
            </div>

        </div>
        <div class="progress">
            <div class="row">
                <p>Voortgangsbalk</p>
                <p>
                    <?php echo $progressRounded . "%" ?>
                </p>
            </div>
            <div class="bars">
                <p class="bar1"></p>
                <p class="bar2" style="width: <?php echo $progressRounded ?>%"></p>
            </div>
        </div>
    </div>

    <script>
        function submitReadForm(event, taskId) {
            event.preventDefault();
            var form = document.getElementById('readForm' + taskId);
            var formData = new FormData(form);
            fetch(form.action, {
                method: form.method,
                body: formData
            })
                .then(response => {
                    if (response.ok) {
                        console.log('Form submission success.');
                        // Herlaad de pagina nadat het formulier met succes is verzonden
                        location.reload();
                    } else {
                        console.error('Form submission failed.');
                    }
                })
                .catch(error => {
                    console.error('Error occurred during form submission:', error);
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const activeTask = document.querySelector('.tasks .active');
            if (activeTask) {
                window.scrollTo({
                    top: activeTask.offsetTop,
                    behavior: 'smooth'
                });
            }


            document.querySelectorAll('.task').forEach(task => {
                const icon = task.querySelector('.fa');
                if (icon) {
                    icon.addEventListener('click', function (e) {
                        e.stopPropagation(); // Voorkom dat het klikken op het icoontje de klik op de hele taak activeert

                        const row = task.querySelector('.row.display');
                        const answer = task.querySelector('.answer');

                        if (row && answer) {
                            // Toggle de display-stijl van de rij en het antwoord
                            row.style.display = 'none';
                            answer.style.display = 'none';
                            icon.classList.remove(" fa-angle-up"); icon.classList.add("fa-angle-down");
                        }
                    });
                }
                task.addEventListener('click', function (e) {
                    const row = task.querySelector('.row.display');
                    const answer = task.querySelector('.answer');
                    const icon = task.querySelector('.fa');

                    if (row && answer && icon) {
                        // Als het klikken niet op het icoontje was, open de taak
                        if (!e.target.classList.contains('fa')) {
                            // Toggle de display-stijl van de rij en het antwoord
                            row.style.display = row.style.display === 'flex' ? 'none' : 'flex';
                            answer.style.display = answer.style.display === 'flex' ? 'none' : 'flex';

                            // Toggle tussen de klassen van het icoontje
                            icon.classList.toggle("fa-angle-down");
                            icon.classList.toggle("fa-angle-up");
                        }
                    }
                });
            }); document.querySelectorAll(".task .display i").forEach(icon => {
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