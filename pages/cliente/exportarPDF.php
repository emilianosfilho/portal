<?php 
session_start();
date_default_timezone_set('America/Manaus');
error_reporting(E_ALL ^E_NOTICE ^E_WARNING ); 
ini_set('error_reporting', E_ALL ^E_NOTICE ^E_WARNING );
ini_set("display_errors", 1);
require_once "../../pages/conf/define.php";
require_once "../../pages/conf/functions.php";
require_once "../../pages/conf/conectaOracle.php";
require_once '../../pages/vendas/function.php';
//PREPARA PARA GERAR O PDF
define("FPDF_FONTPATH", "../../plugins/fpdf/font/");
require("../../plugins/fpdf/fpdf.php");
require("../../plugins/fpdf/stylesheet.php");

// varDump2($_SESSION['DADOSEXPORT']);
// varDump2($_SESSION['ORC_CLIENTE']);
// die();


$ORC = $_SESSION['ORC_CLIENTE'];
$ARRAY_EXIBE = array();

//T�TULO DO RELAT�RIO
$titulo = "ORCAMENTO";

//ENDERE�O ONDE SER� GERADO O PDF
$end_final = $ORC['CAB']['IDORCAMENTO'].'_'.str_replace(" ", "_", $titulo).".pdf";


$ARRAY_EXIBE = buscaItensOrcamento($_SESSION['ORC_CLIENTE']['CAB']['IDORCAMENTO']);
if ($ARRAY_EXIBE === false || is_null($ARRAY_EXIBE)) {
	$ARRAY_EXIBE = array();
}


### TIPO DO PDF GERADO ###
//I-> envia o arquivo embutido para o navegador. O visualizador de PDF � usado, se dispon�vel.
//D-> enviar para o navegador e for�ar o download de um arquivo com o nome fornecido pelo nome.
//F-> salvar em um arquivo local com o nome dado pelo nome (pode incluir um caminho).
//S-> retorna o documento como uma string.
$tipo_pdf = "I";

//NUMERO DE RESULTADOS POR P�GINA
$regPorPagina = 25;
$alturaLinhaItem = 6;
$erro = 0;

//LOGO QUE SER� COLOCADO NO RELAT�RIO
$logo_header 	= "../../".DIR_IMG."logo_header.png";
$logo_rodape 	= "../../".DIR_IMG."logo_rodape.jpeg";

$regTotal = count($ARRAY_EXIBE);
$totalPaginas = intval(ceil($regTotal/$regPorPagina));
$vltotal = 0;

if ($totalPaginas == 0) {
	$totalPaginas = 1;
}


$pdf = new FPDF();
$pdf = new FPDF('P','mm','A4');

$pdf->AliasNbPages();

for ($paginaAtual=1; $paginaAtual <= $totalPaginas; $paginaAtual++) { 

	$pdf->AddPage();

	$pdf->Image($logo_header,10,10,36,10);

	$pdf->SetTextColor($r_danger,$g_danger,$b_danger);
	$pdf->SetFont($font_arial,$style_b,$tam_14);
	$pdf->Cell(190,9,"", 0, 1, 'C');
			
	if ($existeRestricao != false) {
		$pdf->SetTextColor($r_danger,$g_danger,$b_danger); /* Vermelho */
		if (isset($_SESSION['DADOSEXPORT']['somenteDisponiveis']) 
			&& $_SESSION['DADOSEXPORT']['somenteDisponiveis'] == "on") {
			$restricao = "SOMENTE DISPON�VEIS";
		}
	}


	$pdf->Cell(190,5, $restricao , "B", 1, 'C');
	$pdf->Cell(190,2, "" , 0, 1);
	$pdf->SetXY(10,25);


	/*#####################################################################################################*/

	//MONTA O CABE�ALHO DO ORCAMENTO
	$pdf->SetFont($font_arial,$style_n,$tam_10);
	$pdf->SetFillColor($r_lightblue,$g_lightblue,$b_lightblue);
	$pdf->SetTextColor($r_white,$g_white,$b_white);
	$pdf->Cell(90, 5, "Cliente"			  , 1, 0, 'L', 1);
	$pdf->Cell(10, 5, "UF"				  , 1, 0, 'C', 1);
	$pdf->Cell(55, 5, "Vendedor(a)"		  , 1, 0, 'L', 1);
	$pdf->SetFillColor($r_warning,$g_warning,$b_warning);/* Amarelo */
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(35, 5, "N�m Orc."		  , 'LTR', 1, 'C', 1);

	$pdf->SetFont($font_arial,$style_n,$tam_10);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(90, 7, substr($ORC['CLIENTE']['CODCLI']."-".converterUTF8($ORC['CLIENTE']['CLIENTE']),0,40) , 1, 0, 'L');
	$pdf->Cell(10, 7, $ORC['CLIENTE']['UF']	, 1, 0, 'C');
	$pdf->Cell(55, 7, substr($ORC['VENDEDOR']['NOME'],0,25) 		, 1, 0, 'L');
	$pdf->SetFont($font_arial,$style_b,$tam_20);
	$pdf->SetFillColor($r_warning,$g_warning,$b_warning);/* Amarelo */
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(35, 7, $ORC['CAB']['IDORCAMENTO'] , 'LBR', 1, 'C', 1);

	$pdf->SetFont($font_arial,$style_b,$tam_10);
	$pdf->SetFillColor($r_lightblue,$g_lightblue,$b_lightblue);
	$pdf->SetTextColor($r_white,$g_white,$b_white);
	$pdf->Cell(90, 5, "Observa��o" , 1, 0, 'L', 1);
	$pdf->Cell(35, 5, "M�quina" , 1, 0, 'L', 1);
	$pdf->Cell(30, 5, "Ordem Compra"		  , 1, 0, 'L', 1);
	$pdf->Cell(35, 5, "Status" , 1, 1, 'C', 1);

	$pdf->SetFont($font_arial,$style_n,$tam_10);
	$pdf->SetTextColor($r_black,$g_black,$b_black);

	$contato = "";
	$contato .= ($ORC['CAB']['CONTATO'])?" ".$ORC['CAB']['CONTATO']:"";
	$contato .= ($ORC['CAB']['TELCELULAR'])?" ".$ORC['CAB']['TELCELULAR']:"";
	$contato .= ($ORC['CAB']['TELFIXO'])?" ".$ORC['CAB']['TELFIXO']:"";

	$pdf->Cell(90, 7, $ORC['CAB']['OBSENTREGA1'], 1, 0, 'L');
	$pdf->Cell(35, 7, $ORC['CAB']['MAQUINA'], 1, 0, 'L');
	$pdf->Cell(30, 7, $ORC['CAB']['NUMPEDCOMP'] , 1, 0, 'L');
	$pdf->SetFont($font_courier,$style_b,$tam_16);
	switch ($ORC['CAB']['STATUS']) {
		case 'ORCAMENTO':
			$pdf->SetFillColor($r_gray,$gray,$b_gray);
			$pdf->SetTextColor($r_black,$g_black,$b_black);
			break;
		case 'REJEITADO':
			$pdf->SetFillColor($r_danger,$g_danger,$b_danger);
			$pdf->SetTextColor($r_white,$g_white,$b_white);
			break;
		case 'CANCELADO':
			$pdf->SetFillColor($r_danger,$g_danger,$b_danger);
			$pdf->SetTextColor($r_white,$g_white,$b_white);
			break;
		case 'PENDENTE':
			$pdf->SetFillColor($r_warning,$g_warning,$b_warning);
			$pdf->SetTextColor($r_black,$g_black,$b_black);
			break;
		case 'BLOQUEADO':
			$pdf->SetFillColor($r_warning,$g_warning,$b_warning);
			$pdf->SetTextColor($r_black,$g_black,$b_black);
			break;
		case 'MONTADO':
			$pdf->SetFillColor($r_primary,$g_primary,$b_primary);
			$pdf->SetTextColor($r_white,$g_white,$b_white);
			break;
		case 'LIBERADO':
			$pdf->SetFillColor($r_primary,$g_primary,$b_primary);
			$pdf->SetTextColor($r_white,$g_white,$b_white);
			break;
		case 'FATURADO':
			$pdf->SetFillColor($r_success,$g_success,$b_success);
			$pdf->SetTextColor($r_white,$g_white,$b_white);
			break;
		default:
			$pdf->SetFillColor($r_info,$g_info,$b_info);
			$pdf->SetTextColor($r_white,$g_white,$b_white);
			break;
	}
	$pdf->Cell(35, 7, $ORC['CAB']['STATUS'], 1, 1, 'C', 1);

	$pdf->Cell(1, 5, "", 0, 1); /* PULA LINHA */


	/*#####################################################################################################*/
	// //MONTA O CABE�ALHO DOS ITENS
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(7,  $alturaLinhaItem, "Item"				, 'B', 0, 'L');
	$pdf->Cell(25, $alturaLinhaItem, "Pe�a"				, 'B', 0, 'L');
	$pdf->Cell(66, $alturaLinhaItem, "Descri��o"		, 'B', 0, 'L');
	$pdf->Cell(30, $alturaLinhaItem, "Marca/Linha"		, 'B', 0, 'L');
	$pdf->Cell(20, $alturaLinhaItem, "Dispon."			, 'B', 0, 'L');
	$pdf->Cell(7,  $alturaLinhaItem, "Qtd"				, 'B', 0, 'R');
	$pdf->Cell(17, $alturaLinhaItem, "Pre�o Unit."		, 'B', 0, 'R');
	$pdf->Cell(18, $alturaLinhaItem, "Sub Total"		, 'B', 1, 'R');


	$contador = 0;
	
	// varDump2($_SESSION['DADOSEXPORT']); 

	if (count($ARRAY_EXIBE)>0) {
	   // varDump2($ARRAY_EXIBE); 
	   // die();

		foreach ($ARRAY_EXIBE as $key => $item) {

			if (is_null($item['CODPROD'])) {
				$item['CODPROD'] = "";
			}

			$registroInicial = (($paginaAtual-1) * $regPorPagina);
			// varDump2($registroInicial);

			$registroFinal = (($paginaAtual * $regPorPagina)-1);
			if ($registroFinal > $regTotal) {
				$registroFinal = $regTotal;
			}
			// varDump2($registroFinal);

        	if (($key >= $registroInicial) && ($key <= $registroFinal)) {

			   // varDump2($item);
				$contador++;
				$registroAtual = $registroInicial + $contador;

				// $pdf->SetFont($font_courier,$style_b,$tam_9);
				$pdf->SetFont($font_courier,$style_b,$tam_8);
				$pdf->SetTextColor($r_black,$g_black,$b_black);
				if ($registroAtual % 2) {
					$pdf->SetFillColor($r_light,$g_light,$b_light);
				} else {
					$pdf->SetFillColor($r_white,$g_white,$b_white);
				}

				$pdf->Cell(7, $alturaLinhaItem, str_pad($registroAtual, 3, '0', STR_PAD_LEFT), 0, 0, 'L',1);

				$pdf->Cell(25, $alturaLinhaItem, strtoupper(trim($item['CODPECA']))		, 0, 0, 'L',1);

				$pdf->Cell(66, $alturaLinhaItem, substr(strtoupper(trim($item['DESCRICAO'])),0,30) , 0, 0, 'L', 1);
				$pdf->Cell(30, $alturaLinhaItem, strtoupper(trim($item['MARCA'])), 0, 0, 'L',1);
				$pdf->Cell(20, $alturaLinhaItem, $item['DISPONIBILIDADE']		, 0, 0, 'L',1);
				$pdf->Cell(7, $alturaLinhaItem, intval($item['QTPEDIDA'])					  , 0, 0, 'R',1);
				$pdf->Cell(17, $alturaLinhaItem, moeda(floatval($item['PVENDA']),2), 0, 0, 'R',1);
				$pdf->Cell(18, $alturaLinhaItem, moeda((intval($item['QTPEDIDA']) * floatval($item['PVENDA'])),2), 0, 1, 'R',1);
				$vltotal += (intval($item['QTPEDIDA']) * floatval($item['PVENDA']));

			}
		}

	
	}

	if ($totalPaginas == $paginaAtual) {
		$pdf->Cell(1, 5, "", 0, 1); /* PULA LINHA */
		$pdf->SetFont($font_courier,$style_b,$tam_12);
		$pdf->SetTextColor($r_lightblue,$g_lightblue,$b_lightblue);
		$pdf->Cell(100 , 8, "", 0, 0, 'R');
		$pdf->Cell(55 , 8, "Valor total", 'T', 0, 'R');
		$pdf->SetTextColor($r_black,$g_black,$b_black);
		$pdf->Cell(35, 8, moeda($vltotal)		, 'T', 1, 'R');
	}


	$pdf->Image($logo_rodape,7,218,195,50);

	$pdf->SetXY(10, 268);
	$pdf->SetFont($font_arial, $style_n, $tam_9);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(190, 4, "AV. MAX TEIXEIRA, N� 1057, COL�NIA SANTO ANT�NIO, MANAUS/AM, CEP 69093-770", 0, 1, 'C');
	$pdf->SetFont($font_arial, $style_i, $tam_9);
	$pdf->Cell(190, 4, "P�gina ".$paginaAtual."/".$totalPaginas, 0, 1, 'C');
}

//SAIDA DO PDF
$pdf->Output($tipo_pdf, $end_final);
$pdf->Close();
?>