<?php

class Stat
{
    // ======================== STATISTICS PROPERTIES ========================

    private $profit_loss;
    private $equityCapital;
    private $grossMargin;
    private $revenue;
    private $costs;
    private $personnel;
    private $year;

    // ======================== STATISTICS GETTERS & SETTERS ========================

    /**
     * Get the value of profit_loss
     */
    public function getProfit_loss()
    {
        return $this->profit_loss;
    }

    /**
     * Set the value of profit_loss
     *
     * @return  self
     */
    public function setProfit_loss($profit_loss)
    {
        $this->profit_loss = $profit_loss;

        return $this;
    }

    /**
     * Get the value of equityCapital
     */
    public function getEquityCapital()
    {
        return $this->equityCapital;
    }

    /**
     * Set the value of equityCapital
     *
     * @return  self
     */
    public function setEquityCapital($equityCapital)
    {
        $this->equityCapital = $equityCapital;

        return $this;
    }

    /**
     * Get the value of grossMargin
     */
    public function getGrossMargin()
    {
        return $this->grossMargin;
    }

    /**
     * Set the value of grossMargin
     *
     * @return  self
     */
    public function setGrossMargin($grossMargin)
    {
        $this->grossMargin = $grossMargin;

        return $this;
    }

    /**
     * Get the value of revenue
     */
    public function getRevenue()
    {
        return $this->revenue;
    }

    /**
     * Set the value of revenue
     *
     * @return  self
     */
    public function setRevenue($revenue)
    {
        $this->revenue = $revenue;

        return $this;
    }

    /**
     * Get the value of costs
     */
    public function getCosts()
    {
        return $this->costs;
    }

    /**
     * Set the value of costs
     *
     * @return  self
     */
    public function setCosts($costs)
    {
        $this->costs = $costs;

        return $this;
    }

    /**
     * Get the value of personnel
     */
    public function getPersonnel()
    {
        return $this->personnel;
    }

    /**
     * Set the value of personnel
     *
     * @return  self
     */
    public function setPersonnel($personnel)
    {
        $this->personnel = $personnel;

        return $this;
    }

    /**
     * Get the value of year
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set the value of year
     *
     * @return  self
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    // ======================== STATISTICS DATABASE OPERATIONS ========================

    public static function getStats(PDO $pdo, $year, $user_id)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM stats WHERE year = :year AND user_id = :user_id");
            $stmt->bindParam(':year', $year);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $stats;
        } catch (PDOException $e) {
            error_log('Database error in getStats(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to retrieve stats');
        }
    }

    public static function getAllStatsByStatuteSector(PDO $pdo, $startYear, $endYear, $statute_id, $sector_id)
    {
        try {
            $stmt = $pdo->prepare("SELECT stats.* FROM stats, users WHERE year BETWEEN :startYear AND :endYear AND stats.user_id = users.id AND users.statute_id = :statute_id AND users.sector_id = :sector_id ORDER BY year ASC");
            $stmt->bindParam(':startYear', $startYear);
            $stmt->bindParam(':endYear', $endYear);
            $stmt->bindParam(':statute_id', $statute_id);
            $stmt->bindParam(':sector_id', $sector_id);
            $stmt->execute();
            $allStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $allStats;
        } catch (PDOException $e) {
            error_log('Database error in getAllStatsByStatuteSector(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to retrieve AllStatsByStatuteSector');
        }
    }

    public static function getAllStatsByType(PDO $pdo, $year, $stat, $sector_id)
    {
        try {
            $stmt = $pdo->prepare("SELECT stats.$stat FROM stats, users WHERE year = :year AND stats.user_id = users.id AND users.sector_id = :sector_id");
            $stmt->bindParam(':year', $year);
            $stmt->bindParam(':sector_id', $sector_id);
            $stmt->execute();
            $allStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $allStats;
        } catch (PDOException $e) {
            error_log('Database error in getAllStatsByType(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to retrieve AllStatsByType');
        }
    }

    public static function getUserYears(PDO $pdo, $user_id)
    {
        try {
            $stmt = $pdo->prepare("SELECT DISTINCT year FROM stats WHERE user_id = :user_id ORDER BY year ASC");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $userYears = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $userYears;
        } catch (PDOException $e) {
            error_log('Database error in getUserYears(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to retrieve userYears');
        }
    }

    public static function getSectorYears(PDO $pdo)
    {
        try {
            $stmt = $pdo->prepare("SELECT DISTINCT year FROM stats ORDER BY year DESC");
            $stmt->execute();
            $sectorYears = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $sectorYears;
        } catch (PDOException $e) {
            error_log('Database error in getSectorYears(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to retrieve sectorYears');
        }
    }

    // ======================== STATISTICS CALCULATIONS ========================

    public static function calculateMedian($values)
    {
        sort($values);
        $count = count($values);
        $middle = floor($count / 2);
        if ($count % 2 == 0) {
            $median = ($values[$middle - 1] + $values[$middle]) / 2;
        } else {
            $median = $values[$middle];
        }
        return $median;
    }

    public static function calculateAverage($values)
    {
        $sum = array_sum($values);
        $count = count($values);
        $average = $sum / $count;
        return $average;
    }
}
