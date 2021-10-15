<?php
require_once 'connection.php';
session_start();

if (!isset($_SESSION['id'])) {
  header('location: ../index.php');
}

try {
  if (isset($_REQUEST['id'])) {
    $id = $_REQUEST['id'];
    $select_books = $db->prepare("SELECT b.id , b.name , b.qty , b.price , b.image , c.id as category_id , c.name as category ,a.id as author_id , a.name as author FROM books as b LEFT JOIN categories as c ON b.category = c.id LEFT JOIN authors as a ON b.author = a.id WHERE b.id = :id");
    $select_books->bindParam(':id', $id);
    $select_books->execute();
  }
} catch (PDOException $e) {
  $e->getMessage();
}

$select_categories = $db->prepare("SELECT id , name FROM categories");
$select_categories->execute();

$select_authors = $db->prepare("SELECT id , name FROM authors");
$select_authors->execute();

if (isset($_REQUEST['btn_update'])) {
  $name = $_REQUEST['name'];
  $price = $_REQUEST['price'];
  $qty = $_REQUEST['qty'];
  $author = $_REQUEST['author'];
  $category = $_REQUEST['category'];
  $updatedBy = $_REQUEST['updatedBy'];

  if (isset($_FILES['image']['name'])) {
    $image_file = $_FILES['image']['name'];
    $type = $_FILES['image']['type'];
    $size = $_FILES['image']['size'];
    $temp = $_FILES['image']['tmp_name'];
    $path = "upload/" . $image_file; // set upload folder path
  }
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
  } else  if (empty($updatedBy)) {
    $errorMsg = "Please enter UpdatedBy";
  } else {
    try {
      if (isset($image_file)) {
        echo "Running";
        if ($type == "image/jpg" || $type == 'image/jpeg' || $type == "image/png" || $type == "image/gif") {
          if (!file_exists($path)) { // check file not exist in your upload folder path
            if ($size < 5000000) { // check file size 5MB
              // Delete Image Old
              $book = $select_books->fetch(PDO::FETCH_ASSOC);
              $path_delete = "upload/" .  $book['image']; // set delete folder path
              unlink($path_delete);

              // Create Image New
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
        $update_data = $db->prepare("UPDATE books SET name = :name , price = :price , qty= :qty , updatedBy = :updatedBy , author = :author , category = :category , image = :image WHERE id = :id");
        $update_data->bindParam(":id", $id);
        $update_data->bindParam(":name", $name);
        $update_data->bindParam(":price", $price);
        $update_data->bindParam(":qty", $qty);
        $update_data->bindParam(":author", $author);
        $update_data->bindParam(":updatedBy", $updatedBy);
        $update_data->bindParam(":category", $category);
        $update_data->bindParam(":image", $image_file);

        if ($update_data->execute()) {
          $insertMsg = "Update Successfully...";
          // header("refresh:2;book_list.php");
          header("location: book_list.php");
        }
      }
    } catch (Exception $e) {
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
  <title>Book Edit</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
              <p class="nav-link btn btn-outline-white my-2">
                <?php
                echo $_SESSION['email']
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
      <h3 class="mt-2">Book Edit</h3>
      <hr>
      <div class="row">
        <div class="col-12 col-lg-6 offset-lg-3">
          <form action="" method="post" class="form-horizontal mb-3" enctype="multipart/form-data">
            <?php while ($book = $select_books->fetch(PDO::FETCH_ASSOC)) { ?>
              <div class=" mb-3">
                <label for="name" class="form-label">Book Name</label>
                <input type="text" class="form-control" value='<?php echo $book['name'] ?>' name="name" required>
              </div>

              <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" class="form-control" value='<?php echo $book['price'] ?>' step="0.01" name="price" required>
              </div>

              <div class="mb-3">
                <label for="qty" class="form-label">Qty</label>
                <input type="number" class="form-control" value='<?php echo $book['qty'] ?>' name="qty" required>
              </div>

              <div class="mb-3">
                <label for="author" class="form-label">Author</label>
                <select class="form-select" name="author" required>
                  <?php while ($author = $select_authors->fetch(PDO::FETCH_ASSOC)) {  ?>
                    <option value=<?php echo $author['id'] ?><?= $book['author_id'] == $author['id'] ? ' selected=selected' : ''; ?>>
                      <?php echo $author['name'] ?>
                    </option>
                  <?php } ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" name="category" required>
                  <?php while ($row = $select_categories->fetch(PDO::FETCH_ASSOC)) {  ?>
                    <option value=<?php echo $row['id'] ?><?= $book['category_id'] == $row['id'] ? ' selected=selected' : ''; ?>>
                      <?php echo $row['name'] ?>
                    </option>
                  <?php } ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" name="image" class="form-control" id="id_image">
              </div>

              <div class="mb-3 text-center text-lg-start">
                <img src=<?php echo 'upload/' . $book['image'] ?> id="blah" alt="" class="img-fluid">
              </div>

              <input type="text" class="form-control d-none" name="updatedBy" value=<?php echo $_SESSION['id'] ?>>

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


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
</body>

</html>