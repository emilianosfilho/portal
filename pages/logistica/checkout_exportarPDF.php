<?php 
session_start();
ini_set("xdebug.var_display_max_depth", -1);
ini_set("xdebug.var_display_max_children", -1);
ini_set("xdebug.var_display_max_data", -1);
ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('memory_limit', '24G');
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE));
date_default_timezone_set('America/Manaus');
require_once "../../pages/conf/define.php";
require_once "../../pages/conf/functions.php";
require_once "../../pages/conf/conectaOracle.php";
require_once '../../pages/logistica/function.php';
require_once "../../plugins/code39/code39.php";

// $debug = true;

if (!empty($_POST)) {
	$dados = $_POST;
} else {
	$dados = $_GET;
}
if($debug) varDump2($dados);


// NUMERO DE RESULTADOS POR PÁGINA
$regTotal 			= 0;
$totalPaginas 		= 0;
$paginaAtual 		= 0;
$regAtual 			= 0;
$regPorPagina 	= 20;
$paginas 			  = array();

$r_dark = $r_dark??0;
$g_dark = $g_dark??0;
$b_dark = $b_dark??0;

if (isset($PDF)) 
	unset($PDF);
$PDF = array();

if (!isset($dados['IDCHECKOUT']) || empty($dados['IDCHECKOUT'])) {
	exibeMensagem("ERRO IDCHECKOUT inválido!");
	fechaAba();
	die();
}

if($PDF['CAB'] = buscaOMGCHECKOUTC($dados['IDCHECKOUT'])){
	$PDF['ITENS'] = buscaOMGCHECKOUTI($dados['IDCHECKOUT']);
}
if($debug) varDump2($PDF); 


if ($PDF['ITENS']==false || empty($PDF['ITENS'])) {
	exibeMensagem("Nenhum produto válido no pedido de venda!");
	fechaAba();
	die();
} else {

	$regTotal = count($PDF['ITENS']);
	if($debug) varDump2("regTotal: {$regTotal}");
	
	$paginas[$paginaAtual]["inicio"] = 0;

	if ($regTotal <= $regPorPagina){
		$paginas[$paginaAtual]["fim"] = ($regTotal-1);
	} else {
		$paginas[$paginaAtual]["fim"] = ($regPorPagina-1);

		if ($regTotal > $regPorPagina){
			$regTmp = ($regTotal - $regPorPagina);
			while ($paginas[$paginaAtual]["fim"] < ($regTotal-1)) {
				$paginaAtual++;
				$paginas[$paginaAtual]["inicio"] = ($paginaAtual*$regPorPagina);
				$paginas[$paginaAtual]["fim"] 	 = ((($paginaAtual+1)*$regPorPagina)-1);

				if ($paginas[$paginaAtual]["fim"] > ($regTotal-1) ) {
					$paginas[$paginaAtual]["fim"] = ($regTotal-1);
				}
			}

		}
		
	}
}

$ultimaPagina = reset($paginas);
// if($debug) varDump2($ultimaPagina);

if (($ultimaPagina['fim'] - $ultimaPagina['inicio']) > 10) {
	$paginaAtual++;
	$paginas[$paginaAtual]["inicio"] = false;
	$paginas[$paginaAtual]["fim"] 	 = false;
}
if($debug) varDump2($paginas);
// varDump2($paginas);

$erro 			= 0;
$alturaLinha	= 5;

/*############################################################################################*/
$pdf = new PDF_Code39();
$pdf = new PDF_Code39('L','mm','A4');

$pdf->AliasNbPages();

foreach ($paginas as $pagina => $value) {

	$pdf->AddPage();

	// LOGO QUE SERÁ COLOCADO NO RELATÓRIO
	$regAtualogo_header 	= "../../".DIR_IMG."logo_header.png";
	$pdf->SetTextColor(139,0,0);
	$pdf->SetFont($font_arial,$style_b,$tam_18);
	$pdf->SetFillColor($r_light,$g_light,$b_light);
	$pdf->Cell(40,6,"", 0, 0);
	$pdf->Cell(180,10, converterUTF8("ESPELHO DE CONFERÊNCIA DE SAÍDA / CHECK-OUT"), 0, 0, 'C', 1);
	$pdf->Image($regAtualogo_header,10,10,40,10);

	$pdf->SetTextColor($r_dark,$g_dark,$b_dark);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(60,4, converterUTF8("NÚM. PEDIDO:"), 'LRT', 1, 'C');
	$pdf->SetFont($font_arial,$style_b,$tam_18);
	$pdf->Cell(220,6,"", 0, 0);
	$pdf->Cell(60,6, $PDF['CAB']['NUMPED'], 'LRB', 1, 'C');
	

	/*#####################################################################################################*/
	$pdf->SetFillColor(255,255,255);/* Branco */
	$pdf->SetTextColor(0,0,0); /* Preto */
	$pdf->Cell(1,$alturaLinha,"", 0, 1);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(15, $alturaLinha, "Cliente:" 				, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(85, $alturaLinha, substr($PDF['CAB']['CODCLI']."- ".converterUTF8($PDF['CAB']['CLIENTE']), 0, 40) 	, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(10, $alturaLinha, "CNPJ:" 						, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(52, $alturaLinha, $PDF['CAB']['CNPJ']  		, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(30, $alturaLinha, converterUTF8("Número do Pedido:") 				, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(40, $alturaLinha, $PDF['CAB']['NUMPED']  		, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(35, $alturaLinha, converterUTF8("Número do Orçamento:") 			, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(20, $alturaLinha, $PDF['CAB']['NUMPEDCLI']		, 0, 0);
	$pdf->Cell(1, $alturaLinha,  ""  , 0, 1);


	/*#####################################################################################################*/
	$contadorRegPagina = 0;

	if ($value["inicio"] !== false && $value["fim"] !== false) {

		for ($regAtual=$value["inicio"]; $regAtual <= $value["fim"]; $regAtual++) { 
			
			if($debug) varDump2("regAtual: {$regAtual}");

			$contadorRegPagina++;

			if ($contadorRegPagina == 1) {
				/*#####################################################################################################*/
				$pdf->Cell(280, $alturaLinha,  ""  , 0, 1); // Pula 1 linha
				$pdf->SetFont($font_arial,$style_b,$tam_9);
				$pdf->SetTextColor($r_black,$g_black,$b_black);
				$pdf->Cell(10, $alturaLinha, "#"						, 'B', 0);
				$pdf->Cell(20, $alturaLinha, "Cod VEMAP"				, 'B', 0);
				$pdf->Cell(90, $alturaLinha, converterUTF8("Descrição")			, 'B', 0);
				$pdf->Cell(40, $alturaLinha, "Marca"				, 'B', 0);
				$pdf->Cell(30, $alturaLinha, converterUTF8("Locação")				, 'B', 0);
				$pdf->Cell(10, $alturaLinha, "UN"					, 'B', 0);
				$pdf->Cell(10, $alturaLinha, "Qtd"					, 'B', 0, 'R');
				$pdf->Cell(30, $alturaLinha, converterUTF8("Preço Un")				, 'B', 0, 'R');
				$pdf->Cell(40, $alturaLinha, "Vl. Total"			, 'B', 1, 'R');
			}
			
			
			if ($regAtual <= $regTotal) {
				if (isset($PDF['CAB']['IMPORTADO']) && ($PDF['CAB']['IMPORTADO'] == '3')) {
					$pdf->SetFont($font_arial,$style_n,$tam_9);
					$pdf->SetTextColor($r_danger,$g_danger,$b_danger);
					$pdf->Cell(10, $alturaLinha, ($regAtual+1)	, 0, 0);
					$pdf->Cell(20, $alturaLinha, "www".$PDF['ITENS'][$regAtual]['CODPROD']			, 0, 0);
					$pdf->Cell(90, $alturaLinha, $PDF['ITENS'][$regAtual]['PRODUTO']			, 0, 0);
					$pdf->Cell(40, $alturaLinha, $PDF['ITENS'][$regAtual]['MARCA']				, 0, 0);
					$pdf->Cell(120, $alturaLinha, $PDF['ITENS'][$regAtual]['OBSERVACAO_PC']	, 0, 1, 'L');
				} else {

					$PDF['ITENS'][$regAtual]['VALORTOTAL'] = (moedaPHP($PDF['ITENS'][$regAtual]['QT']) * moedaPHP($PDF['ITENS'][$regAtual]['PVENDA']));
					$QUANTIDADETOTALPEDIDO 	+= moedaPHP($PDF['ITENS'][$regAtual]['QT']);
					$VALORTOTALPEDIDO 		+= moedaPHP($PDF['ITENS'][$regAtual]['VALORTOTAL']);

					$pdf->SetFont($font_arial,$style_n,$tam_9);
					$pdf->SetTextColor($r_black,$g_black,$b_black);
					$pdf->Cell(10, $alturaLinha, ($regAtual+1) 	, 0, 0);
					$pdf->Cell(20, $alturaLinha, $PDF['ITENS'][$regAtual]['CODPROD']			, 0, 0);
					$pdf->Cell(90, $alturaLinha, $PDF['ITENS'][$regAtual]['PRODUTO']			, 0, 0);
					$pdf->Cell(40, $alturaLinha, $PDF['ITENS'][$regAtual]['MARCA']				, 0, 0);
					$pdf->Cell(30, $alturaLinha, $PDF['ITENS'][$regAtual]['LOCACAO']			, 0, 0);
					$pdf->Cell(10, $alturaLinha, $PDF['ITENS'][$regAtual]['UNIDADE']			, 0, 0);
					$pdf->Cell(10, $alturaLinha, moeda($PDF['ITENS'][$regAtual]['QT'])		, 0, 0, 'R');
					$pdf->Cell(30, $alturaLinha, moeda($PDF['ITENS'][$regAtual]['PVENDA'])			, 0, 0, 'R');
					$pdf->Cell(40, $alturaLinha, moeda($PDF['ITENS'][$regAtual]['VALORTOTAL'])		, 0, 1, 'R');
				}
			}
		}
	}


	/*#####################################################################################################*/
	$pdf->setXY(10,180);

	$pdf->SetFont('arial','',8);
	$pdf->SetTextColor(0,0,0);
	$pdf->Cell(140, $alturaLinha, "Arquivo gerado em: ".@date('d/m/Y H:i:s'), 'T', 0, 'L');
	$pdf->Cell(140, $alturaLinha, converterUTF8("Página ".($pagina+1)." de ".count($paginas)), 'T', 1, 'R');

}



	/*#####################################################################################################*/

if(!$debug){
	### TIPO DO PDF GERADO ###
	//I-> envia o arquivo embutido para o navegador. O visualizador de PDF é usado, se disponível.
	//D-> enviar para o navegador e forçar o download de um arquivo com o nome fornecido pelo nome.
	//F-> salvar em um arquivo local com o nome dado pelo nome (pode incluir um caminho).
	//S-> retorna o documento como uma string.
	$tipo_pdf = "I";

	//TÍTULO DO RELATÓRIO
	$titulo = "CONFERÊNCIA E EXPEDIÇÃO";

	//ENDEREÇO ONDE SERÁ GERADO O PDF
	$end_final = $PDF['CAB']['NUMPED'].'_'.str_replace(" ", "_", $titulo).".pdf";

	//SAIDA DO PDF
	$pdf->Output($tipo_pdf, $end_final);
	$pdf->Close();
}


?>
