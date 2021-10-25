<?php
require_once 'connection.php';
// ใช้สำหรับเริ่มต้นใช้งาน session
session_start();

// เช็คว่าไม่มี session = Admin Login ให้ Rediect กลับไปหน้า login
if (!isset($_SESSION['admin_login'])) {
  header('location: ../index.php');
}

// Config Pagination
$limit = 5;

if (empty($_POST['page'])) {
  $page = 1;
  $start_index = ($page - 1) * $limit;
} else if (isset($_POST['page'])) {
  $page = $_POST['page'];
  $start_index = ($page - 1) * $limit;
}


try {
  $select_category_list = $db->prepare('SELECT c.id , c.name as category , u.username , c.createdAt FROM categories as c LEFT JOIN user_info as u ON c.createdBy = u.id ');
  $select_category_list->execute();

  // Pagination
  $count_categories = $db->prepare("SELECT count(id) from `categories` ");
  $count_categories->execute();
  $count = $count_categories->fetch(PDO::FETCH_ASSOC);
  foreach ($count as $key => $value) {
    $total_page =  ceil($value / $limit);
  }
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}

if (isset($_REQUEST['delete_id'])) {
  try {
    $id = $_REQUEST['delete_id'];

    $delete_id = $db->prepare("DELETE FROM categories WHERE id = :id");
    $delete_id->bindParam(":id", $id);
    if ($delete_id->execute()) {
      $_SESSION['success'] = "Delete Category Successfully...";
      // use refresh:2 from unset session in same page
      header('refresh:2;category_list.php');
    };
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Page</title>
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
              <a class="nav-link my-2 text-center" aria-current="page" href="dashboard.php">Dashboard</a>
            </li>

            <li class="nav-item">
              <p class="nav-link pe-none btn btn-outline-white my-2">
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

    <?php if (isset($_SESSION['success'])) : ?>
      <div class="alert alert-success">
        <h3>
          <?php echo $_SESSION['success'];
          unset($_SESSION['success']);
          ?>
        </h3>
        <br>
      </div>
    <?php endif ?>

    <?php if (isset($insertMsg)) : ?>
      <div class="alert alert-success">
        <h3>
          <?php echo $insertMsg;
          unset($insertMsg);
          ?>
        </h3>
        <br>
      </div>
    <?php endif ?>

    <div class="row m-0 mt-3">
      <div class="col-md-2">
        <a href="book_list.php" class="btn btn-success w-100 mb-2">Book List</a>

        <a href="add_category.php" class="btn btn-warning w-100 mb-2">Add Category</a>
        <div class="border border-2 border-light">

        </div>
      </div>

      <div class="col-auto col-lg-10 mt-2 mt-lg-0">
        <div class="container p-0 px-lg-5 w-100">
          <div class="table-responsive">
            <table class="table table-hover table-bordered">
              <thead>
                <tr>
                  <th scope="col" class="text-center">Id</th>
                  <th scope="col" class="text-center">Name</th>
                  <th scope="col" class="text-center">Created By</th>
                  <th scope="col" class="text-center">Created At</th>
                  <th scope="col" class="text-center w-25">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                while ($select_categories = $select_category_list->fetch(PDO::FETCH_ASSOC)) { ?>
                  <tr>
                    <th scope="row" class="text-center"><?php echo $select_categories['id'] ?></th>
                    <td class="text-center"><?php echo $select_categories['category'] ?></td>
                    <td class="text-center"><?php echo $select_categories['username'] ?></td>
                    <td class="text-center"><?php echo $select_categories['createdAt'] ?></td>
                    <td class="text-center">
                      <div class="row m-0">
                        <div class="col-12 col-lg-6 px-3 mb-2 mb-lg-0">
                          <a href="edit_book.php?id=<?php echo $select_categories['id'] ?>" class="btn btn-warning w-100">Edit</a>
                        </div>
                        <div class="col-12 col-lg-6 px-3">
                          <a href="?delete_id=<?php echo $select_categories['id'] ?>" class="btn btn-danger w-100">Delete</a>
                        </div>
                      </div>
                    </td>
                  </tr>
                <?php   } ?>
              </tbody>
            </table>
          </div>


          <form action="" method='post'>
            <nav aria-label="Page navigation example">
              <ul class="pagination">
                <?php for ($index = 1; $index <= $total_page; $index++) {  ?>
                  <li class="page-item <?php if ($page == $index) {
                                          echo 'active';
                                        } ?>">
                    <input type="submit" class="page-link " name="page" value=<?php echo $index ?>>
                  </li>
                <?php } ?>
              </ul>
            </nav>

          </form>

        </div>
      </div>
    </div>


  </div>




  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
</body>

</html>