<html>
<head>
  <title>Epoch System</title>
  <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
  <div>



<form action="<?=$_SERVER['PHP_SELF'];?>" method="post" name="createSystem">
<?php
$config = parse_ini_file('./config.ini');

$db = new \PDO(   "mysql:host=".$config['servername'].";dbname=".$config['database'].";charset=utf8mb4",
                        $config['username'],
                        $config['password'],
                        array(
                            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                            \PDO::ATTR_PERSISTENT => false
                        )
                    );

function generateDropDownSQL($table) {
  switch ($table) {
    case 'animal':
      $sql = "SELECT DISTINCT x.id, x.description, x.preselect";
      $sql .= " FROM epoch_transmitter as tx INNER JOIN epoch_receiver as rec ON tx.receiver_id = rec.id";
      $sql .= " INNER JOIN epoch_$table as x ON tx.".$table."_id=x.id WHERE tx.part_number!=''";
      if ($_POST['system']!='none') {$sql .= " AND rec.system_id='".$_POST['system']."'"; }
      $sql .= " AND x.enable=1 ORDER BY x.description ASC";
      break;
    case 'biopotential':
      $sql = "SELECT DISTINCT x.id, x.description, x.preselect";
      $sql .= " FROM epoch_transmitter as tx INNER JOIN epoch_receiver as rec ON tx.receiver_id = rec.id";
      $sql .= " INNER JOIN epoch_$table as x ON tx.".$table."_id=x.id WHERE tx.part_number!=''";
      if ($_POST['system']!='none') {$sql .= " AND rec.system_id='".$_POST['system']."'"; }
      $sql .= " AND tx.animal_id='".$_POST['animal']."'";
      $sql .= " AND x.enable=1 ORDER BY x.description ASC";
      break;
    case 'channels':
      $sql = "SELECT DISTINCT x.id, x.description, x.preselect";
      $sql .= " FROM epoch_transmitter as tx INNER JOIN epoch_receiver as rec ON tx.receiver_id = rec.id";
      $sql .= " INNER JOIN epoch_$table as x ON tx.".$table."_id=x.id WHERE tx.part_number!=''";
      if ($_POST['system']!='none') {$sql .= " AND rec.system_id='".$_POST['system']."'"; }
      $sql .= " AND tx.animal_id='".$_POST['animal']."'";
      $sql .= " AND tx.biopotential_id='".$_POST['biopotential']."'";
      $sql .= " AND x.enable=1 ORDER BY x.description ASC";
      break;
    case 'duration':
      $sql = "SELECT DISTINCT x.id, x.description, x.preselect";
      $sql .= " FROM epoch_transmitter as tx INNER JOIN epoch_receiver as rec ON tx.receiver_id = rec.id";
      $sql .= " INNER JOIN epoch_$table as x ON tx.".$table."_id=x.id WHERE tx.part_number!=''";
      if ($_POST['system']!='none') {$sql .= " AND rec.system_id='".$_POST['system']."'"; }
      $sql .= " AND tx.animal_id='".$_POST['animal']."'";
      $sql .= " AND tx.biopotential_id='".$_POST['biopotential']."'";
      $sql .= " AND tx.channels_id='".$_POST['channels']."'";
      $sql .= " AND x.enable=1 ORDER BY x.description ASC";
      break;
    case 'system':
    default:
      $sql="SELECT id, description, preselect FROM epoch_$table WHERE enable=1 ORDER BY description ASC";
      break;
  }
  //echo $sql;
  return $sql;
}

function createDropDown($db, $label, $select, $table, $active, $hint) {
  // Create a label or not
  if (!is_null($label)) {
    echo "<br />$label: ";
  }
  // Open Select Tag
  echo "<select name=\"$select\" onchange=\"document.getElementById('currentDropDown').value='$select';\"";
  if (!$active) { echo " disabled"; }
  echo ">", PHP_EOL;

  // Generate Select Tag Options
  $sql = generateDropDownSQL($table);
  $query = $db->query($sql);
  while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
     echo '<option value="'.$row['id'].'"';
     if (!$_POST[$select]) {
       ($row['preselect'] == 1) ? $_POST[$select] = $row['id'] : '';
     }
     echo ($row['id'] == $_POST[$select]) ? ' selected' : '';
     echo '>'.$row['description'].'</option>', PHP_EOL;
  }

  // Close Select Tag
  echo '</select>', PHP_EOL;

  // Tooltip
  if (!is_null($hint)) { echo "<div class='tooltip'>[?] <span class='tooltiptext'>$hint</span></div>"; }
}

// Multidimensional array to create dropdowns.
$dropdowns = array
  (
    array('1', 'Existing System', 'system', 'system', "Existing Epoch 2 system biopotentials CANNOT be changed."),
    array('2', 'Animal', 'animal', 'animal', null),
    array('3', 'Biopotential', 'biopotential', 'biopotential', null),
    array('4', 'Channels', 'channels', 'channels', null),
    array('5', 'Duration', 'duration', 'duration', "reusable 2-month transmitters use the plastic-1 base and can be moved from animal to animal")
  );

  // Default Dropdown
  if (!$_POST['currentDropDown']) { $_POST['currentDropDown'] = 'system'; }

  // Enable the next dropdown in the $dropdowns array, unless it is the last one.
  for ($row = 0; $row < sizeof($dropdowns); $row++) {
    if ($dropdowns[$row][2] == $_POST['currentDropDown'] && $_POST['currentDropDown'] != 'duration' && $_POST['system']) {
      $_POST['currentDropDown'] = $dropdowns[$row+1][2];
      break;
    }
  }
  echo "<input type='hidden' id='currentDropDown' name='currentDropDown' value='".$_POST['currentDropDown']."'>", PHP_EOL;
  /*echo "<pre>"; print_r($_POST); echo "</pre>";*/

  // Create dropdowns
  $active = true;
  for ($row = 0; $row < sizeof($dropdowns); $row++) {
    createDropDown($db, $dropdowns[$row][1], $dropdowns[$row][2], $dropdowns[$row][3], $active, $dropdowns[$row][4]);
    // Disable all the select statements after the currentDropDown
    if ($dropdowns[$row][2] == $_POST['currentDropDown']) { $active = false; }
    // Hide inactive dropdowns
    //break;
  }

?>
<br /><input type="reset" name="reset" value="Reset" onclick="document.getElementById('currentDropDown').value='system';location.reload();">
<input type="submit" name="Submit" value="Next">
</form>
</div>

<?php
  if ($_POST['currentDropDown'] == 'duration' && $_POST['duration']) {
 // calculate the form result

 $sql = "SELECT tx.part_number as transmitter_pn, tx.receiver_id as receiver_pn, tx.biopotential_id as biopotential, tx.channels_id as channels, tx.default_gain1_id, tx.default_gain2_id, msg.id as msg_id, msg.description as note";
 $sql .= " FROM epoch_transmitter as tx INNER JOIN epoch_receiver as rec ON tx.receiver_id = rec.id";
 $sql .= " LEFT JOIN epoch_message as msg ON tx.message_id = msg.id";
 $sql .= " WHERE tx.animal_id='".$_POST['animal']."'";
 $sql .= " AND tx.biopotential_id='".$_POST['biopotential']."'";
 $sql .= " AND tx.channels_id='".$_POST['channels']."'";
 $sql .= " AND tx.duration_id='".$_POST['duration']."'";
 if ($_POST['system']!="none") {
   $sql .= " AND rec.system_id='".$_POST['system']."'";
 } else {
   // Allow everything EXCEPT Classic
   $sql .= " AND rec.enable=1";
 }

 $query = $db->query($sql);

 if ($query->rowCount()>0) {
   // Loop through the query results, outputing the options one by one
   $option = 1;
   while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
     if (!empty($row['transmitter_pn'])) {
     // CHEAT: Set Gain Dropdown Defaults for Differential
     if (strlen($row['biopotential'])==7) {
       $_POST["transmitter_gain_1"] = $row['default_gain1_id'];
       $_POST["transmitter_gain_2"] = $row['default_gain2_id'];
     }
     echo PHP_EOL,"<br/>Option #$option: Receiver ".$row['receiver_pn']." and Transmitter ".$row['transmitter_pn'];
     // Create a Transmitter Gain Dropdown for each channel
     for ($i = 1; $i <= $row['channels']; $i++) {
       // Set Gain Dropdown Defaults
       if (strlen($row['biopotential'])==3) {
         $_POST["transmitter_gain_$i"] = $row['default_gain1_id'];
       }
       echo "-";
       createDropDown($db, null, "transmitter_gain_$i",'transmitter_gain', true, null);
     }
     $option++;
     echo "<div class='tooltip'>[?] <span class='tooltiptext'>";
     echo "Gain (peak-to-peak) per channel recommendations:";
     echo "<br/>Adult EEG 2mV± <br/>Pup EEG 1mV± <br/>EMG 5mV± <br/>ECG 2mV±";
     echo "</span></div>";
   } else {
     ?>
       <p>Currently there are no Epoch Receivers / Transmitters for the options you selected.  <?=$row['note'];?></p>
     <?php
     //echo $sql;
     // CHEAT: Avoid multiple not found messages
     break;
   }
  }
} else {
?>
  <p>Currently there are no Epoch Receivers / Transmitters for the options you selected.</p>

<?php
  }
} ?>

<p><img src="flowchart.png"></p>
</body>
</html>
