<?php  
require_once("pages/cliente/function.php");

if (isset($dados['acao'])) {
	// varDump2($dados);
	
	switch ($dados['acao']) {

		case 'new':
			$IDORCAMENTO = geraNovoOrcamentoCliente();
			$_SESSION['ORC_CLIENTE'] = buscaOrcamentoCompleto($IDORCAMENTO);
			redireciona("index.php?op=151&IDORCAMENTO=".$IDORCAMENTO);
			break;
			
		case 'consultaOrcamento':
			$_SESSION['ORC_CLIENTE'] = buscaOrcamentoCompleto($dados['IDORCAMENTO']);
			redireciona("index.php?op=151&IDORCAMENTO=".$dados['IDORCAMENTO']);
			break;

		case 'defineMaquina':
			defineMaquina($dados);
			break;

		case 'consultaPeca':
			include_once('pages/cliente/consultaPeca.php');
			break;

		case 'atualizaItemOrcamento':
			include_once('pages/cliente/atualizaItemOrcamento.php');
			break;

		case 'excluirItemOrcamento':
			excluirItemOrcamento($dados);
			break;

		case 'exportarPDF':
			abreNova('pages/cliente/exportarPDF.php');
			break;

		case 'defineObservacao':
			defineObservacao($dados);
			break;

	}
}