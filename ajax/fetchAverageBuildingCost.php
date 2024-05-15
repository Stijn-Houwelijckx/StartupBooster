<?php

include_once (__DIR__ . "/../classes/Db.php");
include_once (__DIR__ . "/../classes/User.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'error.log');

session_start();

$pdo = Db::getInstance();

// Check if the user is logged in and manager
if (!isset($_SESSION["user_id"])) {
    http_response_code(401); // Unauthorized
    exit();
}

// Check if the form is submitted
if (!empty($_POST)) {
    try {
        $averageBuildingPrice = User::priceByCity($pdo, $_POST['city']);

        $response = [
            "status" => "success",
            "price" => number_format($averageBuildingPrice["avgPrice"], 2, ',', ' ')
        ];
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());

        $response = [
            "status" => "error",
            "message" => "Something went wrong, please try again later."
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}