<?php
session_start();
require_once('connectvars.php');
// Usuwanie komunikatu o błędzie.
$error_msg = "";
if (!isset($_SESSION['user_id'])) {
    if (isset($_POST['submit'])) {
        // Łączenie się z bazą danych.
        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        // Pobieranie danych logowania podanych przez użytkownika.
        $user_username = mysqli_real_escape_string($dbc, trim($_POST['username']));
        $user_password = mysqli_real_escape_string($dbc, trim($_POST['password']));
        if (!empty($user_username) && !empty($user_password)) {
            // Wyszukiwanie nazwy i hasła w bazie.
            $query = "SELECT user_id, username FROM mismatch_user WHERE username = '$user_username' "
                . "AND password = SHA('$user_password')";
            $data = mysqli_query($dbc, $query);
            if (mysqli_num_rows($data) == 1) {
                // Dane logowania są poprawne, dlatego należy ustawić pliki cookie i przejść do strony.
                $row = mysqli_fetch_array($data);
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
                header('Location: ' . $home_url);
            } else {
                // Para nazwa użytkownika - hasło jest nieprawidłowe, dlatego należy ustawić komunikat.
                $error_msg = 'Musisz podać poprawną parę nazwa - hasło, aby się zalogować.';
            }
        } else {
            // Użytkownik nie podał pary nazwy - hasło, dlatego należy ustawić komunikat
            $error_msg = 'Musisz podać parę nazwa - hasło, aby się zalogować.';
        }
    }
} ?>
<html>

<head>
    <meta charset="utf-8" />
    <title>Niedopasowani - logowanie</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body>
    <h3>Niedopasowanie - Logowanie</h3>
    <?php
    if (empty($_SESSION['user_id'])) {
        echo '<p class="error"' . $error_msg . '</p>'; ?>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <fieldset>
                <legend>Logowanie</legend>
                <label for="username">Nazwa użytkownika:</label>
                <input type="text" id="username" name="username" value="<?php if (!empty($user_username)) {
                                                                            echo $user_username;
                                                                        } ?>" /><br />
                <label type="password">Hasło:</label>
                <input type="password" id="password" name="password" />
            </fieldset>
            <input type="submit" value="Zaloguj" name="submit" />
        </form>
    <?php
    } else {
        // Potwierdzenie udanego zalogowania.
        echo '<p class="login">Zalogowany użytkownik: ' . $_SESSION['username'] . '.</p>';
    }
    ?>
</body>

</html>