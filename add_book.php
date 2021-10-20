<?php
require_once 'connection.php';
session_start();

if (!isset($_SESSION['admin_login'])) {
  header('location: ../index.php');
}

$select_categories = $db->prepare("SELECT id , name FROM categories");
$select_categories->execute();

$select_authors = $db->prepare("SELECT id , name FROM authors");
$select_authors->execute();

if (isset($_POST['btn_insert'])) {
  $name = $_REQUEST['name'];
  $price = $_REQUEST['price'];
  $qty = $_REQUEST['qty'];
  $author = $_REQUEST['author'];
  $category = $_REQUEST['category'];
  $createdBy = $_REQUEST['createdBy'];

  if (empty($name)) {
    $errorMsg = "Please enter Name";
  } else  if (empty($price)) {
    $errorMsg = "Please enter Price";
  } else  if (empty($qty)) {
    $errorMsg = "Please enter Qty";
  } else  if (empty($category)) {
    $errorMsg = "Please enter Category";
  } else  if (empty($author)) {
    $errorMsg = "Please enter Author";
  } else  if (empty($createdBy)) {
    $errorMsg = "Please enter CreateBy";
  } else {
    if ($_FILES['image']['name']) {
      $image_file = $_FILES['image']['name'];
      $type = $_FILES['image']['type'];
      $size = $_FILES['image']['size'];
      $temp = $_FILES['image']['tmp_name'];
      $path = "upload/" . $image_file; // set upload folder path

      try {
        if (isset($image_file)) {
          if ($type == "image/jpg" || $type == 'image/jpeg' || $type == "image/png" || $type == "image/gif") {
            if (!file_exists($path)) { // check file not exist in your upload folder path
              if ($size < 5000000) { // check file size 5MB
                move_uploaded_file($temp, 'upload/' . $image_file); // move upload file temperory directory to your upload folder
              } else {
                $errorMsg = "Your file too large please upload 5MB size"; // error message file size larger than 5mb
              }
            } else {
              $errorMsg = "File already exists... Check upload filder"; // error message file not exists your upload folder path
            }
          } else {
            $errorMsg = "Upload JPG, JPEG, PNG & GIF file formate...";
          }
        }

        if (!isset($errorMsg)) {
          $insert_data = $db->prepare("INSERT INTO books(name,price,qty,author,createdBy,category ,	image) VALUES (:name,:price,:qty,:author,:createdBy,:category ,:image)");
          $insert_data->bindParam(":name", $name);
          $insert_data->bindParam(":price", $price);
          $insert_data->bindParam(":qty", $qty);
          $insert_data->bindParam(":author", $author);
          $insert_data->bindParam(":createdBy", $createdBy);
          $insert_data->bindParam(":category", $category);
          $insert_data->bindParam(":image", $image_file);

          if ($insert_data->execute()) {
            $insertMsg = "Insert Successfully...";
            // header("refresh:2;book_list.php");
            header("location: book_list.php");
          }
        }
      } catch (PDOException $e) {
        $errorMsg = $e->getMessage();
      }
    } else {
      try {
        if (!isset($errorMsg)) {
          $insert_data = $db->prepare("INSERT INTO books(name,price,qty,author,createdBy,category) VALUES (:name,:price,:qty,:author,:createdBy,:category )");
          $insert_data->bindParam(":name", $name);
          $insert_data->bindParam(":price", $price);
          $insert_data->bindParam(":qty", $qty);
          $insert_data->bindParam(":author", $author);
          $insert_data->bindParam(":createdBy", $createdBy);
          $insert_data->bindParam(":category", $category);

          if ($insert_data->execute()) {
            $insertMsg = "Insert Successfully...";
            // header("refresh:2;book_list.php");
            header("location: book_list.php");
          }
        }
      } catch (PDOException $e) {
        $errorMsg = $e->getMessage();
      }
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
  <title>Book Add</title>
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
              <a class="nav-link my-2 text-center" aria-current="page" href="admin/admin_home.php">Dashboard</a>
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
      <h3 class="mt-2">Book Add</h3>
      <hr>
      <div class="row">
        <div class="col-12 col-lg-6 offset-lg-3">

          <form action="" method="post" class="form-horizontal mb-3" enctype="multipart/form-data">
            <div class=" mb-3">
              <label for="name" class="form-label">Book Name</label>
              <input type="text" class="form-control" name="name" required>
            </div>

            <div class="mb-3">
              <label for="price" class="form-label">Price</label>
              <input type="number" class="form-control" step="0.01" name="price" required>
            </div>

            <div class="mb-3">
              <label for="qty" class="form-label">Qty</label>
              <input type="number" class="form-control" name="qty" required>
            </div>

            <div class="mb-3">
              <label for="author" class="form-label">Author</label>
              <select class="form-select" name="author" required>
                <?php while ($author = $select_authors->fetch(PDO::FETCH_ASSOC)) {  ?>
                  <option <?php echo "value=" . $author['id']  ?>><?php echo $author['name'] ?></option>
                <?php } ?>
              </select>
            </div>

            <div class="mb-3">
              <label for="category" class="form-label">Category</label>
              <select class="form-select" name="category" required>
                <?php while ($row = $select_categories->fetch(PDO::FETCH_ASSOC)) {  ?>
                  <option <?php echo "value=" . $row['id']  ?>><?php echo $row['name'] ?></option>
                <?php } ?>
              </select>
            </div>

            <div class="mb-3">
              <label for="image" class="form-label">Image</label>
              <input type="file" name="image" class="form-control" id="id_image" onchange="showImage()">
            </div>

            <div class="mb-3 text-center text-lg-start">
              <img src="#" id="blah" alt="" class="img-fluid">
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