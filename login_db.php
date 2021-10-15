<?php

require_once 'connection.php';

session_start();

if (isset($_POST['submit'])) {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
}

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
        $select_user = $db->prepare("SELECT  id , username, email , password , role FROM user_info WHERE email = :uemail AND username = :uusername  AND role = :urole");
        $select_user->bindParam(":uusername", $username);
        $select_user->bindParam(":uemail", $email);
        $select_user->bindParam(":urole", $role);
        $select_user->execute();

        while ($row = $select_user->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $dbusername = $row['username'];
            $dbemail = $row['email'];
            $dbpassword = $row['password'];
            $dbrole = $row['role'];
        }

        if ($username != null and $email != null and $password != null and $role != null) {
            // echo $select_user->rowCount();
            if ($select_user->rowCount() > 0) {
                $hash = password_verify($password, $dbpassword);

                if (empty($hash)) {
                    $_SESSION['error'] = "Wrong username or email or password or role";
                    header("location: index.php");
                }

                if ($username == $dbusername and $email == $dbemail and $hash  and $role == $dbrole) {

                    switch ($dbrole) {
                        case '1':
                            $_SESSION['id'] = $id;
                            $_SESSION['email'] = $email;
                            $_SESSION['success'] = "Admin Login Successfully...";
                            header("location: admin/admin_home.php");
                            break;

                        case '3':
                            $_SESSION['employee_login'] = $email;
                            $_SESSION['success'] = "Employee Login Successfully...";
                            header("location: employee/employee_home.php");
                            break;

                        case '2':
                            $_SESSION['user_login'] = $email;
                            $_SESSION['success'] = "User Login Successfully...";
                            header("location: user/user_home.php");
                            break;

                        default:
                            $_SESSION['error'] = "Wrong username or email or password or role";
                            header("location: index.php");
                    }
                }
            } else {
                $_SESSION['error'] = "Wrong username or email or password or role";
                header("location: index.php");
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
