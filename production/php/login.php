<?php
/**
 * Created by PhpStorm.
 * User: JoshB
 * Date: 12/27/2018
 * Time: 3:25 PM
 */

$json = [];
$json['error'] = false;

$username = '';
$password = '';

if(isset($_POST['username'])){
    $username = $_POST['username'];
}

if(isset($_POST['password'])){
    $password = $_POST['password'];
}

if(empty($username)){
    $json['error'] = true;
    $json['username_error'] = "Please enter your username.";
}

if(empty($password)){
    $json['error'] = true;
    $json['password_error'] = "Please enter your password.";
}

if($json['error']){
    echo json_encode($json);
    exit();
}

require_once 'db.inc.php';

try {
    $result = $db->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $result->bindParam(1, $username);
    $result->execute();
    $user = $result->fetchAll(PDO::FETCH_ASSOC);
    if(count($user) === 1){
        if(password_verify($password, $user[0]['password'])){
            session_start();
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user[0]['id'];
        }else{
            $json['error'] = true;
            $json['db_error'] = "username and password do not match. Try again.";
        }
        echo json_encode($json);
    }
} catch (PDOException $e) {
    die("DB ERROR: ". $e->getMessage());
}

