<?php

class Question
{
    private $function;
    private $question;
    private $answer;
    private $image;

    /**
     * Get the value of function
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Set the value of function
     *
     * @return  self
     */
    public function setFunction($function)
    {
        $this->function = $function;

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
     * Get the value of image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set the value of image
     *
     * @return  self
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public static function getStudentZelfstandigeQuestions(PDO $pdo)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM questions WHERE function = :function");
            $stmt->bindParam(':function', $function);
            $function = 'student-zelfstandige';
            $stmt->execute();
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $questions ? $questions : null;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }

    public static function getZelfstandigeQuestions(PDO $pdo)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM questions WHERE function = :function");
            $stmt->bindParam(':function', $function);
            $function = 'Zelfstandige';
            $stmt->execute();
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $questions ? $questions : null;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }
}
