<?php
include_once (__DIR__ . "../../classes/Db.php");
include_once (__DIR__ . "../../classes/User.php");
include_once (__DIR__ . "../../classes/Chat.php");
include_once (__DIR__ . "../../classes/Message.php");

$pdo = Db::getInstance();
$user = User::getUserById($pdo, $_SESSION["user_id"]);
$firstnameAdmin = Chat::getAdminName($pdo, $_SESSION["user_id"]);
$profilePictureAdmin = Chat::getAdminProfilePicture($pdo, $_SESSION["user_id"]);
$profilePictureUser = Chat::getMyProfilePicture($pdo, $_SESSION["user_id"]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["startChat"])) {
        try {
            $chat = new Chat;
            $chat->setUser_id($_SESSION["user_id"]);
            $getAllAdminsThatHaveNoChat = Chat::getAvailableAdmin($pdo, $_SESSION["user_id"]);
            if ($getAllAdminsThatHaveNoChat !== null && !empty($getAllAdminsThatHaveNoChat)) {
                $randomKey = array_rand($getAllAdminsThatHaveNoChat);
                $randomAdminThatHaveNoChat = $getAllAdminsThatHaveNoChat[$randomKey];

                $randomAdminId = $randomAdminThatHaveNoChat['id'];
                $chat->setAdmin_id($randomAdminId);
            }

            $howManyChats = Chat::howManyChats($pdo, $_SESSION["user_id"]);
            if ($howManyChats == null) {
                $chat->addChat($pdo);
            }
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
        }
    }

    if (isset($_POST["message"])) {
        try {
            $message = new Message;
            $messageContent = $_POST["message"];
            $message->setSender_id($_SESSION["user_id"]);

            // Haal de admin ID op
            $receiverId = Chat::getAdminId($pdo, $_SESSION["user_id"]);
            if ($receiverId !== null) {
                // Alleen als er een admin ID is gevonden
                $message->setMessage($messageContent);
                $message->setReceiver_id($receiverId);
                var_dump($messageContent);
                var_dump($receiverId);
                $message->addMessage($pdo);
                var_dump($message);
            } else {
                // Handel het geval af waarin geen admin ID is gevonden
                // Hier kun je een foutmelding weergeven of iets anders doen
                echo "Er is geen admin gevonden om het bericht naar te sturen.";
            }
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
        }
    }

    if (isset($_POST["leaveChat"])) {
        try {
            $user_id = $_SESSION["user_id"];
            Chat::deleteChat($pdo, $user_id);
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
        }
    }
}


$messages = Message::getAll($pdo, $_SESSION["user_id"]);
?>

<form action="" method="post">
    <input type="text" name="startChat" hidden>
    <button type="submit" class="chatButton">
        <i class="fa fa-comment-o"></i>
        <p>Chatten</p>
    </button>
</form>

<div class="chat">
    <div class="top">
        <i class="fa fa-plus"></i>
        <div class="row">
            <div class="profilePictureAdmin" style="background-image: url('<?php echo $profilePictureAdmin ?>')"></div>
            <div class="column">
                <span>Chat met</span>
                <h3><?php echo $firstnameAdmin ?></h3>
            </div>
        </div>
    </div>
    <div class="center">
        <p>Vandaag 18:34</p>
        <div class="row admin">
            <div class="profilePicture" style="background-image: url('<?php echo $profilePictureAdmin ?>')">
            </div>
            <p>Hey, hallo! Met David hier, hoe kan ik u helpen?</p>
        </div>
        <?php if ($messages !== null): ?>
            <?php foreach ($messages as $message): ?>
                <div
                    class="row <?php echo (isset($message['sender_id']) && $message['sender_id'] == $_SESSION["user_id"]) ? 'admin' : 'user'; ?>">
                    <div class="profilePicture" style="background-image: url('<?php if (isset($message['sender_id']) && $message['sender_id'] == $_SESSION["user_id"]) {
                        echo $profilePictureUser;
                    } else {
                        echo $profilePictureAdmin;
                    } ?>');"></div>
                    <p>
                        <?php echo $message["message"]; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class=" bottom">
        <form action="" method="post">
            <input type="text" name="message" placeholder="Schrijf uw bericht...">
            <p class="border"></p>
            <div class="row">
                <i class="fa fa-paperclip"></i>
                <button class="btn" type="submit">Verzenden</button>
            </div>
        </form>
        <form action="" method="POST">
            <input type="text" hidden name="leaveChat" value="<?php echo $_SESSION["user_id"] ?>">
            <button type="submit">Leave</button>
        </form>
    </div>
</div>

<script>
    document.querySelector(".chatButton").addEventListener("click", function (e) {
        e.preventDefault(); // Voorkom standaard formulierversending

        // AJAX verzoek om chat te starten
        var xhrStartChat = new XMLHttpRequest();
        xhrStartChat.open("POST", ""); // lege string betekent dat het naar dezelfde URL wordt verzonden als het huidige document
        xhrStartChat.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhrStartChat.onload = function () {
            if (xhrStartChat.status >= 200 && xhrStartChat.status < 300) {
                document.querySelector(".chat").style.display = "flex"; // Toon chatvenster
            } else {
                console.error('Er is een fout opgetreden bij het starten van de chat.');
            }
        };
        xhrStartChat.onerror = function () {
            console.error('Er is een fout opgetreden bij het maken van het verzoek.');
        };
        // Verzend het verzoek om de chat te starten
        xhrStartChat.send("startChat=");
    });

    // document.querySelector(".chat .fa-plus").addEventListener("click", function (e) {
    //     e.preventDefault(); // Voorkom standaard gedrag van de link

    //     // AJAX verzoek om chatvenster te sluiten
    //     var xhrCloseChat = new XMLHttpRequest();
    //     xhrCloseChat.open("POST", ""); // lege string betekent dat het naar dezelfde URL wordt verzonden als het huidige document
    //     xhrCloseChat.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    //     xhrCloseChat.onload = function () {
    //         if (xhrCloseChat.status >= 200 && xhrCloseChat.status < 300) {
    //             document.querySelector(".chat").style.display = "none"; // Verberg chatvenster
    //         } else {
    //             console.error('Er is een fout opgetreden bij het sluiten van de chat.');
    //         }
    //     };
    //     xhrCloseChat.onerror = function () {
    //         console.error('Er is een fout opgetreden bij het maken van het verzoek.');
    //     };
    //     // Verzend het verzoek om de chat te sluiten
    //     xhrCloseChat.send("closeChat=");
    // });
</script>