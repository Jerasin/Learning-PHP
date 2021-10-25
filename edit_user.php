<?php
require_once 'connection.php';
session_start();

if (!isset($_SESSION['admin_login'])) {
  header('location: ../index.php');
}

try {
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $select_user = $db->prepare("SELECT u.id , u.username , u.email , u.createdAt , u.role as role_id , mr.name as role FROM `user_info` as u LEFT JOIN master_role as mr ON u.role = mr.id WHERE u.id = :id");
    $select_user->bindParam(':id', $id);
    $select_user->execute();

    $select_master_role = $db->prepare("SELECT id , name FROM master_role");
    $select_master_role->execute();
  }
} catch (PDOException $e) {
  $e->getMessage();
}

print_r($_POST);

if (isset($_POST['btn_update'])) {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $role = $_POST['role'];
  $updatedAt = date("Y-m-d");

  if (empty($username)) {
    $errorMsg = "Please enter Username";
  } else  if (empty($email)) {
    $errorMsg = "Please enter Email";
  } else {
    try {
      if (!isset($errorMsg)) {
        $update_data = $db->prepare("UPDATE user_info SET username = :username , email = :email , role= :role , updatedAt = :updatedAt   WHERE id = :id");
        $update_data->bindParam(":id", $id);
        $update_data->bindParam(":username", $username);
        $update_data->bindParam(":email", $email);
        $update_data->bindParam(":role", $role);
        $update_data->bindParam(":updatedAt", $updatedAt);

        if ($update_data->execute()) {
          $insertMsg = "Update Successfully...";
          // header("refresh:2;book_list.php");
          header("location: user_list.php");
        }
      }
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
  }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Edit</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <script src="js/show_image.js" type="text/javascript"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
</head>

<body>

  <div class="container-fluid p-0">

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Bookstore</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link my-2 text-center" aria-current="page" href="admin/admin_home.php">Home</a>
            </li>

            <li class="nav-item">
              <a class="nav-link my-2 text-center" aria-current="page" href="dashboard.php">Dashboard</a>
            </li>

            <li class="nav-item">
              <p class="nav-link btn btn-outline-white my-2 pe-none">
                <?php
                echo $_SESSION['admin_login']
                ?>
              </p>
            </li>

            <li class="nav-item">
              <a href="logout.php" class="nav-link btn btn-danger text-white my-2 w-100">Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container">
      <h3 class="mt-2">User Edit</h3>
      <hr>
      <div class="row">
        <div class="col-12 col-lg-6 offset-lg-3">
          <form action="" method="post" class="form-horizontal mb-3">
            <?php while ($user = $select_user->fetch(PDO::FETCH_ASSOC)) { ?>
              <div class=" mb-3">
                <label for="name" class="form-label">Username</label>
                <input type="text" class="form-control" value='<?php echo $user['username'] ?>' name="username" required>
              </div>

              <div class="mb-3">
                <label for="price" class="form-label">Email</label>
                <input type="text" class="form-control" name="email" value='<?php echo $user['email'] ?>' required>
              </div>

              <div class="mb-3">
                <label for="author" class="form-label">Role</label>
                <select class="form-select" name="role" required>
                  <?php while ($role = $select_master_role->fetch(PDO::FETCH_ASSOC)) {  ?>
                    <option value=<?php echo $role['id'] ?><?= $user['role_id'] == $role['id'] ? ' selected=selected' : ''; ?>>
                      <?php echo $role['name'] ?>
                    </option>
                  <?php } ?>
                </select>
              </div>

              <input type="text" class="form-control d-none" name="updatedBy" value=<?php echo $_SESSION['uid'] ?>>

              <?php
              if (isset($errorMsg)) { ?>
                <div class="alert alert-danger">
                  <h3>
                    <?php echo $errorMsg;
                    unset($errorMsg);
                    ?>
                  </h3>
                  <br>
                </div>
              <?php } ?>


              <input type="submit" name="btn_update" class="btn btn-primary" value="Update" />
            <?php   } ?>
            <a href="book_list.php" class="btn btn-success">Back</a>
          </form>

        </div>
      </div>
    </div>

  </div>



</body>

</html>