<?php
include_once (__DIR__ . "/classes/Db.php");

$error = "";
$worked = "";

// Databaseverbinding
try {
    $db = Db::getInstance();
} catch (PDOException $e) {
    die ("Database connection failed: " . $e->getMessage());
}

// Logic for updating the password in the database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset ($_POST['password']) && isset ($_POST['password2'])) {
        $password = $_POST['password'];
        $password2 = $_POST['password2'];

        // Check if both passwords match
        if ($password === $password2) {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Update the password in the database
            $query = "UPDATE users SET password = :password";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':password', $hashed_password);

            if ($stmt->execute()) {
                $worked = "Wachtwoord succesvol bijgewerkt.";
                header("Location: login.php");
            } else {
                $error = "Er is een fout opgetreden bij het bijwerken van het wachtwoord in de database.";
            }
        } else {
            $error = "De ingevoerde wachtwoorden komen niet overeen. Probeer het opnieuw.";
        }
    } else {
        $error = "Vul beide wachtwoorden in.";
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
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>" />
    <link rel="icon" type="image/x-icon" href="assets/images/Favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div id="login">
        <div class="text">
            <h1>Wachtwoord vergeten?</h1>
            <p>Geef een nieuw wachtwoord in.</p>
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
                <div class="column passwordInput">
                    <label for="password">Nieuw wachtwoord</label>
                    <div class="row">
                        <input type="password" id="password" name="password" placeholder="Wachtwoord">
                        <i class="fa fa-eye-slash"></i>
                    </div>
                </div>
                <div class="column passwordInput">
                    <label for="password2">Herhaal nieuw wachtwoord</label>
                    <div class="row">
                        <input type="password" id="password2" name="password2" placeholder="Herhaal wachtwoord">
                        <i class="fa fa-eye-slash"></i>
                    </div>
                </div>
                <button type="submit" class="btn" id="btnsignup">Wachtwoord herstellen</button>
            </form>
            <div class="row">
                <p>Geen code ontvangen?</p>
                <a href="code.php?resend=true" class="active">Opnieuw versturen</a>
            </div>
        </div>
        <div class="image"></div>
    </div>

    <script>
        var eyeIcons = document.querySelectorAll('.fa-eye-slash');
        eyeIcons.forEach(function (eyeIcon) {
            eyeIcon.addEventListener('click', function () {
                var passwordInput = this.parentElement.querySelector('input[type="password"], input[type="text"]');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                } else {
                    passwordInput.type = 'password';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                }
            });
        });
    </script>
</body>

</html>