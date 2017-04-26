<?php

/**
 * QuoteItem
 */
class QuoteItem {
  public $name;
  public $qty;
  public $biopac_pn;
  public $epitel_pn;
  public $notes;
  public $url;

  function __construct($name, $qty, $biopac_pn, $epitel_pn, $notes, $url) {
    $this->name = $name;
    $this->qty = $qty;
    $this->biopac_pn = $biopac_pn;
    $this->epitel_pn = $epitel_pn;
    $this->notes = $notes;
    $this->url = $url;
  }

  function getHTML() {
    $html = null;

    $html .= '<div class="divTableRow">';
    $html .= '  <div class="divTableCell">'.$this->qty.'</div>';
    $html .= '  <div class="divTableCell">'.$this->name.'</div>';
    if (!empty($this->url)) {
      $html .= '  <div class="divTableCell"><a href="'.$this->url.'">'.$this->biopac_pn.'</a></div>';
    } else {
      $html .= '  <div class="divTableCell">'.$this->biopac_pn.'</div>';
    }
    $html .= '  <div class="divTableCell">'.$this->epitel_pn.'</div>';
    $html .= '  <div class="divTableCell">'.$this->notes.'</div>';
    $html .= '</div>';

    return $html;
  }
}
?>
