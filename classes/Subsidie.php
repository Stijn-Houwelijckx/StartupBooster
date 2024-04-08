<?php

class Subsidie
{
    private $name;
    private $description;
    private $who;
    private $what;
    private $amount;
    private $link;

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of who
     */
    public function getWho()
    {
        return $this->who;
    }

    /**
     * Set the value of who
     *
     * @return  self
     */
    public function setWho($who)
    {
        $this->who = $who;

        return $this;
    }

    /**
     * Get the value of what
     */
    public function getWhat()
    {
        return $this->what;
    }

    /**
     * Set the value of what
     *
     * @return  self
     */
    public function setWhat($what)
    {
        $this->what = $what;

        return $this;
    }

    /**
     * Get the value of amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the value of amount
     *
     * @return  self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get the value of link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set the value of link
     *
     * @return  self
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    public static function getSubsidies(PDO $pdo)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM subsidies");
            $stmt->execute();
            $subsidies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $subsidies ?: [];
        } catch (PDOException $e) {
            error_log('Database error in getSubsidies(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to retrieve subsidies');
        }
    }

    public static function getSubsidieById(PDO $pdo, $id)
    {
        try {
            if ($id == 0) {
                $stmt = $pdo->prepare("SELECT * FROM subsidies LIMIT 1");
            } else {
                $stmt = $pdo->prepare("SELECT * FROM subsidies WHERE id = :id");
                $stmt->bindParam(':id', $id);
            }
            $stmt->execute();
            $subsidie = $stmt->fetch(PDO::FETCH_ASSOC);
            return $subsidie ? $subsidie : null;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }

    public static function addSubsidie(PDO $pdo, $name, $description, $who, $what, $amount, $link)
    {
        try {
            $stmt = $pdo->prepare("INSERT INTO subsidies (name, description, who, what, amount, link) VALUES (:name, :description, :who, :what, :amount, :link)");
            $stmt->execute(
                array(
                    ':name' => $name,
                    ':description' => $description,
                    ':who' => $who,
                    ':what' => $what,
                    ':amount' => $amount,
                    ':link' => $link
                )
            );
            return true;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return false;
        }
    }

    public static function deleteSubsidie(PDO $pdo, $name)
    {
        try {
            $stmt = $pdo->prepare("DELETE FROM subsidies WHERE name = :name");
            $stmt->bindParam(':name', $name);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return false;
        }
    }
}
