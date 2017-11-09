<?php

/**
 * Class Quote
 */
class Quote {
  public $daq;
  public $receiver;
  public $transmitter;
  public $cable;
  public $activator;

  function __construct($daq, $receiver, $transmitter, $cable, $activator) {
    $this->daq = $daq;
    $this->receiver = $receiver;
    $this->transmitter = $transmitter;
    $this->cable = $cable;
    $this->activator = $activator;
  }

  function getHTML() {
    $html = null;

    $html .= '<div class="divTable" style="width: 100%;" >';
    $html .= '  <div class="divTableBody">';
    if ($this->daq->name!=null) {$html .= '    '.$this->daq->getHTML();}
    if ($this->receiver->name!=null) {$html .= '    '.$this->receiver->getHTML();}
    $html .= '    '.$this->transmitter->getHTML();
    if ($this->cable->name!=null) {$html .= '    '.$this->cable->getHTML();}
    if ($this->activator->name!=null) {$html .= '    '.$this->activator->getHTML();}
    $html .= '  </div>';
    $html .= '</div>';

    return $html;
  }

}
?>
