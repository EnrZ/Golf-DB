<?php
    include 'functions.php';
    $db = get_db_handle();

    delete_player_round($db,
        get_param('pid'),
        get_param('rid'));

    mysqli_close($db);
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $rid = round_id();
    $extra = "Round.php?rid=$rid";
    header("Location: http://$host$uri/$extra",TRUE,303);
    exit
?>
