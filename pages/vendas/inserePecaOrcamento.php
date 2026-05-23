<?php 
// $debug = true;
if($debug) varDump2("Entrou no INSERE PECA");


$PECA_SELECIONADA = array();
if (isset($_SESSION['CONSULTA'])) {
  foreach ($_SESSION['CONSULTA'] as $key => $value) {
    if (isset($_POST['key'])) {
      foreach ($_POST['key'] as $key2 => $value2) {
        if ($key == $key2) {
          array_push($PECA_SELECIONADA, $value);
        }
      }
    }
  }
}
if($debug) varDump2($PECA_SELECIONADA);


foreach ($PECA_SELECIONADA as $keyPeca => $valuePeca) {
  // varDump2($valuePeca);
  if (validaItemOrcamento($valuePeca)) {
    insereModal('danger', "A peça ".$valuePeca['CODPECA']." - ".$valuePeca['MARCA']." já existe no orçamento!");
  } else {

    if (in_array($valuePeca['ORIGEM'], ['NPR', 'VIDE', 'VIDE1', 'VIDE2']) ) {
      $valuePeca['NUMORIGINAL'] = $valuePeca['VIDE'];
    }

    $valuePeca['CODPECA']    = (!is_null($valuePeca['CODPECA']))?str_replace("'", " ", trim($valuePeca['CODPECA'])):"";
    $valuePeca['DESCRICAO']  = (!is_null($valuePeca['DESCRICAO']))?str_replace("'", " ", trim($valuePeca['DESCRICAO'])):"";
    $valuePeca['MARCA']      = (!is_null($valuePeca['MARCA']))?str_replace("'", " ", trim($valuePeca['MARCA'])):"";
    $valuePeca['LOCACAO']    = (!is_null($valuePeca['LOCACAO']))?str_replace("'", " ", trim($valuePeca['LOCACAO'])):"";
    // varDump2($valuePeca['LOCACAO']);

    if (intval($valuePeca['QTDISP']) >= intval($valuePeca['QTPEDIDA'])) {
      $valuePeca['DISPONIBILIDADE'] = 'IMEDIATA';
    } else {
      if (intval($valuePeca['QTDISP']) <= 0) {
        $valuePeca['DISPONIBILIDADE'] = 'SOB CONSULTA';
      } else {
        $valuePeca['DISPONIBILIDADE'] = 'IMEDIATA ('.$valuePeca['QTDISP'].')';
      }
    }

    $valuePeca['PVENDA'] = moedaPHP($valuePeca['PVENDA']);
    if ((moedaPHP($valuePeca['PVENDA_301']) > 0)) {
      $valuePeca['PVENDA'] = min(moedaPHP($valuePeca['PVENDA_301']), moedaPHP($valuePeca['PVENDA']));
    } else {
      if ((moedaPHP($valuePeca['PVENDA_561']) > 0)) {
        $valuePeca['PVENDA'] = min(moedaPHP($valuePeca['PVENDA_561']), moedaPHP($valuePeca['PVENDA']));
      }
    }

    $valuePeca['PVENDAMIN'] = moedaPHP($valuePeca['PVENDA']);

    insereItemOrcamento($_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'], $valuePeca);

  }

}
