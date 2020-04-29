<?php session_start();
// Jeśli użytkownik jest zalogowany, należy usunąć zmienne sesji, aby go wylogować.
if (isset($_SESSION['user_id'])) {
$_SESSION = array();// Usunięcie zmiennych sesji przez wykasowanie elementów tablicy $_SESSION.
if (isset($_COOKIE[session_name()])) { setcookie(session_name(), '', time() - 3600); }
session_start(); // Kończenie sesji.
}
// Usunięcie plików cookie z identyfikatorem i nazwą użytkownika.
setcookie('user_id', '', time() - 3600);
setcookie('username', '', time() - 3600);
// Skierowanie użytkownika do strony głównej.
$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
header('Location: ' . $home_url);