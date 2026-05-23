<?php 
session_start();
ini_set("xdebug.var_display_max_depth", -1);
ini_set("xdebug.var_display_max_children", -1);
ini_set("xdebug.var_display_max_data", -1);
ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ E_NOTICE ^ E_DEPRECATED);
ini_set('memory_limit', '24G');
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
require_once "../../pages/conf/define.php";
require_once "../../pages/conf/functions.php";
require_once "../../pages/conf/conectaOracle.php";
require_once '../../pages/vendas/function.php';
require_once "../../plugins/code39/code39.php";

$debug = false;
// $debug = true;

$dados = $_GET;

if($debug) varDump2($dados);

$PDF['CABECALHO'] = buscaPCPEDC($dados);
$PDF['ITENS'] = buscaPCPEDI($dados);
if($debug) varDump2($PDF);

$NUMPEDRCA = $PDF['CABECALHO']['NUMPEDRCA'];
if($debug) varDump2('NUMPEDRCA: '.$NUMPEDRCA);


//T�TULO DO RELAT�RIO
$titulo = "PEDIDO VENDA";

//ENDERE�O ONDE SER� GERADO O PDF
$end_final = $PDF['CABECALHO']['NUMPED'].'_'.str_replace(" ", "_", $titulo).".pdf";

### TIPO DO PDF GERADO ###
//I-> envia o arquivo embutido para o navegador. O visualizador de PDF � usado, se dispon�vel.
//D-> enviar para o navegador e for�ar o download de um arquivo com o nome fornecido pelo nome.
//F-> salvar em um arquivo local com o nome dado pelo nome (pode incluir um caminho).
//S-> retorna o documento como uma string.
$tipo_pdf = "I";

//NUMERO DE RESULTADOS POR P�GINA
$alturaLinha	= 5;
$regPorPagina 	= 20;

$regTotal 	= count($PDF['ITENS']);
if($debug) varDump2("regTotal ".$regTotal);

$totalPaginas = intval(ceil($regTotal/$regPorPagina));
if($debug) varDump2("totalPaginas ".$totalPaginas);

// varDump2($totalPaginas); die();
$erro = 0;

//LOGO QUE SER� COLOCADO NO RELAT�RIO
$logo_header 	= "../../".DIR_IMG."logo_header.png";


/*############################################################################################*/
$pdf = new PDF_Code39();
$pdf = new PDF_Code39('L','mm','A4');

$pdf->AliasNbPages();

$QUANTIDADETOTALPEDIDO =0;
$VALORTOTALDESCONTO =0;
$VALORTOTALPEDIDO =0;
$PESOLIQTOTAL =0;

/*#####################################################################################################*/
for ($paginaAtual=1; $paginaAtual <= $totalPaginas; $paginaAtual++) { 
	$pdf->AddPage();
	$pdf->Image($logo_header,10,10,40,10);
	$pdf->SetTextColor(139,0,0);
	$pdf->SetFont($font_arial,$style_b,$tam_14);
	$pdf->Cell(280,15,"317 - Emitir Pedido de venda", 0, 1, 'C');
	$pdf->SetFillColor(255,255,255);/* Branco */
	$pdf->SetTextColor(0,0,0); /* Preto */
	
// 	/*#####################################################################################################*/
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(12, $alturaLinha, "Cliente:" 				, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(88, $alturaLinha, substr($PDF['CABECALHO']['CODCLI']."- ".$PDF['CABECALHO']['CLIENTE'], 0, 40) 	, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(30, $alturaLinha, "Num. OR�AMENTO:" 				, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(30, $alturaLinha, $PDF['CABECALHO']['NUMPEDCLI']  		, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(20, $alturaLinha, "Num. PEDIDO:" 				, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(20, $alturaLinha, $PDF['CABECALHO']['NUMPED']  		, 0, 0);
	// $pdf->SetFont('arial','b',8);
	// $pdf->Cell(27, $alturaLinha, "Num. Pedido RCA:" 			, 0, 0);
	// $pdf->SetFont('arial','',8);
	// $pdf->Cell(20, $alturaLinha, $PDF['CABECALHO']['NUMPEDRCA']  	, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(22, $alturaLinha, "Carregamento:" 						, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(20, $alturaLinha, $PDF['CABECALHO']['NUMCAR']  		, 0, 0);
	$pdf->Cell(1, $alturaLinha,  ""  , 0, 1);
	$pdf->Code39(250, 20, $PDF['CABECALHO']['NUMPEDCLI'], 1, 10);

// 	/*#####################################################################################################*/
// 	//MONTA O CABE�ALHO DO ORCAMENTO
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(10, $alturaLinha, "CNPJ:" 						, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(40, $alturaLinha, $PDF['CABECALHO']['CNPJ']  		, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(20, $alturaLinha, "Insc. Estadual:" 						, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(30, $alturaLinha, $PDF['CABECALHO']['IESTADUAL']  		, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(15, $alturaLinha, "Munic�pio:" 						, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(44, $alturaLinha, $PDF['CABECALHO']['CIDADE']  		, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(5, $alturaLinha, "UF:" 						, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(15, $alturaLinha, $PDF['CABECALHO']['UF']  		, 0, 1);

	
// 	/*#####################################################################################################*/
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(15, $alturaLinha, "Endere�o:" 			, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(80, $alturaLinha, $PDF['CABECALHO']['ENDERECO']  		, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(12, $alturaLinha, "N�mero:" 			, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(20, $alturaLinha, $PDF['CABECALHO']['NUMERO']  		, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(10, $alturaLinha, "Bairro:" 			, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(40, $alturaLinha, $PDF['CABECALHO']['BAIRRO']  		, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(8, $alturaLinha, "CEP:" 			, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(30, $alturaLinha, $PDF['CABECALHO']['CEPENT']  		, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(20, $alturaLinha, "Telefone:" 			, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(44, $alturaLinha, $PDF['CABECALHO']['TELENT']  		, 0, 0);
	$pdf->Cell(1, $alturaLinha,  ""  , 0, 1); // Quebra linha
	
// 	/*#####################################################################################################*/
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(20, $alturaLinha, "Cobran�a:" 						, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(50, $alturaLinha, substr($PDF['CABECALHO']['CODCOB']."- ".$PDF['CABECALHO']['COBRANCA'], 0, 40)  		, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(25, $alturaLinha, "Plano Pagamento:" 						, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(50, $alturaLinha, $PDF['CABECALHO']['CODPLPAG']."- ".$PDF['CABECALHO']['PLPAG']  		, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(24, $alturaLinha, "Prazo M�dio:" 						, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(10, $alturaLinha, $PDF['CABECALHO']['PRAZOMEDIO']  		, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(20, $alturaLinha, "Prazo Adic.:" 						, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(10, $alturaLinha, $PDF['CABECALHO']['PRAZOADICIONAL']  		, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(10, $alturaLinha, "Prazo:" 						, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(60, $alturaLinha, $PDF['CABECALHO']['PRAZOPAGAMENTO']  		, 0, 0);
	$pdf->Cell(1, $alturaLinha,  ""  , 0, 1); // Quebra linha


	$pdf->SetFont('arial','b',8);
	$pdf->Cell(10, $alturaLinha, "Data:" 					, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(20, $alturaLinha, formataDataOracletoBr($PDF['CABECALHO']['DATA'])  	, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(10, $alturaLinha, "Filial:" 					, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(10, $alturaLinha, $PDF['CABECALHO']['CODFILIAL']  	, 0, 0);
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(10, $alturaLinha, "RCA:" 					, 0, 0);
	$pdf->SetFont('arial','',8);
	$pdf->Cell(90, $alturaLinha, $PDF['CABECALHO']['CODUSUR']."- ".$PDF['CABECALHO']['RCA']  	, 0, 1);

	
// 	/*#####################################################################################################*/
	$pdf->Cell(280, $alturaLinha,  ""  , 0, 1); // Pula 1 linha
	$pdf->SetFont('arial','b',8);
	$pdf->Cell(5, $alturaLinha, "#"					, 'B', 0);
	$pdf->Cell(13, $alturaLinha, "Cod"					, 'B', 0);
	$pdf->Cell(5, $alturaLinha, "DV"					, 'B', 0);
	$pdf->Cell(42, $alturaLinha, "Descri��o"			, 'B', 0);
	$pdf->Cell(20, $alturaLinha, "Marca"				, 'B', 0);
	$pdf->Cell(20, $alturaLinha, "Loca��o"				, 'B', 0);
	$pdf->Cell(20, $alturaLinha, "NumOriginal"			, 'B', 0);
	$pdf->Cell(7, $alturaLinha, "UN"					, 'B', 0);
	$pdf->Cell(13, $alturaLinha, "Qt.(Un)"				, 'B', 0);
	$pdf->Cell(20, $alturaLinha, "P.Tabela"			, 'B', 0);
	$pdf->Cell(20, $alturaLinha, "%Desc"				, 'B', 0);
	$pdf->Cell(20, $alturaLinha, "Vl. Desc"			, 'B', 0);
	$pdf->Cell(15, $alturaLinha, "P.L�quido"			, 'B', 0);
	$pdf->Cell(20, $alturaLinha, "Tot.Desc"			, 'B', 0);
	$pdf->Cell(20, $alturaLinha, "Vl. Total"			, 'B', 0);
	$pdf->Cell(20, $alturaLinha, "Saldo"				, 'B', 0);
	$pdf->Cell(1, $alturaLinha,  ""  , 0, 1); // Quebra linha
	
// 	/*#####################################################################################################*/
	if($debug) varDump2("###########################################");
	if($debug) varDump2("paginaAtual ".$paginaAtual);
	
	
	// 	/*#####################################################################################################*/
	$reginicio = (($paginaAtual-1)*$regPorPagina);
	if($debug) varDump2("reginicio ".$reginicio);
	$regfim = (($paginaAtual*$regPorPagina)-1);
	if ($regfim > $regTotal) {
		$regfim = ($regTotal-1);
	}
	if($debug) varDump2("regfim ".$regfim);
	if($debug) varDump2("----------------");


	for ($l=$reginicio; $l <= $regfim; $l++) { 
		$regAtual = ($l+1);
		if($debug) varDump2($regAtual);
		// if($debug) varDump2($PDF['ITENS'][$l]);
		$pdf->SetFont('arial','',7);
		$pdf->Cell(5, $alturaLinha, $regAtual 			, 0, 0);
		$pdf->Cell(13, $alturaLinha, $PDF['ITENS'][$l]['CODPROD']			, 0, 0);
		$pdf->Cell(5, $alturaLinha, $PDF['ITENS'][$l]['DV']				, 0, 0);
		$pdf->Cell(42, $alturaLinha, $PDF['ITENS'][$l]['PRODUTO']			, 0, 0);
		$pdf->Cell(20, $alturaLinha, $PDF['ITENS'][$l]['MARCA']			, 0, 0);
		$pdf->Cell(20, $alturaLinha, $PDF['ITENS'][$l]['LOCACAO']			, 0, 0);
		$pdf->Cell(20, $alturaLinha, $PDF['ITENS'][$l]['NUMORIGINAL']		, 0, 0);
		$pdf->Cell(7, $alturaLinha, $PDF['ITENS'][$l]['UNIDADE']			, 0, 0);
		$pdf->Cell(13, $alturaLinha, $PDF['ITENS'][$l]['QTFATURADA']		, 0, 0);
		$pdf->Cell(20, $alturaLinha, $PDF['ITENS'][$l]['PTABELA']			, 0, 0);
		$pdf->Cell(20, $alturaLinha, percentual($PDF['ITENS'][$l]['PERCDESC'])		, 0, 0);
		$pdf->Cell(20, $alturaLinha, $PDF['ITENS'][$l]['TOTALDESC']		, 0, 0);
		$pdf->Cell(15, $alturaLinha, $PDF['ITENS'][$l]['PESOLIQ']			, 0, 0);
		$pdf->Cell(20, $alturaLinha, $PDF['ITENS'][$l]['TOTALDESC']		, 0, 0);
		$pdf->Cell(20, $alturaLinha, $PDF['ITENS'][$l]['VALORTOTAL']		, 0, 0);
		$pdf->Cell(20, $alturaLinha, $PDF['ITENS'][$l]['SALDO']			, 0, 0);
		$pdf->Cell(1, $alturaLinha,  ""  , 0, 1); // Quebra linha
		$QUANTIDADETOTALPEDIDO += $PDF['ITENS'][$l]['QTFATURADA'];
		$VALORTOTALDESCONTO += $PDF['ITENS'][$l]['TOTALDESC'];
		$VALORTOTALPEDIDO += $PDF['ITENS'][$l]['VALORTOTAL'];
		$PESOLIQTOTAL += $PDF['ITENS'][$l]['PESOLIQ'];
	}

// 	/*#####################################################################################################*/
	if ($regfim == ($regTotal-1)) {
		$pdf->SetFont('arial','b',8);
		$pdf->Cell(127, $alturaLinha, "Total:     "			, 'T', 0, 'R');
		$pdf->Cell(13, $alturaLinha, $QUANTIDADETOTALPEDIDO 	, 'T', 0);
		$pdf->Cell(20, $alturaLinha, ""			 			, 'T', 0);
		$pdf->Cell(20, $alturaLinha, percentual($VALORTOTALDESCONTO / $VALORTOTALPEDIDO)	, 'T', 0);
		$pdf->Cell(20, $alturaLinha, ""				, 'T', 0);
		$pdf->Cell(20, $alturaLinha, $PESOLIQTOTAL				, 'T', 0);
		$pdf->Cell(20, $alturaLinha, moeda($VALORTOTALDESCONTO)	, 'T', 0);
		$pdf->Cell(20, $alturaLinha, moeda($VALORTOTALPEDIDO)			, 'T', 0);
		$pdf->Cell(20, $alturaLinha, "", 'T', 0);
		$pdf->Cell(1, $alturaLinha,  ""  , 0, 1); // Quebra linha
		$pdf->Cell(1, $alturaLinha,  ""  , 0, 1); // Quebra linha

	// 	/*#####################################################################################################*/
		$pdf->SetFont('arial','b',9);
		$pdf->Cell(15, $alturaLinha, "Emitente:" 			, 0, 0);
		$pdf->SetFont('arial','',9);
		$pdf->Cell(165, $alturaLinha, converterUTF8($PDF['CABECALHO']['EMITENTE'])  		, 0, 0);
		$pdf->SetFont('arial','b',9);
		$pdf->Cell(25, $alturaLinha, "Vl. Pedido:" 			, 0, 0, 'R');
		$pdf->SetFont('arial','',9);
		$pdf->Cell(60, $alturaLinha, "R$ ".moeda($VALORTOTALPEDIDO)  		, 0, 0);
		$pdf->Cell(1, $alturaLinha,  ""  , 0, 1); // Quebra linha

	// 	/*#####################################################################################################*/
		$pdf->SetFont('arial','b',9);
		$pdf->Cell(20, $alturaLinha, "Transportador:" 			, 0, 0);
		$pdf->SetFont('arial','',9);
		$pdf->Cell(60, $alturaLinha, $PDF['CABECALHO']['TRANSPORTADORA']  		, 0, 0);
		$pdf->SetFont('arial','b',9);
		$pdf->Cell(30, $alturaLinha, "Frete Despacho:" 			, 0, 0);
		$pdf->SetFont('arial','',9);
		$pdf->Cell(20, $alturaLinha, $PDF['CABECALHO']['FRETEDESPACHO']  		, 0, 0);
		$pdf->SetFont('arial','b',9);
		$pdf->Cell(30, $alturaLinha, "Frete Redespacho:" 			, 0, 0);
		$pdf->SetFont('arial','',9);
		$pdf->Cell(20, $alturaLinha, $PDF['CABECALHO']['FRETEREDESPACHO']  		, 0, 0);
		$pdf->SetFont('arial','b',9);
		$pdf->Cell(25, $alturaLinha, "Vl. Frete:" 			, 0, 0, 'R');
		$pdf->SetFont('arial','',9);
		$pdf->Cell(60, $alturaLinha, "R$ ".moeda($PDF['CABECALHO']['VLFRETE'])  		, 0, 0);
		$pdf->Cell(1, $alturaLinha,  ""  , 0, 1); // Quebra linha

	// 	/*#####################################################################################################*/
		$pdf->SetFont('arial','b',9);
		$pdf->Cell(20, $alturaLinha, "Observa��es:" 			, 0, 0);
		$pdf->SetFont('arial','',9);
		$pdf->Cell(160, $alturaLinha, $PDF['CABECALHO']['OBSERVACAO'].' [## '.converterUTF8($PDF['CABECALHO']['OBSENTREGA1']).' ##]'.' [## '.converterUTF8($PDF['CABECALHO']['OBSENTREGA2']).' ##]'  		, 0, 0);
		$pdf->SetFont('arial','b',9);
		$pdf->Cell(25, $alturaLinha, "Vl. Total:" 			, 0, 0, 'R');
		$pdf->SetFont('arial','',9);
		$pdf->Cell(60, $alturaLinha, "R$ ".moeda($VALORTOTALPEDIDO + $PDF['CABECALHO']['VLFRETE'])  		, 0, 0);
		$pdf->Cell(1, $alturaLinha,  ""  , 0, 1); // Quebra linha
	} else {
		$pdf->Cell(280, ($alturaLinha*3),  ""  , 'T', 1); // Quebra linha
	}


// 	/*#####################################################################################################*/
	$pdf->setXY(10,183);
	$pdf->SetFont('arial','',8);
	$pdf->SetTextColor(0,0,0);
	$pdf->Cell(140, $alturaLinha, "Arquivo gerado em: ".@date('d/m/Y H:i:s'), 'T', 0, 'L');
	$pdf->Cell(140, $alturaLinha, "P�gina ".$paginaAtual." de ".$totalPaginas, 'T', 1, 'R');
}


//SAIDA DO PDF
$pdf->Output($tipo_pdf, $end_final);
$pdf->Close();
redireciona($end_final);
?>
