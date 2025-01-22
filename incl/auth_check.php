<?php

/**
 * This script checks if a user is authenticated by verifying the presence of a session variable 'usr'.
 * If the session variable 'usr' is not set, the user is redirected to the 'index.php' page.
 * 
 */

if (!isset($_SESSION['usr'])) {
   header("Location: http://localhost/SNH_WebProj_Novels/index.php");
   exit;
}

?>