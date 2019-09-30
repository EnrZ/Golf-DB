<?php
    include 'functions.php';

    $date = $_POST["round_date"];
    if(preg_match('/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/'  , $date)) {
        $valid = "true";
    }
    else{
        $valid = "false";
}


    $db = get_db_handle();

    $rid = add_round($db,
        get_param('round_date'),
        get_param('cid'));


    mysqli_close($db);
    if ($rid > 0) {
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = "Round.php?rid=$rid";
        header("Location: http://$host$uri/$extra",TRUE,303);
        exit;
    }
    else {
        die("error handling");
    }
?>
