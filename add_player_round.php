<?php
    include 'functions.php';
    $db = get_db_handle();

    $holes = golf_query($db, "
        select h.hole_number
        from GOLF_HOLE h
        join GOLF_ROUND r on h.cid=r.cid
        where r.rid='%s'
        order by h.hole_number", array(round_id()));

    $scores = array();
    while ($hole = mysqli_fetch_assoc($holes)) {
        $number = $hole['hole_number'];
        $scores[$hole['hole_number']] = get_param("hole_$number");
    }

    add_player_round($db,
        get_param('pid'),
        get_param('rid'),
        $scores);

    mysqli_close($db);
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $rid = round_id();
    $extra = "Round.php?rid=$rid";
    header("Location: http://$host$uri/$extra",TRUE,303);
    exit
?>
