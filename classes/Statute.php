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
}