<?php
require_once  'connection.php';
session_start();


if (!$_SESSION['pid']) {
  if (isset($_SESSION['admin_login'])) {
    header("location: admin/admin_home.php");
  } else if (isset($_SESSION['user_login'])) {
    header("location: user/user_home.php");
  } else if (isset($_SESSION['employee_login'])) {
    header("location: employee/employee_home.php");
  }
}

if (isset($_REQUEST['update_cart'])) {
  foreach ($_SESSION['pid'] as $id) {
    if (isset($_POST['change_qty=' . $id])) {
      // echo 'id: ' . $_SESSION['pid'][$id] . ' Name: ' . $_SESSION['pname'][$id] . '<br>';
      $_SESSION['sum_qty'][$id] = $_POST['change_qty=' . $id];
      $_SESSION['grand_total'][$id] = ($_SESSION['pprice'][$id] * $_POST['change_qty=' . $id]);
    }
  }
}

if (isset($_REQUEST['btn_save'])) {

  try {
    foreach ($_SESSION['pid'] as $id) {
      $saleorder_id = $_SESSION['so_id'];
      $name = $_SESSION['pname'][$id];
      $qty = $_SESSION['sum_qty'][$id];
      $price = $_SESSION['pprice'][$id];
      $category = $_SESSION['category'][$id];
      $author = $_SESSION['author'][$id];
      $createdBy = $_SESSION['uid'];

      $_SESSION['recal_qty'][$id] = ($_SESSION['pqty'][$id]
        - $_SESSION['sum_qty'][$id]);

      $insert_saleorder = $db->prepare("INSERT INTO saleorders(saleorder_id,name, qty, price, category , author , createdBy) VALUES (:saleorder_id,:name, :qty, :price, :category , :author , :createdBy)");
      $insert_saleorder->bindParam(":saleorder_id", $saleorder_id);
      $insert_saleorder->bindParam(":name", $name);
      $insert_saleorder->bindParam(":qty", $qty);
      $insert_saleorder->bindParam(":price", $price);
      $insert_saleorder->bindParam(":category", $category);
      $insert_saleorder->bindParam(":author", $author);
      $insert_saleorder->bindParam(":createdBy", $createdBy);
      $insert_saleorder->execute();
    }

    foreach ($_SESSION['pid'] as $id) {
      $update_book =  $db->prepare("UPDATE books SET qty=:qty WHERE id= :id");
      $update_book->bindParam(":id", $id);
      $update_book->bindParam(":qty", $_SESSION['recal_qty'][$id]);
      $update_book->execute();
    }


    $insert_saleorderlist = $db->prepare("INSERT INTO saleorderlist(saleorder_id,qty_total, price_total , createdBy) VALUES (:saleorder_id, :qty_total, :price_total ,  :createdBy)");
    $insert_saleorderlist->bindParam(":saleorder_id", $saleorder_id);
    $insert_saleorderlist->bindParam(":qty_total", $_SESSION['qty_total']);
    $insert_saleorderlist->bindParam(":price_total", $_SESSION['price_total']);
    $insert_saleorderlist->bindParam(":createdBy", $_SESSION['uid']);
    // $insert_saleorderlist->execute();
    foreach ($_SESSION['pid'] as $id) {
      unset($_SESSION['pid'][$id]);
      unset($_SESSION['pname'][$id]);
      unset($_SESSION['pqty'][$id]);
      unset($_SESSION['pprice'][$id]);
      unset($_SESSION['sum_qty'][$id]);
      unset($_SESSION['grand_total'][$id]);
      unset($_SESSION['category'][$id]);
      unset($_SESSION['author'][$id]);
      unset($_SESSION['so_id'][$id]);
    }


    // echo '<pre>' . print_r($_SESSION, TRUE) . '</pre>';

    if ($insert_saleorderlist->execute()) {
      $_SESSION['success'] = "Create SaleOrder Successfully...";
      if (isset($_SESSION['admin_login'])) {
        header("location: admin/admin_home.php");
      } else if (isset($_SESSION['user_login'])) {
        header("location: user/user_home.php");
      } else if (isset($_SESSION['employee_login'])) {
        header("location: employee/employee_home.php");
      }
    } else {
      $_SESSION['error'] = "Create SaleOrder Failed...";
      header("location: cart_list.php");
    }
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
}

if (isset($_REQUEST['btn_delete'])) {
  echo '<pre>' . print_r($_SESSION, TRUE) . '</pre>';
  foreach ($_SESSION['pid'] as $id) {
    unset($_SESSION['pid'][$id]);
    unset($_SESSION['pname'][$id]);
    unset($_SESSION['pqty'][$id]);
    unset($_SESSION['pprice'][$id]);
    unset($_SESSION['sum_qty'][$id]);
    unset($_SESSION['grand_total'][$id]);
    unset($_SESSION['category'][$id]);
    unset($_SESSION['author'][$id]);
    unset($_SESSION['so_id']);
  }
  // if (isset($_SESSION['admin_login'])) {
  //   header("location: admin/admin_home.php");
  // } else if (isset($_SESSION['user_login'])) {
  //   header("location: user/user_home.php");
  // } else if (isset($_SESSION['employee_login'])) {
  //   header("location: employee/employee_home.php");
  // }
}


if (isset($_REQUEST['delete_id'])) {
  $id = $_REQUEST['delete_id'];
  // echo '<pre>' . print_r($_SESSION, TRUE) . '</pre>';
  unset($_SESSION['pid'][$id]);
  unset($_SESSION['pname'][$id]);
  unset($_SESSION['pqty'][$id]);
  unset($_SESSION['pprice'][$id]);
  unset($_SESSION['sum_qty'][$id]);
  unset($_SESSION['grand_total'][$id]);
  unset($_SESSION['category'][$id]);
  unset($_SESSION['author'][$id]);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cart List</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
</head>

<body>

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
            <a class="nav-link my-2 text-center <?php if (empty($_SESSION['admin_login'])) {
                                                  echo 'd-none';
                                                } ?>" aria-current="page" href="dashboard.php">Dashboard</a>
          </li>

          <li class="nav-item">
            <a class="nav-link my-2 text-center <?php
                                                if (empty($_SESSION['pid'])) {
                                                  echo 'd-none';
                                                }
                                                ?>" aria-current="page" href="cart_list.php">Cart</a>
          </li>

          <li class="nav-item">
            <a class="nav-link my-2 text-center pe-none" aria-current="page" href="#"> <?php
                                                                                        if (isset($_SESSION['admin_login'])) {
                                                                                          echo $_SESSION['admin_login'];
                                                                                        } else if (isset($_SESSION['user_login'])) {
                                                                                          echo $_SESSION['user_login'];
                                                                                        } else if (isset($_SESSION['employee_login'])) {
                                                                                          echo $_SESSION['employee_login'];
                                                                                        }
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
    <div class="row m-0">
      <h3 class="ps-3">SaleOrder ID: <?php echo $_SESSION['so_id'] ?></h3>

      <div class="col-12 col-lg-8 offset-lg-2">

        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th class="text-center">Id</th>
                <th class="text-center">Name</th>
                <th class="text-center">qty</th>
                <th class="text-center">price</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <form action="" method="post">
              <tbody>

                <?php
                foreach ($_SESSION['pid'] as $k) { ?>
                  <tr>
                    <td class="text-center"><?php echo $_SESSION['pid'][$k] ?></td>
                    <input type="text" class="form-control d-none" name='id' value=<?php echo $_SESSION['pid'][$k] ?> />
                    <td><?php echo $_SESSION['pname'][$k] ?></td>

                    <td class="text-center">
                      <input class="form-control m-auto" min="1" max=<?php echo $_SESSION['pqty'][$k] ?> style="width: 60px" onchange='update_Btn()' type="number" name="change_qty=<?php echo $_SESSION['pid'][$k] ?>" value=<?php echo $_SESSION['sum_qty'][$k]  ?> />
                    </td>

                    <td class="text-center"><?php echo $_SESSION['pprice'][$k] ?></td>
                    <td class="text-center">
                      <a href="?delete_id=<?php echo $_SESSION['pid'][$k] ?>" class="btn btn-danger ">Delete</a>
                    </td>
                  </tr>
                <?php } ?>

              </tbody>
          </table>

        </div>


        <div class="row">
          <div class="col-12">
            <p class="text-end h4">GrandTotal: <?php
                                                $_SESSION['price_total'] = 0;
                                                $_SESSION['qty_total'] = 0;
                                                foreach ($_SESSION['pid'] as $id) {
                                                  $_SESSION['price_total'] += $_SESSION['grand_total'][$id];
                                                  $_SESSION['qty_total'] += $_SESSION['sum_qty'][$id];
                                                }
                                                echo $_SESSION['price_total'];
                                                ?></p>
          </div>
        </div>


        <div class="row">
          <div class="col-12 col-lg-3">
            <div class="w-100">
              <input type="submit" id="btn_save" class="btn btn-success w-100" name="btn_save" Value="Save" />
            </div>

            <div class="w-100">
              <input type="submit" name="update_cart" id='btn_update' onchange='save_Btn()' value="Update" class="btn btn-warning w-100" />
            </div>

            <div class="w-100">
              <input type="submit" class="btn btn-danger w-100 mt-2" name="btn_delete" value="Delete" />
            </div>

          </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
<script src='js/hide_btn.js' type="text/javascript">
</script>

</html>