<?php

class Chat
{
    private $user_id;
    private $admin_id;
    private $status;

    /**
     * Get the value of user_id
     */
    public function getUser_id()
    {
        return $this->user_id;
    }

    /**
     * Set the value of user_id
     *
     * @return  self
     */
    public function setUser_id($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Get the value of admin_id
     */
    public function getAdmin_id()
    {
        return $this->admin_id;
    }

    /**
     * Set the value of admin_id
     *
     * @return  self
     */
    public function setAdmin_id($admin_id)
    {
        $this->admin_id = $admin_id;

        return $this;
    }

    /**
     * Get the value of status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }


    /**
     * Finds the ID of an available admin for the chat.
     *
     * @param PDO $pdo The PDO object for the database connection.
     * @return int|null The ID of the available admin, or null if no admin is available.
     */
    public static function findAvailableAdminId(PDO $pdo): ?int
    {
        try {
            // Query to find all available admins
            $query = "SELECT id FROM users WHERE isAdmin = 'on' AND id NOT IN (SELECT admin_id FROM chat WHERE status = 1)";

            // Prepare and execute the query
            $stmt = $pdo->prepare($query);
            $stmt->execute();

            // Fetch the result
            // We only want the first available admin id so we use fetchColumn
            $result = $stmt->fetchColumn();

            // Return the result if it exists, otherwise return null
            return $result ? $result : null;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Creates a new chat in the database.
     *
     * @param PDO $pdo The PDO object representing the database connection.
     * @return int|null The ID of the inserted chat, or null if an error occurred.
     */
    public function createChat(PDO $pdo): ?int
    {
        try {
            // Query to insert a new chat
            $query = "INSERT INTO chat (user_id, admin_id) VALUES (:user_id, :admin_id)";

            // Prepare the query
            $stmt = $pdo->prepare($query);

            // Bind the parameters
            $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindParam(':admin_id', $this->admin_id, PDO::PARAM_INT);

            // Execute the query
            $stmt->execute();

            // Return the ID of the inserted chat
            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Checks if a user has an active chat.
     *
     * @param PDO $pdo The PDO object for the database connection.
     * @param int $user_id The ID of the user to check.
     * @return bool Returns true if the user has an active chat, otherwise returns false.
     */
    public static function hasActiveChat(PDO $pdo, $user_id): bool
    {
        try {
            // Query to check if the user has an active chat
            $query = "SELECT COUNT(*) FROM chat WHERE (user_id = :user_id OR admin_id = :admin_id) AND status = 1";

            // Prepare the query
            $stmt = $pdo->prepare($query);

            // Bind the parameter
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':admin_id', $user_id, PDO::PARAM_INT);

            // Execute the query
            $stmt->execute();

            // Fetch the result
            $result = $stmt->fetchColumn();

            // Return true if the user has an active chat, otherwise return false
            return $result > 0;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves the active chat for a given user from the database.
     *
     * @param PDO $pdo The PDO object representing the database connection.
     * @param int $user_id The ID of the user.
     * @return array|null Returns an array containing the active chat details if it exists, otherwise returns null.
     */
    public static function getActiveChat(PDO $pdo, $user_id): ?array
    {
        try {
            // Query to get the ID of the active chat
            $query = "SELECT * FROM chat WHERE (user_id = :user_id OR admin_id = :admin_id) AND status = 1";

            // Prepare the query
            $stmt = $pdo->prepare($query);

            // Bind the parameter
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':admin_id', $user_id, PDO::PARAM_INT);

            // Execute the query
            $stmt->execute();

            // Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Return the result if it exists, otherwise return null
            return $result ? $result : null;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Ends the chat by updating the status of the chat to 0 for a given user ID or admin ID.
     *
     * @param PDO $pdo The PDO object for the database connection.
     * @param int $user_id The ID of the user or admin for whom the chat should be ended.
     * @return bool Returns true if the chat was successfully ended, false otherwise.
     */
    public static function endChat(PDO $pdo, $chat_id): bool
    {
        try {
            // Query to end the chat
            // $query = "UPDATE chat SET status = 0 WHERE user_id = :user_id OR admin_id = :admin_id";
            $query = "UPDATE chat SET status = 0 WHERE id = :chat_id";

            // Prepare the query
            $stmt = $pdo->prepare($query);

            // Bind the parameters
            // $stmt->bindParam(':user_id', $user_id);
            // $stmt->bindParam(':admin_id', $user_id);
            $stmt->bindParam(':chat_id', $chat_id);

            // Execute the query
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return false;
        }
    }
}