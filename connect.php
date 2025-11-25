    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ua_db";
    // maak connectie
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connectie
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Connected bericht
    $conn->set_charset("utf8"); // Set the character set to UTF-8
    ?>