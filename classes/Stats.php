<?php

class Stats
{
    private $profit_loss;
    private $equityCapital;
    private $grossMargin;
    private $revenue;
    private $costs;
    private $personnel;
    private $year;

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

    public static function getStats(PDO $pdo, $year, $user_id)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM stats WHERE year = :year AND user_id = :user_id");
            $stmt->bindParam(':year', $year);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $stats ?: [];
        } catch (PDOException $e) {
            error_log('Database error in getTasks(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to retrieve tasks');
        }
    }
}