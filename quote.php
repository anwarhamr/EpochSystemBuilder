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

  function __construct($daq, $receiver, $transmitter, $cables, $activator) {
    $this->daq = $daq;
    $this->receiver = $receiver;
    $this->transmitter = $transmitter;
    $this->cables = $cables;
    $this->activator = $activator;
  }

  function getHTML() {
    $html = null;

    $html .= '<h3>Recommended EPOCH System results</h3>';
    $html .= '<div class="divTable">';
    $html .= '  <div class="divTableBody">';
    $html .= '    <div class="divTableRow">';
    $html .= '      <div class="divTableCell"><strong>EPOCH System Component</strong></div>';
    $html .= '      <div class="divTableCell"><strong>BIOPAC Part #</strong></div>';
    $html .= '      <div class="divTableCell"><strong>Epitel Part #</strong></div>';
    $html .= '      <div class="divTableCell"><strong>Notes</strong></div>';
    $html .= '      <div class="divTableCell"><strong>Qty</strong></div>';
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
