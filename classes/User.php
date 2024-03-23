<?php
// session_start();

class User
{
    private $firstname;
    private $lastname;
    private $email;
    private $statute;
    private $sector;
    private $street;
    private $houseNumber;
    private $zipCode;
    private $city;
    private $phoneNumber;
    private string $password;

    /**
     * Get the value of firstname
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set the value of firstname
     *
     * @return  self
     */
    public function setFirstname($firstname)
    {
        if (empty (trim($firstname))) {
            throw new Exception("Voornaam is verplicht.");
        }

        $reValid = '/^(?!.*\s\s)[A-Za-z]+([-\' ][A-Za-z]+)*$/';

        if (!preg_match($reValid, $firstname)) {
            throw new Exception("Voornaam is niet geldig.");
        }
        
        $_SESSION["firstname"] = $firstname;
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get the value of lastname
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set the value of lastname
     *
     * @return  self
     */
    public function setLastname($lastname)
    {
        if (empty (trim($lastname))) {
            throw new Exception("Achternaam is verplicht.");
        }
        
        $reValid = '/^(?!.*\s\s)[A-Za-z]+([-\' ][A-Za-z]+)*$/';

        if (!preg_match($reValid, $lastname)) {
            throw new Exception("Achternaam is niet geldig.");
        }
        
        $_SESSION["lastname"] = $lastname;
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        if (empty (trim($email))) {
            throw new Exception("Email is verplicht.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email is niet geldig.");
        }

        $_SESSION["email"] = $email;
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of statute
     */ 
    public function getStatute()
    {
        return $this->statute;
    }

    /**
     * Set the value of statute
     *
     * @return  self
     */ 
    public function setStatute($statute)
    {
        if (empty (trim($statute))) {
            throw new Exception("Statuut kan niet leeg zijn.");
        }

        $_SESSION["statute"] = $statute;
        $this->statute = $statute;

        return $this;
    }

    /**
     * Get the value of sector
     */ 
    public function getSector()
    {
        return $this->sector;
    }

    /**
     * Set the value of sector
     *
     * @return  self
     */ 
    public function setSector($sector)
    {
        if (empty (trim($sector))) {
            throw new Exception("Sector kan niet leeg zijn.");
        }

        $_SESSION["sector"] = $sector;
        $this->sector = $sector;

        return $this;
    }

    /**
     * Get the value of street
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set the value of street
     *
     * @return  self
     */
    public function setStreet($street)
    {
        if (empty (trim($street))) {
            throw new Exception("Straat is verplicht.");
        }

        $reValid = '/^(?!.*\s\s)[A-Za-z]+([-\' ][A-Za-z]+)*$/';
        
        if (!preg_match($reValid, $street)) {
            throw new Exception("Straat is niet geldig.");
        }

        $_SESSION["street"] = $street;
        $this->street = $street;

        return $this;
    }

    /**
     * Get the value of houseNumber
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * Set the value of houseNumber
     *
     * @return  self
     */
    public function setHouseNumber($houseNumber)
    {
        if (empty (trim($houseNumber))) {
            throw new Exception("Huisnummer is verplicht.");
        }

        $reValid = '/^[0-9]+[a-zA-Z]*$/';
        if (!preg_match($reValid, $houseNumber)) {
            throw new Exception("Huisnummer is niet geldig.");
        }

        $_SESSION["houseNumber"] = $houseNumber;
        $this->houseNumber = $houseNumber;

        return $this;
    }

    /**
     * Get the value of zipCode
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set the value of zipCode
     *
     * @return  self
     */
    public function setZipCode($zipCode)
    {
        if (empty (trim($zipCode))) {
            throw new Exception("Postcode is verplicht.");
        }

        $reValid = '/^[1-9][0-9]{3}$/';
        if (!preg_match($reValid, $zipCode)) {
            throw new Exception("Postcode is niet geldig.");
        }

        $_SESSION["zipCode"] = $zipCode;
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get the value of city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the value of city
     *
     * @return  self
     */
    public function setCity($city)
    {
        if (empty(trim($city))) {
            throw new Exception("Gemeente is verplicht.");
        }

        $reValid = '/^(?!.*\s\s)[A-Za-z]+([- ][A-Za-z]+)*$/';
        if (!preg_match($reValid, $city)) {
            throw new Exception("Gemeente is niet geldig.");
        }

        $_SESSION["city"] = $city;
        $this->city = $city;

        return $this;
    }

    /**
     * Get the value of phoneNumber
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set the value of phoneNumber
     *
     * @return  self
     */
    public function setPhoneNumber($phoneNumber)
    {
        if (empty (trim($phoneNumber))) {
            $phoneNumber = NULL;
        }

        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword($password)
    {
        if (empty (trim($password))) {
            throw new Exception("Vul een wachtwoord in");
        }

        if (strlen($password) < 8) {
            throw new Exception("Wachtwoord moet minstens 8 karakters lang zijn.");
        }

        $reValid = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[a-zA-Z\d!@#$%^&*]{8,}$/';
        if (!preg_match($reValid, $password)) {
            throw new Exception("Wachtwoord moet minstens 1 hoofdletter, 1 kleine letter, 1 cijfer en 1 speciaal karakter (!@#$%^&*) bevatten.");
        }

        $this->password = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }

    public function addUser(PDO $pdo): bool
    {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, email, statute_id, sector_id, street, houseNumber, zipCode, city, password) VALUES (:firstname, :lastname, :email, :statute, :sector, :street, :houseNumber, :zipCode, :city, :password)");
            $stmt->bindParam(':firstname', $this->firstname);
            $stmt->bindParam(':lastname', $this->lastname);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':statute', $this->statute);
            $stmt->bindParam(':sector', $this->sector);
            $stmt->bindParam(':street', $this->street);
            $stmt->bindParam(':houseNumber', $this->houseNumber);
            $stmt->bindParam(':zipCode', $this->zipCode);
            $stmt->bindParam(':city', $this->city);
            $stmt->bindParam(':password', $this->password);

            // Controleer of de SQL-instructie met succes is uitgevoerd
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateUser(PDO $pdo, $user_id): bool
    {
        try {
            $stmt = $pdo->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, street = :street, houseNumber = :houseNumber, zipCode = :zipCode, city = :city, email = :email, phoneNumber = :phoneNumber WHERE id = :user_id");
            $stmt->bindParam(':firstname', $this->firstname);
            $stmt->bindParam(':lastname', $this->lastname);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':phoneNumber', $this->phoneNumber);
            $stmt->bindParam(':street', $this->street);
            $stmt->bindParam(':houseNumber', $this->houseNumber);
            $stmt->bindParam(':zipCode', $this->zipCode);
            $stmt->bindParam(':city', $this->city);
            $stmt->bindParam(':user_id', $user_id);

            // Controleer of de SQL-instructie met succes is uitgevoerd
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


    public static function getUserByEmail(PDO $pdo, string $email)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }

    public static function getUserById(PDO $pdo, int $id)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }
}
