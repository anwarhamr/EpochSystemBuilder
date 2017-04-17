
<?php
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

function createDropDown($db, $label, $select, $table, $active, $tooltip) {
  // Open Select Tag
  echo "<br /><select name=\"$select\" onchange=\"document.getElementById('currentDropDown').value='$select';document.getElementById('createSystem').submit();\"";
  if (!$active) { echo " disabled"; }
  echo ">", PHP_EOL;

  // Generate Select Tag Options
  $sql = generateDropDownSQL($table);
  $query = $db->query($sql);
  echo "<option value=''>$label</option>";
  while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
     echo '<option value="'.$row['id'].'"';
     echo ($row['id'] == $_POST[$select]) ? ' selected' : '';
     echo '>'.$row['description'].'</option>', PHP_EOL;
  }

  // Close Select Tag
  echo '</select>', PHP_EOL;

  // Tooltip
  if (!is_null($tooltip)) { echo "<div class='tooltip'>[?] <span class='tooltiptext'>$tooltip</span></div>"; }
}

function getDefaultGain($biopotential, $animal) {
  // Adult EEG 2mV±
  // Pup EEG 1mV±
  // EMG 5mV±
  // ECG 2mV±
  $default_gain = 0;

  if (strpos($animal, 'pup') == false) {
    // adult
    switch ($biopotential) {
      case 'emg':
        $default_gain = 5;
        break;
      case 'ecg':
      case 'eeg':
      default:
        $default_gain = 2;
        break;
    }
  } else {
    // pup
    $default_gain = 1;
  }

  return $default_gain;
}

function createGainDropdowns($db, $active) {
  // Set Default Differential Gains
  $biopotentials = explode("-", $_POST['biopotential']);
  for ($i = 1; $i <= sizeof($biopotentials); $i++) {
    if (!$_POST["transmitter_gain_$i"]) {
      $_POST["transmitter_gain_$i"] = getDefaultGain($biopotentials[$i-1], $_POST['animal']);
    }
  }

  // Gain Tooltip
  $tooltip = "Gain (peak-to-peak) per channel recommendations:";
  $tooltip .= "<br/>Adult EEG 2mV± <br/>Pup EEG 1mV± <br/>EMG 5mV± <br/>ECG 2mV±";

  // Create a Transmitter Gain Dropdown for each channel
  for ($i = 1; $i <= $_POST['channels']; $i++) {
    // Set Default Common Gains
    if (strlen($_POST['biopotential'])==3) {
      if (!$_POST["transmitter_gain_$i"]) {
        $_POST["transmitter_gain_$i"] = getDefaultGain($_POST['biopotential'], $_POST['animal']);
      }
    }

    createDropDown($db, "Channel $i Gain", "transmitter_gain_$i", 'transmitter_gain', $active, $tooltip);
  }

}

function getGainCombinationKey($db) {
  $gain_desc = "";
  for ($i = 1; $i <= 6; $i++) {
    if ($_POST["transmitter_gain_$i"]) {
      $gain_desc .= "-".sprintf("%02d", $_POST["transmitter_gain_$i"]);
    } else {
      $gain_desc .= "-00";
    }
  }
  $sql = "SELECT id from epoch_gains WHERE description='$gain_desc'";

  $query = $db->query($sql);

  if ($query->rowCount()>0) {
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      return $row['id'];
    }
  }
  return "ERROR";
}

function getGainCombinationValue($db, $id) {
  $sql = "SELECT description from epoch_gains WHERE id='$id'";
  $query = $db->query($sql);

  if ($query->rowCount()>0) {
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      return $row['description'];
    }
  }
  echo $sql;
  return "ERROR";
}

function getActivatorMsg() {
  // Activator
  $msg = "";
  if ($_POST['system']!="classic" ) {
    $msg = "<br />You also need Activator <a href='https://www.biopac.com/product/epoch-sensor-activation-utility/'>EPOCH-ACTI</a> (10029).";
  } elseif ($_POST['system']=="classic" && $_POST['duration']=="reusable" ) {
    $msg = "<br />Old activators do not work with reusable transmitters.  You also need Activator <a href='https://www.biopac.com/product/epoch-sensor-activation-utility/'>EPOCH-ACTI</a> (10029).";
  }
  return $msg;
}

function getCableMsg() {
  // BIOPAC cables
  $msg = "";

  switch ($_POST['dac']) {
    case 'mp160':
      $msg = "<br />You need ".$_POST['channels']."x of <a href='https://www.biopac.com/product/interface-cables/?attribute_pa_size=unisolated-rj11-to-bnc-male'>CBL123</a> to connect to the ".$_POST['dac'].".";
      break;
    case 'mp100':
    case 'mp150':
      $msg = "<br />You need ".$_POST['channels']."x of <a href='https://www.biopac.com/product/interface-cables/?attribute_pa_size=cbl-3-5mm-to-bnc-m-2-m'>CBL102</a> to connect to the <a href='https://www.biopac.com/product/mp150-data-acquisition-systems/'>".$_POST['dac']."</a>.";
      break;
  }

  return $msg;
}

function getPartNumbersMsg($db) {
  $msg = "";

  $sql = "SELECT tx.part_number as transmitter_pn, rec.biopac_id as biopac_receiver_pn, tx.receiver_id as receiver_pn, tx.biopotential_id as biopotential, tx.channels_id as channels, tx.default_gain1_id, tx.default_gain2_id, msg.id as msg_id, msg.description as note";
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
       $key = getGainCombinationKey($db);
       $msg .= "<br/>Option #$option: Epoch Receiver Tray ".$row['biopac_receiver_pn']." (".$row['receiver_pn'].")";
       $msg .= " and Epoch Transmitter EPTX".$row['transmitter_pn']."-".sprintf("%05d", $key)." (".$row['transmitter_pn'].getGainCombinationValue($db, $key).")";
       $option++;
     } else {
       if (empty($row['note'])) {
         $msg .= "<p>Currently there are no Epoch Receiver Trays / Transmitters for the options you selected.";
       } else {
         $msg .= $row['note'];
       }
       // CHEAT: Avoid multiple not found messages
       break;
     }
   }
   $msg .= getCableMsg();
   $msg .= getActivatorMsg();
  } else {
    $msg .= "<p>Currently there are no Epoch Receiver Trays / Transmitters for the options you selected.</p>";
  }

  return $msg;
}

function checkDefaultDropdown() {
  // CHEAT: gain dropdowns are not in the $dropdowns array, so consider them channels.
  if (strpos($_POST['currentDropDown'], 'transmitter_gain_') !== false) {
    $_POST['currentDropDown'] = "channels";
  }
}

function resetForm() {
  unset($_POST['currentDropDown']);
  unset($_POST['dac']);
  unset($_POST['system']);
  unset($_POST['animal']);
  unset($_POST['biopotential']);
  unset($_POST['channels']);
  unset($_POST['transmitter_gain_1']);
  unset($_POST['transmitter_gain_2']);
  unset($_POST['transmitter_gain_3']);
  unset($_POST['transmitter_gain_4']);
  unset($_POST['transmitter_gain_5']);
  unset($_POST['transmitter_gain_6']);
  unset($_POST['duration']);
}

function advanceDefaultDropdown($dropdowns) {
  checkDefaultDropdown();
  // Enable the next dropdown in the $dropdowns array, unless it is the last one or blank.
  if (!$_POST['currentDropDown'] || $_POST['currentDropDown']=="") {
    resetForm();
    $_POST['currentDropDown'] = 'dac';
  } else {
    for ($row = 0; $row < sizeof($dropdowns); $row++) {
      if ($dropdowns[$row][2] == $_POST['currentDropDown'] && $_POST['currentDropDown'] != 'duration' && $_POST['dac']) {
        $_POST['currentDropDown'] = $dropdowns[$row+1][2];
        break;
      }
    }
  }
  return "<input type='hidden' id='currentDropDown' name='currentDropDown' value='".$_POST['currentDropDown']."'>";
}

function showPOST() {
  echo "<pre>"; print_r($_POST); echo "</pre>";
}
?>

<html>
<head>
  <title>Epoch System Builder</title>
  <link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
  <div>
<form action="<?=$_SERVER['PHP_SELF'];?>" method="post" name="createSystem" id="createSystem">

<?php
// For security place, config.ini outsite of browseable files and change the path
$config = parse_ini_file('../config.ini');

// Database Connection
$db = new \PDO(   "mysql:host=".$config['servername'].";dbname=".$config['database'].";charset=utf8",
                        $config['username'],
                        $config['password'],
                        array(
                            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                            \PDO::ATTR_PERSISTENT => false
                        )
                    );

// Multidimensional array to create dropdowns.
$dropdowns = array
  (
    array('1', 'Select Existing Data Acquisition System', 'dac', 'dac', null),
    array('2', 'Select Existing Epoch Receiver Tray', 'system', 'system', "Selected Epoch 2 system biopotentials CANNOT be changed."),
    array('3', 'Select Animal', 'animal', 'animal', null),
    array('4', 'Select Biopotential', 'biopotential', 'biopotential', "'Differential' reference electrode layout uses different grounds as opposed to a 'Common' reference electrode layout which uses a common ground."),
    array('5', 'Select Channels', 'channels', 'channels', null),
    array('6', 'Select Duration', 'duration', 'duration', "reusable 2-month transmitters use the <a href='http://www.plastics1.com/Gallery-PRC.php?FILTER_CLEAR&FILTER_FCATEGORY=Electrophysiology%20&FILTER_F1=Electrode%20&FILTER_F3=3%20channel'>Plastics1 MSS33</a> base and can be moved from animal to animal")
  );

echo advanceDefaultDropdown($dropdowns); // write hidden tag

// Create dropdowns
$active = true;
for ($row = 0; $row < sizeof($dropdowns); $row++) {
  createDropDown($db, $dropdowns[$row][1], $dropdowns[$row][2], $dropdowns[$row][3], $active, $dropdowns[$row][4]);
  if ($dropdowns[$row][2] == "channels") { createGainDropdowns($db, $active); } // Once channels have been selected, show the Gain Options
  if ($dropdowns[$row][2] == $_POST['currentDropDown']) {
    $active = false; // Disable all the select statements after the currentDropDown
    //break; // Hide inactive dropdowns
  }
}

?>
<script>
function reloadForm() {
  // location.reload();
  document.getElementById("createSystem").submit();
}
</script>
<br /><input type="reset" name="reset" value="Reset" onclick="document.getElementById('currentDropDown').value='';document.getElementById('createSystem').submit();">
</form>
</div>

<?php if ($_POST['currentDropDown'] == 'duration' && $_POST['duration']) { echo getPartNumbersMsg($db); } ?>

<p><img src="https://www.biopac.com/wp-content/uploads/EPOCH-BIOPAC-System-1024x551.jpg"></p>
</body>
</html>
