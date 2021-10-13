<?php
require_once '../connection.php';
// ใช้สำหรับเริ่มต้นใช้งาน session
session_start();

// เช็คว่าไม่มี session = Admin Login ให้ Rediect กลับไปหน้า login
if (!isset($_SESSION['admin_login'])) {
    header('location: ../index.php');
}

if (isset($_REQUEST['id'])) {
    try {
        $id = $_REQUEST['id'];
        $select_books = $db->prepare("SELECT * FROM books WHERE category = :id");
        $select_books->bindParam(':id', $id);
        $select_books->execute();
    } catch (PDOException $e) {
        $e->getMessage();
    }
} else if (empty($_REQUEST['id'])) {
    try {
        $select_books = $db->prepare("SELECT * FROM books");
        $select_books->execute();
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
                            <p class="nav-link btn btn-outline-white my-2">
                                <?php
                                echo $_SESSION['admin_login']
                                ?>
                            </p>
                        </li>

                        <li class="nav-item">
                            <a href="../logout.php" class="nav-link btn btn-danger text-white my-2 w-100">Logout</a>
                        </li>
                    </ul>

                    <div class="d-lg-flex">


                    </div>
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
                <a href="../add.php" class="btn btn-success w-100 mb-2">Add Book</a>

                <a href="#" class="btn btn-warning w-100 mb-2">Add Category</a>
                <div class="border border-2 border-light">
                    <ul class="list-group">
                        <li class="list-group-item text-center"><b>Categories</b></li>
                        <li class="list-group-item <?php
                                                    if (empty($_REQUEST['id'])) {
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
                                <a class="nav-link  p-0 text-dark" href="admin_home.php?id=<?php echo $row['id']; ?>">
                                    <?php
                                    echo $row['name'];
                                    ?>
                                </a>
                            </li>
                        <?php   } ?>

                    </ul>
                </div>
            </div>

            <div class="col-auto col-lg-10">
                <div class="row mt-3 mt-lg-0">
                    <?php
                    while ($books = $select_books->fetch(PDO::FETCH_ASSOC)) { ?>

                        <div class="col-12 col-lg-3 mb-3">
                            <div class="card" style="width: 100%;">
                                <!-- <img src="..." class="card-img-top" alt="..."> -->
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $books['name'] ?></h5>
                                    <p class="card-text m-0">Author: <?php echo $books['author'] ?> </p>
                                    <p class="card-text m-0">Price: <?php echo $books['price'] ?> </p>
                                    <p class="card-text m-0">Qty: <?php echo $books['qty'] ?> </p>
                                    <a href="#" class="btn btn-primary mt-2">Go somewhere</a>
                                </div>
                            </div>
                        </div>

                    <?php   } ?>
                </div>
            </div>
        </div>


    </div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
</body>

</html>