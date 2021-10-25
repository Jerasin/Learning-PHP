<?php

require_once 'connection.php';
session_start();

if (!isset($_SESSION['admin_login'])) {
  header('location: ../index.php');
}

// Config Pagination
$limit = 5;

try {
  $count_userlist = $db->prepare("SELECT count(id) from user_info");
  $count_userlist->execute();
  $count_users = $count_userlist->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}

try {
  $count_saleorderlist_approve = $db->prepare("SELECT count(so.id) from saleorderlist as so LEFT JOIN status_code as status ON so.status = status.id WHERE so.status = 1");
  $count_saleorderlist_approve->execute();
  $count_saleorders_approve = $count_saleorderlist_approve->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}

try {
  $count_saleorderlist_wait = $db->prepare("SELECT count(so.id) from saleorderlist as so LEFT JOIN status_code as status ON so.status = status.id WHERE so.status = 3");
  $count_saleorderlist_wait->execute();
  $count_saleorders_wait = $count_saleorderlist_wait->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}

try {
  $count_saleorderlist_reject = $db->prepare("SELECT count(so.id) from saleorderlist as so LEFT JOIN status_code as status ON so.status = status.id WHERE so.status = 2");
  $count_saleorderlist_reject->execute();
  $count_saleorders_reject = $count_saleorderlist_reject->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}
// print_r($count_saleorders_reject);
// echo $count_users['count(id)']





if (isset($_POST['btn_search'])) {
  if ($_POST['customer_name'] and !$_POST['salesorder_code']) {
    try {
      $date_from = $_POST['date_from'];
      $date_to = $_POST['date_to'];
      $customer_name = $_POST['customer_name'];

      // convert format d/m/y to y-m-d
      $reformat_date_from = str_replace('/', '-',  $date_from);
      $date_from_select =  date('Y-m-d', strtotime($reformat_date_from));

      $reformat_date_to = str_replace('/', '-',  $date_to);
      $date_to_select =  date('Y-m-d', strtotime($reformat_date_to));

      $page = $_POST['page'] ?? 1;
      $start_index = ($page - 1) * $limit;
      $saleorderlist = $db->prepare("SELECT so.saleorder_id , so.qty_total , so.price_total , status.name as status , u.username , so.createdAt as createdAt FROM saleorderlist as so LEFT JOIN status_code as status ON so.status = status.id LEFT JOIN user_info as u ON so.createdBy = u.id WHERE so.createdAt >= :date_from AND so.createdAt <= :date_to AND u.username = :username  LIMIT :start_index,:limit");
      $saleorderlist->bindParam(':username', $customer_name);
      $saleorderlist->bindParam(':date_from', $date_from_select);
      $saleorderlist->bindParam(':date_to', $date_to_select);
      $saleorderlist->bindValue(':start_index', intval($start_index),  PDO::PARAM_INT);
      $saleorderlist->bindValue(':limit', intval($limit),  PDO::PARAM_INT);
      $saleorderlist->execute();

      // Pagination
      $count_saleorderlists = $db->prepare("SELECT count(so.id)  FROM saleorderlist as so LEFT JOIN status_code as status ON so.status = status.id LEFT JOIN user_info as u ON so.createdBy = u.id WHERE so.createdAt >= :date_from AND so.createdAt <= :date_to AND u.username = :username");
      $count_saleorderlists->bindParam(':username', $customer_name);
      $count_saleorderlists->bindParam(':date_from', $date_from_select);
      $count_saleorderlists->bindParam(':date_to', $date_to_select);
      $count_saleorderlists->execute();
      $count_saleorderlist = $count_saleorderlists->fetch(PDO::FETCH_ASSOC);
      $total_page =  ceil($count_saleorderlist['count(so.id)'] / $limit);
      if ($total_page == 0) {
        $_SESSION['warning'] = 'No Data';
      }
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
  } else if ($_POST['salesorder_code'] and !$_POST['customer_name']) {
    try {
      $date_from = $_POST['date_from'];
      $date_to = $_POST['date_to'];
      $salesorder_code = $_POST['salesorder_code'];
      echo "5555";

      // convert format d/m/y to y-m-d
      $reformat_date_from = str_replace('/', '-',  $date_from);
      $date_from_select =  date('Y-m-d', strtotime($reformat_date_from));

      $reformat_date_to = str_replace('/', '-',  $date_to);
      $date_to_select =  date('Y-m-d', strtotime($reformat_date_to));

      $page = $_POST['page'] ?? 1;
      $start_index = ($page - 1) * $limit;
      $saleorderlist = $db->prepare("SELECT so.saleorder_id , so.qty_total , so.price_total , status.name as status , u.username , so.createdAt as createdAt FROM saleorderlist as so LEFT JOIN status_code as status ON so.status = status.id LEFT JOIN user_info as u ON so.createdBy = u.id WHERE so.createdAt >= :date_from AND so.createdAt <= :date_to AND so.saleorder_id = :saleorder_id  LIMIT :start_index,:limit");
      $saleorderlist->bindParam(':saleorder_id', $salesorder_code);
      $saleorderlist->bindParam(':date_from', $date_from_select);
      $saleorderlist->bindParam(':date_to', $date_to_select);
      $saleorderlist->bindValue(':start_index', intval($start_index),  PDO::PARAM_INT);
      $saleorderlist->bindValue(':limit', intval($limit),  PDO::PARAM_INT);
      $saleorderlist->execute();

      // Pagination
      $count_saleorderlists = $db->prepare("SELECT count(so.id)  FROM saleorderlist as so LEFT JOIN status_code as status ON so.status = status.id LEFT JOIN user_info as u ON so.createdBy = u.id WHERE so.createdAt >= :date_from AND so.createdAt <= :date_to AND so.saleorder_id = :saleorder_id");
      $count_saleorderlists->bindParam(':saleorder_id', $salesorder_code);
      $count_saleorderlists->bindParam(':date_from', $date_from_select);
      $count_saleorderlists->bindParam(':date_to', $date_to_select);
      $count_saleorderlists->execute();
      $count_saleorderlist = $count_saleorderlists->fetch(PDO::FETCH_ASSOC);
      print_r($count_saleorderlist);
      $total_page =  ceil($count_saleorderlist['count(so.id)'] / $limit);
      if ($total_page == 0) {
        $_SESSION['warning'] = 'No Data';
      }
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
  } else if ($_POST['salesorder_code'] and $_POST['customer_name']) {
    try {
      $date_from = $_POST['date_from'];
      $date_to = $_POST['date_to'];
      $customer_name = $_POST['customer_name'];
      $salesorder_code = $_POST['salesorder_code'];

      // convert format d/m/y to y-m-d
      $reformat_date_from = str_replace('/', '-',  $date_from);
      $date_from_select =  date('Y-m-d', strtotime($reformat_date_from));

      $reformat_date_to = str_replace('/', '-',  $date_to);
      $date_to_select =  date('Y-m-d', strtotime($reformat_date_to));

      $page = $_POST['page'] ?? 1;
      $start_index = ($page - 1) * $limit;
      $saleorderlist = $db->prepare("SELECT so.saleorder_id , so.qty_total , so.price_total , status.name as status , u.username , so.createdAt as createdAt FROM saleorderlist as so LEFT JOIN status_code as status ON so.status = status.id LEFT JOIN user_info as u ON so.createdBy = u.id WHERE so.createdAt >= :date_from AND so.createdAt <= :date_to AND u.username = :username AND so.saleorder_id = :saleorder_id  LIMIT :start_index,:limit");
      $saleorderlist->bindParam(':saleorder_id', $salesorder_code);
      $saleorderlist->bindParam(':username', $customer_name);
      $saleorderlist->bindParam(':date_from', $date_from_select);
      $saleorderlist->bindParam(':date_to', $date_to_select);
      $saleorderlist->bindValue(':start_index', intval($start_index),  PDO::PARAM_INT);
      $saleorderlist->bindValue(':limit', intval($limit),  PDO::PARAM_INT);
      $saleorderlist->execute();

      // Pagination
      $count_saleorderlists = $db->prepare("SELECT count(so.id)  FROM saleorderlist as so LEFT JOIN status_code as status ON so.status = status.id LEFT JOIN user_info as u ON so.createdBy = u.id WHERE so.createdAt >= :date_from AND so.createdAt <= :date_to AND u.username = :username AND so.saleorder_id = :saleorder_id ");
      $count_saleorderlists->bindParam(':saleorder_id', $salesorder_code);
      $count_saleorderlists->bindParam(':username', $customer_name);
      $count_saleorderlists->bindParam(':date_from', $date_from_select);
      $count_saleorderlists->bindParam(':date_to', $date_to_select);
      $count_saleorderlists->execute();
      $count_saleorderlist = $count_saleorderlists->fetch(PDO::FETCH_ASSOC);
      $total_page =  ceil($count_saleorderlist['count(so.id)'] / $limit);
      if ($total_page == 0) {
        $_SESSION['warning'] = 'No Data';
      }
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
  } else if (!$_POST['salesorder_code'] and !$_POST['customer_name']) {
    try {
      $date_from = $_POST['date_from'];
      $date_to = $_POST['date_to'];

      // convert format d/m/y to y-m-d
      $reformat_date_from = str_replace('/', '-',  $date_from);
      $date_from_select =  date('Y-m-d', strtotime($reformat_date_from));

      $reformat_date_to = str_replace('/', '-',  $date_to);
      $date_to_select =  date('Y-m-d', strtotime($reformat_date_to));

      $page = $_POST['page'] ?? 1;
      $start_index = ($page - 1) * $limit;
      $saleorderlist = $db->prepare("SELECT so.saleorder_id , so.qty_total , so.price_total , status.name as status , u.username , so.createdAt as createdAt FROM saleorderlist as so LEFT JOIN status_code as status ON so.status = status.id LEFT JOIN user_info as u ON so.createdBy = u.id WHERE so.createdAt >= :date_from AND so.createdAt <= :date_to  LIMIT :start_index,:limit");
      $saleorderlist->bindParam(':date_from', $date_from_select);
      $saleorderlist->bindParam(':date_to', $date_to_select);
      $saleorderlist->bindValue(':start_index', intval($start_index),  PDO::PARAM_INT);
      $saleorderlist->bindValue(':limit', intval($limit),  PDO::PARAM_INT);
      $saleorderlist->execute();

      // Pagination
      $count_saleorderlists = $db->prepare("SELECT count(so.id)  FROM saleorderlist as so LEFT JOIN status_code as status ON so.status = status.id LEFT JOIN user_info as u ON so.createdBy = u.id WHERE so.createdAt >= :date_from AND so.createdAt <= :date_to ");
      $count_saleorderlists->bindParam(':date_from', $date_from_select);
      $count_saleorderlists->bindParam(':date_to', $date_to_select);
      $count_saleorderlists->execute();
      $count_saleorderlist = $count_saleorderlists->fetch(PDO::FETCH_ASSOC);
      $total_page =  ceil($count_saleorderlist['count(so.id)'] / $limit);
      if ($total_page == 0) {
        $_SESSION['warning'] = 'No Data';
      }
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
  } else {
    $_SESSION['warning'] = 'Error Function';
  }
} else if (empty($_POST['btn_search'])) {
  try {
    $page = $_POST['page'] ?? 1;
    $start_index = ($page - 1) * $limit;
    $saleorderlist = $db->prepare("SELECT so.saleorder_id , so.qty_total , so.price_total , status.name as status , u.username , so.createdAt as createdAt FROM saleorderlist as so LEFT JOIN status_code as status ON so.status = status.id LEFT JOIN user_info as u ON so.createdBy = u.id LIMIT :start_index,:limit");
    $saleorderlist->bindValue(':start_index', intval($start_index),  PDO::PARAM_INT);
    $saleorderlist->bindValue(':limit', intval($limit),  PDO::PARAM_INT);
    $saleorderlist->execute();

    // Pagination
    $count_saleorderlists = $db->prepare("SELECT count(id) from saleorderlist");
    $count_saleorderlists->execute();
    $count_saleorderlist = $count_saleorderlists->fetch(PDO::FETCH_ASSOC);
    $total_page =  ceil($count_saleorderlist['count(id)'] / $limit);
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}

// approve Saleorders and Saleorderlist
if (isset($_POST['btn_approve'])) {
  if (isset($_SESSION['so_id_list'])) {
    foreach ($_SESSION['so_id_list'] as $id) {
      // echo $_SESSION['so_id_list'][$id] . '<br>' . 'POST:  ' . $_POST['checkbox=' . $id];
      if (isset($_POST['checkbox=' . $id])) {
        try {
          $update_saleorders = $db->prepare("UPDATE saleorders SET status = :status WHERE saleorder_id = :id");
          $update_saleorders->bindParam(":id", $_POST['checkbox=' . $id]);
          $update_saleorders->bindValue(":status", intval(1),  PDO::PARAM_INT);
          $update_saleorders->execute();
          // echo $_POST['change_qty=' . $id] . "<br>";

          $update_saleorderlist = $db->prepare("UPDATE saleorderlist SET status = :status WHERE saleorder_id = :id");
          $update_saleorderlist->bindParam(":id", $_POST['checkbox=' . $id]);
          $update_saleorderlist->bindValue(":status", intval(1),  PDO::PARAM_INT);
          $update_saleorderlist->execute();
        } catch (PDOException $e) {
          echo "Error: " . $e->getMessage();
        }
      }
      unset($_SESSION['so_id_list'][$id]);
    }
    $insertMsg = "Update Successfully...";
    header("refresh:2;dashboard.php");
    // header("location: dashboard.php");
  }
}

// reject Saleorders and Saleorderlist
if (isset($_POST['btn_reject'])) {
  echo "555";
  if (isset($_SESSION['so_id_list'])) {
    foreach ($_SESSION['so_id_list'] as $id) {
      echo $_POST['checkbox=' . $id];

      if (isset($_POST['checkbox=' . $id])) {
        try {
          $update_saleorders = $db->prepare("UPDATE saleorders SET status = :status WHERE saleorder_id = :id");
          $update_saleorders->bindParam(":id", $_POST['checkbox=' . $id]);
          $update_saleorders->bindValue(":status", intval(2),  PDO::PARAM_INT);
          $update_saleorders->execute();
          // echo $_POST['change_qty=' . $id] . "<br>";

          $update_saleorderlist = $db->prepare("UPDATE saleorderlist SET status = :status WHERE saleorder_id = :id");
          $update_saleorderlist->bindParam(":id", $_POST['checkbox=' . $id]);
          $update_saleorderlist->bindValue(":status", intval(2),  PDO::PARAM_INT);
          $update_saleorderlist->execute();
        } catch (PDOException $e) {
          echo "Error: " . $e->getMessage();
        }
      }
      unset($_SESSION['so_id_list'][$id]);
    }
    $insertMsg = "Update Successfully...";
    header("refresh:2;dashboard.php");
    // header("location: dashboard.php");
  }
}
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

  <!--  Jquery  -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" type="text/javascript"></script>
  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" type="text/javascript"></script> -->


  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js" type="text/javascript"></script> -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" type="text/javascript"></script>



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
              <a href="user_list.php">
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

      <div class="row border border-dark mt-2 m-0 mb-3">
        <div class="col-lg-12">
          <h1 class="my-3">Search:</h1>

          <form action="" method="post">
            <div class="row">
              <div class="col-12 col-lg-3">
                <div class="mb-3">
                  <label for="date_from" class="form-label">From</label>
                  <div class="input-group date" id='datepicker_from'>
                    <input type="text" class="form-control" placeholder="d/m/Y" name='date_from' value=<?php
                                                                                                        if (isset($date_from)) {
                                                                                                          echo $date_from;
                                                                                                        } else {
                                                                                                          echo date("d/m/Y");
                                                                                                        }
                                                                                                        ?>>
                    <span class="input-group-append">
                      <span class="input-group-text bg-white d-block h-100 pt-2">
                        <i class="fa fa-calendar"></i>
                      </span>
                    </span>
                  </div>
                </div>
              </div>

              <div class="col-12 col-lg-3">
                <div class="mb-3">
                  <label for="exampleFormControlInput1" class="form-label">To</label>
                  <div class="input-group date" id='datepicker_to'>
                    <input type="text" class="form-control" placeholder="d/m/Y" name='date_to' value=<?php
                                                                                                      if (isset($date_to)) {
                                                                                                        echo $date_to;
                                                                                                      } else {
                                                                                                        echo date("d/m/Y");
                                                                                                      }
                                                                                                      ?>>
                    <span class="input-group-append">
                      <span class="input-group-text bg-white d-block h-100 pt-2">
                        <i class="fa fa-calendar"></i>
                      </span>
                    </span>
                  </div>
                </div>
              </div>

              <div class="col-12 col-lg-3">
                <div class="mb-3">
                  <label for="exampleFormControlInput1" class="form-label">Customer Name</label>
                  <input type="text" class="form-control" name="customer_name" value=<?php
                                                                                      if (isset($customer_name)) {
                                                                                        echo $customer_name;
                                                                                      }
                                                                                      ?>>
                </div>
              </div>

              <div class="col-12 col-lg-3">
                <div class="mb-3">
                  <label for="exampleFormControlInput1" class="form-label">Saleorder Code</label>
                  <input type="text" class="form-control" name="salesorder_code" value=<?php
                                                                                        if (isset($salesorder_code)) {
                                                                                          echo $salesorder_code;
                                                                                        }
                                                                                        ?>>
                </div>
              </div>

              <div class="col-12">
                <div class="mb-3 float-end">

                  <button type="submit" name="btn_search" class="btn btn-success">
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
                <form action="" method="post">
                  <tr>
                    <th><input type="checkbox" class="form-check-input" name="select_all" id="select_all"></th>
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
                  if ($saleorder['status'] == 'wait') {
                    $_SESSION['so_id_list'][$saleorder['saleorder_id']] = $saleorder['saleorder_id'];
                  }
                ?>
                  <tr>
                    <td class="text-center <?php
                                            if ($saleorder['status'] == 'approve') {
                                              echo 'bg-success';
                                            } else if ($saleorder['status'] == 'reject') {
                                              echo 'bg-danger';
                                            }
                                            ?>"><input class="form-check-input <?php
                                                                                if ($saleorder['status'] == 'approve' or $saleorder['status'] == 'reject') {
                                                                                  echo 'd-none';
                                                                                }
                                                                                ?>" type="checkbox" name="checkbox=<?php echo $saleorder['saleorder_id'] ?>" value=<?php echo $saleorder['saleorder_id'] ?>></td>
                    <td class="text-center <?php
                                            if ($saleorder['status'] == 'approve') {
                                              echo 'bg-success';
                                            } else if ($saleorder['status'] == 'reject') {
                                              echo 'bg-danger';
                                            }
                                            ?>"><?php echo $saleorder['saleorder_id'] ?></td>

                    <td class="text-center <?php
                                            if ($saleorder['status'] == 'approve') {
                                              echo 'bg-success';
                                            } else if ($saleorder['status'] == 'reject') {
                                              echo 'bg-danger';
                                            }
                                            ?>"><?php echo $saleorder['price_total'] ?></td>

                    <td class="text-center <?php
                                            if ($saleorder['status'] == 'approve') {
                                              echo 'bg-success';
                                            } else if ($saleorder['status'] == 'reject') {
                                              echo 'bg-danger';
                                            }
                                            ?>"><?php
                                                $date = date_create($saleorder['createdAt']);
                                                echo date_format($date, "d/m/Y");
                                                ?></td>

                    <td class="text-center <?php
                                            if ($saleorder['status'] == 'approve') {
                                              echo 'bg-success';
                                            } else if ($saleorder['status'] == 'reject') {
                                              echo 'bg-danger';
                                            }
                                            ?>"><?php echo $saleorder['username'] ?></td>

                    <td class="text-center <?php
                                            if ($saleorder['status'] == 'approve') {
                                              echo 'bg-success';
                                            } else if ($saleorder['status'] == 'reject') {
                                              echo 'bg-danger';
                                            }
                                            ?>"><?php echo $saleorder['status'] ?></td>

                    <td class="text-center <?php
                                            if ($saleorder['status'] == 'approve') {
                                              echo 'bg-success';
                                            } else if ($saleorder['status'] == 'reject') {
                                              echo 'bg-danger';
                                            }
                                            ?>">
                      <a href="detail_saleorder.php?id=<?php echo $saleorder['saleorder_id'] ?>" class="btn btn-warning"'btn btn-warning'>detail</a>
                    </td>
                  </tr>
                <?php } ?>

              </tbody>
            </table>
          </div>
        </div>

        <div class="mb-2">
          <div class="float-end">
            <input type="submit" class="btn btn-success" name='btn_approve' value="Approve">

            <input type="submit" class="btn btn-danger" name='btn_reject' value="Reject">
          </div>
        </div>

        </form>
        <div class="m-0 p-0 px-2">
          <?php if (isset($_SESSION['warning'])) : ?>
            <div class="alert alert-warning ">
              <h3>
                <?php echo $_SESSION['warning'];
                unset($_SESSION['warning']);
                ?>
              </h3>
              <br>
            </div>
          <?php endif ?>
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

</body>


<script src='js/dashborad.js'></script>

</html>