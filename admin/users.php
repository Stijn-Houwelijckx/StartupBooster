<?php
include_once (__DIR__ . "../../classes/Db.php");
include_once (__DIR__ . "../../classes/User.php");
session_start();

$pdo = Db::getInstance();


if (isset($_SESSION["user_id"])) {
    $user = User::getUserById($pdo, $_SESSION["user_id"]);
    $current_page = 'users';
} else {
    header("Location: login.php?error=notLoggedIn");
    exit();
}

$selectedUser = User::getUserById($pdo, 0);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["user_id"])) {
        try {
            $user_id = $_POST["user_id"];
            var_dump($user_id);
            $selectedUser = User::getUserById($pdo, $user_id);

        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
        }
    }
}

$users = User::getAll($pdo);
?>

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
    <div id="users">
        <h1>Gebruikers</h1>
        <form action="" id="userSelector" onchange="submitUserForm()" method="post">
            <select name="user_id">
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user["id"] ?>">
                        <?php echo htmlspecialchars($user["firstname"]) . " " . htmlspecialchars($user["lastname"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <div class="users">
            <?php if (!empty($users)): ?>
                <form action="" method="post">
                    <div class="user">
                        <div class="text">
                            <input type="text" name="firstname"
                                value="<?php echo htmlspecialchars($selectedUser["firstname"]); ?>">
                            <input type="text" name="lastname"
                                value="<?php echo htmlspecialchars($selectedUser["lastname"]); ?>">
                            <input type="text" name="statue_id"
                                value="<?php echo htmlspecialchars($selectedUser["statute_id"]); ?>">
                            <input type="text" name="sector_id"
                                value="<?php echo htmlspecialchars($selectedUser["sector_id"]); ?>">
                            <input type="text" name="email" value="<?php echo htmlspecialchars($selectedUser["email"]); ?>">
                            <input type="text" name="phoneNumber"
                                value="<?php echo htmlspecialchars($selectedUser["phoneNumber"]); ?>">
                            <input type="text" name="street"
                                value="<?php echo htmlspecialchars($selectedUser["street"]); ?>">
                            <input type="text" name="houseNumber"
                                value="<?php echo htmlspecialchars($selectedUser["houseNumber"]); ?>">
                            <input type="text" name="zipCode"
                                value="<?php echo htmlspecialchars($selectedUser["zipCode"]); ?>">
                            <input type="text" name="city" value="<?php echo htmlspecialchars($selectedUser["city"]); ?>">
                            <input type="text" name="signupDate"
                                value="<?php echo htmlspecialchars($selectedUser["signupDate"]); ?>">
                            <input type="text" name="id" hidden
                                value="<?php echo htmlspecialchars($selectedUser["id"]); ?>">
                        </div>
                    </div>
                    <div class="buttons">
                        <button type="submit" class="btn">Toevoegen</button>
                        <input type="hidden" name="question" value="<?php echo $selectedUser["id"]; ?>">
                        <button type="submit" class="btn remove">Verwijderen</button>
                    </div>
                </form>
            <?php else: ?>
                <p>No subsidies found.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function submitUserForm() {
            document.getElementById("userSelector").submit();
        }
    </script>
</body>

</html>