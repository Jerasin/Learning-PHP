<?php
require_once 'connection.php';
session_start();

if (!isset($_SESSION['admin_login'])) {
  header('location: ../index.php');
}



if (isset($_POST['btn_insert'])) {
  $name = $_REQUEST['name'];
  $createdBy = $_REQUEST['createdBy'];

  if (empty($name)) {
    $errorMsg = "Please enter Name";
  } else  if (empty($createdBy)) {
    $errorMsg = "Please enter CreateBy";
  } else {
    try {
      if (!isset($errorMsg)) {
        $insert_data = $db->prepare("INSERT INTO categories(name,createdBy) VALUES (:name,:createdBy)");
        $insert_data->bindParam(":name", $name);
        $insert_data->bindParam(":createdBy", $createdBy);

        if ($insert_data->execute()) {
          $_SESSION['success'] = "Insert Successfully...";
          // header("refresh:2;category_list.php");
          header("location: category_list.php");
        }
      }
    } catch (PDOException $e) {
      $errorMsg = $e->getMessage();
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
  <title>Category Add</title>
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
      <h3 class="mt-2">Category Add</h3>
      <hr>
      <div class="row">
        <div class="col-12 col-lg-6 offset-lg-3">

          <form action="" method="post" class="form-horizontal mb-3">
            <div class=" mb-3">
              <label for="name" class="form-label">Category Name</label>
              <input type="text" class="form-control" name="name" required>
            </div>

            <input type="text" class="form-control d-none" name="createdBy" value=<?php echo $_SESSION['uid'] ?>>

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


            <button type="submit" name="btn_insert" class="btn btn-primary">Submit</button>
            <a href="book_list.php" class="btn btn-success">Back</a>
          </form>

        </div>
      </div>
    </div>

  </div>


</body>

</html>