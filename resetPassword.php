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

// Logica voor het genereren van de resetcode en het versturen van e-mail
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset ($_POST['email'])) {
        $email = $_POST['email'];

        // Controleer of het e-mailadres een geldig formaat heeft
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Fout: Ongeldig e-mailformaat.";
        } else {
            // Controleer of het e-mailadres in de database bestaat
            $query = "SELECT COUNT(*) FROM users WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $userCount = $stmt->fetchColumn();

            if ($userCount > 0) {
                // Genereer een willekeurige 4-cijferige code
                $code = sprintf("%04d", mt_rand(0, 9999));

                // Sla de code op in de sessie
                $_SESSION['reset_code'] = $code;
                $_SESSION['reset_email'] = $email;

                // Sla de code op in de database
                $query = "UPDATE users SET code = :code WHERE email = :email";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':code', $code);
                $stmt->bindParam(':email', $email);
                if ($stmt->execute()) {
                    // E-mailconfiguratie
                    $to = $email;
                    $subject = "Herstelcode voor wachtwoordreset";
                    // Afbeelding naar base64 converteren
                    $img_path = 'https://i.ibb.co/1sxNZ7R/logoStartupBooster.png';
                    $img_type = pathinfo($img_path, PATHINFO_EXTENSION);
                    $img_data = file_get_contents($img_path);
                    $img_base64 = 'data:image/' . $img_type . ';base64,' . base64_encode($img_data);

                    // HTML-code voor e-mailbericht met inline afbeelding
                    $message = "
                        <html>
                        <head>
                            <title>Herstelcode voor wachtwoordreset</title>
                        </head>
                        <body>
                            <p>Beste gebruiker,</p>
                            <p>Je herstelcode voor wachtwoordreset is:</p>
                            <h2>$code</h2>
                            <p>Gebruik deze code om je wachtwoord opnieuw in te stellen.</p>
                            <p>Met vriendelijke groeten,<br>
                            Startup Booster</p>
                            <img src='$img_base64' alt='Logo' style='border:none; width:100px; height:100%'>
                        </body>
                        </html>
                        ";
                    $headers = "From: info@startupbooster.com\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                    // Verzend de e-mail
                    if (mail($to, $subject, $message, $headers)) {
                        $worked = "Herstelcode is verzonden naar $email";
                        header("Location: code.php");
                    } else {
                        $error = "Er is een fout opgetreden bij het verzenden van de herstelcode. Probeer het later opnieuw.";
                    }
                } else {
                    $error = "Er is een fout opgetreden bij het opslaan van de herstelcode in de database: " . $stmt->errorInfo()[2];
                }
            } else {
                $error = "Dit e-mailadres is niet geregistreerd.";
            }
        }
    } else {
        $error = "Fout: E-mailadres niet opgegeven.";
    }
}
?>


<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wachtwoord herstellen</title>
    <link rel="stylesheet" href="https://use.typekit.net/kqy0ynu.css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/x-icon" href="assets/images/Favicon.svg">
</head>

<body>
    <div id="login">
        <div class="text">
            <h1>Wachtwoord herstellen</h1>
            <p>Vul je e-mailadres in en ontvang een herstelcode om je wachtwoord opnieuw in te stellen.</p>

            <?php if ($error !== ''): ?>
                <p class="alert">
                    <?php echo $error; ?>
                </p>
            <?php endif; ?>

            <?php if ($worked !== ''): ?>
                <p class="alert success">
                    <?php echo $worked; ?>
                </p>
            <?php endif; ?>

            <form class="form form--login" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="column">
                    <label for="email">E-mailadres</label>
                    <input type="email" id="email" name="email" placeholder="Vul je e-mailadres in" required>
                </div>
                <button type="submit" class="btn" id="btnsignup">Volgende</button>
            </form>
            <div class="row">
                <p>Geen code ontvangen?</p>
                <a href="#" class="active">Opnieuw versturen</a>
            </div>
        </div>
        <div class="image"></div>
    </div>
</body>

</html>