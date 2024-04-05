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

    public static function getPopulatedSector(PDO $pdo) {
        try {
            $query = "SELECT DISTINCT sectors.* FROM sectors, users WHERE users.sector_id = sectors.id;";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }

    public static function getUserCountBySectorId($pdo, $sectorId)
    {
        $stmt = $pdo->prepare("SELECT COUNT(id) as user_count FROM users WHERE sector_id = :sector_id");
        $stmt->bindparam(':sector_id', $sectorId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['user_count'];
    }
}