<?php

class Sector
{
    public static function getAll(PDO $pdo)
    {
        try {
            $query = "SELECT * FROM sectors";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }
}