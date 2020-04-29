<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Niedopasowani - Wyświetl profil</title>
    <link rel="stylesheet" type="text/css" href="styles/style.css" />
</head>

<body>
    <h3>Niedopasowani - Wyświetl profil</h3>
    <?php
    session_start();
    require_once('appvars.php');
    require_once('connectvars.php');
    // Przed przejściem do dalszych operacji należy się upewnić, że użytkownik jest zalogowany.
    if (!isset($_SESSION['user_id'])) {
        echo '<p class="login"><a href="login.php">Zaloguj się</a>, aby uzyskać dostęp do strony.</p>';
        exit();
    } else {
        echo ('<p class="login">Zalogowany użytkownik: ' . $_SESSION['username']
            . '. <a href="logout.php">Wyloguj się</a>.</p>');
        $user_id = $_SESSION['user_id'];
    }
    // Łączenie się z bazą danych.
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Pobieranie danych użytkownika z bazy.
    if (!isset($_GET['user_id'])) {
        $query = "SELECT first_name, last_name, gender, birthdate, city, state, picture "
            . "FROM mismatch_user WHERE user_id = '$user_id'";
    } else {
        $query = "SELECT first_name, last_name, gender, birthdate, city, state, picture "
            . "FROM mismatch_user WHERE user_id = '" . $_GET['user_id'] . "'";
    }
    $data = mysqli_query($dbc, $query);
    if (mysqli_num_rows($data) == 1) {
        // Znaleziono wiersz z danymi użytkownika, dlatego należy je wyświetlić.
        $row = mysqli_fetch_array($data);
        echo '<table>';
        if (!empty($row['username'])) {
            echo '<tr><td class="label">Nazwa użytkownika:</td><td>' . $row['username'] . '</td></tr>';
        }
        if (!empty($row['first_name'])) {
            echo '<tr><td class="label">Imię:</td><td>' . $row['first_name'] . '</td></tr>';
        }
        if (!empty($row['last_name'])) {
            echo '<tr><td class="label">Nazwisko:</td><td>' . $row['last_name'] . '</td></tr>';
        }
        if (!empty($row['gender'])) {
            echo '<tr><td class="label">Płeć:</td><td>';
            if ($row['gender'] == 'M') {
                echo 'Mężczyzna';
            } else if ($row['gender'] == 'K') {
                echo 'Kobieta';
            } else {
                echo '?';
            }
            echo '</td></tr>';
        }
        if (!empty($row['birthdate'])) {
            if (!isset($_GET['user_id']) || ($user_id == $_GET['user_id'])) {
                // Wyświetlanie dnia urodzenia danemu użytkownikowi.
                echo '<tr><td class="label">Data urodzenia:</td><td>' . $row['birthdate'] . '</td></tr>';
            } else {
                // Wyświetlanie samego roku pozostałym użytkownikom.
                list($year, $month, $day) = explode('-', $row['birthdate']);
                echo '<tr><td class="label">Rok urodzenia:</td><td>' . $year . '</td></tr>';
            }
        }
        if (!empty($row['city']) || !empty($row['state'])) {
            echo '<tr><td class="label">Miejscowość:</td><td>' . $row['city'] . ', ' . $row['state'] . '</td></tr>';
        }
        if (!empty($row['picture'])) {
            echo '<tr><td class="label">Zdjęcie:</td><td><img src="' . MM_UPLOADPATH . $row['picture'] . '" alt="Zdjęcie z profilu" /></td></tr>';
        }
        echo '</table>';
        if (!isset($_GET['user_id']) || ($user_id == $_GET['user_id'])) {
            echo '<p>Czy chcesz <a href="editprofile.php">zmodyfikować profil</a>?</p>';
        }
    } // Koniec przetwarzania wiersza z danymi użytkownika.
    else {
        echo '<p class="error">Wystąpił problem przy próbie dostępu do profilu.</p>';
    }
    mysqli_close($dbc);
    ?>
</body>

</html>