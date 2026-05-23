<?php
// $debug = true;

$dados = $dados??[];
if($debug) varDump2($dados); 
// die();

####################################################################################################
/*BLOCO PESQUISA CLIENTE*/
/*Este bloco é garregado a partir da pesquisa de cliente em um novo orçamento*/
if (isset($dados['abrirNovoOrcamento'])) {
	include("pages/vendas/abrirNovoOrcamento.php");
}


###############################################################################################
/*CONTROLE DE ABA ATIVA*/
if (isset($dados['tab'])) {
	switch ($dados['tab']) {
		case 'pedidos':
			$active_geral = false;
			$active_exportar = false;
			$active_faturamento = false;
			$active_pedidos = true;
			break;
	}
} else {
}

if (isset($dados['acao']) && $dados['acao']<>""){
	switch ($dados['acao']) {

		case 'enviarArquivo':
			require_once("pages/vendas/enviarArquivo.php");
			if ($arquivo) 
				require_once("pages/vendas/radar_processarArquivo.php");
			break;

		case 'consultaOrcamento':
			include("pages/vendas/consultaOrcamento.php");
			break;

		case 'consultaPeca':
			include_once('pages/vendas/consultaPeca.php');
			break;

		case 'inserePecaOrcamento':
			include_once('pages/vendas/inserePecaOrcamento.php');
			break;

		case 'defineAtendimento':
			$_SESSION['ORCAMENTO']['CAB']['ATENDIMENTO'] = alteraAtendimentoOrcamento($dados['ATENDIMENTO'], $_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']);
			break;

		case 'atualizaItemOrcamento':
			include('pages/vendas/atualizaItemOrcamento.php');
			break;

		case 'excluirItemOrcamento':
			excluirItemOrcamento($dados);
			break;

		case 'duplicarOrcamento':
			$active_faturamento = true;
			include("pages/vendas/duplicarOrcamento.php");
			break;

		case 'faturarOrcamento':
			$active_faturamento = true;
			abreNova("pages/vendas/faturarOrcamento.php?IDORCAMENTO={$dados['IDORCAMENTO']}");			
			break;

		case 'defineContato':
			defineContato($dados);
			break;

		case 'defineEXIBIRCODPECAETIQUETA':
			$active_faturamento = true;
			defineEXIBIRCODPECAETIQUETA($dados['EXIBIRCODPECAETIQUETA'], $_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']);
			break;

		case 'defineOrdemdecompra':
			defineOrdemdecompra($dados);
			break;

		case 'defineMaquina':
			defineMaquina($dados);
			break;

		case 'defineObsGerais':
			defineObsGerais($dados);
			break;

		case 'defineCLIENTEBALCAO':
			$active_faturamento = true;
			defineCLIENTEBALCAO($dados);
			break;

		case 'defineSEPARARPEDIDO':
			$active_faturamento = true;
			defineSEPARARPEDIDO($dados);
			break;

		case 'definePlpag':
			$active_faturamento = true;
			alteraPlanopagamentoOrcamento($_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'], $dados['CODPLPAG']);
			$_SESSION['ORCAMENTO']['CAB']['CODPLPAG'] = $dados['CODPLPAG'];
			$_SESSION['ORCAMENTO']['PLPAG'] = buscaDadosPlpag($_SESSION['ORCAMENTO']['CAB']['CODPLPAG']);
			break;

		case 'defineFrete':
			$active_faturamento = true;
			defineFrete($dados);
			break;

		case 'defineCobranca':
			$active_faturamento = true;
			if ($dados['CODCOB'] == "") {
				insereModal("danger", "Não foi possível identificar a cobrança");
			} else {
				
				$_SESSION['ORCAMENTO']['COBRANCA'] = buscaDadosCobranca($dados['CODCOB']);
				$_SESSION['BASE']['LISTAPLANOS'] 	= buscaListaPlanosCodcob($dados['CODCOB']);
				$_SESSION['ORCAMENTO']['PLPAG'] = buscaDadosPlpag($_SESSION['BASE']['LISTAPLANOS'][0]['CODPLPAG']);
				
				alteraCobrancaOrcamento($_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'], $_SESSION['ORCAMENTO']['COBRANCA']['CODCOB']);

				alteraPlanopagamentoOrcamento($_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'], $_SESSION['ORCAMENTO']['PLPAG']['CODPLPAG']);

				$_SESSION['ORCAMENTO']['CAB'] = buscaDadosCabecalho($_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']);
			}
			break;

		case 'exibeCotacoes':
			if ($dadosCotacoes = buscaCotacoes($dados['NUMORIGINAL'])) {
			    insereModalCotacoes($dados['NUMORIGINAL'], $dadosCotacoes);
			} else {
				insereModal("warning", "Não foram encontradas COTAÇÕES para a peça ".$dados['CODPECA_OLD']);
			}
			break;

		case 'exibeVendas':
		  if (empty($dados['NUMORIGINAL'])) {
		  	$dados['NUMORIGINAL'] = $dados['CODPECA_OLD'];
		  }
			$dadosVendas = buscaVendas($dados['NUMORIGINAL']);
			// varDump2($dadosVendas); 
			// die();
			if ($dadosVendas) {
			    insereModalVendas($dadosVendas);
			} else {
				insereModal("warning", "Não foram encontradas VENDAS para a peça ".$dados['CODPECA_OLD']);
			}
			break;

		case 'exibeCompras':
			if ($dadosCompras = buscaCompras($dados['NUMORIGINAL'])) {
			    insereModalCompras($dadosCompras);
			} else {
				insereModal("warning", "Não foram encontradas COMPRAS para a peça ".$dados['CODPECA_OLD']);
			}
			break;

		case 'exibeInformacoes':
			if ($dadosInformacoes = buscaInformacoes($dados['CODPECA_OLD'])) {
			    insereModalInformacoes($dadosInformacoes);
			} else {
				insereModal("warning", "Não foram encontradas INFORMACÕES NPR para a peça ".$dados['CODPECA_OLD']);
			}
			break;

		case 'exportaPDF-pedidoConferencia':
			$active_pedidos = true;
			if (isset($_SESSION['DADOS'])) {
				unset($_SESSION['DADOS']);
			}
			$_SESSION['DADOS'] = $dados;
			abreNova("pages/vendas/exportaPDF-pedidoConferencia.php");
			break;

		case 'exportaPDFPedidoVenda':
			$active_pedidos = true;
			if (isset($_SESSION['DADOS'])) {
				unset($_SESSION['DADOS']);
			}
			$_SESSION['DADOS'] = $dados;
			abreNova("pages/vendas/exportaPDFPedidoVenda.php");
			break;

		case 'exportaPDFEtiquetaVenda':
			$active_pedidos = true;
			$_SESSION['DADOS'] = $dados;
			abreNova("pages/vendas/exportaPDFEtiquetaVenda.php");
			break;

		case 'exportarPDF':
			$active_exportar = true;
			abreNova("pages/vendas/exportaPDF-orcamento.php?".http_build_query($dados));
			break;

		case 'exportarExcel':
			$_SESSION['DADOSEXPORT'] = $dados;
			abreNova("pages/vendas/exportarExcel.php");
			break;

		case 'processarIntegradora':
			prodessaIntegradora($dados['CODUSUR'], $dados['NUMPEDRCA']);
			break;

		case 'novaConsulta':
			if (isset($_SESSION['CONSULTA'])) {
				unset($_SESSION['CONSULTA']);
			}
			break;

		case 'consultaPecaRadar':
			include_once('pages/vendas/consultaPecaRadar.php');
			break;

		case 'ajustarAliquotaRR':
			ajustarAliquotaRR($dados['CODCLI'], $dados['NUMPED']);
			break;
		
	}
}

if (!$active_exportar && !$active_faturamento && !$active_pedidos) {
	$active_geral = true;
}

