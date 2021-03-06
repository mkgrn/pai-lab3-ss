<?php
session_start();
// Jeśli zmienne sesji nie są ustawione, skrypt próbuje użyć plików cookie.
if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) {
        $_SESSION['user_id'] = $_COOKIE['user_id'];
        $_SESSION['username'] = $_COOKIE['username'];
    }
} ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Niedopasowani - Tu przeciwieństwa się przyciągają!</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body>
    <h3>Niedopasowani - Tu przeciwieństwa się przyciągają!</h3>
    <?php
    require_once('source/appvars.php');
    require_once('source/connectvars.php');
    // Generowanie menu nawigacyjnego.
    if (isset($_SESSION['username'])) {
        echo '&#10084; <a href="source/viewprofile.php">Wyświetl profil</a><br />';
        echo '&#10084; <a href="source/editprofile.php">Edytuj profil</a><br />';
        echo '&#10084; <a href="source/logout.php">Wyloguj się (' . $_SESSION['username'] . ')</a><br />';
    } else {
        echo '&#10084; <a href="source/login.php">Zaloguj się</a><br />';
        echo '&#10084; <a href="source/signup.php">Zarejestruj się</a><br />';
    }
    // Łączenie się z bazą danych.
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    // Pobieranie danych użytkowników z bazy MySQL.
    $query = "SELECT user_id, first_name, picture FROM mismatch_user WHERE first_name "
        . "IS NOT NULL ORDER BY join_date DESC LIMIT 5";
    $data = mysqli_query($dbc, $query);
    // Przejście w pętli po tablicy danych użytkowników i wyświetlenie ich w kodzie HTML.
    echo '<h4>Najnowsi członkowie:</h4>';
    echo '<table>';
    while ($row = mysqli_fetch_array($data)) {
        if (is_file(MM_UPLOADPATH . $row['picture']) && filesize(MM_UPLOADPATH . $row['picture']) > 0) {
            echo '<tr><td><img src="' . MM_UPLOADPATH . $row['picture'] . '" alt="' . $row['first_name']
                . '" /></td>';
        } else {
            echo '<tr><td><img src="' . MM_UPLOADPATH . 'nopic.jpg" alt="' . $row['first_name']
                . '" /></td>';
        }
        if (isset($_SESSION['user_id'])) { // Dodanie odnośnika umożliwiającego przeglądanie kont użytkowników
            echo '<td><a href="viewprofile.php?user_id=' . $row['user_id'] . '">' . $row['first_name']
                . '</a></td></tr>';
        } else {
            echo '<td>' . $row['first_name'] . '</td></tr>';
        }
    }
    echo '</table>';
