<?php
    include 'functions.php';
    $db = get_db_handle();

    include 'header.php';
    if ( player_id() == '' && get_param('searchstr') == '' ) {
?>      
      <h2>Add New Player</h2>
        <form method="POST" action="add_player.php">
          <input type="text" id="player_name" name="player_name" onfocus="if (this.value == 'Player Name') this.value='';" onblur="if (this.value == '') this.value = 'Player Name'; validatePlayerName()" value="Player Name" autocomplete="off" spellcheck="off" />
          <input type="submit" id="submit" value="Add Player" />
        </form>
        <div id="nameError"></div>
  <?php
    }
    else {
      if ( player_id() == '' ) {
        $pids = player_search( $db, get_param('searchstr'));
      }
      else {
        $pids = array( player_id() );
      }
      foreach( $pids as $pid ) {
      
      $rounds = get_player_rounds($db, $pid);

      $name = get_param('str');
?>
    <h2><?php echo $name ?></h2>
<?php
      foreach( $rounds as $round ) {
?>
    <h3><?php echo $round['round_date'] ?> -- <?php echo $round['course_name'] ?></h3> 
    <h4><?php echo $round['course_location'] ?></h4> 
      <table class="tables">
        <tr>
            <th class="cell_head">Hole</th>
            <?php
                $holes = get_course_holes($db, $round['cid']);
                while ($hole = mysqli_fetch_assoc($holes)) {
            ?>
            <th class="cell_width"><?php echo $hole['hole_number'] ?></th>
            <?php } ?>
            <th>&nbsp;</th>
        </tr>

        <tr>
          <td>Score</td>
        <?php
          $scores = get_player_scores($db, $round['rid'], player_id());        
          foreach ($scores as $score) {
        ?>
            <td class="score"><?php echo $score ?></td>
        <?php } ?>
        </tr>

        <tr>
            <td>Par</td>
            <?php
                $holes = get_course_holes($db, $round['cid']);
                while ($hole = mysqli_fetch_assoc($holes)) {
            ?>
            <td class="par"><?php echo $hole['par'] ?></td>
            <?php } ?>
        </tr>
      </table>
      <?php } 
       
     } 
    } ?> 
<?php include 'footer.php'; ?>
