<?php
include_once (__DIR__ . "../../classes/Db.php");
include_once (__DIR__ . "../../classes/User.php");
include_once (__DIR__ . "../../classes/Chat.php");
include_once (__DIR__ . "../../classes/Message.php");

$pdo = Db::getInstance();
$user = User::getUserById($pdo, $_SESSION["user_id"]);

if ($user["isAdmin"] == "on") {
    $pathExtention = "../";
} else {
    $pathExtention = "";
}

$receiverName = "David";
$adminProfilePicture = "assets/images/Stijn.jpg";
$userProfilePicture = "assets/images/Tom.jpg";

if (isset($_SESSION["user_id"])) {
    // Check if the user has an active chat

    if (Chat::hasActiveChat($pdo, $_SESSION["user_id"])) {
        // If the user has an active chat, get the chat id    
        $chat = Chat::getActiveChat($pdo, $_SESSION["user_id"]);

        // Get receiver info
        if ($user["isAdmin"] == "on") {
            $receiverName = User::getUserById($pdo, $chat["user_id"])["firstname"];
        } else {
            $receiverName = User::getUserById($pdo, $chat["admin_id"])["firstname"];
        }

        // Get profile pictures
        $adminProfilePicture = User::getUserById($pdo, $chat["admin_id"])["profileImg"];
        $userProfilePicture = User::getUserById($pdo, $chat["user_id"])["profileImg"];


        // Retreive messages
        $messages = Message::getMessagesByChatId($pdo, $chat["id"]);

        // var_dump($messages);
    } else {
        $messages = null;
    }
} else {
    header("Location: ../login.php?error=notLoggedIn");
}
?>

<?php if ($user["isAdmin"] == "on" && !Chat::hasActiveChat($pdo, $_SESSION["user_id"])): ?>
    <div class="chat" style="display: flex;">
        <div class="center" id="chatWindow">
            <p>Er zijn geen chats beschikbaar.</p>
        </div>
    </div>
<?php else: ?>
    <button class="chatButton" onclick="openChat()">
        <i class="fa fa-comment-o"></i>
        <p>Help</p>
    </button>

    <div class="chat">
        <div class="top">
            <i class="fa fa-plus"></i>
            <div class="row">
                <div class="profilePictureAdmin"
                    style="background-image: url('<?php echo $pathExtention;
                    echo $user["isAdmin"] == "on" ? $userProfilePicture : $adminProfilePicture; ?>')">
                </div>
                <div class="column">
                    <span>Chat met</span>
                    <h3><?php echo $receiverName ?></h3>
                </div>
            </div>
        </div>
        <div class="center" id="chatWindow">
            <p>Vandaag 18:34</p>
            <div class="row admin">
                <p class="errorMsg"></p>
            </div>
            <?php if ($messages): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="row <?php echo $message["sender_id"] == $_SESSION["user_id"] ? "user" : "admin" ?>"
                        data-messageid=<?php echo $message["id"]; ?>>
                        <div class="profilePicture"
                            style="background-image: url('<?php echo $pathExtention;
                            echo User::getUserById($pdo, $message["sender_id"])["isAdmin"] == "on" ? $adminProfilePicture : $userProfilePicture; ?>');">
                        </div>
                        <p><?php echo $message["message"] ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="bottom">
            <input type="text" name="message" id="chatMessage" placeholder="Schrijf uw bericht...">
            <p class="border"></p>
            <div class="row">
                <i class="fa fa-paperclip"></i>

                <a href="#" class="btn" id="btnSendMessage">Verzenden</a>
            </div>

            <!-- <button id="btnEndChat"><i class="fa fa-sign-out"></i></button> -->

            <div class="center"
                style="display: <?php echo Chat::getActiveChat($pdo, $_SESSION["user_id"]) ? "block" : "none"; ?>">
                <a href="" class="btn" id="btnEndChat">Beïndig de chat</a>
            </div>
        </div>
    </div>

    <!-- Javascript path for dashboard.php but not yet for admin/dashboard.php -->
    <script>
        const isAdmin = <?php echo $user["isAdmin"] == "on" ? "true" : "false" ?>;
    </script>
    <script src="<?php echo $pathExtention ?>javascript/chat.js"></script>
<?php endif; ?>