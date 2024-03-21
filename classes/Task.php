<?php
class Task
{
    private $label;
    private $question;
    private $answer;
    private $done;
    private $read;

    /**
     * Get the value of label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the value of label
     *
     * @return  self
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the value of question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set the value of question
     *
     * @return  self
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get the value of answer
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set the value of answer
     *
     * @return  self
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }


    /**
     * Get the value of done
     */
    public function getDone()
    {
        return $this->done;
    }

    /**
     * Set the value of done
     *
     * @return  self
     */
    public function setDone($done)
    {
        $this->done = $done;

        return $this;
    }

    /**
     * Get the value of read
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * Set the value of read
     *
     * @return  self
     */
    public function setRead($read)
    {
        $this->read = $read;

        return $this;
    }

    public static function getTasks(PDO $pdo)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM roadmap");
            $stmt->execute();
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $tasks ?: [];
        } catch (PDOException $e) {
            error_log('Database error in getTasks(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to retrieve tasks');
        }
    }
}