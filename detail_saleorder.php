<?php
require_once 'connection.php';
// ใช้สำหรับเริ่มต้นใช้งาน session
session_start();

// เช็คว่าไม่มี session = Admin Login ให้ Rediect กลับไปหน้า login
if (!isset($_SESSION['admin_login'])) {
  header('location: index.php');
}

// Config Pagination
$limit = 5;

if (isset($_GET['id'])) {
  try {
    $id = $_GET['id'];
    $select_saleorder = $db->prepare("SELECT so.saleorder_id , so.name , so.price , so.qty , c.name as category , a.name as author FROM saleorders as so LEFT JOIN categories as c ON so.category = c.id LEFT JOIN authors as a ON so.author = a.id WHERE so.saleorder_id =  :id ");
    $select_saleorder->bindParam(':id', $id);
    $select_saleorder->execute();

    // Pagination
    $count_saleorder_list = $db->prepare("SELECT count(id) from `saleorders` WHERE saleorder_id =  :id ");
    $count_saleorder_list->bindParam(':id', $id);
    $count_saleorder_list->execute();
    $count = $count_saleorder_list->fetch(PDO::FETCH_ASSOC);
    foreach ($count as $key => $value) {
      $total_page =  ceil($value / $limit);
    }
  } catch (PDOException $e) {
    $e->getMessage();
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="css/style.css">
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
              <a class="nav-link my-2 text-center" aria-current="page" href=<?php
                                                                            if (isset($_SESSION['admin_login'])) {
                                                                              echo "admin/admin_home.php";
                                                                            } else if (isset($_SESSION['user_login'])) {
                                                                              echo "user/user_home.php";
                                                                            } else if (isset($_SESSION['employee_login'])) {
                                                                              echo "employee/employee_home.php";
                                                                            }
                                                                            ?>>Home</a>
            </li>

            <li class="nav-item">
              <a class="nav-link my-2 text-center" aria-current="page" href="dashboard.php">Dashboard</a>
            </li>

            <li class="nav-item">
              <a class="nav-link my-2 text-center pe-none" aria-current="page" href="admin/admin_home.php">
                <?php
                if (isset($_SESSION['admin_login'])) {
                  echo $_SESSION['admin_login'];
                } else if (isset($_SESSION['user_login'])) {
                  echo $_SESSION['user_login'];
                } else if (isset($_SESSION['employee_login'])) {
                  echo $_SESSION['employee_login'];
                }
                ?>
              </a>
            </li>

            <li class="nav-item">
              <a href="logout.php" class="nav-link btn btn-danger text-white my-2 w-100">Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <h3 class="ms-3 mt-3">Saleorder Id: <?php echo $id ?></h3>
      <div class="row m-0 mt-3">
        <div class="col-auto col-lg-8 offset-2">
          <div class="container p-0 px-lg-5 w-100">
            <div class="table-responsive">
              <table class="table table-hover table-bordered">
                <thead>
                  <tr>
                    <th scope="col" class="text-center">Name</th>
                    <th scope="col" class="text-center">Price</th>
                    <th scope="col" class="text-center">Qty</th>
                    <th scope="col" class="text-center">Category</th>
                    <th scope="col" class="text-center w-25">Author</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  while ($saleorder = $select_saleorder->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                      <td class="text-center"><?php echo $saleorder['name'] ?></td>
                      <td class="text-center"><?php echo $saleorder['price'] ?></td>
                      <td class="text-center"><?php echo $saleorder['qty'] ?></td>
                      <td class="text-center"><?php echo $saleorder['category'] ?></td>
                      <td class="text-center"><?php echo $saleorder['author'] ?></td>

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


  </div>
</body>

</html>