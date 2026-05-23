<?php 
session_start();

ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ (E_WARNING|E_NOTICE|E_DEPRECATED));
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
clearstatcache();
require_once "../../pages/conf/define.php";
require_once "../../pages/conf/functions.php";
require_once "../../pages/conf/conectaOracle.php";
require_once '../../pages/vendas/function.php';
// $debug = true;
if(!empty($_POST)){
	$dados = $_POST;
} else {
	$dados = $_GET;
}
if($debug) varDump2($dados);
if (!isset($dados["IDORCAMENTO"]) || empty($dados["IDORCAMENTO"])) {
	insereModal("danger", " IDORCAMENTO inválido para faturamento");
	
}
if (isset($orcamento)) {
	unset($orcamento);
}
$orcamento 													= array();
$orcamento['CAB'] 									= buscaORCORCAMENTOC($dados["IDORCAMENTO"]);
$orcamento['CAB']['IMPORTADO']			= '1'; /*1: PROCESSAR / 5: NÃO PROCESSAR */
$orcamento['CAB']['CONDVENDA']			= '1'; /*1 – Normal, 5 – Bonificação, 10 - Transferencia.*/
$orcamento['CAB']['ORIGEMPED']			= 'F'; /*F – Força de vendas, R – Balcão Reserva, W - Web. */
$orcamento['CAB']['CODEMITENTE']		= '8888'; /*8888: FORÇA DE VENDA */
$orcamento['CAB']['OBS1']						= 'PORTAL DE SERVICOS VEMAP';
$orcamento['CAB']['OBS2']						= $orcamento['CAB']['OBSERVACAO'];
$orcamento['CAB']['NUMPEDCLI']			= $orcamento['CAB']["IDORCAMENTO"];
$orcamento['CAB']['NUMPEDRCA']			= buscaProximoNUMPEDRCA();
$orcamento['CAB']['CODUSUR']				= $orcamento['CAB']["CODUSUR"];
$orcamento['CAB']['CODCLI']					= $orcamento['CAB']["CODCLI"];
$orcamento['CAB']['CGCCLI']					= buscaCGCENT($orcamento['CAB']["CODCLI"]);
$orcamento['CAB']['CODFILIALNF']		= $orcamento['CAB']["CODFILIALNF"];
$orcamento['CAB']['CODCOB']					= $orcamento['CAB']["CODCOB"];
$orcamento['CAB']['CODPLPAG']				= $orcamento['CAB']["CODPLPAG"];
$orcamento['CAB']['VLMINPEDIDO']		= buscaVLMINPEDIDO($orcamento['CAB']["CODPLPAG"]);
$orcamento['CAB']['FRETEDESPACHO']	= ((empty($orcamento['CAB']['FRETEDESPACHO']))?'N':$orcamento['CAB']['FRETEDESPACHO']);
$orcamento['CAB']['VLFRETE']				= moedaPHP((empty($orcamento['CAB']['FRETEDESPACHO']))?0:$orcamento['CAB']['VALORFRETE']);
###########################################################################################

if ($orcamento['CAB']['SEPARARPEDIDO'] == "S") {
	$orcamento['CAB']['OBSENTREGA1'] = '# SEPARAR ESTE PEDIDO # ';
} else {
	$orcamento['CAB']['OBSENTREGA1'] = '# NAO SEPARAR >> Motivo: '.$orcamento['CAB']['MOTIVONAOSEPARAR']." #";
}
###########################################################################################

if ($orcamento['CAB']['CLIENTEBALCAO'] == "S") {
	$orcamento['CAB']['OBSENTREGA2'] = '# CLIENTE ESPERANDO NO BALCAO # '.$orcamento['CAB']['ORDEMDECOMPRA'];
} else {
	$orcamento['CAB']['OBSENTREGA2'] = '# PEDIDO EM PRATELEIRA # '.$orcamento['CAB']['ORDEMDECOMPRA'];
}
###########################################################################################
// OBSERVAÇÕES DE FRETE
$orcamento['CAB']['OBSENTREGA3'] = $orcamento['CAB']['OBSENTREGA3'];
$orcamento['CAB']['OBSENTREGA4'] = $orcamento['CAB']['OBSENTREGA4'];
###########################################################################################
// ITENS DO PEDIDO
$orcamento['CAB']['VLTOTALPEDIDO'] = 0;
$orcamento['ITE'] = buscaORCORCAMENTOIFaturar($dados["IDORCAMENTO"]);
// varDump2($orcamento['ITE']); 

if ($orcamento['ITE']) {
	foreach ($orcamento['ITE'] as $key => $value) {
		if (moedaPHP($value['SALDO']) < moedaPHP($value['QTPEDIDA'])) {
			unset($orcamento['ITE'][$key]);
			exibeMensagem("produto {$value['CODPROD']} Não possui saldo suficiente para atender a quantidade pedida {$value['QTPEDIDA']}");
		}
		if (moedaPHP($value['PVENDA']) <= moedaPHP(0.01)) {
			unset($orcamento['ITE'][$key]);
			exibeMensagem("produto {$value['CODPROD']} Não possui Preço de venda válido {$value['PVENDA']}");
		}
		if (moedaPHP($value['PTABELA']) <= moedaPHP(0.01)) {
			unset($orcamento['ITE'][$key]);
			exibeMensagem("produto {$value['CODPROD']} Não possui Preço de tabela válido {$value['PTABELA']}");
		}
		$orcamento['CAB']['VLTOTALPEDIDO'] += (moedaPHP($value['QTPEDIDA']) * moedaPHP($value['PVENDA']));
	}
} else {
	$orcamento['ITE'] = false;
}
// varDump2($orcamento); die();

if (empty($orcamento['ITE']) || !$orcamento['ITE']) {
	exibeMensagem('ERRO O Orçamento não pode ser faturado, pois não possui nenhum produto winthor válido.');
	// if(!$debug) fechaAba();
}
##########################################################################################
// VALIDAÇÕES
if ($orcamento['CAB']['NUMPEDRCA'] 		== "") 			die("NUMPEDRCA inválido.");
if ($orcamento['CAB']['CODUSUR'] 		== "") 			die("CODUSUR inválido.");
if ($orcamento['CAB']['CODCLI'] 		== "") 			die("CODCLI inválido.");
if ($orcamento['CAB']['CGCCLI'] 		== "") 			die("CGCCLI inválido.");
if ($orcamento['CAB']['CODFILIALNF'] 	== "") 			die("CODFILIALNF inválido.");
if ($orcamento['CAB']['CODCOB'] 		== "") 			die("CODCOB inválido.");
if ($orcamento['CAB']['CODPLPAG'] 		== "") 			die("CODPLPAG inválido.");
if ($_SESSION['login']['IDUSUARIO'] != 1) {
	$clientesVemap = array(16837, 1031, 17825, 44109, 44117, 44118, 44119, 44120);
	if ( in_array($orcamento['CAB']['CODCLI'], $clientesVemap) ) {
		insereModal("danger", " O orcamento não pode ser faturado usando cliente VEMAP. Favor selecionar um cliente válido!");
		if(!$debug) redireciona("index.php?op=62");
	}
}
$pedidoValido = false;
if ($faturados = validaOrcamentoJaFaturado($orcamento['CAB']['NUMPEDCLI'])) {
	foreach ($faturados as $key => $value) {
		switch ($value["IMPORTADO"]) {
			case '1':
				exibeMensagem("Já Existe um pedido em processamento no Winthor!");
				break;
			case '2':
				if ($value["IMPORTADO"] == "C") {
					$pedidoValido = true;
				} else {
					exibeMensagem("Já Existe um pedido no Winthor para este orçamento!");
				}
				break;
			case '3':
				$pedidoValido = true;
				break;
			case '4':
				exibeMensagem("Já Existe um pedido em processamento no Winthor!");
				break;
		}
	}
	if(!$debug) fechaaba();
} else {
	$pedidoValido = true;
} 

if ($pedidoValido === false) {
	fechaAba();
	die();
}

if ($orcamento['CAB']['VLTOTALPEDIDO'] < $orcamento['CAB']['VLMINPEDIDO']) {
	insereModal("danger", " O Orcamento não pode ser faturado, pois o valor Total do pedido [{$orcamento['CAB']['VLTOTALPEDIDO']}] é menor que o valor mínimo do plano de paramento selecionado [{$orcamento['CAB']['VLMINPEDIDO']}]");
	if(!$debug) redireciona("index.php?op=62");
	
}

if($debug) varDump2($orcamento);

if (inserePCPEDIFV($orcamento)) {
	if ( !inserePCPEDCFV($orcamento) ) {
		exibeMensagem('ERRO ao inserir CABECALHO do pedido na PCPEDCFV');
		
	} else {
		if(!$debug) {
			if (!prodessaIntegradora() ) {
				exibeMensagem('ERRO ao processar a INTEGRADORA');
				
			} else {
				exibeMensagem('Orçamento NR '.$orcamento['CAB']['NUMPEDCLI'].' faturado com sucesso!');
			}
		} 
	}
}
// redireciona("index.php?op=62&acao=consultaOrcamento&IDORCAMENTO={$dados["IDORCAMENTO"]}");
fechaAba();
?>