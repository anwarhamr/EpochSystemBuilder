<?php
require_once('utils.php');

// For security place, config.ini outsite of browseable files and change the path
$config_file = '../../config.ini';
$locale_file = './locale/en_US.ini';

$open_config_file = @file($config_file) or die ("Failed opening config file: $php_errormsg");
$config = parse_ini_file($config_file);
$prefix = $config['prefix'];

$open_locale_file = @file($locale_file) or die ("Failed opening locale file: $php_errormsg");
$locale = parse_ini_file($locale_file);

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
  <title><?=$locale['TITLE'];?></title>
  <link href="css/style.css" rel="stylesheet" type="text/css">
  <link href="css/normalize.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <meta charset="UTF-8">
  <meta name="viewport" content="user-scalable=yes, initial-scale=1.0, maximum-scale=2.0, width=device-width" />
</head>
<body>
  <div class="wrapper">
  <div>
    <form action="<?=$_SERVER['PHP_SELF'];?>" method="post" name="createSystem" id="createSystem">

<?php
// Multidimensional array to create dropdowns.
$dropdowns = array
  (
    array("1", $locale["SELECT_DAQ"], "dac", $prefix."dac", $locale["SELECT_DAQ_TOOLTIP"]),
    array("2", $locale["SELECT_EPOCH_TRAY"], "system", $prefix."system", $locale["SELECT_EPOCH_TRAY_TOOLTIP"]),
    array("3", $locale["SELECT_ANIMAL"], "animal", $prefix."animal", $locale["SELECT_ANIMAL_TOOLTIP"]),
    array("4", $locale["SELECT_BIOPOTENTIAL"], "biopotential", $prefix."biopotential", $locale["SELECT_BIOPOTENTIAL_TOOLTIP"]),
    array("5", $locale["SELECT_CHANNELS"], "channels", $prefix."channels", $locale["SELECT_CHANNELS_TOOLTIP"]),
    array("6", $locale["SELECT_DURATION"], "duration", $prefix."duration", $locale["SELECT_DURATION_TOOLTIP"])
  );

showDropDowns($db, $prefix, $dropdowns);
echo getHiddenCurrentDropDown($dropdowns); // write hidden tag

?>
<br /><input type="reset" name="reset" value="<?=$locale["RESET_BUTTON"];?>" onclick="document.getElementById('currentDropDown').value='';document.getElementById('createSystem').submit();">
</form>
</div>

<?php showQuotes($db, $prefix); ?>
</div>
<section  class="section-images">
<a href="https://github.com/jonfen/EpochSystemBuilder"><img src="images/GitHub-Mark-Light-120px-plus.png" style="filter: invert(100%); width: 32px;"></a>
</section>

</body>
</html>
