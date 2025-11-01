<?php
session_start();
require_once 'config.php';

if (isset($_POST['register'])) {
    $ime = $_POST['ime'];
    $priimek = $_POST['priimek'];
    $email = $_POST['email'];
    $uporabnisko_ime = $_POST['uporabnisko_ime'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    
    $vloga = ($role === 'profesor') ? 'ucitelj' : 'ucenec';

    $conn->query("INSERT INTO uporabniki (uporabnisko_ime, geslo, ime, priimek, email, vloga) 
                 VALUES ('$uporabnisko_ime', '$password', '$ime', '$priimek', '$email', '$vloga')");

    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM uporabniki WHERE email = '$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['geslo'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['ime'] . ' ' . $user['priimek'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['vloga'];  // Changed from vloga to role for consistency

            if ($user['vloga'] === 'admin') {
                header("Location: admin_page.php");
            } else if ($user['vloga'] === 'ucitelj') {
                header("Location: profesor_page_new.php");
            } else if ($user['vloga'] === 'ucenec') {
                header("Location: ucenec_page.php");
            }
            exit();
        }   
    }

    $_SESSION['login_error'] = 'Incorrect email or password';
    header("Location: index.php");
    exit();
}
?>