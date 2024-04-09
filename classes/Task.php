<?php
class Task
{
    private $label;
    private $question;
    private $answer;
    private $status;

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
     * Get the value of status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public static function linkTasksToUser(PDO $pdo, $user_id)
    {
        try {
            $stmt = $pdo->prepare("INSERT INTO user_tasks (user_id, task_id) SELECT :user_id, id FROM tasks");
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

    public static function getTasks(PDO $pdo, $user_id)
    {
        try {
            $stmt = $pdo->prepare("SELECT tasks.id, tasks.label, tasks.question, tasks.answer, tasks.status, user_tasks.is_complete FROM tasks, user_tasks WHERE user_tasks.task_id = tasks.id AND user_tasks.user_id = :user_id AND tasks.status = 1");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $tasks ?: [];
        } catch (PDOException $e) {
            error_log('Database error in getTasks(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to retrieve tasks');
        }
    }

    public static function getAllTasks(PDO $pdo)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM tasks WHERE status = 1");
            $stmt->execute();
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $tasks ?: [];
        } catch (PDOException $e) {
            error_log('Database error in getTasks(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to retrieve tasks');
        }
    }

    public static function addTask(PDO $pdo, $label, $question, $answer, $status)
    {
        try {
            $stmt = $pdo->prepare("INSERT INTO tasks (label, question, answer, status) VALUES (:label, :question, :answer, :status)");
            $stmt->execute(
                array(
                    ':label' => $label,
                    ':question' => $question,
                    ':answer' => $answer,
                    ':status' => $status,
                )
            );
            return true;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getActiveTask(PDO $pdo, $user_id)
    {
        try {
            $stmt = $pdo->prepare("SELECT task_id FROM user_tasks WHERE is_complete = 0 AND user_id = :user_id ORDER BY id ASC LIMIT 1");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $tasks = $stmt->fetch(PDO::FETCH_ASSOC);
            return $tasks;
        } catch (PDOException $e) {
            error_log('Database error in getTasks(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to retrieve tasks');
        }
    }

    public static function updateRead(PDO $pdo, $taskId, $user_id)
    {
        try {
            $stmt = $pdo->prepare("UPDATE user_tasks SET is_complete = 1 WHERE task_id = :taskId AND user_id = :user_id");
            $stmt->bindParam(':taskId', $taskId);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log('Database error in updateRead(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to update read status');
        }
    }


    public static function getProgress(PDO $pdo, $user_id)
    {
        try {
            $query = "SELECT (
                        SELECT COUNT(id)
                        FROM user_tasks
                        WHERE is_complete = 1
                        AND user_id = :user_id_1
                    ) AS finished_steps,
                    (
                        SELECT COUNT(id)
                        FROM user_tasks
                        WHERE user_id = :user_id_2
                    ) AS total_steps;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':user_id_1', $user_id);
            $stmt->bindParam(':user_id_2', $user_id);
            $stmt->execute();
            $tasks = $stmt->fetch(PDO::FETCH_ASSOC);
            return $tasks;
        } catch (PDOException $e) {
            error_log('Database error in getProgress(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to retrieve tasks');
        }
    }

    public static function getTaskByQuestion(PDO $pdo, $question)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM tasks WHERE question = :question");
            $stmt->bindParam(':question', $question);
            $stmt->execute();
            $task = $stmt->fetch(PDO::FETCH_ASSOC);
            return $task ? $task : null;
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }

    public static function updateTasks(PDO $pdo, $id, $label, $question, $answer)
    {
        try {
            $stmt = $pdo->prepare("UPDATE tasks SET label = :label, question = :question, answer = :answer WHERE id = :id");
            $stmt->bindParam(':label', $label);
            $stmt->bindParam(':question', $question);
            $stmt->bindParam(':answer', $answer);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log('Database error in updateTasks(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to update tasks');
        }
    }

    public static function deleteTask(PDO $pdo, $id)
    {
        try {
            $stmt = $pdo->prepare("UPDATE tasks SET status = 0 WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log('Database error in updateRead(): ' . $e->getMessage());
            throw new Exception('Database error: Unable to update read status');
        }
    }
}