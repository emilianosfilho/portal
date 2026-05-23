<?php  
$debug = true;
$debug = false;

// Starting clock time in seconds
if($debug) $start_time = microtime(true);
if($debug) varDump2('Entrou no consultaOrcamento');
if($debug) varDump2($dados);

if (isset($_SESSION['ORCAMENTO'])) {
  unset($_SESSION['ORCAMENTO']);
}
$ORCAMENTO = array();

if (!isset($dados['IDORCAMENTO']) || empty($dados['IDORCAMENTO'])) {
  
  insereModal("danger", "Não foi possível identificar o IDORCAMENTO");

} else {

  $ORCAMENTO['CAB']       = buscaDadosCabecalho($dados['IDORCAMENTO']);
  $ORCAMENTO['CLIENTE']   = buscaDadosCliente($ORCAMENTO['CAB']['CODCLI']);
  $ORCAMENTO['VENDEDOR']  = buscaDadosVendedor($ORCAMENTO['CAB']['CODUSUR']);
  $ORCAMENTO['FILIAL']    = buscaDadosFilial($ORCAMENTO['CAB']['CODFILIALNF']);
  $ORCAMENTO['COBRANCA']  = buscaDadosCobranca($ORCAMENTO['CAB']['CODCOB']);
  $ORCAMENTO['PLPAG']     = buscaDadosPlpag($ORCAMENTO['CAB']['CODPLPAG']);
  
  $itensDesatualizados    = buscaItensOrcamento($ORCAMENTO['CAB']['IDORCAMENTO']);
  
  $itensAtualizados       = buscaItensAtualizados($ORCAMENTO['CAB']['IDORCAMENTO']);


  foreach ($itensDesatualizados as $keyOLD => $valueOLD) {
    if($debug) varDump2($valueOLD);
    
    foreach ($itensAtualizados as $keyNEW => $valueNEW) {
      if ($valueOLD['CODPROD'] == $valueNEW['CODPROD']) {
        if($debug) varDump2($valueNEW);

        $msg = "";

        $UPDATE = array();
        
        $UPDATE['QTDISPONIVEL'] = $valueNEW['SALDO'];

        if ($valueNEW['SALDO'] <= 0) {
          $UPDATE['DISPONIBILIDADE'] = "SOB CONSULTA";
        } else {
          if ($valueOLD['QTPEDIDA'] > $valueNEW['SALDO']) {
            $UPDATE['DISPONIBILIDADE'] = "IMEDIATA (".$valueNEW['SALDO'].")";
          } else {
            $UPDATE['DISPONIBILIDADE'] = "IMEDIATA";
          }
        }
        if ($valueOLD['DISPONIBILIDADE'] <> $UPDATE['DISPONIBILIDADE']) {
          $msg .= "<p>campo DISPONIBILIDADE mudou de ".$valueOLD['DISPONIBILIDADE']." para ".$UPDATE['DISPONIBILIDADE']."</p>";
        }



        if ($valueOLD['PVENDA'] < $valueNEW['PMINIMO']) {
          $UPDATE['PVENDA'] = $valueNEW['PMINIMO'];
        } else {
          $UPDATE['PVENDA'] = $valueOLD['PVENDA'];
        }

        if ($valueOLD['PVENDA'] <> $UPDATE['PVENDA']) {
          $msg .= "<p>campo PVENDA mudou de ".moeda($valueOLD['PVENDA'])." para ".moeda($UPDATE['PVENDA'])."</p>";
        }

        $UPDATE['TRIBUT'] = $valueNEW['TRIBUT'];
        if ($valueOLD['TRIBUT'] <> $valueNEW['TRIBUT']) {
          $msg .= "<p>campo TRIBUT mudou de ".($valueOLD['TRIBUT'])." para ".($valueNEW['TRIBUT'])."</p>";
        }

        if($debug) varDump2($UPDATE); 
        
        $sql = "UPDATE ORCORCAMENTOI SET ".
               "DISPONIBILIDADE = '".$UPDATE['DISPONIBILIDADE']."', ".
               "QTDISPONIVEL = '".$UPDATE['QTDISPONIVEL']."', ".
               "PVENDA = '".$UPDATE['PVENDA']."', ".
               "TRIBUT = '".$UPDATE['TRIBUT']."' ".
               "WHERE IDORCAMENTOI = ".$valueOLD['IDORCAMENTOI'];
        if($debug) varDump2($sql);
        // if($debug) die();

        executarOracle($sql);

        if ($msg <> "") {
          $cab = "<h3>Hove alterações no produto ".$valueOLD['CODPECA']." ".$valueOLD['DESCRICAO']."</h3>";
          insereModal("warning", $cab.$msg);
        }

      }
    }
  }

  $_SESSION['ORCAMENTO'] = $ORCAMENTO;
  $_SESSION['BASE'] = array(
    'LISTACOBRANCAS' => buscaListaCobrancasNivel($ORCAMENTO['CLIENTE']['NIVELVENDA']),
    'LISTAPLANOS' => buscaListaPlanosCodcob($ORCAMENTO['COBRANCA']['CODCOB'])
    );
  

}


if($debug) varDump2($_SESSION['ORCAMENTO']); 


if($debug) $end_time = microtime(true);
if($debug) varDump2( "Execution time of script = ".$execution_time = ($end_time - $start_time)." sec" );
if($debug) die();