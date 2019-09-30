<?php
    include 'functions.php';
    $db = get_db_handle();

    $rid = add_player($db,
        get_param('player_name'));

    $db->close;
    if ($rid > 0) {
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = "Player.php?pid=$pid";
        header("Location: http://$host$uri/$extra",TRUE,303);
        exit;
    }
    else {
        die("How do we want to do error handling?");
    }
?>
