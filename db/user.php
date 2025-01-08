<?php

require_once __DIR__ . '/../incl/utils.php';

class User
{
    private $db;

    function __construct($conn)
    {
        if (!$conn) {
            throw new Exception("Database connection is invalid.");
        }
        $this->db = $conn;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Imposta PDO in modalità eccezione.
        $this->db->setAttribute(PDO::ATTR_TIMEOUT, 2); // Timeout di 2 secondi per la query
    }

    /**
     * Inserts a new user into the database.
     *
     * @param int $premium Indicates if the user is a premium member (1 for true, 0 for false).
     * @param string $usrname The username of the user.
     * @param string $name The first name of the user.
     * @param string $surname The surname of the user.
     * @param string $email The email address of the user.
     * @param string $psw The password of the user.
     * @return bool Returns true if the user was successfully inserted, false otherwise.
     * @throws Exception If the input data is invalid.
     */
    public function insertUser($premium, $usrname, $name, $surname, $email, $psw)
    {
        try {
            // Sanitizzazione e validazione input
            $premium = sanitize_input($premium);
            $usrname = sanitize_input($usrname);
            $name = sanitize_input($name);
            $surname = sanitize_input($surname);
            $email = sanitize_input($email);
            $psw = sanitize_input($psw);

            if (
                !in_array($premium, ['0', '1'], true) ||
                !filter_var($email, FILTER_VALIDATE_EMAIL) ||
                !$usrname || !$name || !$surname || !$psw
            ) {
                throw new Exception("Invalid input data.");
            }

            // every username is different
            $usrs_count = $this->getNumOfUsersByUsrname($usrname);
            $email_count = $this->getNumOfUsersByEmail($email);
            //user already exists
            if ($usrs_count['users_count'] > 0 || $email_count['users_count'] > 0) {
                return false;
            } else {
                $encr_psw = password_hash($psw, PASSWORD_BCRYPT);
                $Q = "INSERT INTO users (PREMIUM, USERNAME, NAME, SURNAME, EMAIL, PASSWORD) VALUES (:premium, :usr, :name, :surname, :email, :psw)";
                $stmt = $this->db->prepare($Q);

                $stmt->bindParam(':premium', $premium, PDO::PARAM_INT);
                $stmt->bindParam(':usr', $usrname, PDO::PARAM_STR);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':psw', $encr_psw, PDO::PARAM_STR);

                $stmt->execute();
                return true;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    /**
     * Retrieves a user from the database by their ID.
     *
     * @param int $id The ID of the user to retrieve.
     * @return array|false The user data as an associative array if found, false on failure.
     * @throws Exception If the input data is invalid.
     */
    public function getUserByID($id)
    {
        try {
            // Sanitizzazione e validazione input
            $id = sanitize_input($id);
            if (!filter_var($id, FILTER_VALIDATE_INT)) {
                throw new Exception("Invalid input data.");
            }

            $Q = "SELECT * FROM users WHERE ID = :id";
            $stmt = $this->db->prepare($Q);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    /**
     * Retrieves a user record from the database by username.
     *
     * @param string $usrname The username of the user to retrieve.
     * @return array|false The user record as an associative array if found, false otherwise.
     * @throws Exception If the input data is invalid.
     */
    public function getUserByUsername($usrname)
    {
        try {
            // Sanitizzazione e validazione input
            $usrname = sanitize_input($usrname);
            if (!$usrname) {
                throw new Exception("Invalid input data.");
            }

            $Q = "SELECT * FROM users WHERE USERNAME = :usr";
            $stmt = $this->db->prepare($Q);
            $stmt->bindParam(':usr', $usrname, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    /**
     * Retrieves a user from the database by their email address.
     *
     * @param string $email The email address of the user to retrieve.
     * @return array|false The user data as an associative array if found, or false on failure.
     * @throws Exception If the provided email is not valid.
     */
    public function getUserByEmail($email)
    {
        try {
            // Sanitizzazione e validazione input
            $email = sanitize_input($email);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid input data.");
            }

            $Q = 'SELECT * FROM users WHERE EMAIL = :e';
            $stmt = $this->db->prepare($Q);
            $stmt->bindParam('e', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    /**
     * Get the number of users by username.
     *
     * This method retrieves the count of users from the database that match the given username.
     *
     * @param string $usrname The username to search for.
     * @return array|false An associative array containing the count of users with the key 'users_count', or false on failure.
     * @throws Exception If the input data is invalid.
     */
    public function getNumOfUsersByUsrname($usrname)
    {
        try {
            // Sanitizzazione e validazione input
            $usrname = sanitize_input($usrname);
            if (!$usrname) {
                throw new Exception("Invalid input data.");
            }

            $Q = "SELECT COUNT(*) AS users_count FROM users WHERE USERNAME = :usr";
            $stmt = $this->db->prepare($Q);
            $stmt->bindParam(':usr', $usrname, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    /**
     * Retrieves the number of users with the specified email address.
     *
     * @param string $email The email address to search for.
     * @return array|false An associative array containing the count of users with the specified email address, or false on failure.
     * @throws Exception If the provided email is invalid.
     */
    public function getNumOfUsersByEmail($email)
    {
        try {
            // Sanitizzazione e validazione input
            $email = sanitize_input($email);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid input data.");
            }

            $Q = "SELECT COUNT(*) AS users_count FROM users WHERE EMAIL = :e";
            $stmt = $this->db->prepare($Q);
            $stmt->bindParam(':e', $email, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    /**
     * Retrieves a list of non-premium users from the database.
     *
     * This method executes a SQL query to select all users where the PREMIUM field is set to 0.
     * It returns an associative array of the results.
     *
     * @return array|false An array of non-premium users, or false if an error occurs.
     */
    public function getNonPremiumUsers()
    {
        try {
            $Q = "SELECT * FROM users WHERE PREMIUM = 0";
            $stmt = $this->db->prepare($Q);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    /**
     * Retrieves all premium users from the database.
     *
     * This method executes a SQL query to select all users with a premium status
     * from the users table. It returns an associative array of premium users.
     *
     * @return array|false An associative array of premium users, or false on failure.
     */
    public function getPremiumUsers()
    {
        try {
            $Q = "SELECT * FROM users WHERE PREMIUM = 1";
            $stmt = $this->db->prepare($Q);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    /**
     * Retrieves all users from the database.
     *
     * @return array|false An array of associative arrays representing the users, or false on failure.
     */
    public function getAllUsers()
    {
        try {
            $Q = "SELECT * FROM users";
            $stmt = $this->db->prepare($Q);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    /**
     * Updates the premium status of a user in the database.
     *
     * @param int $idU The ID of the user.
     * @param int $premium The premium status to be set (e.g., 1 for premium, 0 for non-premium).
     * @return bool Returns true on success, false on failure.
     * @throws Exception If the input data is invalid.
     */
    public function updatePremium($idU, $premium)
    {
        try {
            // Sanitizzazione e validazione input
            $idU = sanitize_input($idU);
            $premium = sanitize_input($premium);

            if (
                !filter_var($idU, FILTER_VALIDATE_INT) ||
                !in_array($premium, ['0', '1'], true)
            ) {
                throw new Exception("Invalid input data.");
            }

            $Q = "UPDATE users SET PREMIUM = :p WHERE ID = :id";
            $stmt = $this->db->prepare($Q);
            $stmt->bindParam(':p', $premium, PDO::PARAM_INT);
            $stmt->bindParam(':id', $idU, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    /**
     * Updates the password of a user in the database.
     *
     * @param int $idU The ID of the user whose password is to be updated.
     * @param string $newPsw The new password to be set for the user.
     * @return bool Returns true on success, false on failure.
     * @throws Exception If the input data is invalid.
     */
    public function updatePsw($idU, $newPsw)
    {
        try {
            // Sanitizzazione e validazione input
            $idu = sanitize_input($idU);
            $psw = sanitize_input($newPsw);

            if (!filter_var($idu, FILTER_VALIDATE_INT) || !$psw) {
                throw new Exception("Invalid input data.");
            }

            $Q = 'UPDATE users SET PASSWORD = :p WHERE ID = :id';
            $stmt = $this->db->prepare($Q);
            $stmt->bindParam(':id', $idu, PDO::PARAM_INT);
            $stmt->bindParam(':p', $psw, PDO::PARAM_STR);
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