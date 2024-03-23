<?php

class Statute
{
    public static function getAll(PDO $pdo)
    {
        try {
            $query = "SELECT * FROM statutes";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }

    public static function getStatuteByUser(PDO $pdo, $user_id, $statute_id)
    {
        try {
            $query = "SELECT statutes.title FROM users, statutes  WHERE users.id = $user_id AND statutes.id = $statute_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }
}