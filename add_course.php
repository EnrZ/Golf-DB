<?php
    include 'functions.php';
    $db = get_db_handle();

    $cid = add_course($db,
        get_param('course_name'),
        get_param('course_location'),
        get_param('holes'));

    mysqli_close($db);
    if ($cid > 0) {
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = "Course.php?cid=$cid";
        header("Location: http://$host$uri/$extra",TRUE,303);
        exit;
    }
    else {
        die("How do we want to do error handling?");
    }
?>
