<?php
include_once (__DIR__ . "../../classes/Db.php");
include_once (__DIR__ . "../../classes/User.php");
include_once (__DIR__ . "../../classes/Chat.php");
include_once (__DIR__ . "../../classes/Message.php");

$pdo = Db::getInstance();
$user = User::getUserById($pdo, $_SESSION["user_id"]);
$firstnameAdmin = Chat::getAdminName($pdo, $_SESSION["user_id"]);
$profilePictureReceiver = "";
$profilePictureUser = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["startChat"]) && $user["isAdmin"] == "off") {
        try {
            $chat = new Chat;
            $chat->setUser_id($_SESSION["user_id"]);
            $getAllAdminsThatHaveNoChat = Chat::getAvailableAdmin($pdo);
            var_dump($getAllAdminsThatHaveNoChat);
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
            $message->setChat_id($chat_id);
            $messageContent = $_POST["message"];
            $message->setSender_id($_SESSION["user_id"]);
            $receiverIds = Chat::getReceiverId($pdo, $_SESSION["user_id"]);
            if ($user["isAdmin"] == "on") {
                $receiverId = $receiverIds[0]["user_id"];
            } else {
                $receiverId = $receiverIds[0]["admin_id"];
            }
            if ($receiverId !== null) {
                $message->setMessage($messageContent);
                $message->setReceiver_id($receiverId);

                $message->addMessage($pdo);
            } else {
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

if ($messages != null) {
    $message = new Message;
    $chat_id = Message::getChatIdFunction($pdo, $_SESSION["user_id"]);
    $profilePictures = Chat::getProfilePictures($pdo, $chat_id);
    $profilePictureReceiver = $profilePictures[0]["profileImg"];
    $profilePictureUser = $profilePictures[1]["profileImg"];
}

if ($user["isAdmin"] == "on") {
    $profilePictureReceiver = "../" . $profilePictureReceiver;
    $profilePictureUser = "../" . $profilePictureUser;
}
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
            <div class="profilePictureAdmin" style="background-image: url('<?php echo $profilePictureUser ?>')">
            </div>
            <div class="column">
                <span>Chat met</span>
                <h3><?php echo $firstnameAdmin ?></h3>
            </div>
        </div>
    </div>
    <div class="center">
        <p>Vandaag 18:34</p>
        <?php if ($messages !== null): ?>
            <?php foreach ($messages as $message): ?>
                <div class="row <?php echo ($message['sender_id'] == $_SESSION["user_id"]) ? 'user' : 'admin'; ?>">
                    <div class="profilePicture"
                        style="background-image: url('<?php echo ($message['sender_id'] == $_SESSION["user_id"]) ? $profilePictureReceiver : $profilePictureUser; ?>');">
                    </div>
                    <p><?php echo $message["message"]; ?></p>
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
            <input type="text" hidden name="leaveChat"></input>
            <button type="submit" class="signout"><i class="fa fa-sign-out"></i></button>
        </form>
    </div>
</div>

<!-- <script>
    document.querySelector(".chatButton").addEventListener("click", function (e) {
        e.preventDefault();

        var xhrStartChat = new XMLHttpRequest();
        xhrStartChat.open("POST", "");
        xhrStartChat.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhrStartChat.onload = function () {
            if (xhrStartChat.status >= 200 && xhrStartChat.status < 300) {
                document.querySelector(".chat").style.display = "flex";
            } else {
                console.error('Er is een fout opgetreden bij het starten van de chat.');
            }
        };
        xhrStartChat.onerror = function () {
            console.error('Er is een fout opgetreden bij het maken van het verzoek.');
        };
        xhrStartChat.send("startChat=");
    });

    document.querySelector(".chat .fa-plus").addEventListener("click", function (e) {
        document.querySelector(".chat").style.display = "none";
    });
</script> -->