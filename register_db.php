<?php

require_once 'connection.php';

session_start();

if (isset($_POST['submit'])) {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hash_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
}

// echo "$username $email $password $role" 

if (empty($username)) {
    $errorMsg = "Please Enter Username";
} else if (empty($email)) {
    $errorMsg = "Please Enter Email";
} else if (empty($password)) {
    $errorMsg = "Please Enter Password";
} else if (empty($role)) {
    $errorMsg = "Please Enter Role";
} else if ($username and $email and $password and $role) {
    try {
        $select_data = $db->prepare("SELECT username, email , password , role FROM user_info WHERE email = :uemail ");

        // $select_data->bindParam(":uusername", $username);
        $select_data->bindParam(":uemail", $email);
        $select_data->execute();

        $row = $select_data->fetch(PDO::FETCH_ASSOC);
        if (empty($row['email']) and !isset($_SESSION['error'])) {
            $insert_data = $db->prepare("INSERT INTO user_info(username, email, password, role) VALUES (:uusername, :uemail, :upassword, :urole)");

            $insert_data->bindParam(":uusername", $username);
            $insert_data->bindParam(":uemail", $email);
            $insert_data->bindParam(":upassword", $hash_password);
            $insert_data->bindParam(":urole", $role);

            if ($insert_data->execute()) {
                $_SESSION['success'] = "Register Successfully...";
                header("location: index.php");
            } else {
                $_SESSION['error'] = "Register Failed...";
                header("location: register.php");
            }
        } else if ($row['email'] == $email) {
            $_SESSION['error'] = "Email already exists";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}
