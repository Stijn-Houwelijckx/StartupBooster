<?php

include_once (__DIR__ . "/../classes/Db.php");
include_once (__DIR__ . "/../classes/Message.php");
include_once (__DIR__ . "/../classes/Chat.php");
include_once (__DIR__ . "/../classes/User.php");

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('error_log', 'error.log');

session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    http_response_code(401); // Unauthorized
    exit();
}

// Check if the form is submitted
if (!empty($_POST)) {
    $pdo = Db::getInstance();
    $user = User::getUserById($pdo, $_SESSION["user_id"]);
    
    // Check if the user is an admin to determine the path extention
    if ($user["isAdmin"] == "on") {
        $pathExtention = "../";
    } else {
        $pathExtention = "";
    }

    // Get the active chat
    $chat = Chat::getActiveChat($pdo, $_SESSION["user_id"]);
    
    if ($chat == null) {
        // Find an available admin to chat with
        $adminId = Chat::findAvailableAdminId($pdo);
        
        if ($adminId) {
            // If an admin is found, create a new chat
            $chat = new Chat();
            $chat->setUser_id($_SESSION["user_id"]);
            $chat->setAdmin_id($adminId);
            $chat->createChat($pdo);
        } else {
            // If no admin is found, return an error message
            $error = "Er is geen admin beschikbaar om mee te chatten. Probeer het later opnieuw.";

            $response = [
                "status" => "error",
                "message" => $error
            ];

            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }
    
    $chat = Chat::getActiveChat($pdo, $_SESSION["user_id"]);

    // Check if the chat exists
    if($chat) {
        // Determine the sender and receiver IDs based on the chat information
        $senderId = $_SESSION["user_id"];
        $receiverId = ($senderId == $chat["user_id"]) ? $chat["admin_id"] : $chat["user_id"];
        $profileImg = User::getUserById($pdo, $senderId)["profileImg"];

        // Create a new message object

        try {
            $message = new Message();
            $message->setChatId($chat["id"]);
            $message->setMessage($_POST["message"]);
            $message->setSenderId($senderId);
            $message->setReceiverId($receiverId);
    
            // Save the message to the database
            $message->saveMessage($pdo);
    
            // Return a success message
            $response = [
                "status" => "success",
                "body" => htmlspecialchars($message->getMessage()),
                "sender" => "user",
                "profileImg" => $pathExtention . $profileImg,
                "message" => "Message sent!"
            ];
        } catch (Exception $e) {
            // If an error occurs, return an error message
            http_response_code(500); // Internal Server Error
            
            $response = [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    } else {
        // If no active chat is found, return an error message
        $response = [
            "status" => "error",
            "message" => "No active chat found."
        ];
    }

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}