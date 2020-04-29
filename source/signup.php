<?php session_start(); ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Niedopasowani - Rejestracja</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body>
    <h3>Niedopasowani - Rejestracja</h3>
    <?php
    require_once('appvars.php');
    require_once('connectvars.php');
    // Łączenie się z bazą danych.
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (isset($_POST['submit'])) {
        // Pobieranie danych do profilu z żądania POST.
        $username = mysqli_real_escape_string($dbc, trim($_POST['username']));
        $password1 = mysqli_real_escape_string($dbc, trim($_POST['password1']));
        $password2 = mysqli_real_escape_string($dbc, trim($_POST['password2']));
        if (!empty($username) && !empty($password1) && !empty($password2) && ($password1 == $password2)) {
            // Sprawdzanie, czy dana nazwa nie jest już zajęta.
            $query = "SELECT * FROM mismatch_user WHERE username = '$username'";
            $data = mysqli_query($dbc, $query);
            if (mysqli_num_rows($data) == 0) {
                // Nazwa jest nowa, dlatego można wstawić dane do bazy.
                $query = "INSERT INTO mismatch_user (username, password, join_date) "
                    . "VALUES ('$username', SHA('$password1'), NOW())";
                mysqli_query($dbc, $query);
                // Informowanie użytkownika o udanym zakończeniu rejestracji.
                echo '<p>Tworzenie konta zakończyło się powodzeniem. Możesz się zalogować i '
                    . '<a href="editprofile.php">zmodyfikować profil</a>.</p>';
                mysqli_close($dbc);
                exit();
            } else {
                // Dana nazwa jest już zajęta, dlatego należy wyświetlić komunikat o błędzie.
                echo '<p class="error">Dana nazwa jest już zajęta - spróbuj użyć innej.</p>';
                $username = "";
            }
        } else {
            echo '<p class="error">Musisz wpisać wszystkie dane (hasło należy wprowadzić dwukrotnie).'
                . '</p>';
        }
    }
    mysqli_close($dbc);
    ?>
    <p>Wpisz nazwę i hasło, aby zarejestrować się w witrynie Niedopasowani.</p>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <fieldset>
            <legend>Dane do rejestracji</legend>
            <label for="username">Nazwa użytkownika:</label>
            <input type="text" id="username" name="username" value="<?php if (!empty($username)) {
                                                                        echo $username;
                                                                    } ?>" /><br />
            <label for="password1">Hasło:</label>
            <input type="password" id="password1" name="password1" /><br />
            <label for="password2">Hasło (wpisz ponownie):</label>
            <input type="password" id="password2" name="password2" /><br />
        </fieldset>
        <input type="submit" value="Zarejestruj" name="submit" />
    </form>
</body>

</html>