<?php
    include 'functions.php';
    $db = get_db_handle();
    
    include 'header.php';
    if ( round_id() == '' ) {
?>      
      <h2>Add New Golf Round</h2>
        <form method="POST" action="add_round.php">
          Enter date (YYYY-MM-DD):
          <input type="text" id="round_date" name="round_date" onblur="validateDate()"/> 
          <select name="cid">
          <?php
            $courses = get_courses($db);
            while ($course = mysqli_fetch_assoc($courses)) {
          ?>
          <option value="<?php echo $course['cid']?>"><?php echo $course['name'] ?></option>
          <?php } ?>
          </select>
          <input type="submit" id="submit" value="Add Round" />
          <div id="dateError"></div>
        </form>
<?php
    }
    else {
        $round = get_round($db, round_id());
?>
    <h2><?php echo $round['round_date'] ?> -- <?php echo $round['course_name'] ?></h2> 
    <h3><?php echo $round['course_location'] ?></h3> 
      <table>
        <tr>
            <th>&nbsp;</th>
            <?php
                $holes = get_course_holes($db, $round['cid']);
                while ($hole = mysqli_fetch_assoc($holes)) {
            ?>
            <th><?php echo $hole['hole_number'] ?></th>
            <?php } ?>
            <th>&nbsp;</th>
        </tr>


        <?php
            $round_scores = get_round_scores($db, round_id());
            $participating_player = array();
            foreach ($round_scores as $player_scores) {
                $participating_player[ $player_scores['pid'] ] = 1;
        ?>
        <tr>
          <form method="POST" action="delete_player_round.php">
            <input type="hidden" name="rid" value="<?php echo round_id() ?>"/>
            <input type="hidden" name="pid" value="<?php echo $player_scores['pid'] ?>"/>
            <td><?php echo $player_scores['player_name']?></td>
            <?php
                foreach ($player_scores['scores'] as $score) {
            ?>
            <td class="score"><?php echo $score ?></td>
            <?php } ?>
            <td><input type="submit" value="Delete" class="roundUpdate"></td>
          </form>
        </tr>
        <?php } ?>


        <tr class="roundUpdate" >
          <form method="POST" action="add_player_round.php">
            <input type="hidden" name="rid" value="<?php echo round_id() ?>"/>
            <td><select name="pid">
              <?php
                $players = get_players($db);
                while ($player = mysqli_fetch_assoc($players)) {
                    if ( $participating_player[ $player['pid'] ] ) {
                        // Exclude players that have already been listed
                        // from being an option for adding new scores
                        continue;
                    }
              ?>
                <option value="<?php echo $player['pid']?>"><?php echo $player['name'] ?></option>
              <?php } ?>
            </select></td>
            <?php
                mysqli_data_seek($holes, 0);
                while ($hole = mysqli_fetch_assoc($holes)) {
            ?>
            <td><input
                type="text"
                id="hole_<?php echo $hole['hole_number'] ?>"
                name="hole_<?php echo $hole['hole_number'] ?>"
                onblur="validateScores(this.id);" size="2"></td>
            <?php } ?>
            <td><input type="submit" value="Submit Scores"></td>
          </form>
        </tr>
        <tr>
            <td colspan="18"><div id="scoreError"></div></td>
        </tr> 
        <tr>
            <td>Par</td>
            <?php
                mysqli_data_seek($holes, 0);
                while ($hole = mysqli_fetch_assoc($holes)) {
            ?>
            <td class="par"><?php echo $hole['par'] ?></td>
            <?php } ?>
        </tr>
        <tr id="updateButton" >
          <td><button type="button" onclick="roundUpdateButton();" >Update Round</button></td>
        </tr>
      </table>
<?php }
      include 'footer.php'; ?>
