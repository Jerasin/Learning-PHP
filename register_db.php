<?php 

    require_once 'connection.php';

    session_start();

    if(isset($_POST['submit'])){

        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $hash_password = password_hash($_POST['password'],PASSWORD_DEFAULT);
        $role = $_POST['role'];
    }

    // echo "$username $email $password $role" 

    if(empty($username)){
        $errorMsg = "Please Enter Username";
    }
    else if(empty($email)){
        $errorMsg = "Please Enter Email";
    }
    else if(empty($password)){
        $errorMsg = "Please Enter Password";
    }
    else if(empty($role)){
        $errorMsg = "Please Enter Role";
    }
    else if($username AND $email AND $password AND $role){
        try{
            $select_data = $db->prepare("SELECT username, email , password , role FROM user_info WHERE email = :uemail AND username = :uusername AND password = :upassword AND role = :urole");

            $select_data->bindParam(":uusername" , $username);
            $select_data->bindParam(":uemail" , $email);
            $select_data->bindParam(":upassword" , $password);
            $select_data->bindParam(":urole" , $role);
            $select_data->execute();

            $row = $select_data->fetch(PDO::FETCH_ASSOC);

            if($row['username'] == $username){
                $errorMsg = "Username already exists";
            }
            else if($row['email'] == $email){
                $errorMsg = "Email already exists";
            }
            else if(!isset($errorMsg)){
                $insert_data = $db->prepare("INSERT INTO user_info(username, email, password, role) VALUES (:uusername, :uemail, :upassword, :urole)");

                $insert_data->bindParam(":uusername" , $username);
                $insert_data->bindParam(":uemail" , $email);
                $insert_data->bindParam(":upassword" , $hash_password);
                $insert_data->bindParam(":urole" , $role);
                
                if($insert_data->execute()){
                    $_SESSION['success'] = "Register Successfully...";
                    header("location: index.php");
                }
                else{
                    $_SESSION['error'] = "Register Failed...";
                    header("location: register.php");
                }
            }
        }
        catch(PDOException $e){
            echo "Error: " . $e->getMessage();
        }
    }

?>