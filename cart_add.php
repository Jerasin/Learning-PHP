<?php
require_once  'connection.php';
session_start();

if (isset($_REQUEST['id'])) {
  try {
    $id = $_REQUEST['id'];
    $duplicate = false;
    if (isset($_SESSION['pid'])) {
      foreach ($_SESSION['pid'] as $k) {
        if ($k == $_REQUEST['id']) {
          $duplicate = true;
        }
      }
    } else {
      // echo "Create Arrary";
      $_SESSION['pid'] = array();
    }

    if ($duplicate) {
      $_SESSION['sum_qty'][$_REQUEST['id']] += 1;
      $_SESSION['grand_total'][$_REQUEST['id']] = ($_SESSION['pprice'][$_REQUEST['id']] * $_SESSION['sum_qty'][$_REQUEST['id']]);
    } else {
      $select_books = $db->prepare("SELECT b.id , b.name , a.id as author_id , a.name as author , b.price , b.qty , c.id as category_id , c.name as category , image FROM `books` as b LEFT JOIN categories as c ON b.category = c.id LEFT JOIN authors as a ON b.author = a.id WHERE b.id = :id ");
      $select_books->bindParam(':id', $id);
      $select_books->execute();
      $_SESSION['so_id'] = rand(10, 100000);
      while ($book = $select_books->fetch(PDO::FETCH_ASSOC)) {
        $_SESSION['pid'][$_REQUEST['id']] = $book['id'];
        $_SESSION['pname'][$_REQUEST['id']] = $book['name'];
        $_SESSION['pqty'][$_REQUEST['id']] = $book['qty'];
        $_SESSION['pprice'][$_REQUEST['id']] = $book['price'];
        $_SESSION['sum_qty'][$_REQUEST['id']] = 1;
        $_SESSION['category'][$_REQUEST['id']] = $book['category_id'];
        $_SESSION['author'][$_REQUEST['id']] = $book['author_id'];

        // sum price all product
        $_SESSION['grand_total'][$_REQUEST['id']] += $_SESSION['pprice'][$_REQUEST['id']];
      }
    }
    if (isset($_SESSION['admin_login'])) {
      header('location: admin/admin_home.php');
    } else if (isset($_SESSION['user_login'])) {
      header('location: user/user_home.php');
    } else if (isset($_SESSION['employee_login'])) {
      header('location: employee/employee_home.php');
    }
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage;
  }
}
