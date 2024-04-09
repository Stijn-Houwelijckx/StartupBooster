<?php

class Message
{
    private $message;

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

    public static function getAll($pdo)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM message");
            $stmt->execute();
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $questions ? $questions : null;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }
}