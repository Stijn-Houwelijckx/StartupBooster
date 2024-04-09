<?php
include_once (__DIR__ . "../../classes/Db.php");
include_once (__DIR__ . "../../classes/User.php");
include_once (__DIR__ . "../../classes/Message.php");

$pdo = Db::getInstance();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["message"])) {
        try {
            $messageContent = $_POST["message"];
            $message = new Message;
            $message->setSender_id($_SESSION["user_id"]);
            $message->setMessage($messageContent);
            $message->addMessage($pdo);
            $pdo = Db::getInstance();
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
        }
    }
}

$messages = Message::getAll($pdo, $_SESSION["user_id"]);
?>

<div class="chatButton">
    <i class="fa fa-comment-o"></i>
    <p>Chatten</p>
</div>

<div class="chat">
    <div class="top">
        <i class="fa fa-plus"></i>
        <div class="row">
            <div class="profilePictureAdmin"></div>
            <div class="column">
                <span>Chat met</span>
                <h3>David</h3>
            </div>
        </div>
    </div>
    <div class="center">
        <p>Vandaag 18:34</p>
        <div class="row admin">
            <div class="profilePicture" style="background-image: url('assets/images/Tom.jpg')">
            </div>
            <p>Hey, hallo! Met David hier, hoe kan ik u helpen?</p>
        </div>
        <!-- <div class="row user">
            <div class="profilePicture" style="background-image: url('../assets/images/Stijn.jpg')"></div>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Officiis molestias veniam nisi earum, itaque,
                aut commodi voluptatibus non natus alias enim provident. Totam sunt maxime sequi repellat unde quae
                eius.</p>
        </div> -->
        <?php if ($messages !== null): ?>
            <?php foreach ($messages as $message): ?>
                <div class="row <?php echo ($message['isAdmin'] == "on") ? 'admin' : 'user'; ?>">
                    <div class="profilePicture" style="background-image: url('assets/images/Stijn.jpg')"></div>
                    <p>
                        <?php echo $message["message"] ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="bottom">
        <form action="" method="post">
            <input type="text" name="message" placeholder="Schrijf uw bericht...">
            <p class="border"></p>
            <div class="row">
                <i class="fa fa-paperclip"></i>
                <button class="btn" type="submit">Verzenden</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelector(".chatButton").addEventListener("click", function (e) {
        document.querySelector(".chat").style.display = "flex";
        document.querySelector(".chat .fa-plus").addEventListener("click", function (e) {
            document.querySelector(".chat").style.display = "none";
        });
    })
</script>