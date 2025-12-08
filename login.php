<?php
session_start();
include 'connect.php';

// Gebruik PRG: verwerk POST dan redirect (geen pagina-output vóór header())
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Bereid een correcte prepared statement voor
    $query = "SELECT `password` FROM `users` WHERE `e-mail` = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        $_SESSION['flash_error'] = 'Databasefout.';
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($db_password);
        $stmt->fetch();

        // Eenvoudige vergelijking; gebruik password_hash/verify bij gehashte wachtwoorden
        if ($password === $db_password) {
            $_SESSION['logged_in'] = true;
            $_SESSION['email'] = $email;
            $stmt->close();
            // Succes: redirect naar admin
            header('Location: admin.php');
            exit;
        } else {
            $_SESSION['flash_error'] = 'Wachtwoord klopt niet';
            $stmt->close();
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
    } else {
        $_SESSION['flash_error'] = 'Gebruiker niet gevonden';
        $stmt->close();
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Utrechts Archief</title>

    <style>
        body {
            background: white;
            color: black;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-box {
            width: 320px;
            padding: 20px;
            border: 1px solid rgba(36, 112, 101, 0.4);
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-top: 0;
        }

        input[type="text"],
        input[type="password"] {
            width: 300px;
            padding: 10px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 15px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: rgba(36, 112, 101);
            color: black;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            opacity: 0, 9;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="login-box">
        <h2>Admin Login</h2>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Gebruikersnaam" required>
            <input type="password" name="password" placeholder="Wachtwoord" required>
            <button type="submit">Inloggen</button>
        </form>
    </div>

</body>

</html>