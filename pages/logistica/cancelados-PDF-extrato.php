<?php 
session_start();
ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL ^ E_NOTICE);
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE));
date_default_timezone_set("America/Manaus");
require_once("../../pages/conf/define.php");
require_once("../../pages/conf/functions.php");
require_once("../../pages/conf/conectaOracle.php");
require_once("../../pages/logistica/function.php");
require_once("../../plugins/fpdf/stylesheet.php");
require_once("../../plugins/fpdf/fpdf.php");
require_once("../../plugins/fpdf/code39.php");


//TÍTULO DO RELATÓRIO
$titulo = "EXTRATO PRODUTOS CANCELADOS ";

//ENDEREÇO ONDE SERÁ GERADO O PDF
$end_final = str_replace(" ", "_", mb_strtolower($titulo)).".pdf";

### TIPO DO PDF GERADO ###
//I-> envia o arquivo embutido para o navegador. O visualizador de PDF é usado, se disponível.
//D-> enviar para o navegador e forçar o download de um arquivo com o nome fornecido pelo nome.
//F-> salvar em um arquivo local com o nome dado pelo nome (pode incluir um caminho).
//S-> retorna o documento como uma string.
$tipo_pdf = "I";

//NUMERO DE RESULTADOS POR PÁGINA
$regPorPagina = 30;
$alturaLinhaItem = 6;
$erro = 0;

//LOGO QUE SERÁ COLOCADO NO RELATÓRIO
$logo_header 	= "../../".DIR_IMG."logo_header.png";
$logo_rodape 	= "../../".DIR_IMG."logo_rodape.jpeg";

$existeRestricao = false;
$restricao = "";

$regTotal = count($_SESSION['CANCELADOS']);
$totalPaginas = intval(ceil($regTotal/$regPorPagina));
$vltotal = 0;

$ARRAY_RCA = array();
$contPag=0;
$contReg=0;


foreach ($_SESSION['CANCELADOS'] as $key => $value) {
	$ARRAY_EXIBE[$value['CODUSUR']][] = $value;
}


// foreach ($_SESSION['CANCELADOS'] as $key => $value) {
// 	if ($contReg==$regPorPagina) {
// 		$contPag++;
// 	}
// 	$ARRAY_EXIBE[$contPag][] = $value;
// 	$contReg++;
// }
// varDump2($ARRAY_EXIBE);

$contadorGeral = 0;

$pdf = new FPDF();
$pdf = new FPDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetXY(10, 10);
$pdf->Cell(60, 13, "" , 0, 0);
$pdf->SetFont($font_arial,$style_b,$tam_16);
$pdf->SetTextColor($r_black,$g_black,$b_black);
$pdf->Cell(210, 13, $titulo , 0, 1);

$pdf->Image('../../dist/img/logo_header.png', 10, 10, 50, 13);


foreach ($ARRAY_EXIBE as $paginaAtual => $pagina) {
	
	$pdf->Cell(270,5, "", 0, 1); // PULA LINHA

	//MONTA O CABEÇALHO DO ORCAMENTO
	$pdf->SetFont($font_arial,$style_b,$tam_10);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->SetFillColor($r_light,$g_light,$b_light);
	$pdf->Cell(20, 8, "RCA:" , 'B', 0, 'L');
	$pdf->Cell(100, 8, $pagina[0]['CODUSUR'].' '.$pagina[0]['RCA'] , 'B', 0, 'L');
	$pdf->Cell(20, 8, "DATA:" , 'B', 0, 'L');
	$pdf->Cell(130, 8, formataDataOracletoBr($pagina[0]['DATACANC']) , 'B', 1, 'L');

	$pdf->Cell(270,5, "", 0, 1); // PULA LINHA

	//MONTA O CABEÇALHO DOS ITENS
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->SetFillColor($r_light,$g_light,$b_light);
	$pdf->Cell(10, 8, "#"			  		, 'B', 0, 'L', 1);
	$pdf->Cell(20, 8, "CODPROD"		  		, 'B', 0, 'L', 1);
	$pdf->Cell(50, 8, "DESCRIÇÃO"		  	, 'B', 0, 'L', 1);
	$pdf->Cell(30, 8, "MARCA"		  		, 'B', 0, 'L', 1);
	$pdf->Cell(120, 8, "MOTIVO"		  		, 'B', 0, 'L', 1);
	$pdf->Cell(20, 8, "LOCAÇÃO"		  		, 'B', 0, 'C', 1);
	$pdf->Cell(20, 8, "QT"		  			, 'B', 1, 'C', 1);


	foreach ($pagina as $itemAtual => $item) {
		$contadorGeral++;
		// varDump2($item); die();

		$pdf->SetFont($font_helvetica,$style_n,$tam_8);
		$pdf->SetFillColor($r_white,$g_white,$b_white);
		$pdf->SetTextColor($r_black,$g_black,$b_black);
		$pdf->Cell(10, 8, $contadorGeral 						, 'B', 0, 'L');
		$pdf->Cell(20, 8, 'W'.$item['CODPROD'].'-'.$item['DV'] 	, 'B', 0, 'L');
		$pdf->Cell(50, 8, $item['DESCRICAO'] 					, 'B', 0, 'L');
		$pdf->Cell(30, 8, $item['MARCA'] 						, 'B', 0, 'L');
		$pdf->Cell(120, 8, $item['MOTIVO'] 						, 'B', 0, 'L');
		$pdf->Cell(20, 8, $item['LOCACAO'] 						, 'B', 0, 'C');
		$pdf->Cell(20, 8, $item['QT'] 							, 'B', 1, 'C');
	
	}

}



//SAIDA DO PDF
$pdf->Output($tipo_pdf, $end_final);
$pdf->Close();




// 	/*#####################################################################################################*/

// 	//MONTA O CABEÇALHO DO ORCAMENTO
// 	$pdf->SetFont($font_arial,$style_n,$tam_10);
// 	$pdf->SetFillColor($r_lightblue,$g_lightblue,$b_lightblue);
// 	$pdf->SetTextColor($r_white,$g_white,$b_white);
// 	$pdf->Cell(90, 5, "Cliente"			  , 1, 0, 'L', 1);
// 	$pdf->Cell(10, 5, "UF"				  , 1, 0, 'C', 1);
// 	$pdf->Cell(55, 5, "Vendedor(a)"		  , 1, 0, 'L', 1);
// 	$pdf->SetFillColor($r_warning,$g_warning,$b_warning);/* Amarelo */
// 	$pdf->SetTextColor($r_black,$g_black,$b_black);
// 	$pdf->Cell(35, 5, "Núm Orc."		  , 'LTR', 1, 'C', 1);

// 	$pdf->SetFont($font_arial,$style_n,$tam_10);
// 	$pdf->SetTextColor($r_black,$g_black,$b_black);

// 	$pdf->Cell(35, 7, $INVENTARIO['CAB']['IDINVENTARIO'] , 'LBR', 1, 'C', 1);

// 	$pdf->SetFont($font_arial,$style_b,$tam_10);
// 	$pdf->SetFillColor($r_lightblue,$g_lightblue,$b_lightblue);
// 	$pdf->SetTextColor($r_white,$g_white,$b_white);
// 	$pdf->Cell(90, 5, "Contato" , 1, 0, 'L', 1);
// 	$pdf->Cell(35, 5, "Equipamento" , 1, 0, 'L', 1);
// 	$pdf->Cell(30, 5, "Ordem Compra"		  , 1, 0, 'L', 1);
// 	$pdf->Cell(35, 5, "Status" , 1, 1, 'C', 1);

// 	$pdf->Cell(1, 2, "", 0, 1); /* PULA LINHA */

// 	/*#####################################################################################################*/
// 	$tamanhoDescricao = 98;

// 	// //MONTA O CABEÇALHO DOS ITENS
// 	$pdf->SetFont($font_arial,$style_b,$tam_8);
// 	$pdf->SetFillColor($r_lightblue,$g_lightblue,$b_lightblue);
// 	$pdf->SetTextColor($r_white,$g_white,$b_white);
// 	$pdf->Cell(7, $alturaLinhaItem, "Item"							, 1, 0, 'L', 1);
// 	$pdf->Cell($tamanhoDescricao, $alturaLinhaItem, "Descrição"		, 1, 0, 'L', 1);
// 	$pdf->Cell(23, $alturaLinhaItem, "Marca/Linha"								, 1, 0, 'L', 1);
// 	$pdf->Cell(20, ($alturaLinhaItem/2), "Dispon."						, 'RLT', 0, 'L', 1);
// 	$pdf->Cell(7, $alturaLinhaItem, "Qtd"						, 1, 0, 'R', 1);
// 	$pdf->Cell(17, $alturaLinhaItem, "Preço Unit."			   		, 1, 0, 'R', 1);
// 	$pdf->Cell(18, $alturaLinhaItem, "Sub Total"							, 1, 1, 'R', 1);
// 	$pdf->Cell(20, ($alturaLinhaItem/2), "Manaus-AM"					, 'RLB', 1, 'C', 1);


// 	$contador = 0;
	
// 	// varDump2($_SESSION['DADOSEXPORT']); 

// 	if (count($ARRAY_EXIBE)>0) {
// 		 // varDump2($ARRAY_EXIBE); 
// 		 // die();

// 		foreach ($ARRAY_EXIBE as $key => $item) {

// 			if (is_null($item['CODPROD'])) {
// 				$item['CODPROD'] = "";
// 			}

// 			$registroInicial = (($paginaAtual) * $regPorPagina);
// 			// varDump2($registroInicial);

// 			$registroFinal = (($paginaAtual * $regPorPagina)-1);
// 			if ($registroFinal > $regTotal) {
// 				$registroFinal = $regTotal;
// 			}
// 			// varDump2($registroFinal);

// 					if (($key >= $registroInicial) && ($key <= $registroFinal)) {

// 				 // varDump2($item);
// 				$contador++;
// 				$registroAtual = $registroInicial + $contador;

// 				if (isset($_SESSION['DADOSEXPORT']['incluirCST']) || isset($_SESSION['DADOSEXPORT']['incluirNCM'])) {
// 					if (isset($item['CODPROD']) && $item['CODPROD'] != "") {
// 						$tribProd = buscaTribProd($item['CODPROD'], $INVENTARIO['CLIENTE']['UF'], 1);
// 					} else {
// 						$tribProd['CST'] = "";
// 						$tribProd['NCM'] = "";
// 					}
// 				}

// 				// $pdf->SetFont($font_courier,$style_b,$tam_9);
// 				$pdf->SetFont($font_courier,$style_b,$tam_8);
// 				$pdf->SetTextColor($r_black,$g_black,$b_black);
// 				if ($registroAtual % 2) {
// 					$pdf->SetFillColor($r_light,$g_light,$b_light);
// 				} else {
// 					$pdf->SetFillColor($r_white,$g_white,$b_white);
// 				}

// 				$pdf->Cell(7, $alturaLinhaItem, str_pad($registroAtual, 3, '0', STR_PAD_LEFT), 1, 0, 'L',1);

// 				if (isset($_SESSION['DADOSEXPORT']['incluirCodpeca'])) {
// 					$pdf->Cell(20, $alturaLinhaItem, strtoupper(trim($item['CODPECA']))		, 1, 0, 'L',1);
// 				}

// 				if (isset($_SESSION['DADOSEXPORT']['incluirCodprod'])) {
// 					$pdf->Cell(11, $alturaLinhaItem, strtoupper(trim($item['CODPROD']))		, 1, 0, 'L',1);
// 				}

// 				if (isset($_SESSION['DADOSEXPORT']['incluirLocacao'])) {
// 					$pdf->Cell(13, $alturaLinhaItem, ($item['LOCACAO'])?strtoupper(trim($item['LOCACAO'])):""		, 1, 0, 'L',1);
// 				}

// 				if (isset($_SESSION['DADOSEXPORT']['incluirCST'])) {
// 					$pdf->Cell(8, $alturaLinhaItem, strtoupper(trim($tribProd['CST']))			, 1, 0, 'L',1);
// 				}

// 				if (isset($_SESSION['DADOSEXPORT']['incluirNCM'])) {
// 					$pdf->Cell(15, $alturaLinhaItem, strtoupper(trim($tribProd['NCM']))		, 1, 0, 'L',1);
// 				}

// 				$pdf->Cell($tamanhoDescricao, $alturaLinhaItem, substr(strtoupper(trim($item['DESCRICAO'])),0,30) , 1, 0, 'L', 1);
// 				$pdf->Cell(23, $alturaLinhaItem, strtoupper(trim($item['MARCA'])), 1, 0, 'L',1);
// 				$pdf->Cell(20, $alturaLinhaItem, $item['DISPONIBILIDADE']		, 1, 0, 'L',1);
// 				$pdf->Cell(7, $alturaLinhaItem, intval($item['QTPEDIDA'])					  , 1, 0, 'R',1);
// 				$pdf->Cell(17, $alturaLinhaItem, moeda(floatval($item['PVENDA']),2), 1, 0, 'R',1);
// 				$pdf->Cell(18, $alturaLinhaItem, moeda((intval($item['QTPEDIDA']) * floatval($item['PVENDA'])),2), 1, 1, 'R',1);
// 				$vltotal += (intval($item['QTPEDIDA']) * floatval($item['PVENDA']));

// 			}
// 		}

	
// 	}

// 	if ($totalPaginas == $paginaAtual) {
// 		$pdf->SetFont($font_courier,$style_b,$tam_12);
// 		$pdf->SetTextColor($r_lightblue,$g_lightblue,$b_lightblue);
// 		$pdf->Cell(155 , 8, "Valor total", 0, 0, 'R');
// 		$pdf->SetFillColor($r_lightblue,$g_lightblue,$b_lightblue);
// 		$pdf->SetTextColor($r_white,$g_white,$b_white);
// 		$pdf->Cell(35, 8, moeda($vltotal)		, 1, 1, 'R', 1);
// 	}


// 	$pdf->Image($logo_rodape,7,218,195,50);

// 	$pdf->SetXY(10, 268);
// 	$pdf->SetFont($font_arial, $style_n, $tam_9);
// 	$pdf->SetTextColor($r_black,$g_black,$b_black);
// 	$pdf->Cell(270, 4, "AV. MAX TEIXEIRA, Nº 1057, COLÔNIA SANTO ANTÔNIO, MANAUS/AM, CEP 69093-770", 0, 1, 'C');
// 	$pdf->SetFont($font_arial, $style_i, $tam_9);
// 	$pdf->Cell(270, 4, "Página ".$paginaAtual."/".$totalPaginas, 0, 1, 'C');
// }
