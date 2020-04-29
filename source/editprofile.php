<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Niedopasowani - Wyświetl profil</title>
    <link rel="stylesheet" type="text/css" href="styles/style.css" />
</head>

<body>
    <h3>Niedopasowani - Edycja profilu</h3>
    <?php
    session_start();
    require_once('appvars.php');
    require_once('connectvars.php');
    // Przed przejściem do dalszych operacji należy się upewnić, że użytkownik jest zalogowany.
if (!isset($_SESSION['user_id'])) {
    echo '<p class="login"><a href="login.php">Zaloguj się</a>, aby uzyskać dostęp do strony.</p>';
    exit();
    } else {
    echo('<p class="login">Zalogowany użytkownik: ' . $_SESSION['username']
    . '. <a href="logout.php">Wyloguj się</a>.</p>');
    $user_id = $_SESSION['user_id'];
    }
    // Łączenie się z bazą danych.
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (isset($_POST['submit'])) {
        // Pobieranie danych do profilu użytkownika z żądania POST.
        $first_name = mysqli_real_escape_string($dbc, trim($_POST['firstname']));
        $last_name = mysqli_real_escape_string($dbc, trim($_POST['lastname']));
        $gender = mysqli_real_escape_string($dbc, trim($_POST['gender']));
        $birthdate = mysqli_real_escape_string($dbc, trim($_POST['birthdate']));
        $city = mysqli_real_escape_string($dbc, trim($_POST['city']));
        $state = mysqli_real_escape_string($dbc, trim($_POST['state']));
        $old_picture = mysqli_real_escape_string($dbc, trim($_POST['old_picture']));
        $new_picture = mysqli_real_escape_string($dbc, trim($_FILES['new_picture']['name']));
        $new_picture_type = $_FILES['new_picture']['type'];
        $new_picture_size = $_FILES['new_picture']['size'];
        list($new_picture_width, $new_picture_height) = getimagesize($_FILES['new_picture']['tmp_name']);
        $error = false;

        // Walidacja i (w razie potrzeby) przenoszenie przesłanego pliku graficznego.
        if (!empty($new_picture)) {
            if ((($new_picture_type == 'image/gif') || ($new_picture_type == 'image/jpeg') || ($new_picture_type == 'image/pjpeg') || ($new_picture_type == 'image/png')) && ($new_picture_size > 0) && ($new_picture_size <= MM_MAXFILESIZE) && ($new_picture_width <= MM_MAXIMGWIDTH) && ($new_picture_height <= MM_MAXIMGHEIGHT)) {
                if ($_FILES['new_picture']['error'] == 0) {
                    // Przenoszenie pliku do docelowego katalogu.
                    $target = MM_UPLOADPATH . basename($new_picture);
                    if (move_uploaded_file($_FILES['new_picture']['tmp_name'], $target)) {
                        // Przenoszenie nowego pliku zakończyło się powodzeniem.
                        // Teraz trzeba usunąć poprzednie zdjęcie.
                        if (!empty($old_picture) && ($old_picture != $new_picture)) {
                            @unlink(MM_UPLOADPATH . $old_picture);
                        }
                    } else {
                        // Przenoszenie nowego pliku zakończyło się powodzeniem.
                        // Należy usunąć tymczasowy plik i ustawić flagę błędu.
                        @unlink($_FILES['new_picture']['tmp_name']);
                        $error = true;
                        echo '<p class="error">Wystąpił problem przy przesyłaniu pliku.</p>';
                    }
                }
            } else {
                // Nowy plik jest nieprawidłowy, dlatego trzeba usunąć tymczasowy plik
                // i ustawić flagę błędu.
                @unlink($_FILES['new_picture']['tmp_name']);
                $error = true;
                echo '<p class="error">Musisz wybrać plik graficzny GIF, JPEG lub PNG o rozmiarze'
                    . ' nie większym niż ' . (MM_MAXFILESIZE / 1024) . ' (w kilobajtach) i '
                    . MM_MAXIMGWIDTH . 'x' . MM_MAXIMGHEIGHT . ' (w pikselach).</p>';
            }
        }
        // Aktualizowanie profilu w bazie danych.
        if (!$error) {
            if (!empty($first_name) && !empty($last_name) && !empty($gender) && !empty($birthdate) && !empty($city) && !empty($state)) {
                // Kolumnę picture należy ustawić tylko wtedy, jeśli użytkownik przesłał zdjęcie.
                if (!empty($new_picture)) {
                    $query = "UPDATE mismatch_user SET first_name = '$first_name', "
                        . "last_name = '$last_name', gender = '$gender', "
                        . "birthdate = '$birthdate', city = '$city', state = '$state', "
                        . "picture = '$new_picture' WHERE user_id = '$user_id'";
                } else {
                    $query = "UPDATE mismatch_user SET first_name = '$first_name', "
                        . "last_name = '$last_name', gender = '$gender', "
                        . " birthdate = '$birthdate', city = '$city', state = '$state' "
                        . "WHERE user_id = '$user_id'";
                }
                mysqli_query($dbc, $query);

                // Informowanie użytkownika o sukcesie.
                echo '<p>Aktualizacja profilu zakończyła się sukcesem. Czy chcesz <a href="viewprofile.php">zobaczyć swój profil</a>?</p>';
                mysqli_close($dbc);
                exit();
            } else {
                echo '<p class="error">Musisz podać wszystkie dane (zdjęcie jest opcjonalne).</p>';
            }
        }
    }       // Koniec obsługi przesyłania formularza.
    else {  // Pobieranie danych z profilu z bazy.
        $query = "SELECT first_name, last_name, gender, birthdate, city, state, picture FROM mismatch_user WHERE user_id = '$user_id'";
        $data = mysqli_query($dbc, $query);
        $row = mysqli_fetch_array($data);
        if ($row != NULL) {
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $gender = $row['gender'];
            $birthdate = $row['birthdate'];
            $city = $row['city'];
            $state = $row['state'];
            $old_picture = $row['picture'];
        } else {
            echo '<p class="error">Wystąpił problem przy próbie dostępu do profilu.</p>';
        }
    }
    mysqli_close($dbc);
    ?>
    <form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MM_MAXFILESIZE; ?>" />
        <fieldset>
            <legend>Dane osobowe</legend>
            <label for="firstname">Imię:</label>
            <input type="text" id="firstname" name="firstname" value="<?php if (!empty($first_name)) echo $first_name; ?>" /><br />
            <label for="lastname">Nazwisko:</label>
            <input type="text" id="lastname" name="lastname" value="<?php if (!empty($last_name)) echo $last_name; ?>" /><br />
            <label for="gender">Płeć:</label>
            <select id="gender" name="gender">
                <option value="M" <?php if (!empty($gender) && $gender == 'M') echo 'selected = "selected"'; ?>>Mężczyzna</option>
                <option value="K" <?php if (!empty($gender) && $gender == 'K') echo 'selected = "selected"'; ?>>Kobieta</option>
            </select><br />
            <label for="birthdate">Data urodzenia:</label>
            <input type="text" id="birthdate" name="birthdate" value="<?php if (!empty($birthdate)) echo $birthdate;
                                                                        else echo 'YYYY-MM-DD'; ?>" /><br />
            <label for="city">Miejscowość:</label>
            <input type="text" id="city" name="city" value="<?php if (!empty($city)) echo $city; ?>" /><br />
            <label for="state">Województwo:</label>
            <input type="text" id="state" name="state" value="<?php if (!empty($state)) echo $state; ?>" /><br />
            <input type="hidden" name="old_picture" value="<?php if (!empty($old_picture)) echo $old_picture; ?>" />
            <label for="new_picture">Zdjęcie:</label>
            <input type="file" id="new_picture" name="new_picture" />
            <?php if (!empty($old_picture)) {
                echo '<img class="profile" src="' . MM_UPLOADPATH . $old_picture . '"/>';
            } ?>
        </fieldset>
        <input type="submit" value="Zapisz profil" name="submit" />
    </form>
</body>

</html>