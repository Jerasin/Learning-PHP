<?php
require_once '../connection.php';
// ใช้สำหรับเริ่มต้นใช้งาน session
session_start();

// Config Pagination
$limit = 10;

// echo '<pre>' . print_r($_SESSION, TRUE) . '</pre>';

// เช็คว่าไม่มี session = Admin Login ให้ Rediect กลับไปหน้า login
if (!isset($_SESSION['admin_login'])) {
    header('location: ../index.php');
}

if (empty($_POST['page'])) {
    $page = 1;
    $start_index = ($page - 1) * $limit;
} else if (isset($_POST['page'])) {
    $page = $_POST['page'];
    $start_index = ($page - 1) * $limit;
}

if (isset($_GET['category'])) {
    // $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    // echo $actual_link;
    try {
        $id = $_GET['category'];
        $select_books = $db->prepare("SELECT b.id , b.name , a.name as author , b.price , b.qty , c.name as category ,b.image FROM `books` as b LEFT JOIN categories as c ON b.category = c.id LEFT JOIN authors as a ON b.author = a.id WHERE c.id = :id and b.qty >= 1 LIMIT :start_index,:limit");
        $select_books->bindParam(':id', $id);
        $select_books->bindValue(':start_index', intval($start_index),  PDO::PARAM_INT);
        $select_books->bindValue(':limit', intval($limit),  PDO::PARAM_INT);
        $select_books->execute();

        // Pagination
        $count_books = $db->prepare("SELECT count(b.id) from `books` as b LEFT JOIN categories as c ON b.category = c.id WHERE c.id = :id and b.qty >= 1");
        $count_books->bindParam(':id', $id);
        $count_books->execute();
        $count = $count_books->fetch(PDO::FETCH_ASSOC);
        foreach ($count as $key => $value) {
            $total_page =  ceil($value / $limit);
        }
    } catch (PDOException $e) {
        $e->getMessage();
    }
} else if (empty($_GET['category'])) {
    try {
        $id = 0;
        // LIMIT 0, 4  =  "return only 4 records, start on record 0 "
        $select_books = $db->prepare("SELECT b.id , b.name , a.name as author , b.price , b.qty , c.name as category , b.image FROM `books` as b LEFT JOIN categories as c ON b.category = c.id LEFT JOIN authors as a ON b.author = a.id WHERE b.qty >= 1  LIMIT :start_index,:limit");
        $select_books->bindValue(':start_index', intval($start_index),  PDO::PARAM_INT);
        $select_books->bindValue(':limit', intval($limit),  PDO::PARAM_INT);
        $select_books->execute();

        // Pagination
        $count_books = $db->prepare("SELECT count(id) from `books`");
        $count_books->execute();
        $count = $count_books->fetch(PDO::FETCH_ASSOC);
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
    <title>Admin Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
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
                            <a class="nav-link my-2 text-center" aria-current="page" href="admin_home.php">Home</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link my-2 text-center" aria-current="page" href="../dashboard.php">Dashboard</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link my-2 text-center <?php
                                                                if (empty($_SESSION['pid'])) {
                                                                    echo 'd-none';
                                                                }
                                                                ?>" aria-current="page" href="../cart_list.php">Cart</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link my-2 text-center pe-none" aria-current="page" href="admin/admin_home.php"><?php
                                                                                                                            echo $_SESSION['admin_login']
                                                                                                                            ?></a>
                        </li>

                        <li class="nav-item">
                            <a href="../logout.php" class="nav-link btn btn-danger text-white my-2 w-100">Logout</a>
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

        <div class="row m-0 mt-3">
            <div class="col-md-2">
                <a href="../book_list.php" class="btn btn-success w-100 mb-2">Book List</a>

                <a href="../category_list.php" class="btn btn-warning w-100 mb-2">Category List</a>
                <div class="border border-2 border-light">
                    <ul class="list-group">
                        <li class="list-group-item text-center"><b>Categories</b></li>
                        <li class="list-group-item <?php
                                                    if (empty($_GET['category'])) {
                                                        echo "active";
                                                    }
                                                    ?>  text-center">
                            <a class="nav-link  p-0 text-dark" aria-current="page" href="admin_home.php">
                                All
                            </a>
                        </li>

                        <?php
                        $select_categories = $db->prepare("SELECT id ,name FROM categories");
                        $select_categories->execute();

                        while ($row = $select_categories->fetch(PDO::FETCH_ASSOC)) { ?>
                            <li class="list-group-item <?php if ($id == $row['id']) {
                                                            echo "active";
                                                        }  ?> text-center nav-item">
                                <a class="nav-link  p-0 text-dark" href="admin_home.php?category=<?php echo $row['id']; ?>">
                                    <?php
                                    echo $row['name'];
                                    ?>
                                </a>
                            </li>
                        <?php   } ?>

                    </ul>
                </div>
            </div>

            <div class="col-12 col-lg-10">
                <div class="row mt-3 mt-lg-0">
                    <?php
                    while ($books = $select_books->fetch(PDO::FETCH_ASSOC)) { ?>

                        <div class="col-12 col-lg-3 mb-3">

                            <div class="card" style="width: 100%;">
                                <div class="card-header">
                                    <?php if ($books['image']) { ?>
                                        <img src=<?php echo '../upload/' . $books['image'] ?> class="card-img-top p-0" alt="...">
                                    <?php } ?>

                                    <?php if (!$books['image']) { ?>
                                        <img src='../media/noimage.jpg' class="card-img-top p-0" alt="...">
                                    <?php } ?>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title wlc"><?php echo $books['name'] ?></h5>
                                    <p class="card-text m-0">Author: <?php echo $books['author'] ?> </p>
                                    <p class="card-text m-0">Price: <?php echo $books['price'] ?> </p>
                                    <p class="card-text m-0">Qty: <?php echo $books['qty'] ?> </p>
                                    <span>Category:</span>
                                    <span class="badge bg-secondary"><?php echo $books['category']  ?></span>
                                    <a href="../detail_book.php?id=<?php echo $books['id'] ?>" class="btn btn-warning w-100 mt-2">Detail</a>
                                </div>
                            </div>

                        </div>

                    <?php   } ?>
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

</html>