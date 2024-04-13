<?php

class Message
{
    private $chatId;
    private $senderId;
    private $receiverId;
    private $message;
    private $timestamp;

        /**
     * Get the value of chatId
     */ 
    public function getChatId()
    {
        return $this->chatId;
    }

    /**
     * Set the value of chatId
     *
     * @return  self
     */ 
    public function setChatId($chatId)
    {
        $this->chatId = $chatId;

        return $this;
    }

    /**
     * Get the value of senderId
     */ 
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * Set the value of senderId
     *
     * @return  self
     */ 
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;

        return $this;
    }

    
    /**
     * Get the value of receiverId
     */ 
    public function getReceiverId()
    {
        return $this->receiverId;
    }

    /**
     * Set the value of receiverId
     *
     * @return  self
     */ 
    public function setReceiverId($receiverId)
    {
        $this->receiverId = $receiverId;

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
        $this->message = $message;

        return $this;
    }

    /**
     * Get the value of timestamp
     */ 
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set the value of timestamp
     *
     * @return  self
     */ 
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }
    
    /**
     * Retrieves all messages from a specific chat by chat ID.
     *
     * @param PDO $pdo The PDO object for the database connection.
     * @param int $chatId The ID of the chat.
     * @return array|null An array of messages if they exist, otherwise null.
     */
    public static function getMessagesByChatId(PDO $pdo, $chatId) : ?array
    {
        try {
            // Query to get all messages from a chat
            $query = "SELECT * FROM messages WHERE chat_id = :chat_id ORDER BY timestamp";

            // Prepare the query
            $stmt = $pdo->prepare($query);

            // Bind the parameters
            $stmt->bindParam(':chat_id', $chatId, PDO::PARAM_INT);

            // Execute the query
            $stmt->execute();

            // Fetch the result
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return the result if it exists, otherwise return null
            return $result ? $result : null;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Saves the message to the database.
     *
     * @param PDO $pdo The PDO object representing the database connection.
     * @return bool Returns true if the message is successfully saved, false otherwise.
     */
    public function saveMessage(PDO $pdo): bool
    {
        try {
            // Query to insert a new message
            $query = "INSERT INTO messages (chat_id, sender_id, receiver_id, message) VALUES (:chat_id, :sender_id, :receiver_id, :message)";

            // Prepare the query
            $stmt = $pdo->prepare($query);

            // Bind the parameters
            $stmt->bindParam(':chat_id', $this->chatId, PDO::PARAM_INT);
            $stmt->bindParam(':sender_id', $this->senderId, PDO::PARAM_INT);
            $stmt->bindParam(':receiver_id', $this->receiverId, PDO::PARAM_INT);
            $stmt->bindParam(':message', $this->message, PDO::PARAM_STR);

            // Execute the query and return true if successful
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return false;
        }
    }
}