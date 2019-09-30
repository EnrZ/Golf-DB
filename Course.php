<?php
    include 'functions.php';
    $db = get_db_handle();

    include 'header.php';
    if ( course_id() == '' && get_param('searchstr') == '' ) {
?>      
      <h2>Add New Golf Course</h2>
        <form method="POST" action="add_course.php">
          <input type="text" id="course_name" name="course_name" onfocus="if (this.value == 'Course Name') this.value='';" onblur="if (this.value == '') this.value = 'Course Name';" value="Course Name" autocomplete="off" spellcheck="off" />
          <input type="text" id="course_location" name="course_location" onfocus="if (this.value == 'Course Location') this.value='';" onblur="if (this.value == '') this.value = 'Course Location'; validateLocation()" value="Course Location" autocomplete="off" spellcheck="off" />
          <div id="locError"></div>
          Holes: <select name="holes" onchange="courseSize(this.value);">
            <option>9</option>
            <option selected="selected">18</option>
          </select>
          <input type="submit" id="submit" value="Add Course" />
          <table>
            <tr>
              <th>Hole</th>
              <?php
              for ( $hole = 1; $hole <=9; $hole++ ) {
              ?>
              <th><?php echo $hole ?></th>
              <?php }
              for ( $hole = 10; $hole <=18; $hole++ ) {
              ?>
              <th class="backNine"><?php echo $hole ?></th>
              <?php } ?>
              <th>&nbsp;</th>
            </tr>
            <tr></tr>
            <tr>
              <td> Par </td>
              <?php
              for ( $hole = 1; $hole <=9; $hole++ ) {
              ?>
              <td><input type="text" id="hole_[<?php echo $hole ?>]" name="hole_<?php echo $hole ?>" size="2" onblur="validatePar(this.name)"></td>
              <?php } 
              for ( $hole = 10; $hole <=18; $hole++ ) {
              ?>
              <td class="backNine"><input type="text" id="hole_<?php echo $hole ?>" name="hole_<?php echo $hole ?>" size="2" onblur="validatePar(this.name)"></td>
              <?php } ?>
              <td><div id="parError"></div></td>
            </tr>
          </table>
        </form>

    <?php
    }
    else {
      $course = get_course( $db, course_id() );
    ?>
    <h2><?php echo $course['name'] ?> -- <?php echo $course['location']; ?></h2> 
  <?php  
      if ( $course != false ) {
        $rounds = get_course_rounds( $db, course_id() );

        foreach( $rounds as $round ) {
          ?><h3><?php echo $round['round_date']; ?></h3>

          <table class="tables">
            <tr>
                <th class="cell_head">Hole</th>
                <?php
                    $holes = get_course_holes($db, $round['cid']);
                    while ($hole = mysqli_fetch_assoc($holes)) {
                ?>
                <th class="cell_width"><?php echo $hole['hole_number'] ?></th>
                <?php } ?>
            </tr>


            <?php
                $round_scores = get_round_scores($db, $round['rid']);
                foreach ($round_scores as $player_scores) {
            ?>
            <tr>
                <td class="cell_width"><?php echo $player_scores['player_name']?></td>
                <?php
                    foreach ($player_scores['scores'] as $score) {
                ?>
                <td class="score"><?php echo $score ?></td>
                <?php } ?>
            </tr>
  <?php } ?>

            <tr>
                <td>Par</td>
                <?php
                    //mysql_data_seek($holes, 0);
                    $holes = get_course_holes($db, $round['cid']);
                    while ($hole = mysqli_fetch_assoc($holes)) {
                ?>
                <td class="par"><?php echo $hole['par'] ?></td>
                <?php } ?>
            </tr>
            </table><?php
        }

      
        
      }
      else {
        echo "Error retrieving Course data.";
      }
    }  
include 'footer.php'; ?>
