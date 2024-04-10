<?php

class Message
{
    private $chat_id;
    private $message;
    private $sender_id;
    private $receiver_id;

    /**
     * Get the value of chat_id
     */
    public function getChat_id()
    {
        return $this->chat_id;
    }

    /**
     * Set the value of chat_id
     *
     * @return  self
     */
    public function setChat_id($chat_id)
    {
        $this->chat_id = $chat_id;

        return $this;
    }

    /**
     * Get the value of message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the value of message
     *
     * @return  self
     */

    public function setMessage($message)
    {
        if (empty(trim($message))) {
            throw new Exception("Message mag niet leeg zijn.");
        }
        $this->message = $message;
        return $this;
    }

    /**
     * Get the value of sender_id
     */
    public function getSender_id()
    {
        return $this->sender_id;
    }

    /**
     * Set the value of sender_id
     *
     * @return  self
     */

    public function setSender_id($sender_id)
    {
        $this->sender_id = $sender_id;

        return $this;
    }

    /**
     * Get the value of receiver_id
     */
    public function getReceiver_id()
    {
        return $this->receiver_id;
    }

    /**
     * Set the value of receiver_id
     *
     * @return  self
     */
    public function setReceiver_id($receiver_id)
    {
        $this->receiver_id = $receiver_id;

        return $this;
    }

    public static function getChatIdFunction(PDO $pdo, $userId)
    {
        try {
            $stmt = $pdo->prepare("SELECT DISTINCT chat.id FROM chat WHERE (chat.user_id = :user_id OR chat.admin_id = :admin_id) AND chat.status = 1");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':admin_id', $userId);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return false;
        }
    }




    // public static function getAll($pdo, $user_id)
    // {
    //     try {
    //         $stmt = $pdo->prepare("SELECT DISTINCT message.message FROM message, chat WHERE sender_id = :sender_id OR receiver_id = :receiver_id AND chat.id = message.chat_id AND chat.status = 1");
    //         $stmt->bindParam(':sender_id', $user_id);
    //         $stmt->bindParam(':receiver_id', $user_id);
    //         $stmt->execute();
    //         $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //         return $messages ? $messages : null;
    //     } catch (PDOException $e) {
    //         error_log('Database error: ' . $e->getMessage());
    //         return null;
    //     }
    // }

    public static function getAll(PDO $pdo, $user_id)
    {
        try {
            $stmt = $pdo->prepare("SELECT DISTINCT message.message FROM message, chat WHERE (message.sender_id = :sender_id OR message.receiver_id = :receiver_id) AND chat.id = message.chat_id AND chat.status = 1");
            $stmt->bindParam(':sender_id', $user_id);
            $stmt->bindParam(':receiver_id', $user_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }


    public function addMessage(PDO $pdo): bool
    {
        try {
            $stmt = $pdo->prepare("INSERT INTO message (chat_id, sender_id, receiver_id, message) VALUES (:chat_id, :sender_id, :receiver_id, :message)");
            $stmt->bindParam(':chat_id', $this->chat_id, PDO::PARAM_INT);
            $stmt->bindParam(':sender_id', $this->sender_id, PDO::PARAM_INT);
            $stmt->bindParam(':receiver_id', $this->receiver_id, PDO::PARAM_INT);
            $stmt->bindParam(':message', $this->message, PDO::PARAM_STR);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return false;
        }
    }
}