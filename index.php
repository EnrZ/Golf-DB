<?php
    include 'functions.php';
    $db = get_db_handle();
    include 'header.php';
?>
<h2>Add New Golf Round</h2>
  <form method="POST" action="add_round.php">
    Enter date (YYYY-MM-DD):
    <input type="text" name="round_date" />
    <select name="cid">
      <?php
        $courses = get_courses($db);
        while ($course = mysqli_fetch_assoc($courses)) {
      ?>
        <option value="<?php echo $course['cid']?>"><?php echo $course['name'] ?></option>
      <?php } ?>
    </select>
    <input type="submit" value="Add Round" />
  </form>
		
<h2>Players</h2>
<ul>
    <?php
        $players = get_players($db);
        while ($player = mysqli_fetch_assoc($players)) {
    ?>
        <li><?php echo $player['name'] ?></li>
    <?php } ?>
</ul>
<h2>Courses</h2>
<ul>
    <?php
        $courses = get_courses($db);
        while ($course = mysqli_fetch_assoc($courses)) {
    ?>
        <li><?php echo $course['name'] ?></li>
    <?php } ?>
</ul>
<h2>Rounds</h2>
<ul>
    <?php
        $rounds = get_rounds($db);
        while ($round = mysqli_fetch_assoc($rounds)) {
    ?>
        <li><a href="round.php?rid=<?php echo $round['rid']?>"><?php echo $round['round_date'] ?></a> <?php echo $round['course_name'] .' '. $round['course_location']?></li>
    <?php } ?>
</ul>
<?php
    include 'footer.php';
    mysqli_close($db);
?>
