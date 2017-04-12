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
$config = parse_ini_file('../config.ini');

$db = new \PDO(   "mysql:host=".$config['servername'].";dbname=".$config['database'].";charset=utf8mb4",
                        $config['username'],
                        $config['password'],
                        array(
                            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                            \PDO::ATTR_PERSISTENT => false
                        )
                    );

function check2WeekAdult() {
  // CHEAT: We know that there is no combination of 2-week and Adult animals.
  return ($_POST['duration']=='2-week' && ($_POST['animal']=='adult-rat' || $_POST['animal']=='adult-mouse'));
}

function existingSystemMsg() {
  // CHEAT: If we do it in the code here, we can reduce the number of database records.
  if (check2WeekAdult()) {
    $msg = "Select a 'Pup' animal for '2 weeks or less' or select a greater duration.";
  } else {
    $msg = "Currently there are no Epoch Receivers / Transmitters for the options you selected.";
  }

  switch ($_POST['system']) {
    case 'epoch2-100-100':
      $msg = "'Epoch 2 (100/100)' systems support 'Adult' animals with '2-Ch' of 'EEG' or 'EEG/EEG (Differential)' for more than 2 weeks.";
      break;
    case 'epoch2-100-200':
      $msg = "'Epoch 2 (100/200)' systems support 'Adult' animals with '2-Ch' of 'EEG/EMG (Differential)' or 'EEG/ECG (Differential)' for more than 2 weeks.";
      break;
    case 'epoch2-200-200':
      $msg = "'Epoch 2 (200/200)' systems support 'Adult' animals with '1-Ch' or '2-Ch' of 'ECG', 'EMG', 'ECG/EMG (Differential)' or 'EMG/EMG (Differential)' for more than 2 weeks.";
      break;
    case 'epoch6':
      $msg = "'Epoch 6' systems support 'Adult' animals with '2-Ch', '4-Ch' or '6-Ch' of 'EEG' for more than 2 weeks.";
      break;
    case 'pup':
      $msg = "'Pup' systems only support 'Pup' animals with '2-Ch' of 'EEG' for 2 months or less.";
      break;
    case 'classic':
      $msg = "'Classic' systems [sold before 2017] support 'Adult' animals with '2-Ch' of 'EEG' for more than 2 weeks.";
      break;
    }

    return $msg;
}

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
createDropDown($db, 'Biopotential', 'biopotential', 'biopotential', "'Differential' reference electrode layout uses different grounds as opposed to a 'Common' reference electrode layout which uses a common ground.");
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
     } elseif (!empty($row['note'])) {
       // Show the message from the database if there is one
       echo "<p>".$row['note']."</p>";
     } else {
       // Otherwise show a generic default
       echo "<p>".existingSystemMsg()."</p>", PHP_EOL;
     }
     // CHEAT: Avoid multiple not found messages
     break;
  }
} else {
  echo "<p>".existingSystemMsg()."</p>", PHP_EOL;
}
?>
<pre>
<?php //print_r($_POST); ?>
</pre>
<p><img src="flowchart.png"></p>
</body>
</html>
