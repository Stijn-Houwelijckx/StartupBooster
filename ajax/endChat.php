<?php

include_once (__DIR__ . "/../classes/Db.php");
include_once (__DIR__ . "/../classes/Chat.php");

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('error_log', 'error.log');

session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    http_response_code(401); // Unauthorized
    exit();
} else {
    $pdo = Db::getInstance();

    // Get the active chat
    $chat = Chat::getActiveChat($pdo, $_SESSION["user_id"]);
    
    // Check if the chat exists
    if($chat) {
        try {
            // End the chat
            $chatId = $chat["id"];
            $success = Chat::endChat($pdo, $chatId);

            if ($success) {
                // Return a success message
                $response = [
                    "status" => "success",
                    "message" => "Chat ended!"
                ];
            } else {
                // If the chat could not be ended, return an error message
                $response = [
                    "status" => "error",
                    "message" => "Failed to end the chat."
                ];
            }
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

