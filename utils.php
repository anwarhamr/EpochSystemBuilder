<?php
require_once('quoteitem.php');
require_once('quote.php');
/**
 * generateDropDownSQL($table, $prefix)
 */
function generateDropDownSQL($table, $prefix) {
  $table_name = str_replace($prefix,"",$table);
  switch ($table) {
    case $prefix.'animal':
      $sql = "SELECT DISTINCT x.id, x.description, x.preselect";
      $sql .= " FROM ".$prefix."transmitter as tx INNER JOIN ".$prefix."receiver as rec ON tx.receiver_id = rec.id";
      $sql .= " INNER JOIN $table as x ON tx.".$table_name."_id=x.id WHERE tx.part_number!=''";
      if ($_POST['system']!='none') {$sql .= " AND rec.system_id='".$_POST['system']."'"; }
      $sql .= " AND x.enable=1 ORDER BY x.description ASC";
      break;
    case $prefix.'biopotential':
      $sql = "SELECT DISTINCT x.id, x.description, x.preselect";
      $sql .= " FROM ".$prefix."transmitter as tx INNER JOIN ".$prefix."receiver as rec ON tx.receiver_id = rec.id";
      $sql .= " INNER JOIN $table as x ON tx.".$table_name."_id=x.id WHERE tx.part_number!=''";
      if ($_POST['system']!='none') {$sql .= " AND rec.system_id='".$_POST['system']."'"; }
      $sql .= " AND tx.animal_id='".$_POST['animal']."'";
      $sql .= " AND x.enable=1 ORDER BY x.description ASC";
      break;
    case $prefix.'channels':
      $sql = "SELECT DISTINCT x.id, x.description, x.preselect";
      $sql .= " FROM ".$prefix."transmitter as tx INNER JOIN ".$prefix."receiver as rec ON tx.receiver_id = rec.id";
      $sql .= " INNER JOIN $table as x ON tx.".$table_name."_id=x.id WHERE tx.part_number!=''";
      if ($_POST['system']!='none') {$sql .= " AND rec.system_id='".$_POST['system']."'"; }
      $sql .= " AND tx.animal_id='".$_POST['animal']."'";
      $sql .= " AND tx.biopotential_id='".$_POST['biopotential']."'";
      $sql .= " AND x.enable=1 ORDER BY x.description ASC";
      break;
    case $prefix.'duration':
      $sql = "SELECT DISTINCT x.id, x.description, x.preselect";
      $sql .= " FROM ".$prefix."transmitter as tx INNER JOIN ".$prefix."receiver as rec ON tx.receiver_id = rec.id";
      $sql .= " INNER JOIN $table as x ON tx.".$table_name."_id=x.id WHERE tx.part_number!=''";
      if ($_POST['system']!='none') {$sql .= " AND rec.system_id='".$_POST['system']."'"; }
      $sql .= " AND tx.animal_id='".$_POST['animal']."'";
      $sql .= " AND tx.biopotential_id='".$_POST['biopotential']."'";
      $sql .= " AND tx.channels_id='".$_POST['channels']."'";
      $sql .= " AND x.enable=1 ORDER BY x.description ASC";
      break;
    case $prefix.'dac':
    $sql="SELECT id, description, preselect FROM $table WHERE enable=1 ORDER BY description DESC";
      break;
    case $prefix.'system':
    default:
      $sql="SELECT id, description, preselect FROM $table WHERE enable=1 ORDER BY description ASC";
      break;
  }

  return $sql;
}

/**
 * createDropDown($db, $label, $select, $table, $prefix, $active, $tooltip, $none)
 */
function createDropDown($db, $label, $select, $table, $prefix, $active, $tooltip, $none) {
  // Open Select Tag
  echo "<br /><select name=\"$select\" onchange=\"document.getElementById('currentDropDown').value='$select';document.getElementById('createSystem').submit();\"";
  if (!$active) { echo " disabled"; }
  echo ">", PHP_EOL;

  // Generate Select Tag Options
  $sql = generateDropDownSQL($table, $prefix);
  $query = $db->query($sql);
  echo "<option value=''>$label</option>";
  while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
     echo '<option value="'.$row['id'].'"';
     echo ($row['id'] == $_POST[$select]) ? ' selected' : '';
     echo '>'.$row['description'].'</option>', PHP_EOL;
  }
  if ($none) {
    echo "<option value='none'";
    echo ($_POST[$select] == 'none') ? ' selected' : '';
    echo ">None</option>";
  }

  // Close Select Tag
  echo '</select>', PHP_EOL;

  // Tooltip
  if (!is_null($tooltip)) { echo "<div class='tooltip'>[?] <span class='tooltiptext'>$tooltip</span></div>"; }
}

/**
 * showDropDowns($db, $prefix, $dropdowns)
 */
function showDropDowns($db, $prefix, $dropdowns) {
  // Create dropdowns
  $active = true;
  for ($row = 0; $row < sizeof($dropdowns); $row++) {
    // Allow "None" dropdown option for dac and system
    if ($dropdowns[$row][2] == "dac" || $dropdowns[$row][2] == "system") { $none = true; } else { $none = false; }

    if (!$active) { unset($_POST[$dropdowns[$row][2]]); }
    createDropDown($db, $dropdowns[$row][1], $dropdowns[$row][2], $dropdowns[$row][3], $prefix, $active, $dropdowns[$row][4], $none);
    if ($dropdowns[$row][2] == "channels") { createGainDropdowns($db, $prefix, $active); } // Once channels have been selected, show the Gain Options
    if ($dropdowns[$row][2] == $_POST['currentDropDown']) {
      $active = false; // Disable all the select statements after the currentDropDown
      //break; // Hide inactive dropdowns
    }
  }
}

/**
 * showQuotes($db, $prefix)
 */
function showQuotes($db, $prefix) {
  if ($_POST['currentDropDown'] == 'duration' && $_POST['duration']) {
    $quotes = getQuotes($db, $prefix);
    foreach ($quotes as $quote) {
      echo '<br /><br />';
      echo $quote->getHTML();
    }
  }
}


/**
 * getDefaultGain($biopotential, $animal)
 */
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

/**
 * createGainDropdowns($db, $prefix, $active)
 */
function createGainDropdowns($db, $prefix, $active) {
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

    createDropDown($db, "Channel $i Gain", "transmitter_gain_$i", $prefix.'transmitter_gain', $prefix, $active, $tooltip, false);
  }

}

/**
 * getGainCombinationKey($db, $prefix)
 */
function getGainCombinationKey($db, $prefix) {
  $gain_desc = "";
  for ($i = 1; $i <= 6; $i++) {
    if ($_POST["transmitter_gain_$i"]) {
      $gain_desc .= "-".sprintf("%02d", $_POST["transmitter_gain_$i"]);
    } else {
      $gain_desc .= "-00";
    }
  }
  $sql = "SELECT id from ".$prefix."gains WHERE description='$gain_desc'";

  $query = $db->query($sql);

  if ($query->rowCount()>0) {
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      return $row['id'];
    }
  }
  return "ERROR";
}

/**
 * getGainCombinationValue($db, $prefix, $id)
 */
function getGainCombinationValue($db, $prefix, $id) {
  $sql = "SELECT description from ".$prefix."gains WHERE id='$id'";
  $query = $db->query($sql);

  if ($query->rowCount()>0) {
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      return $row['description'];
    }
  }
  return "ERROR";
}

/**
 * getDAQ($db)
 */
function getDAQ($db) {
  // BIOPAC DAQ
  $daq = new QuoteItem(null, null, null, null, null, null);

  switch ($_POST['dac']) {
    case 'none':
      $daq = new QuoteItem('DAQ', 1, 'MP160', null, null, 'https://www.biopac.com/product/mp150-data-acquisition-systems/');
      break;
  }

  return $daq;
}

/**
 * getActivator()
 */
function getActivator() {
  // Activator
  $activator = new QuoteItem(null, null, null, null, null, null);
  if ($_POST['system']!="classic" ) {
    $activator = new QuoteItem('Epoch Transmitter Activator', 1, 'EPOCH-ACTI', '10029', null, 'https://www.biopac.com/product/epoch-sensor-activation-utility/');
  } elseif ($_POST['system']=="classic" && $_POST['duration']=="reusable" ) {
    $activator = new QuoteItem('Epoch Transmitter Activator', 1, 'EPOCH-ACTI', '10029', 'Old activators do not work with reusable transmitters.', 'https://www.biopac.com/product/epoch-sensor-activation-utility/');
  }
  return $activator;
}

/**
 * getCable()
 */
function getCable() {
  // BIOPAC cables
  $cable = new QuoteItem(null, null, null, null, null, null);

  switch ($_POST['dac']) {
    case 'none':
    case 'mp160':
      $cable = new QuoteItem('BIOPAC Cable', $_POST['channels'], 'CBL123', null, 'One per channel.', 'https://www.biopac.com/product/interface-cables/?attribute_pa_size=unisolated-rj11-to-bnc-male');
      break;
    case 'mp100':
    case 'mp150':
      $cable = new QuoteItem('BIOPAC Cable', $_POST['channels'], 'CBL102', null, 'One per channel.', 'https://www.biopac.com/product/interface-cables/?attribute_pa_size=cbl-3-5mm-to-bnc-m-2-m');
      break;
  }

  return $cable;
}

/**
 * getDescription($db, $id, $table, $prefix)
 */
function getDescription($db, $id, $table, $prefix) {
  $description = null;
  $sql = "SELECT description from $prefix.$table where id='$id'";
  $query = $db->query($sql);
  if ($query->rowCount()>0) {
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $description = $row['description'];
      break;
    }
  }
  return $description;
}

/**
 * getQuotes($db, $prefix)
 */
function getQuotes($db, $prefix) {
  $quote = [];

  $sql = "SELECT tx.part_number as transmitter_pn, rec.biopac_id as biopac_receiver_pn, tx.receiver_id as receiver_pn, tx.biopotential_id as biopotential, tx.channels_id as channels";
  $sql .= " FROM ".$prefix."transmitter as tx INNER JOIN ".$prefix."receiver as rec ON tx.receiver_id = rec.id";
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
       $key = getGainCombinationKey($db, $prefix);
       $daq = getDAQ($db);
       if ($_POST['system']=="none") {
         $receiver = new QuoteItem('Epoch Receiver Tray', 1, $row['biopac_receiver_pn'], $row['receiver_pn'], null, null);
         if ($_POST['duration'] == "reusable" ) {
           $transmitter = new QuoteItem('Epoch Transmitter Sensor', 1, "EPTX".$row['transmitter_pn']."-".sprintf("%05d", $key), $row['transmitter_pn'].getGainCombinationValue($db, $prefix, $key), "1 complimentary reusable transmitter is included with this receiver.", null);
         } else {
           $transmitter = new QuoteItem('Epoch Transmitter Sensor', 2, "EPTX".$row['transmitter_pn']."-".sprintf("%05d", $key), $row['transmitter_pn'].getGainCombinationValue($db, $prefix, $key), "2 complimentary transmitters are included with this receiver.", null);
         }
       } else {
         $receiver = new QuoteItem('Epoch Receiver Tray', 0, $row['biopac_receiver_pn'], $row['receiver_pn'], null, null);
         $transmitter = new QuoteItem('Epoch Transmitter Sensor', 1, "EPTX".$row['transmitter_pn']."-".sprintf("%05d", $key), $row['transmitter_pn'].getGainCombinationValue($db, $prefix, $key), null, null);
       }
       $cable = getCable();
       $activator = getActivator();
       $quote[] = new Quote($daq, $receiver, $transmitter, $cable, $activator);
       $option++;
     }
   }
 }
 return $quote;
}

/**
 * checkDefaultDropdown()
 */
function checkDefaultDropdown() {
  // CHEAT: gain dropdowns are not in the $dropdowns array, so consider them channels.
  if (strpos($_POST['currentDropDown'], 'transmitter_gain_') !== false) {
    $_POST['currentDropDown'] = "channels";
  }
}

/**
 * resetForm()
 */
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

/**
 * getHiddenCurrentDropDown($dropdowns)
 */
function getHiddenCurrentDropDown($dropdowns) {
  checkDefaultDropdown();
  // Enable the next dropdown in the $dropdowns array, unless it is the last one or blank.
  if (!$_POST['currentDropDown'] || $_POST['currentDropDown']=="") {
    resetForm();
    $_POST['currentDropDown'] = 'dac';
  } else {
    for ($row = 0; $row < sizeof($dropdowns); $row++) {
      if ($dropdowns[$row][2] == $_POST['currentDropDown'] && $_POST['currentDropDown'] != 'duration' && $_POST['dac']) {
        unset($_POST[$dropdowns[$row+1][2]]);
        $_POST['currentDropDown'] = $dropdowns[$row+1][2];
        break;
      }
    }
  }
  return "<input type='hidden' id='currentDropDown' name='currentDropDown' value='".$_POST['currentDropDown']."'>";
}

/**
 * showPOST
 */
function showPOST() {
  echo "<pre>"; print_r($_POST); echo "</pre>";
}
 ?>
