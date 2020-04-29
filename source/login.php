<?php
require_once('connectvars.php');
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
    // Użytkownik nie wpisał nazwy i hasła, dlatego trzeba wysłać nagłówki uwierzytelniania.
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Niedopasowani"');
    exit('<h3>Niedopasowani</h3>Musisz podać nazwę i hasło, aby się zalogować i uzyskać dostęp do strony. '
    . 'Jeśli nie jesteś członkiem społeczności, <a href="signup.php">zarejestruj się</a>.');
    }
// Łączenie się z bazą danych.
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
// Pobieranie danych logowania podanych przez użytkownika.
$user_username = mysqli_real_escape_string($dbc, trim($_SERVER['PHP_AUTH_USER']));
$user_password = mysqli_real_escape_string($dbc, trim($_SERVER['PHP_AUTH_PW']));
// Wyszukiwanie nazwy i hasła w bazie.
$query = "SELECT user_id, username FROM mismatch_user WHERE username = '$user_username' "
. "AND password = SHA('$user_password')";
$data = mysqli_query($dbc, $query);
if (mysqli_num_rows($data) == 1) {
// Dane są poprawne, dlatego można przypisać identyfikator i nazwę użytkownika do zmiennych.
$row = mysqli_fetch_array($data);
$user_id = $row['user_id'];
$username = $row['username'];
} else {
// Para nazwa - hasło jest nieprawidłowa, dlatego należy przesłać nagłówki uwierzytelniania.
header('HTTP/1.1 401 Unauthorized');
header('WWW-Authenticate: Basic realm="Niedopasowani"');
exit('<h2>Niedopasowani</h2>Musisz podać nazwę i hasło, aby się zalogować i uzyskać dostęp do strony.');
}
// Potwierdzanie zalogowania użytkownika.
echo('<p class="login">Zalogowany użytkownik: ' . $username . '.</p>');
