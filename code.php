<?php
include_once (__DIR__ . "/classes/Db.php");

session_start();

$error = '';
$worked = '';

// Databaseverbinding
try {
    $db = Db::getInstance();
} catch (PDOException $e) {
    die ("Database connection failed: " . $e->getMessage());
}

// Logic for comparing the input code with the code from the database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset ($_POST['code'])) {
        $input_code = $_POST['code'];

        // Retrieve email and code from session
        if (isset ($_SESSION['reset_email']) && isset ($_SESSION['reset_code'])) {
            $email = $_SESSION['reset_email'];
            $reset_code = $_SESSION['reset_code'];

            // Check if the input code matches the code from the session
            if ($input_code === $reset_code) {
                // Code matches, proceed with your logic
                $worked = "Correcte herstelcode ingevoerd.";
                header("Location: newPassword.php");
                // Clear session variables
                unset($_SESSION['reset_email']);
                unset($_SESSION['reset_code']);
            } else {
                $error = "Ongeldige herstelcode ingevoerd. Probeer het opnieuw.";
            }
        } else {
            $error = "Herstelcode niet gevonden in de sessie. Probeer opnieuw.";
        }
    } else {
        $error = "Herstelcode niet ingevoerd.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>StartupBooster - Wachtwoord vergeten?</title>
    <link rel="stylesheet" href="https://use.typekit.net/kqy0ynu.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="icon" type="image/x-icon" href="assets/images/Favicon.svg">
</head>

<body>
    <div id="login">
        <div class="text">
            <h1>Wachtwoord vergeten?</h1>
            <?php if ($error !== ''): ?>
                <p class="alert">
                    <?php echo $error ?>
                </p>
            <?php endif; ?>
            <?php if ($worked !== ''): ?>
                <p class="alert success">
                    <?php echo $worked ?>
                </p>
            <?php endif; ?>
            <form class="form form--login" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="column">
                    <label for="code">Herstelcode</label>
                    <input type="code" id="code" name="code" placeholder="4-cijfer code" required>
                </div>
                <button type="submit" class="btn" id="btnsignup">Volgende</button>
            </form>
            <div class="row">
                <p>Geen code ontvangen?</p>
                <a href="code.php?resend=true" class="active">Opnieuw versturen</a>
            </div>
        </div>
        <div class="image"></div>
    </div>
</body>

</html>