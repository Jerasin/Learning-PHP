<?php

    // ใช้สำหรับเริ่มต้นใช้งาน session
    session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>  
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4">
            <form class="mt-5" action="register_db.php" method="post">
            <div class="mb-3">
                <label  class="form-label">Username</label>
                <input type="text" class="form-control" name="username">
            </div>

            <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="email" class="form-control" name="email">
            </div>

            <div class="mb-3">
                <label  class="form-label">Password</label>
                <input type="password" class="form-control" name="password">
            </div>

            <div class="mb-3">
                <select   select class="form-select" name="role">
                    <?php
                    require_once 'connection.php';
                    session_start();
                    try{
                        $select_data = $db->prepare("SELECT id , name FROM master_role ORDER BY id ASC");
                        $select_data->execute();
                        $rows = $select_data->fetchAll();
                        foreach ($rows as $row) {
                            echo '<option  value="'.$row['id'].'">' .$row['name'] .'</option>';
                            }

                    }
                    catch(PDOException $e){
                        echo "Error: " . $e->getMessage();
                    }
                    ?>
                </select>
            </div>

            
                <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <h3>
                    <?php echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    ?>
                    </h3>
                </div>
                <?php endif ?>
                
            

            <div class="w-100">
                <button type="submit" name="submit" class="btn btn-primary w-100">Register</button>
            </div>
            

          
            <div class="mt-3 ">
                <a href="index.php" class="btn btn-warning w-100">Login</a>
            </div>
   

        </form>
            </div>
        </div>
    </div>
    
    
   
    


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>