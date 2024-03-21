<?php
use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Path to PHPMailer autoload.php

function sendEmail($to, $subject, $body)
{
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: info@startupbooster.com" . "\r\n";
    $headers .= "Return-Path: info@startupbooster.com" . "\r\n";

    // Verstuur de e-mail
    if (mail($to, $subject, $body, $headers)) {
        return true;
    } else {
        return 'Error: Unable to send email.';
    }
}

?>