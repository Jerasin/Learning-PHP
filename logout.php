<?php

    session_start();
    header("location: index.php");
    // delete session
    session_destroy();

?>