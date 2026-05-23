<?php 

$CODPRODVALIDO = $PRODUTO['CODWINT'];
if (!array_search($CODPRODVALIDO, $_SESSION['LISTAPRODUTOSSALVOS'])) {
	 array_push($_SESSION['LISTAPRODUTOSSALVOS'], $CODPRODVALIDO);
}

if ($debug) varDump2("####### Script Produto EXISTENTE ###############################");
if ($debug) varDump2($CODPRODVALIDO);
if ($debug) varDump2($PRODUTO);
if ($debug) varDump2($PARAM);
// die();


if ($debug) varDump2("####### updatePCPRODUT ###############################");

if (atualizaDadosPCPRODUT($debug, $CODPRODVALIDO, $PRODUTO)) {

	$PRODUTO = validaSituacaoProduto($PRODUTO['NUMORIGINAL'], $PRODUTO['CODMARCA']);

	if ($PRODUTO) {
		if (!array_search($CODPRODVALIDO, $_SESSION['LISTAPRODUTOSSALVOS'])) {
			 array_push($_SESSION['LISTAPRODUTOSSALVOS'], $CODPRODVALIDO);
		}
		if (!array_search($CODPRODVALIDO, $_SESSION['LISTAPRODUTOSSALVOS'])) {
			 array_push($_SESSION['LISTAPRODUTOSSALVOS'], $CODPRODVALIDO);
		}
		$_SESSION['VALIDACADASTROS'][$CODPRODVALIDO]['PCPRODUT'] = true;
	} else {
		$_SESSION['VALIDACADASTROS'][$CODPRODVALIDO]['PCPRODUT'] = false;
	}

} else {
	$_SESSION['VALIDACADASTROS'][$CODPRODVALIDO]['PCPRODUT'] = false;
}

?>