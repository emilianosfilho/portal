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
require_once '../../pages/financeiro/function.php';
require_once "../../plugins/code39/code39.php";

// $debug = true;

$dados = (!empty($_POST)?$_POST:$_GET);

if($debug) varDump2($dados);

if (!empty($dados['CNPJ'])) {
	$PDF = buscaFichaCNPJ($dados['CNPJ']);
	$end_final = @date('Ymd_His_').'FICHAPJ_'.somenteNumeros($dados['CNPJ']).'.pdf';
} else {
	$PDF = buscaFichaCPF($dados['CPF']);
	$end_final = @date('Ymd_His_').'FICHAPF_'.somenteNumeros($dados['CPF']).'.pdf';
}
if($debug) varDump2($PDF);

$alturaLinha = 6;

//T�TULO DO RELAT�RIO
$titulo = "FICHA CADASTRAL";


//LOGO QUE SER� COLOCADO NO RELAT�RIO
$logo_header 	= "../../".DIR_IMG."logo_header.png";

/*############################################################################################*/
$pdf = new PDF_Code39('P','mm','A4');

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Image($logo_header,10,10,40,10);

$pdf->SetTextColor(139,0,0);
$pdf->SetFont($font_arial,$style_b,$tam_14);
$pdf->Cell(190,($alturaLinha*2),"FICHA CADASTRAL", 0, 1, 'C');
	
/*#####################################################################################################*/
$pdf->SetFillColor(200,200,200);/* Branco */
$pdf->SetTextColor(0,0,0); /* Preto */

if ($PDF['TIPOFJ']=='J') {
	// 	/*#####################################################################################################*/
	$pdf->SetFont('arial','b',10);
	$pdf->Cell(190, $alturaLinha, ''						, 0, 1); //PULA LINHA
	$pdf->Cell(190, ($alturaLinha*2), "DADOS EMPRESARIAIS"	, 1, 1, 'L', 1);

	$pdf->SetFont('arial','b',8);
	$pdf->Cell(30, ($alturaLinha*0.7), "CNPJ" 					, 1, 0, 'L', 1);
	$pdf->Cell(130, ($alturaLinha*0.7), "Nome Empresarial" 		, 1, 0, 'L', 1);
	$pdf->Cell(30, ($alturaLinha*0.7), "Insc. Estadual" 		, 1, 1, 'L', 1);

	$pdf->SetFont('arial','',8);
	$pdf->Cell(30, $alturaLinha, $PDF['CNPJ']			, 1, 0);
	$pdf->Cell(130, $alturaLinha, converterUTF8($PDF['RAZAOSOCIAL'])	, 1, 0);
	$pdf->Cell(30, $alturaLinha, converterUTF8($PDF['INSCESTADUAL'])	, 1, 1);
} else {
	$pdf->Cell(190, ($alturaLinha*0.7), ''				, 0, 1); //PULA LINHA

	$pdf->SetFont('arial','b',8);
	$pdf->Cell(30, ($alturaLinha*0.7), "CPF" 		, 1, 0, 'L', 1);
	$pdf->Cell(130, ($alturaLinha*0.7), "Nome" 		, 1, 0, 'L', 1);
	$pdf->Cell(30, ($alturaLinha*0.7), "RG" 		, 1, 1, 'L', 1);

	$pdf->SetFont('arial','',8);
	$pdf->Cell(30, $alturaLinha, $PDF['CPF']			, 1, 0);
	$pdf->Cell(130, $alturaLinha, converterUTF8($PDF['NOME'])	, 1, 0);
	$pdf->Cell(30, $alturaLinha, $PDF['RG']	, 1, 1);
}


$pdf->SetFont('arial','b',8);
$pdf->Cell(100, ($alturaLinha*0.7), "Logradouro (Avenida / Rua / Beco) " 	, 1, 0, 'L', 1);
$pdf->Cell(30, ($alturaLinha*0.7), "N�mero" 								, 1, 0, 'L', 1);
$pdf->Cell(60, ($alturaLinha*0.7), "Complemento" 							, 1, 1, 'L', 1);

$pdf->SetFont('arial','',8);
$pdf->Cell(100, $alturaLinha, $PDF['ENDERECO']		, 1, 0);
$pdf->Cell(30, $alturaLinha, $PDF['NUMERO']			, 1, 0);
$pdf->Cell(60, $alturaLinha, $PDF['COMPLEMENTO']	, 1, 1);

$pdf->SetFont('arial','b',8);
$pdf->Cell(20, ($alturaLinha*0.7), "CEP" 			, 1, 0, 'L', 1);
$pdf->Cell(70, ($alturaLinha*0.7), "Bairro" 		, 1, 0, 'L', 1);
$pdf->Cell(90, ($alturaLinha*0.7), "Munic�pio"		, 1, 0, 'L', 1);
$pdf->Cell(10, ($alturaLinha*0.7), "UF" 			, 1, 1, 'L', 1);

$pdf->SetFont('arial','',8);
$pdf->Cell(20, $alturaLinha, $PDF['CEP']		, 1, 0);
$pdf->Cell(70, $alturaLinha, converterUTF8($PDF['BAIRRO'])		, 1, 0);
$pdf->Cell(90, $alturaLinha, converterUTF8($PDF['MUNICIPIO'])	, 1, 0);
$pdf->Cell(10, $alturaLinha, $PDF['UF']			, 1, 1);

// 	/*#####################################################################################################*/
$pdf->SetFont('arial','b',10);
$pdf->Cell(190, $alturaLinha, ''					, 0, 1); //PULA LINHA
$pdf->Cell(190, ($alturaLinha*2), "CONTATO"	, 1, 1, 'L', 1);

$pdf->SetFont('arial','b',8);
$pdf->Cell(90, ($alturaLinha*0.7), "Nome" 		, 1, 0, 'L', 1);
$pdf->Cell(100, ($alturaLinha*0.7), "E-mail" 	, 1, 1, 'L', 1);

$pdf->SetFont('arial','',8);
$pdf->Cell(90, $alturaLinha, converterUTF8($PDF['CONTATONOME'])	, 1, 0);
$pdf->Cell(100, $alturaLinha, $PDF['EMAIL']			, 1, 1);

$pdf->SetFont('arial','b',8);
$pdf->Cell(30, ($alturaLinha*0.7), "Tel. Celular" 		, 1, 0, 'L', 1);
$pdf->Cell(30, ($alturaLinha*0.7), "Tel. Fixo" 			, 1, 0, 'L', 1);
$pdf->Cell(30, ($alturaLinha*0.7), "Tel. Financeiro" 	, 1, 0, 'L', 1);
$pdf->Cell(100, ($alturaLinha*0.7), "Observa��es" 		, 1, 1, 'L', 1);

$pdf->SetFont('arial','',8);
$pdf->Cell(30, $alturaLinha, $PDF['TELCELULAR']			, 1, 0);
$pdf->Cell(30, $alturaLinha, $PDF['TELFIXO']			, 1, 0);
$pdf->Cell(30, $alturaLinha, $PDF['TELFINANCEIRO']		, 1, 0);
$pdf->Cell(100, $alturaLinha, converterUTF8($PDF['OBSERVACOES'])		, 1, 1);

// 	/*#####################################################################################################*/
$pdf->SetFont('arial','b',10);
$pdf->Cell(190, $alturaLinha, ''					, 0, 1); //PULA LINHA
$pdf->Cell(190, ($alturaLinha*2), "COMPRADORES"		, 1, 1, 'L', 1);

$pdf->SetFont('arial','b',8);
$pdf->Cell(130, ($alturaLinha*0.7), "Nome" 			, 1, 0, 'L', 1);
$pdf->Cell(30, ($alturaLinha*0.7), "CPF" 			, 1, 0, 'L', 1);
$pdf->Cell(30, ($alturaLinha*0.7), "Tel. Celular" 	, 1, 1, 'L', 1);


if (empty($PDF['COMPRADOR1NOME']) && 
	empty($PDF['COMPRADOR2NOME']) && 
	empty($PDF['COMPRADOR3NOME'])) {
	$pdf->SetFont('arial','b',9);
	$pdf->SetTextColor($r_danger,$g_danger,$b_danger);
	$pdf->Cell(190, $alturaLinha, "Nenhum Comprador infornado", 1, 1);
}

$pdf->SetFont('arial','',8);
$pdf->SetTextColor($r_black,$g_black,$b_black);

if (!empty($PDF['COMPRADOR1NOME'])) {
	$pdf->Cell(130, $alturaLinha, converterUTF8($PDF['COMPRADOR1NOME'])	, 1, 0);
	$pdf->Cell(30, $alturaLinha, $PDF['COMPRADOR1CPF']		, 1, 0);
	$pdf->Cell(30, $alturaLinha, $PDF['COMPRADOR1CELULAR']	, 1, 1);
}

if (!empty($PDF['COMPRADOR2NOME'])) {
	$pdf->Cell(130, $alturaLinha, converterUTF8($PDF['COMPRADOR2NOME'])	, 1, 0);
	$pdf->Cell(30, $alturaLinha, $PDF['COMPRADOR2CPF']		, 1, 0);
	$pdf->Cell(30, $alturaLinha, $PDF['COMPRADOR2CELULAR']	, 1, 1);
}

if (!empty($PDF['COMPRADOR3NOME'])) {
	$pdf->Cell(130, $alturaLinha, converterUTF8($PDF['COMPRADOR3NOME'])	, 1, 0);
	$pdf->Cell(30, $alturaLinha, $PDF['COMPRADOR3CPF']		, 1, 0);
	$pdf->Cell(30, $alturaLinha, $PDF['COMPRADOR3CELULAR']	, 1, 1);
}

// 	/*#####################################################################################################*/
$pdf->SetFont('arial','b',10);
$pdf->Cell(190, $alturaLinha, ''					, 0, 1); //PULA LINHA
$pdf->Cell(190, ($alturaLinha*2), "COMPROVANTES"	, 1, 1, 'L', 1);

$pdf->SetFont('arial','b',8);
$pdf->Cell(60, ($alturaLinha*0.7), "Tipo" 		, 1, 0, 'L', 1);
$pdf->Cell(130, ($alturaLinha*0.7), "Arquivo" 	, 1, 1, 'L', 1);

$pdf->SetFont('arial','',8);
if (empty($PDF['COMPDOCUMENTO1']) && 
	empty($PDF['COMPDOCUMENTO2']) && 
	empty($PDF['COMPENDERECO1']) && 
	empty($PDF['COMPENDERECO2'])) {
	$pdf->SetFont('arial','b',9);
	$pdf->SetTextColor($r_danger,$g_danger,$b_danger,);
	$pdf->Cell(190, $alturaLinha, "Nenhum Comprovante identificado", 1, 1);
}

$pdf->SetTextColor($r_black,$g_black,$b_black);

if (!empty($PDF['COMPDOCUMENTO1'])) {
	$pdf->Cell(60, $alturaLinha, "Comprovante de Documento"		, 1, 0);
	$pdf->Cell(130, $alturaLinha, $PDF['COMPDOCUMENTO1']	, 1, 1);
}

if (!empty($PDF['COMPDOCUMENTO2'])) {
	$pdf->Cell(60, $alturaLinha, "Comprovante de Documento"		, 1, 0);
	$pdf->Cell(130, $alturaLinha, $PDF['COMPDOCUMENTO2']	, 1, 1);
}

if (!empty($PDF['COMPENDERECO1'])) {
	$pdf->Cell(60, $alturaLinha, "Comprovante de Endere�o"		, 1, 0);
	$pdf->Cell(130, $alturaLinha, $PDF['COMPENDERECO1']	, 1, 1);
}

if (!empty($PDF['COMPENDERECO2'])) {
	$pdf->Cell(60, $alturaLinha, "Comprovante de Endere�o"		, 1, 0);
	$pdf->Cell(130, $alturaLinha, $PDF['COMPENDERECO2']	, 1, 1);
}

// 	/*#####################################################################################################*/
$pdf->setXY(10,270);

$pdf->SetFont('arial','b',8);
$pdf->Cell(95, ($alturaLinha*0.7), "Data: ".formataDataOracleToBR($PDF['DATA'])		, 'T', 0, 'L');
$pdf->Cell(95, ($alturaLinha*0.7), "Endere�o IP: ".$PDF['ENDERECOIP'] 				, 'T', 1, 'R');

if(!$debug){
	### TIPO DO PDF GERADO ###
	//I-> envia o arquivo embutido para o navegador. O visualizador de PDF � usado, se dispon�vel.
	//D-> enviar para o navegador e for�ar o download de um arquivo com o nome fornecido pelo nome.
	//F-> salvar em um arquivo local com o nome dado pelo nome (pode incluir um caminho).
	//S-> retorna o documento como uma string.
	$tipo_pdf = "I";

	//SAIDA DO PDF
	$pdf->Output($tipo_pdf, $end_final, true);
	varDump2("Arquivo criado com sucesso ".$end_final);
}

fechaAba();
