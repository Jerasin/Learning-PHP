<?php

require_once 'connection.php';
session_start();

if (!isset($_SESSION['admin_login'])) {
  header('location: ../index.php');
}

$count_userlist = $db->prepare("SELECT count(id) from user_info");
$count_userlist->execute();
$count_users = $count_userlist->fetch(PDO::FETCH_ASSOC);

$count_saleorderlist_approve = $db->prepare("SELECT count(so.id) from saleorderlist as so LEFT JOIN status_code as status ON so.status = status.id WHERE so.status = 3");
$count_saleorderlist_approve->execute();
$count_saleorders_approve = $count_saleorderlist_approve->fetch(PDO::FETCH_ASSOC);

$count_saleorderlist_wait = $db->prepare("SELECT count(so.id) from saleorderlist as so LEFT JOIN status_code as status ON so.status = status.id WHERE so.status = 4");
$count_saleorderlist_wait->execute();
$count_saleorders_wait = $count_saleorderlist_wait->fetch(PDO::FETCH_ASSOC);

$count_saleorderlist_reject = $db->prepare("SELECT count(so.id) from saleorderlist as so LEFT JOIN status_code as status ON so.status = status.id WHERE so.status = 5");
$count_saleorderlist_reject->execute();
$count_saleorders_reject = $count_saleorderlist_reject->fetch(PDO::FETCH_ASSOC);
// print_r($count_saleorders_reject);
// echo $count_users['count(id)']

$saleorderlist = $db->prepare("SELECT so.saleorder_id , so.qty_total , so.price_total , status.name as status , u.username , so.createdAt FROM saleorderlist as so LEFT JOIN status_code as status ON so.status = status.id LEFT JOIN user_info as u ON so.createdBy = u.id");
$saleorderlist->execute();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DashBoard</title>
  <link rel="stylesheet" href="css/dashboard.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>

  <!-- Font Awsome  -->
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
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
              <a class="nav-link my-2 text-center" aria-current="page" href="../dashborad.php">Dashboard</a>
            </li>

            <li class="nav-item">
              <a class="nav-link my-2 text-center <?php
                                                  if (empty($_SESSION['pid'])) {
                                                    echo 'd-none';
                                                  }
                                                  ?>" aria-current="page" href="cart_list.php">Cart</a>
            </li>

            <li class="nav-item">
              <a class="nav-link my-2 text-center pe-none" aria-current="page" href="admin/admin_home.php"><?php
                                                                                                            echo $_SESSION['admin_login']
                                                                                                            ?></a>
            </li>

            <li class="nav-item">
              <a href="logout.php" class="nav-link btn btn-danger text-white my-2 w-100">Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid">

      <div class="row">
        <div class="col-lg-3 col-12 my-2">
          <div class="small-box bg-info">
            <div class="inner">
              <h3 class="text-white"><?php echo $count_users['count(id)']; ?></h3>
              <p class="text-white">Customers</p>
            </div>

            <div class="icon">
              <a href="#">
                <i class="fas fa-users users_icon text-dark" style="font-size: 100px;"></i>
              </a>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-12 my-2">
          <div class="small-box bg-success">
            <div class="inner">
              <h3 class="text-white"><?php echo $count_saleorders_approve['count(so.id)']; ?></h3>
              <p class="text-white">Order Success</p>
            </div>

            <div class="icon">
              <i class="fas fa-clipboard-check" style="font-size: 100px;"></i>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-12 my-2">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3 class="text-dark"><?php echo $count_saleorders_wait['count(so.id)']; ?></h3>
              <p class="text-dark">Order Wait</p>
            </div>

            <div class="icon">
              <i class="far fa-file" style="font-size: 100px;"></i>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-12 my-2">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3 class="text-white"><?php echo $count_saleorders_reject['count(so.id)']; ?></h3>
              <p class="text-white">Order Rejected</p>
            </div>

            <div class="icon">
              <i class="far fa-window-close" style="font-size: 100px;"></i>
            </div>
          </div>
        </div>

      </div>

      <div class="row border border-dark mt-2 m-0">
        <div class="col-lg-12">
          <h1 class="my-3">Search:</h1>

          <form action="" method="post">
            <div class="row">
              <div class="col-12 col-lg-3">
                <div class="mb-3">
                  <label for="exampleFormControlInput1" class="form-label">From</label>
                  <input type="date" class="form-control" value="{{date_now_from}}" id="date_from" name="date_from">
                </div>
              </div>

              <div class="col-12 col-lg-3">
                <div class="mb-3">
                  <label for="exampleFormControlInput1" class="form-label">To</label>
                  <input type="date" class="form-control" id="date_to" name="date_to" value="{{date_now_to}}">
                </div>
              </div>

              <div class="col-12 col-lg-3">
                <div class="mb-3">
                  <label for="exampleFormControlInput1" class="form-label">Customer Name</label>
                  <input type="text" class="form-control" name="customer_name">
                </div>
              </div>

              <div class="col-12 col-lg-3">
                <div class="mb-3">
                  <label for="exampleFormControlInput1" class="form-label">Saleorder Code</label>
                  <input type="text" class="form-control" name="salesorder_code">
                </div>
              </div>

              <div class="col-12">
                <div class="mb-3 float-end">

                  <button type="submit" class="btn btn-success">
                    <i class="fas fa-search"></i>
                    Search
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>


        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead style="text-align: center">
                <tr>
                  <th>Saleorder Code</th>
                  <th>Grand Total</th>
                  <th>Created</th>
                  <th>Created By</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>

              <tbody>
                <?php
                while ($saleorder = $saleorderlist->fetch(PDO::FETCH_ASSOC)) {
                ?>
                  <tr>
                    <td class="text-center"><?php echo $saleorder['saleorder_id'] ?></td>

                    <td class="text-center"><?php echo $saleorder['price_total'] ?></td>

                    <td class="text-center"><?php echo $saleorder['price_total'] ?></td>

                    <td class="text-center"><?php echo $saleorder['username'] ?></td>

                    <td class="text-center"><?php echo $saleorder['status'] ?></td>

                    <td class="text-center">
                      <a href="{% url 'stock_book:salesorder_detail' salesorder=item.saleorder_code %}" class='btn btn-warning'>detail</a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>

        <form action="" method='post'>
          <nav aria-label="Page navigation example">
            <ul class="pagination">
              <li class="page-item">
                <a class="page-link" href="#">
                  Previous
                </a>
              </li>



              <li class="page-item">
                <a class="page-link" href="#">
                  Next
                </a>
              </li>
            </ul>
          </nav>

        </form>

      </div>

    </div>
  </div>
</body>

</html>