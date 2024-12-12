<?php

require_once __DIR__ . '/../incl/utils.php';


class Token
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
     * Inserts a password reset token into the database.
     *
     * @param int $idu The user ID.
     * @param string $token The password reset token.
     * @param int $exp The expiration time of the token.
     * @return bool Returns true on success, false on failure.
     * @throws Exception If the input data is invalid.
     */
    public function insertTokenPswRst($idu, $token, $exp)
    {
        try {
            // Sanitizzazione e validazione input
            $idu = sanitize_input($idu);
            $token = sanitize_input($token);
            $exp = sanitize_input($exp);

            if (
                !filter_var($idu, FILTER_VALIDATE_INT) ||
                !filter_var($exp, FILTER_VALIDATE_INT) ||
                !$token
            ) {
                throw new Exception("Invalid input data.");
            }

            $Q = 'INSERT INTO psw_resets (ID_U, TOKEN, EXPIRE) VALUES (:i, :t, :e)';
            $stmt = $this->db->prepare($Q);
            $stmt->bindParam(':i', $idu, PDO::PARAM_INT);
            $stmt->bindParam(':t', $token, PDO::PARAM_STR);
            $stmt->bindParam(':e', $exp, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    /**
     * Inserts a "Remember Me" token into the database.
     *
     * @param int $idu The user ID.
     * @param string $token The token string.
     * @param int $exp The expiration time of the token.
     * @return bool Returns true on success, false on failure.
     * @throws Exception If the input data is invalid.
     */
    public function insertTokenRememberMe($idu, $token, $exp)
    {
        try {
            // Sanitizzazione e validazione input
            $idu = sanitize_input($idu);
            $token = sanitize_input($token);
            $exp = sanitize_input($exp);

            if (
                !filter_var($idu, FILTER_VALIDATE_INT) ||
                !filter_var($exp, FILTER_VALIDATE_INT) ||
                !$token
            ) {
                throw new Exception("Invalid input data.");
            }

            $Q = 'INSERT INTO remember_me_tokens (ID_U, TOKEN, EXPIRE) VALUES (:i, :t, :e)';
            $stmt = $this->db->prepare($Q);
            $stmt->bindParam(':i', $idu, PDO::PARAM_INT);
            $stmt->bindParam(':t', $token, PDO::PARAM_STR);
            $stmt->bindParam(':e', $exp, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    /**
     * Verifies the validity of a password reset token.
     *
     * This function checks if the provided token is valid and has not expired.
     * It sanitizes and validates the input token, then queries the database
     * to find a matching token that has not yet expired.
     *
     * @param string $token The password reset token to be verified.
     * @return mixed Returns the reset request data if the token is valid and not expired,
     *               false if an error occurs or the token is invalid/expired.
     * @throws Exception If the input token is invalid.
     */
    public function verifyValidityTknPswReset($token)
    {
        try {
            // Sanitizzazione e validazione input
            $token = sanitize_input($token);
            if (!$token) {
                throw new Exception("Invalid input data.");
            }

            $now = time();
            $Q = 'SELECT * FROM psw_resets WHERE TOKEN = :t AND EXPIRE > :n';
            $stmt = $this->db->prepare($Q);
            $stmt->bindParam(':t', $token, PDO::PARAM_STR);
            $stmt->bindParam(':n', $now, PDO::PARAM_INT);
            $stmt->execute();
            $resetRequest = $stmt->fetch();
            return $resetRequest;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    /**
     * Verifies the validity of a "Remember Me" token.
     *
     * This function sanitizes and validates the input token, then checks the database
     * to see if the token exists and has not expired.
     *
     * @param string $token The "Remember Me" token to be verified.
     * @return mixed Returns the token data if valid, false if an error occurs.
     * @throws Exception If the input token is invalid.
     */
    public function verifyValidityTknRememberMe($token)
    {
        try {
            // Sanitizzazione e validazione input
            $token = sanitize_input($token);

            if (!$token) {
                throw new Exception("Invalid input data.");
            }

            $now = time();
            $Q = 'SELECT * FROM remember_me_tokens WHERE TOKEN = :t AND EXPIRE > :n';
            $stmt = $this->db->prepare($Q);
            $stmt->bindParam(':t', $token, PDO::PARAM_STR);
            $stmt->bindParam(':n', $now, PDO::PARAM_INT);
            $stmt->execute();
            $resetRequest = $stmt->fetch();
            return $resetRequest;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return false;
        }
    }

    /**
     * Deletes a password reset token from the database.
     *
     * This function sanitizes and validates the provided token, then attempts to delete
     * the corresponding entry from the `psw_resets` table in the database.
     *
     * @param string $token The password reset token to be deleted.
     * @return bool Returns true if the token was successfully deleted, false otherwise.
     * @throws Exception If the input token is invalid.
     */
    public function deleteTokenPswRst($token)
    {
        try {
            // Sanitizzazione e validazione input
            $token = sanitize_input($token);

            if (!$token) {
                throw new Exception("Invalid input data.");
            }

            $Q = 'DELETE FROM psw_resets WHERE TOKEN = :t';
            $stmt = $this->db->prepare($Q);
            $stmt->bindParam(':t', $token, PDO::PARAM_STR);
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