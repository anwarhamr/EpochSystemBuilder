<?php
require_once('utils.php');
// For security place, config.ini outsite of browseable files and change the path
$config_file = '../../config.ini';
$open_config_file = @file($config_file) or
        die ("Failed opening config file: $php_errormsg");
$config = parse_ini_file($config_file);
$prefix = $config['prefix'];

// Database Connection
try {
  $db = new \PDO(   "mysql:host=".$config['servername'].";port=".$config['port'].";dbname=".$config['database'].";charset=utf8",
                        $config['username'],
                        $config['password'],
                        array(
                            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                            \PDO::ATTR_PERSISTENT => false
                        )
                    );
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
  exit;
}
echo  '<?xml version="1.0" encoding="utf-8"?>';
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Epoch System Builder</title>
  <link href="css/style.css" rel="stylesheet" type="text/css">
  <link href="css/normalize.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <meta charset="UTF-8">
  <meta name="viewport" content="user-scalable=yes, initial-scale=1.0, maximum-scale=2.0, width=device-width" />
</head>
<body>
  <div>
<form action="<?=$_SERVER['PHP_SELF'];?>" method="post" name="createSystem" id="createSystem">

<?php
// Multidimensional array to create dropdowns.
$dropdowns = array
  (
    array('1', 'Select Data Acquisition System', 'dac', $prefix.'dac', null),
    array('2', 'Select Epoch Receiver Tray', 'system', $prefix.'system', 'Serial numbers (S/N) are either located on the side next to the power jack or on the bottom of the receiver. Rat receivers are 16.5"x8.5", Mouse receivers are 13"x8", and Pup receivers are 7"x7"'),
    array('3', 'Select Animal', 'animal', $prefix.'animal', null),
    array('4', 'Select Biopotential', 'biopotential', $prefix.'biopotential', "'Differential' reference electrode layout uses different grounds as opposed to a 'Common' reference electrode layout which uses a common ground."),
    array('5', 'Select Channels', 'channels', $prefix.'channels', null),
    array('6', 'Select Duration', 'duration', $prefix.'duration', "reusable 2-month transmitters use the <a href='http://www.plastics1.com/Gallery-PRC.php?FILTER_CLEAR&FILTER_FCATEGORY=Electrophysiology%20&FILTER_F1=Electrode%20&FILTER_F3=3%20channel' target='_new'>Plastics1 MSS33</a> base and can be moved from animal to animal")
  );

showDropDowns($db, $prefix, $dropdowns);
echo getHiddenCurrentDropDown($dropdowns); // write hidden tag

?>
<br /><input type="reset" name="reset" value="Reset" onclick="document.getElementById('currentDropDown').value='';document.getElementById('createSystem').submit();">
</form>
</div>

<?php showQuotes($db, $prefix); ?>

<section  class="section-images">
<img src="https://www.biopac.com/wp-content/uploads/EPOCH-BIOPAC-System-1024x551.jpg">
<a href="https://www.epitelinc.com/s/Epoch-Product-Catalog.pdf"><i class="material-icons">file_download</i></a>
<a href="https://github.com/jonfen/EpochSystemBuilder"><img src="images/GitHub-Mark-Light-120px-plus.png" style="filter: invert(100%); width: 32px;"></a>
</section>

</body>
</html>
