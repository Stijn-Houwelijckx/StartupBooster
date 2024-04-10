<?php

class Chat
{
    private $user_id;
    private $admin_id;

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

    public static function getAdminId($pdo)
    {
        try {
            $stmt = $pdo->prepare("SELECT admin_id FROM chat LIMIT 1");
            $stmt->execute();
            $adminId = $stmt->fetchColumn(); // Haal alleen de admin_id op

            return $adminId !== false ? $adminId : null;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }


    public static function getAllAdminsThatHaveNoChat($pdo, $user_id)
    {
        try {
            $stmt = $pdo->prepare("
            SELECT users.id
            FROM users
            WHERE users.isAdmin = 'on'
            AND users.id != :user_id
            AND users.id NOT IN (
                SELECT receiver_id
                FROM message
            )            
        ");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $adminsThatHaveNoChat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $adminsThatHaveNoChat ? $adminsThatHaveNoChat : null;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }


    public function addChat(PDO $pdo): bool
    {
        try {
            $stmt = $pdo->prepare("INSERT INTO chat (user_id, admin_id) VALUES (:user_id, :admin_id)");
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':admin_id', $this->admin_id);

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

    public static function deleteChat(PDO $pdo, $user_id)
    {
        try {
            $stmt = $pdo->prepare("UPDATE chat SET status = 0 WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log('Database error in updateRead(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to update read status');
        }
    }
}