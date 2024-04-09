<?php

class Message
{
    private $message;
    private $sender_id;

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
    public function setMessage($message)
    {
        if (empty(trim($message))) {
            throw new Exception("Message mag niet leeg zijn.");
        }
        $this->message = $message;
        return $this;
    }

    public static function getAll($pdo, $user_id)
    {
        try {
            $stmt = $pdo->prepare("SELECT message.*, users.isAdmin FROM message, users WHERE message.sender_id = users.id AND users.id = :user_id");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $messages ? $messages : null;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }

    public function addMessage(PDO $pdo): bool
    {
        try {
            $stmt = $pdo->prepare("INSERT INTO message (chat_id, sender_id, receiver_id, message) VALUES (2, :sender_id, 44, :message)");
            $stmt->bindParam(':sender_id', $this->sender_id);
            $stmt->bindParam(':message', $this->message);

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