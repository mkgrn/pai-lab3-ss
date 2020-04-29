<?php
// Jeśli użytkownik jest zalogowany, należy usunąć pliki cookie, aby go wylogować.
if (isset($_COOKIE['user_id'])) {
// Usuwanie plików cookie z identyfikatorem i nazwą użytkownika przez ustawienie
// daty wygasania na godzinę (3600 sekund) wstecz.
setcookie('user_id', '', time() - 3600);
setcookie('username', '', time() - 3600);
}
// Skierowanie użytkownika do strony głównej.
$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
header('Location: ' . $home_url);