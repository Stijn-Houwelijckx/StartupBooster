<?php
include_once (__DIR__ . "/classes/Question.php");
include_once (__DIR__ . "/classes/Db.php");
include_once (__DIR__ . "/classes/User.php");
session_start();
$current_page = 'helpdesk';
if (isset ($_SESSION["user_id"])) {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);

    try {
        $pdo = Db::getInstance();
    } catch (Exception $e) {
        error_log('Database error: ' . $e->getMessage());
    }
} else {
    header("Location: login.php?error=notLoggedIn");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StartupBooster - helpdesk</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/Favicon.svg">
</head>

<body>
    <?php include_once ('inc/nav.inc.php'); ?>
    <div id="helpdesk">
        <h1>Helpdesk</h1>
        <!--
        <div class="elements">
            <div class="FAQ">
                <div class="top">
                    <h2>FAQ</h2>
                    <div class="filter">
                        <select id="status">
                            <option value="student-zelfstandige" selected>Student-zelfstandige</option>
                            <option value="zelfstandige">Zelfstandige</option>
                        </select>
                        <i class="fa fa-filter"></i>
                    </div>
                </div>
                --
                <div class="questions">
                    <?php if ($questionsStudentZelfstandige !== null): ?>
                        <?php foreach ($questionsStudentZelfstandige as $questionStudentZelfstandige): ?>
                            <div class="question student-zelfstandige">
                                <div>
                                    <h3>
                                        <?php echo $questionStudentZelfstandige["question"]; ?>
                                    </h3>
                                    <i class="fa fa-angle-down"></i>
                                </div>
                                <p class="answer">
                                    <?php echo $questionStudentZelfstandige["answer"]; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="student-zelfstandige">No questions found.</p>
                    <?php endif; ?>
                    <?php if ($questionsZelfstandige !== null): ?>
                        <?php foreach ($questionsZelfstandige as $questionZelfstandige): ?>
                            <div class="question zelfstandige" style="display: none;">
                                <div>
                                    <h3>
                                        <?php echo $questionZelfstandige["question"]; ?>
                                    </h3>
                                    <i class="fa fa-angle-down"></i>
                                </div>
                                <p class="answer">
                                    <?php echo $questionZelfstandige["answer"]; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="zelfstandige">No questions found.</p>
                    <?php endif; ?>
                </div>
            </div> -->
        <div class="contactUs">
            <h2>Support die je echt verder helpt</h2>
            <p>Ons support team is dag en nacht bereikbaar om je te helpen.</p>
            <div class="supports">
                <div class="support">
                    <i class="fa fa-phone"></i>
                    <h3>Via telefoon</h3>
                    <a href="tel:+32476756736" class="btn">Bel ons gratis</a>
                    <a href="tel:+32476756736">+32 476 75 67 36</a>
                </div>
                <div class="support">
                    <i class="fa fa-envelope-o"></i>
                    <h3>Via mail</h3>
                    <a href="mailto:info@startupboost.com" class="btn">Contacteer ons via mail</a>
                    <p>We antwoorden binnen 24 uur</p>
                    <a href="mailto:info@startupboost.com">info@startupboost.com</a>
                </div>
                <div class="support">
                    <i class="fa fa-comment-o"></i>
                    <h3>Via livechat</h3>
                    <a href="#" class="btn">Chat met ons</a>
                    <p>Beschikbaar van 9u tot 17u</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // document.addEventListener('DOMContentLoaded', function () {
        //     let statusFilter = document.getElementById('status');
        //     let questionsToShow = document.querySelectorAll(".question");

        //     statusFilter.addEventListener('change', function () {
        //         let selectedOption = statusFilter.value;

        //         questionsToShow.forEach(function (question) {
        //             if (selectedOption === 'student-zelfstandige') {
        //                 if (question.classList.contains('student-zelfstandige')) {
        //                     question.style.display = 'block';
        //                 } else {
        //                     question.style.display = 'none';
        //                 }
        //             } else if (selectedOption === 'zelfstandige') {
        //                 if (question.classList.contains('zelfstandige')) {
        //                     question.style.display = 'block';
        //                 } else {
        //                     question.style.display = 'none';
        //                 }
        //             }
        //         });
        //     });
        //     let questions = document.querySelectorAll(".question");

        //     questions.forEach(function (question) {
        //         let questionArrow = question.querySelector(".fa");
        //         let answer = question.querySelector(".answer");

        //         question.addEventListener('click', function (e) {
        //             if (answer.classList.contains("show-answer")) {
        //                 answer.classList.remove("show-answer"); // Verwijder de klasse om het antwoord te verbergen
        //                 questionArrow.style.animation = "rotateIconReverse 0.5s forwards"; // Start de animatie om het pictogram terug te draaien
        //             } else {
        //                 answer.classList.add("show-answer"); // Voeg de klasse toe om het antwoord weer te geven
        //                 questionArrow.style.animation = "rotateIcon 0.5s forwards"; // Start de animatie om het pictogram te draaien
        //             }
        //         });
        //     });
        // });
    </script>
</body>

</html>