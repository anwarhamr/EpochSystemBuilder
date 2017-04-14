<?php

function generateGainCombinationSQL() {
  $gains = array("00", "01", "02", "05", "10");
  $increment = 1;
  $dash = "-";

  $sql = "INSERT INTO epoch_gains (id, description, preselect, enable) VALUES ('$increment', '-00-00-00-00-00-00', 0, 1)";
  $increment++;

  $b=$c=$d=$e=$f=0; // First Pass needs to start at 00
  $a = 1; // First Pass needs to start at 01
  for (; $f <= 4; $f++) {
    for (; $e <= 4; $e++) {
      for (; $d <= 4; $d++) {
        for (; $c <= 4; $c++) {
          for (; $b <= 4; $b++) {
            for (; $a <= 4; $a++) {
              $code = $dash.$gains[$a].$dash.$gains[$b].$dash.$gains[$c].$dash.$gains[$d].$dash.$gains[$e].$dash.$gains[$f];
              $sql .= ", ('$increment', '$code', 0, 1)";
              $increment++;
            }
            $a = 1; // Second Pass needs to start at 01
          }
          $b = 1; // Second Pass needs to start at 01
        }
        $c = 1; // Second Pass needs to start at 01
      }
      $d = 1; // Second Pass needs to start at 01
    }
    $e = 1; // Second Pass needs to start at 01
  }
  $sql .= ";";

  return $sql;
}

header('Content-type: application/sql');
header('Content-Disposition: filename="insert_gains.sql"');
echo generateGainCombinationSQL();

?>
