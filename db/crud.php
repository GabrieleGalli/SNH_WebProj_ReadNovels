<?php

require_once __DIR__ . '/../incl/utils.php';

class Crud
{
    private $db;

    function __construct($conn)
    {
        if (!$conn) {
            throw new Exception("Database connection is invalid.");
        }
        $this->db = $conn;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Setup PDO error exceptions mode
        $this->db->setAttribute(PDO::ATTR_TIMEOUT, 2); // Setup PDO timeout to 2 seconds
    }

    public function createShortNovel($id_u, $premium, $title, $content)
    {
        try {
            // Sanitization and input validation
            $id_u = sanitize_input($id_u);
            $premium = sanitize_input($premium);
            $title = sanitize_input($title);
            $content = sanitize_input($content);

            if (
                !filter_var($id_u, FILTER_VALIDATE_INT) ||
                !in_array($premium, ['0', '1'], true) ||
                !$title || !$content
            ) {
                throw new Exception("Invalid input data.");
            }

            $Q = "INSERT INTO short_novels (ID_U, PREMIUM, DATE, TITLE, CONTENT) 
                    VALUES (:id_u, :premium, CURRENT_TIMESTAMP, :title, :content)";
            $stmt = $this->db->prepare($Q);

            $stmt->bindParam(':id_u', $id_u, PDO::PARAM_INT);
            $stmt->bindParam(':premium', $premium, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    public function createLongNovel($id_u, $premium, $title, $fileName)
    {
        try {
            // Sanitization and input validation
            $id_u = sanitize_input($id_u);
            $premium = sanitize_input($premium);
            $title = sanitize_input($title);
            $fileName = sanitize_input($fileName);

            if (
                !filter_var($id_u, FILTER_VALIDATE_INT) ||
                !in_array($premium, ['0', '1'], true) ||
                !$title || !$fileName
            ) {
                throw new Exception("Invalid input data.");
            }

            $Q = "INSERT INTO long_novels (ID_U, PREMIUM, DATE, TITLE, FILENAME) 
                    VALUES (:id_u, :premium, CURRENT_TIMESTAMP, :title, :filename)";
            $stmt = $this->db->prepare($Q);

            $stmt->bindParam(':id_u', $id_u, PDO::PARAM_INT);
            $stmt->bindParam(':premium', $premium, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':filename', $fileName, PDO::PARAM_STR);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    public function getShortNovel($id)
    {
        try {
            $id = sanitize_input($id);
            if (!filter_var($id, FILTER_VALIDATE_INT)) {
                throw new Exception("Invalid input data.");
            }
            $Q = "SELECT * FROM short_novels WHERE ID = :id";
            $stmt = $this->db->prepare($Q);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    public function getLongNovel($id)
    {
        try {
            $id = sanitize_input($id);
            if (!filter_var($id, FILTER_VALIDATE_INT)) {
                throw new Exception("Invalid input data.");
            }
            $Q = "SELECT * FROM long_novels WHERE ID = :id";
            $stmt = $this->db->prepare($Q);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    public function getShortNovels()
    {
        try {
            $Q = "SELECT 
                    short_novels.ID AS shnovel_id,
                    short_novels.PREMIUM AS shnovel_premium,
                    short_novels.DATE AS shnovel_date,
                    short_novels.TITLE AS shnovel_title,
                    short_novels.CONTENT AS shnovel_content,
                    users.ID as user_id,
                    users.NAME AS user_name,
                    users.SURNAME AS user_surname
                    FROM short_novels
                    INNER JOIN users
                    ON short_novels.ID_U = users.ID";

            $stmt = $this->db->query($Q);
            //return $stmt = $this->db->query($Q);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    public function getLongNovels()
    {
        try {
            $Q = "SELECT 
                    long_novels.ID AS lgnovel_id,
                    long_novels.PREMIUM AS lgnovel_premium,
                    long_novels.DATE AS lgnovel_date,
                    long_novels.TITLE AS lgnovel_title,
                    long_novels.FILENAME AS lgnovel_filename,
                    users.ID as user_id,
                    users.NAME AS user_name,
                    users.SURNAME AS user_surname
                    FROM long_novels
                    INNER JOIN users
                    ON long_novels.ID_U = users.ID";

            $stmt = $this->db->query($Q);
            //return $stmt = $this->db->query($Q);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    public function getLongNovelByFilename($filename)
    {
        try {
            $filename = sanitize_input($filename);
            $Q = "SELECT * FROM long_novels WHERE FILENAME = :f";
            $stmt = $this->db->prepare($Q);
            $stmt->bindParam(":f", $filename, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    public function deleteShortNovel($id)
    {
        try {
            $id = sanitize_input($id);
            if (!filter_var($id, FILTER_VALIDATE_INT)) {
                throw new Exception("Invalid input data.");
            }

            $Q = "DELETE FROM short_novels WHERE ID = :id";
            $stmt = $this->db->prepare($Q);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    public function deleteLongNovel($id)
    {
        try {
            $id = sanitize_input($id);
            if (!filter_var($id, FILTER_VALIDATE_INT)) {
                throw new Exception("Invalid input data.");
            }

            $Q = "DELETE FROM long_novels WHERE ID = :id";
            $stmt = $this->db->prepare($Q);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

}

?>