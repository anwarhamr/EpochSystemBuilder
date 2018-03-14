<?php

/**
 * Class Quote
 */
class Quote {
  public $daq;
  public $receiver;
  public $transmitter;
  public $cables;
  public $activator;
  public $locale;

  function __construct($daq, $receiver, $transmitter, $cables, $activator, $locale) {
    $this->daq = $daq;
    $this->receiver = $receiver;
    $this->transmitter = $transmitter;
    $this->cables = $cables;
    $this->activator = $activator;
    $this->locale = $locale;
  }

  function getHTML() {
    $html = null;

    $html .= '<div class="divTable">';
    $html .= '  <div class="divTableBody">';
    $html .= '    <div class="divTableRow">';
    $html .= '      <div class="divTableCell"><strong>'.$this->locale["EPOCH_COL_HEADER"].'</strong></div>';
    $html .= '      <div class="divTableCell"><strong>'.$this->locale["BIOPAC_PART_COL_HEADER"].'</strong></div>';
    $html .= '      <div class="divTableCell"><strong>'.$this->locale["EPITEL_PART_COL_HEADER"].'</strong></div>';
    $html .= '      <div class="divTableCell"><strong>'.$this->locale["NOTES_COL_HEADER"].'</strong></div>';
    $html .= '      <div class="divTableCell"><strong>'.$this->locale["QTY_COL_HEADER"].'</strong></div>';
    $html .= '    </div>';

    if ($this->daq->name!=null) {$html .= '    '.$this->daq->getHTML();}
    if ($this->receiver->name!=null) {$html .= '    '.$this->receiver->getHTML();}
    $html .= '    '.$this->transmitter->getHTML();
    foreach ($this->cables as $cable) {
      if ($cable->name!=null) {$html .= '    '.$cable->getHTML();}
    }
    if ($this->activator->name!=null) {$html .= '    '.$this->activator->getHTML();}
    $html .= '  </div>';
    $html .= '</div>';

    return $html;
  }

}
?>
