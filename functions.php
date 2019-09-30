<?php

    function get_param($param_name) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            return array_key_exists($param_name, $_POST)
                ? $_POST[$param_name]
                : '';
        }
        else {
            return array_key_exists($param_name, $_GET)
                ? $_GET[$param_name]
                : '';
        }
    }

    function course_id() { return get_param('cid'); }
    function round_id()  { return get_param('rid'); }
    function player_id() { return get_param('pid'); }

    function get_db_handle() {
        $dbh = mysqli_connect()        OR die(mysqli_error());
        mysqli_select_db($dbh ,'test')   OR die(mysqli_error());
        return $dbh;
    }

    function golf_query($db, $query_template, $query_params=array(), $debug=0) {
        //
        // given a Database handle, a query template and query parameters
        // produce a query ready to pass to mysqli_query by using
        // real_escape_string() to quote query params
        // and then use sprintf to format the template into a query
        // the query is then passed to mysqli_query and the result is returned

		
        //$quoted_params = array_map('mysqli_real_escape_string', array_fill(1,$count,$db), $query_params);
        $quoted_params = array_map(array($db, 'real_escape_string'), $query_params);
		array_unshift($quoted_params, $query_template);
        $query = call_user_func_array('sprintf', $quoted_params);

        if ($debug) {
            echo "Query: [$query]";
        }

        $result = mysqli_query($db, $query);
        if (!$result) {
            die(mysqli_error(). ": $query: ". debug_print_backtrace());
        }

        return $result;
    }

    function begin_transaction($db) {
        mysqli_query($db, 'START TRANSACTION')   OR die(mysql_error());
    }

    function commit_transaction($db) {
        mysqli_query($db,  'COMMIT')   OR die(mysql_error());
    }

    function rollback_transaction($db) {
        mysqli_query($db,  'ROLLBACK')   OR die(mysql_error());
    }

    function get_players($db) {
        return golf_query($db,
            'select pid, name from GOLF_PLAYER order by name');
    }
    
    function get_courses($db) {
        return golf_query($db,
            'select cid, name, location from GOLF_COURSE order by name');
    }

    function get_rounds($db) {
        return golf_query($db,
            "select r.rid, c.cid, c.name course_name, c.location course_location, r.round_date "
            ."from GOLF_ROUND r "
            ."join GOLF_COURSE c on c.cid=r.cid");
    }

    function get_round($db, $rid) {
        $round = golf_query($db,
            "select c.cid, c.name course_name, c.location course_location, r.round_date "
            ."from GOLF_ROUND r "
            ."join GOLF_COURSE c on c.cid=r.cid "
            ."where r.rid='%s'",
            array($rid));

        return mysqli_fetch_assoc($round);
    }

    function get_course_holes($db, $cid) {
        return golf_query($db,
            "select hole_number, par from "
            ."GOLF_HOLE where cid='%s' "
            ."order by hole_number", array($cid));
    }

    function get_round_scores($db, $rid) {
        $round_scores = golf_query($db,
            "select p.pid, p.name, s.hole_number, s.score "
            ."from GOLF_SCORE s "
            ."join GOLF_PLAYER p on p.pid=s.pid "
            ."where s.rid='%s' "
            ."order by p.pid, s.hole_number",
            array($rid));

        $result = array();

        $player_scores;
        while ($round_score = mysqli_fetch_assoc($round_scores)) {
            if (
                ( !isset($player_scores) ) OR
                ( $round_score['pid'] != $player_scores['pid'] )
            ) {
                if (isset($player_scores)) {
                    array_push($result, $player_scores);
                }
                $player_scores = array(
                    'pid'           => $round_score['pid'],
                    'player_name'   => $round_score['name'],
                    'scores'        => array(
                        $round_score['hole_number'] => $round_score['score'],
                    ),
                );
            }
            else {
                $player_scores['scores'][
                    $round_score['hole_number']
                ] = $round_score['score'];
            }
        }

        if (isset($player_scores)) {
            array_push($result, $player_scores);
        }

        return $result;
    }

    function delete_player_round($db, $pid, $rid) {
        golf_query( $db,
            "delete from GOLF_SCORE where rid='%s' and pid='%s'",
            array($rid, $pid));
    }

    function add_player_round($db, $pid, $rid, $hole_scores) {
        begin_transaction($db);
        foreach ($hole_scores as $hole_number => $score) {
            golf_query( $db,
                "insert into GOLF_SCORE (rid, pid, hole_number, score) values ('%s', '%s', '%s', '%s')",
                array($rid, $pid, $hole_number, $score));
        }
        if (verify_player_round($db, $rid, $pid)) {
            commit_transaction($db);
        }
        else {
            rollback_transaction($db);
        }
    }

    function get_course($db, $cid) {
      $course = golf_query($db, "
        SELECT name, location
        FROM GOLF_COURSE
        WHERE cid='%s'", array($cid));

      if ($course != false) {
        while ($thiscourse = mysqli_fetch_assoc($course)){
        
          $result = array(
            'name'      => $thiscourse['name'],
            'location'  => $thiscourse['location'],
          );
        }
      }
      else {
        $result = false;
      }
      return $result;
    }
   
    function get_course_rounds($db, $cid) {
      $course_rounds = golf_query($db, "
        SELECT cid, rid, round_date
        FROM GOLF_ROUND
        WHERE cid='%s'", array($cid));


      if ($course_rounds != false){
        $result = array();

        while ($course_round = mysqli_fetch_assoc($course_rounds)) {
          $round = array(
            'cid'         => $course_round['cid'],
            'rid'         => $course_round['rid'],
            'round_date'  => $course_round['round_date']
          );

          array_push($result, $round);
        }
      }
      else {
        $result = false;
      }

      return $result;
    }

    function get_player_rounds($db, $pid) {
      $player_rounds = golf_query($db, "
        SELECT DISTINCT s.rid, r.cid, round_date, name course_name, location course_location 
        FROM GOLF_SCORE s, GOLF_ROUND r, GOLF_COURSE c
        WHERE r.rid=s.rid and c.cid=r.cid and s.pid = '%s'
        ", array($pid));
        
      if ($player_rounds != false){
        $result = array();

        while ($player_round = mysqli_fetch_assoc($player_rounds)) {
          $round = array(
            'rid'   => $player_round['rid'],
            'cid'   => $player_round['cid'],
            'round_date' => $player_round['round_date'],
            'course_name' => $player_round['course_name'],
            'course_location' => $player_round['course_location'],
          );

          array_push($result, $round);
        }
      }
      else {
        $result = false;
      }

      return $result;
    }

    function get_player_scores($db, $rid, $pid) {
      $player_scores = golf_query($db, "
        SELECT hole_number, score
        FROM GOLF_SCORE s, GOLF_ROUND r
        WHERE r.rid=s.rid and s.rid = '%s' and s.pid = '%s'
        ", array($rid, $pid)); 

      $result = array();

      while ($player_score = mysql_fetch_assoc($player_scores)) {
        $result[ $player_score['hole_number'] ] = $player_score['score'];
      }
      return $result;
    }

    function verify_player_round($db, $rid, $pid) {
        return 1;
        $result = golf_query($db, "
            select
            from      GOLF_ROUND r
            join      GOLF_HOLE  h on h.cid=r.cid
            left join GOLF_SCORE s
                on  s.pid='%s'
                and s.rid=r.rid
                and s.hole_number=h.hole_number
            where r.rid='%s'
            ", array($pid, $rid));
    }

    function add_player($db, $player_name) {
        golf_query( $db,
            "insert into GOLF_PLAYER (name) values ('%s')",
            array($player_name));

        $result = golf_query( $db,
            "select pid from GOLF_PLAYER where name='%s'",
            array($player_name));

        return mysqli_fetch_assoc($result)['pid'];
    }

    FUNCTION add_round($db, $round_date, $cid) {
        golf_query( $db,
            "insert into GOLF_ROUND (round_date, cid) values ('%s', '%s')",
            array($round_date, $cid));

        $result = golf_query( $db,
            "select rid from GOLF_ROUND where round_date='%s' and cid='%s'",
            array($round_date, $cid));

        return mysqli_fetch_assoc($result)['rid'];
    }

    function add_course($db, $course_name, $course_location, $holes) {
        golf_query( $db,
            "insert into GOLF_COURSE (name, location) values ('%s', '%s')",
            array($course_name, $course_location));

        $result = golf_query( $db,
            "select cid from GOLF_COURSE where name='%s' and location='%s'",
            array($course_name, $course_location));

        $cid = mysqli_fetch_assoc($result)['cid'];

        $default_par = 3; // because I'm lazy
        for ($i = 1; $i <= $holes; $i++) {
            golf_query( $db,
                "insert into GOLF_HOLE (cid, hole_number, par) values ('%s', '%s', '%s')",
                array($cid, $i, $default_par));
        }

        return $cid;
    }

    function player_search($db, $searchstr) {
      $players = golf_query( $db, "
        SELECT pid, name FROM GOLF_PLAYER
        WHERE name LIKE '%s%%'",
        array($searchstr));

      if ( is_array($players) ) {
        $result = array();
        foreach ($players as $player) {
          $name = array(
            'name'  => $player['name'],
            'pid'   => $player['pid']
          );
          array_push($result, $name);
        }
      }
      else {
        $result = false;
      }
      return $result;
    }
?>
