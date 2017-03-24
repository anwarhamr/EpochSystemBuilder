<html>
<head>
  <title>Epoch System</title>
  <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
  <div>
<form action="<?=$_SERVER['PHP_SELF'];?>" method="post" name="createSystem">
<?php
// For security, place config.ini outsite of browseable files and change the path
// ex: $config = parse_ini_file('../../config.ini');
$config = parse_ini_file('./config.ini');

$db = new \PDO(   "mysql:host=".$config['servername'].";dbname=".$config['database'].";charset=utf8mb4",
                        $config['username'],
                        $config['password'],
                        array(
                            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                            \PDO::ATTR_PERSISTENT => false
                        )
                    );

function createDropDown($db, $label, $select, $table, $hint) {
  $query = $db->query("SELECT id, description, preselect FROM epoch_$table WHERE enable=1 ORDER BY description ASC"); // Run your query
  if (!is_null($label)) {
    echo "<br />$label: ";
  }
  echo "<select name='$select' onchange='this.form.submit()'>", PHP_EOL; // Open your drop down box

  // Loop through the query results, outputing the options one by one
  while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
     echo '<option value="'.$row['id'].'"';
     if (!$_POST[$select]) {
       ($row['preselect'] == 1) ? $_POST[$select] = $row['id'] : '';
     }
     echo ($row['id'] == $_POST[$select]) ? ' selected' : '';
     echo '>'.$row['description'].'</option>';
  }

  echo '</select>';
  if (!is_null($hint)) { echo "<div class='tooltip'>[?] <span class='tooltiptext'>$hint</span></div>"; }
}

createDropDown($db, 'Existing System', 'system', 'system', "Existing Epoch 2 system biopotentials CANNOT be changed.");
createDropDown($db, 'Animal', 'animal', 'animal', null);
createDropDown($db, 'Biopotential', 'biopotential', 'biopotential', null);
createDropDown($db, 'Channels', 'channels', 'channels', null);
createDropDown($db, 'Duration', 'duration', 'duration', "reusable 2-month transmitters use the plastic-1 base and can be moved from animal to animal");
?>

</form>
</div>

<?php

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
       createDropDown($db, null, "transmitter_gain_$i", 'transmitter_gain', null);
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

<?php } ?>
<pre>
<?php //print_r($_POST); ?>
</pre>
<p><img src="flowchart.png"></p>
</body>
</html>
