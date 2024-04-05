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

    // In your Stat class or a relevant class// In your Stat class or a relevant class
// In the Sector class or a relevant class
    public static function getUserCountBySectorId($pdo, $sectorId)
    {
        $stmt = $pdo->prepare("SELECT COUNT(*) as user_count FROM users WHERE sector_id = ?");
        $stmt->execute([$sectorId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['user_count'];
    }



}