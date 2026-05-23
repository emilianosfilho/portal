<?php  
if (isset($_SESSION['ORCAMENTO']))  unset($_SESSION['ORCAMENTO']);
if (isset($_SESSION['BASE']))       unset($_SESSION['BASE']);

$ORCAMENTO 				= array();
$ORCAMENTO['CAB']      	= array();

$ORCAMENTO['CLIENTE']   = buscaDadosCliente($_POST['CODCLI']);
$ORCAMENTO['VENDEDOR']  = buscaDadosVendedor($_SESSION['login']['CODUSUR']);
$ORCAMENTO['FILIAL']    = buscaDadosFilial($ORCAMENTO['CLIENTE']['CODFILIALNF']);
$ORCAMENTO['COBRANCA']  = buscaDadosCobranca($ORCAMENTO['CLIENTE']['CODCOB']);
$ORCAMENTO['PLPAG']     = buscaDadosPlpagDefault($_POST['CODCLI']);
$ORCAMENTO['CAB']       = geraNovoCabecalho($ORCAMENTO);
$ORCAMENTO['ITEM']      = array();

// varDump2($ORCAMENTO); die();

$_SESSION['ORCAMENTO'] = $ORCAMENTO;
$_SESSION['BASE'] = array(
	'LISTACOBRANCAS' => buscaListaCobrancasNivel($ORCAMENTO['CLIENTE']['NIVELVENDA']),
	'LISTAPLANOS' => buscaListaPlanosCodcob($ORCAMENTO['COBRANCA']['CODCOB'])
  );
?>
