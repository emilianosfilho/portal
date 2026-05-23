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
require_once '../../pages/vendas/function.php';
require_once "../../plugins/code39/code39.php";

// $debug = true;

if (isset($_SESSION['DADOS'])) {
	$dados = $_SESSION['DADOS'];
} else {
	if (!empty($_POST)) {
		$dados = $_POST;
	} else {
		$dados = $_GET;
	}
}
if($debug) varDump2($dados);

if (isset($PDF)) 
	unset($PDF);
$PDF = array();

if($cabecalho  = buscaPCPEDC($dados)){
	$itensPedido = buscaPCPEDIConferencia($dados);
} else {
	$cabecalho   = buscaPCPEDCFV($dados);
	$itensPedido = buscaPCPEDIFV($dados);
}

// Número máximo de itens por página
$maxPorPagina = 25;

// Número máximo de itens na última página
$maxUltimaPagina = 15;

// Divide o array em páginas
$paginas = [];
$totalItens = count($itensPedido);
$totalPaginas = ceil($totalItens / $maxPorPagina);

for ($i = 0; $i < $totalPaginas; $i++) {
  // Calcula o índice inicial da página
  $inicio = $i * $maxPorPagina;

  // Extrai os itens da página atual
  $paginas[$i] = array_slice($itensPedido, $inicio, $maxPorPagina);
}

if (count(end($paginas)) > $maxUltimaPagina) {
	$paginas[] = [];
}

$totalPaginas = count($paginas);

if($debug) varDump2("totalItens ".$totalItens);
if($debug) varDump2("totalPaginas ".$totalPaginas);
if($debug) varDump2($cabecalho); 
if($debug) varDump2($paginas); 
if($debug) die();


/*############################################################################################*/
//TÍTULO DO RELATÓRIO
$titulo = "CONFERÊNCIA E EXPEDIÇÃO";

//ENDEREÇO ONDE SERÁ GERADO O PDF
$end_final = $cabecalho['NUMPED'].'_'.str_replace(" ", "_", $titulo).".pdf";

//LOGO QUE SERÁ COLOCADO NO RELATÓRIO
$logo_header 	= "../../".DIR_IMG."logo_header.png";

### TIPO DO PDF GERADO ###
//I-> envia o arquivo embutido para o navegador. O visualizador de PDF é usado, se disponível.
//D-> enviar para o navegador e forçar o download de um arquivo com o nome fornecido pelo nome.
//F-> salvar em um arquivo local com o nome dado pelo nome (pode incluir um caminho).
//S-> retorna o documento como uma string.
$tipo_pdf = "I";

$alturaLinha = 4.5;

$erro = 0;
$ord = 0;

$pdf = new PDF_Code39();
$pdf = new PDF_Code39('L','mm','A4');

$pdf->AliasNbPages();

$QUANTIDADETOTALPEDIDO =0;
$VALORTOTALDESCONTO =0;
$VALORTOTALPEDIDO =0;
$PESOLIQTOTAL =0;

/*#####################################################################################################*/
foreach ($paginas as $numPagina => $itensPagina) {

	$pdf->AddPage();
	$pdf->Image($logo_header,10,10,50,15);
	$pdf->SetTextColor(139,0,0);
	$pdf->SetFont($font_arial,$style_b,$tam_18);
	$pdf->SetFillColor($r_light,$g_light,$b_light);
	$pdf->Cell(50,15,"", 0, 0);
	$pdf->Cell(170,15, converterUTF8("CONFERÊNCIA E EXPEDIÇÃO"), 0, 0, 'C', 1);
	
	if (empty($cabecalho['NUMPED'])) {
		$pdf->Cell(235, 10, '', 0, 1);
	} else {
		$pdf->Code39(235, 10, $cabecalho['NUMPED'], 1, 15);
		$pdf->Cell(1,20,"", 0, 1);
	}
	
	/*#####################################################################################################*/
	$pdf->setXY(10,33);
	$pdf->SetTextColor($r_black, $g_black, $b_black); /* Preto */
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(15, $alturaLinha, "Cliente:" 				, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(85, $alturaLinha, substr($cabecalho['CODCLI']."- ".converterUTF8($cabecalho['CLIENTE']), 0, 40) 	, 0, 0);
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(10, $alturaLinha, "CNPJ:" 						, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(52, $alturaLinha, $cabecalho['CNPJ']  		, 0, 0);
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(30, $alturaLinha, converterUTF8("Número do Pedido:") 				, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(30, $alturaLinha, $cabecalho['NUMPED']  		, 0, 0);
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(35, $alturaLinha, converterUTF8("Número do Orçamento:") 			, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(20, $alturaLinha, $cabecalho['NUMPEDCLI'], 0, 1, "R");

// 	/*#####################################################################################################*/
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(15, $alturaLinha, converterUTF8("Endereço:") 			, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(85, $alturaLinha, converterUTF8($cabecalho['ENDERECO'])  		, 0, 0);
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(5, $alturaLinha, "Nr:" 			, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(57, $alturaLinha, $cabecalho['NUMERO']  		, 0, 0);
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(10, $alturaLinha, "Bairro:" 			, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(60, $alturaLinha, converterUTF8($cabecalho['BAIRRO'])  		, 0, 0);
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(8, $alturaLinha, "CEP:" 			, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(40, $alturaLinha, $cabecalho['CEPENT']  		, 0, 0);
	$pdf->Cell(1, $alturaLinha,  ""  , 0, 1); // Quebra linha

// 	/*#####################################################################################################*/
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(15, $alturaLinha, "Telefone:" 			, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(85, $alturaLinha, $cabecalho['TELENT']  		, 0, 0);
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(22, $alturaLinha, "Insc. Estadual:" 						, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(40, $alturaLinha, $cabecalho['IESTADUAL']  		, 0, 0);
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(15, $alturaLinha, converterUTF8("Município:") 						, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(55, $alturaLinha, $cabecalho['CIDADE']  		, 0, 0);
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(5, $alturaLinha, "UF:" 						, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(15, $alturaLinha, $cabecalho['UF']  		, 0, 1);

	

// 	/*#####################################################################################################*/
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(15, $alturaLinha, "RCA:" 					, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(85, $alturaLinha, $cabecalho['CODUSUR']."- ".$cabecalho['RCA']  	, 0, 0);
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(15, $alturaLinha, "Data Fat.:" 					, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(47, $alturaLinha, formataDataOracletoBr($cabecalho['DATA'])  	, 0, 0);
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(25, $alturaLinha, "Ordem Compra:" 					, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(45, $alturaLinha, str_replace('# CLIENTE ESPERANDO NO BALCAO # ', '', str_replace('# PEDIDO EM PRATELEIRA # ', '', $cabecalho['OBSENTREGA1'])) , 0, 1);

	
// 	/*#####################################################################################################*/
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(15, $alturaLinha, "Filial:" 					, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(85, $alturaLinha, $cabecalho['CODFILIAL']."- ".$cabecalho['FILIAL']  	, 0, 0);
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(16, $alturaLinha, converterUTF8("Cobrança:") 						, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(46, $alturaLinha, substr($cabecalho['CODCOB']."- ".$cabecalho['COBRANCA'], 0, 40)  		, 0, 0);
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(27, $alturaLinha, "Plano Pagamento:" 						, 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(43, $alturaLinha, $cabecalho['CODPLPAG']."- ".converterUTF8($cabecalho['PLPAG'])  		, 0, 0);
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->Cell(14, $alturaLinha, converterUTF8("Posição:"), 0, 0);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->Cell(34, $alturaLinha, $cabecalho['POSICAO']  , 0, 1);

// 	/*#####################################################################################################*/
	if (isset($cabecalho['OBSERVACAO_PC']) && !empty($cabecalho['OBSERVACAO_PC'])) {
		$pdf->SetFont($font_arial,$style_b,$tam_8);
		$pdf->Cell(25, $alturaLinha, converterUTF8("Motivo Rejeição:"), 0, 0);
		$pdf->SetFont($font_arial,$style_n,$tam_9);
		$pdf->SetTextColor($r_danger,$g_danger,$b_danger);
		$pdf->Cell(255, $alturaLinha, mb_strtoupper(trim($cabecalho['OBSERVACAO_PC']), 'UTF-8') , 1, 1);
	}
	
	$pdf->Cell(280, 1, '' , 'T', 1);
	
	
	// 	/*#####################################################################################################*/
	foreach ($itensPagina as $pos => $item) {

		if ($pos == 0) {
			$pdf->Cell(280, $alturaLinha,  ""  , 0, 1); // Pula 1 linha
			$pdf->SetFont($font_arial,$style_b,$tam_9);
			$pdf->SetTextColor($r_black,$g_black,$b_black);
			$pdf->SetFillColor($r_light,$g_light,$b_light);
			$pdf->Cell(7, $alturaLinha, "#"						, 'B', 0, 0, 1);
			$pdf->Cell(20, $alturaLinha, "Cod VEMAP"				, 'B', 0, 0, 1);
			$pdf->Cell(90, $alturaLinha, converterUTF8("Descrição")			, 'B', 0, 0, 1);
			$pdf->Cell(43, $alturaLinha, "Marca"				, 'B', 0, 0, 1);
			$pdf->Cell(30, $alturaLinha, converterUTF8("Locação")				, 'B', 0, 0, 1);
			$pdf->Cell(10, $alturaLinha, "UN"					, 'B', 0, 0, 1);
			$pdf->Cell(20, $alturaLinha, "Qtd"					, 'B', 0, 'R', 1);
			$pdf->Cell(30, $alturaLinha, converterUTF8("Preço Un")				, 'B', 0, 'R', 1);
			$pdf->Cell(30, $alturaLinha, "Vl. Total"			, 'B', 1, 'R', 1);
		}
		
		// varDump2($item); die();
		if (!empty($item['CODPROD'])) {
			if (isset($cabecalho['OBSERVACAO_PC']) && !empty($cabecalho['OBSERVACAO_PC'])) {
				$pdf->SetFont($font_arial,$style_n,$tam_8);
				$pdf->SetTextColor($r_danger,$g_danger,$b_danger);
				$pdf->Cell(7, $alturaLinha, ++$ord 	, 0, 0);
				$pdf->Cell(20, $alturaLinha, "W".$item['CODPROD']."-".$item['DV']				, 0, 0);
				$pdf->Cell(90, $alturaLinha, $item['PRODUTO']			, 0, 0);
				$pdf->Cell(43, $alturaLinha, $item['MARCA']				, 0, 0);
				$pdf->Cell(120, $alturaLinha, $item['OBSERVACAO_PC']	, 0, 1, 'L');
			} else {
				$pdf->SetFont($font_arial,$style_n,$tam_8);
				$pdf->SetTextColor($r_black,$g_black,$b_black);
				$pdf->Cell(7, $alturaLinha, ++$ord	, 0, 0);
				$pdf->Cell(20, $alturaLinha, "W".$item['CODPROD']."-".$item['DV']				, 0, 0);
				$pdf->Cell(90, $alturaLinha, $item['PRODUTO']			, 0, 0);
				$pdf->Cell(43, $alturaLinha, $item['MARCA']				, 0, 0);
				$pdf->Cell(30, $alturaLinha, $item['LOCACAO']			, 0, 0);
				$pdf->Cell(10, $alturaLinha, $item['UNIDADE']			, 0, 0);
				$pdf->Cell(20, $alturaLinha, $item['QTFATURADA']		, 0, 0, 'R');
				$pdf->Cell(30, $alturaLinha, moeda($item['PVENDA'])			, 0, 0, 'R');
				$pdf->Cell(30, $alturaLinha, moeda($item['VALORTOTAL'])		, 0, 1, 'R');
				$QUANTIDADETOTALPEDIDO 	+= $item['QTFATURADA'];
				$VALORTOTALPEDIDO 		+= $item['VALORTOTAL'];
			}
		}
	}

	/*#####################################################################################################*/
	if ($totalItens == $ord && !$totalExibido) {
		$totalExibido = true;
		$pdf->SetFont('arial','b',10);
		$pdf->SetTextColor($r_black,$g_black,$b_black);
		$pdf->SetFillColor($r_light,$g_light,$b_light);
		$pdf->Cell(210, ($alturaLinha*1.5), "Total:"					, 'T', 0, 'L', 1);
		$pdf->Cell(10, ($alturaLinha*1.5), $QUANTIDADETOTALPEDIDO 	, 'T', 0, 'R', 1);
		$pdf->Cell(30, ($alturaLinha*1.5), "" 						, 'T', 0, 'R', 1);
		$pdf->Cell(30, ($alturaLinha*1.5), moeda($VALORTOTALPEDIDO)	, 'T', 0, 'R', 1);
	}

	if ($totalPaginas == ($numPagina+1)) {
		
		$pdf->Cell(1, ($alturaLinha*2), "", 0, 1);

		$pdf->SetFont($font_arial,$style_b,$tam_8);
		$pdf->Cell(17, $alturaLinha, "Emitente:" 			, 0, 0);
		$pdf->SetFont($font_arial,$style_n,$tam_10);
		$pdf->Cell(213, $alturaLinha, converterUTF8($cabecalho['EMITENTE'])  		, 0, 0);
		$pdf->SetFont($font_arial,$style_b,$tam_8);
		$pdf->Cell(20, $alturaLinha, "Vl. Pedido:" 			, 0, 0, 'R');
		$pdf->SetFont($font_arial,$style_n,$tam_10);
		$pdf->Cell(30, $alturaLinha, moeda($VALORTOTALPEDIDO)  		, 0, 1, 'R');

		$pdf->SetFont($font_arial,$style_b,$tam_8);
		$pdf->Cell(25, $alturaLinha, "Transportador:" 			, 0, 0);
		$pdf->SetFont($font_arial,$style_n,$tam_10);
		$pdf->Cell(105, $alturaLinha, $cabecalho['TRANSPORTADORA']  		, 0, 0);
		$pdf->SetFont($font_arial,$style_b,$tam_8);
		$pdf->Cell(30, $alturaLinha, "Frete Despacho:" 			, 0, 0);
		$pdf->SetFont($font_arial,$style_n,$tam_10);
		$pdf->Cell(20, $alturaLinha, $cabecalho['FRETEDESPACHO']  		, 0, 0);
		$pdf->SetFont($font_arial,$style_b,$tam_8);
		$pdf->Cell(30, $alturaLinha, "Frete Redespacho:" 			, 0, 0);
		$pdf->SetFont($font_arial,$style_n,$tam_10);
		$pdf->Cell(20, $alturaLinha, $cabecalho['FRETEREDESPACHO']  		, 0, 0);
		$pdf->SetFont($font_arial,$style_b,$tam_8);
		$pdf->Cell(20, $alturaLinha, "Vl. Frete:" 			, 0, 0, 'R');
		$pdf->SetFont($font_arial,$style_n,$tam_10);
		$pdf->Cell(30, $alturaLinha, moeda($cabecalho['VLFRETE'])  		, 0, 1, 'R');

		$pdf->SetFont($font_arial,$style_b,$tam_8);
		$pdf->Cell(30, $alturaLinha, converterUTF8("Observações: ") 			, 0, 0);
		$pdf->SetFont($font_arial,$style_n,$tam_10);
		$pdf->Cell(200, $alturaLinha, converterUTF8($cabecalho['OBSENTREGA1']).converterUTF8($cabecalho['OBSENTREGA2']) , 1, 0);
		$pdf->SetFont($font_arial,$style_b,$tam_8);
		$pdf->Cell(20, $alturaLinha, "Vl. Total:" 			, 0, 0, 'R');
		$pdf->SetFont($font_arial,$style_n,$tam_10);
		$pdf->Cell(30, $alturaLinha, moeda($VALORTOTALPEDIDO + $cabecalho['VLFRETE'])	, 0, 0, 'R');


		$pdf->Cell(280, 30, "", 0, 1);
		$pdf->SetFont($font_arial,$style_n,$tam_8);
		$pdf->Cell(100, $alturaLinha, converterUTF8("SEPARADOR: ".$cabecalho['SEPARADOR']), 'T', 0, 'C');
		$pdf->Cell(80, $alturaLinha, '', 0, 0);
		$pdf->Cell(100, $alturaLinha, converterUTF8("CONFERENTE"), 'T', 1, 'C');
		
	}


	/*#####################################################################################################*/
	$pdf->setXY(10,185);
	$pdf->SetFont($font_arial,$style_n,$tam_8);
	$pdf->SetTextColor(0,0,0);
	$pdf->Cell(140, $alturaLinha, "Arquivo gerado em: ".@date('d/m/Y H:i:s'), 'T', 0, 'L');
	$pdf->Cell(140, $alturaLinha, converterUTF8("Página ".($numPagina+1)." de ".$totalPaginas), 'T', 1, 'R');
}


//SAIDA DO PDF
$pdf->Output($tipo_pdf, $end_final);
$pdf->Close();
redireciona($end_final);
?>
