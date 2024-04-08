<?php

class Sector
{
    private $title;

    /**
     * Get the value of title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public static function getAll(PDO $pdo)
    {
        try {
            $query = "SELECT * FROM sectors WHERE status = 1";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }

    public static function addSector(PDO $pdo, $title)
    {
        try {
            $stmt = $pdo->prepare("INSERT INTO sectors (title) VALUES (:title)");
            $stmt->execute(
                array(
                    ':title' => $title
                )
            );
            return true;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return false;
        }
    }

    public static function deleteSector(PDO $pdo, $title)
    {
        try {
            $stmt = $pdo->prepare("UPDATE sectors SET status = 0 WHERE title = :title");
            $stmt->bindParam(':title', $title);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log('Database error in updateRead(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to update read status');
        }
    }

    public static function getPopulatedSector(PDO $pdo)
    {
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

    public static function updateSectors(PDO $pdo, $old_title, $new_title)
    {
        try {
            $stmt = $pdo->prepare("UPDATE sectors SET title = :new_title WHERE title = :old_title");
            $stmt->bindParam(':new_title', $new_title);
            $stmt->bindParam(':old_title', $old_title);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log('Database error in updateSectors(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to update sectors');
        }
    }
}