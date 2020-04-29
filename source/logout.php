<?php
session_start();
// Jeśli użytkownik jest zalogowany, należy usunąć zmienne sesji, aby go wylogować.
if (isset($_SESSION['user_id'])) {
// Usunięcie zmiennych sesji przez wykasowanie elementów tablicy $_SESSION.
$_SESSION = array();
session_start();
}
// Skierowanie użytkownika do strony głównej.
$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
header('Location: ' . $home_url);