<?php

class Message
{
    private $message;
    private $sender_id;
    private $receiver_id;

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

    public static function getAll($pdo, $user_id)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM message WHERE sender_id = :sender_id OR receiver_id = :receiver_id");
            $stmt->bindParam(':sender_id', $user_id);
            $stmt->bindParam(':receiver_id', $user_id);
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
            $stmt = $pdo->prepare("INSERT INTO message (sender_id, receiver_id, message) VALUES (:sender_id, :receiver_id, :message)");
            $stmt->bindParam(':sender_id', $this->sender_id);
            $stmt->bindParam(':receiver_id', $this->receiver_id);
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