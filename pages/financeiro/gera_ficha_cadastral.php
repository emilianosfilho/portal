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
require_once '../../pages/financeiro/function.php';
require_once '../../plugins/code39/code39.php';

// $debug = true;

$dados = (!empty($_POST)?$_POST:$_GET);
if($debug) varDump2($dados);

$PDF = buscaFichaID($dados['IDFICHACADASTRAL']);
if($debug) varDump2($PDF);

$alturaLinha = 6;

/*############################################################################################*/
$pdf = new PDF_Code39('P','mm','A4');

$pdf->AliasNbPages();
$pdf->AddPage();

//LOGO QUE SER� COLOCADO NO RELAT�RIO
$logo_header 	= "../../".DIR_IMG."logo_header.png";
$pdf->Image($logo_header,160,11,40,10);

$pdf->SetFont($font_arial,$style_b,$tam_22);
$pdf->Cell(190,($alturaLinha*2),"FICHA CADASTRO DE CLIENTE", 0, 1, 'L');
$pdf->Cell(190, $alturaLinha, '', 0, 1); //PULA LINHA
	
/*#####################################################################################################*/
$pdf->SetFillColor(200,200,200);/* Branco */
$pdf->SetTextColor(0,0,0); /* Preto */

if ($PDF['TIPOFJ']=='J') {
	// 	/*#####################################################################################################*/
	$pdf->SetFillColor(139,0,0);
	$pdf->SetTextColor($r_white,$g_white,$b_white);
	$pdf->SetFont($font_arial,$style_b,$tam_18);
	$pdf->Cell(10, ($alturaLinha*2), "1"	, 1, 0, 'C', 1);
	$pdf->SetTextColor(139,0,0);
	$pdf->Cell(180, ($alturaLinha*2), "IDENTIFICA��O DA EMPRESA"	, 1, 1, 'L');

	$pdf->Cell(190, ($alturaLinha/2), "", 0, 1);

	$pdf->SetTextColor($r_black,$g_black,$b_black);
	
	$pdf->SetFont($font_arial,$style_n,$tam_10);
	$pdf->Cell(60, $alturaLinha, "CNPJ: "	, 0, 0, 'R');
	$pdf->SetFont($font_arial,$style_b,$tam_11);
	$pdf->Cell(130, $alturaLinha, converterUTF8($PDF['CNPJ'])	, 0, 1, 'L');
	
	$pdf->SetFont($font_arial,$style_n,$tam_10);
	$pdf->Cell(60, $alturaLinha, "Nome Empresarial: "	, 0, 0, 'R');
	$pdf->SetFont($font_arial,$style_b,$tam_11);
	$pdf->Cell(130, $alturaLinha, mb_strtoupper(trim($PDF['RAZAOSOCIAL']),'UTF-8')	, 0, 1, 'L');
	
	$pdf->SetFont($font_arial,$style_n,$tam_10);
	$pdf->Cell(60, $alturaLinha, "Insc. Estadual: "	, 0, 0, 'R');
	$pdf->SetFont($font_arial,$style_b,$tam_11);
	$pdf->Cell(130, $alturaLinha, converterUTF8($PDF['INSCESTADUAL'])	, 0, 1, 'L');

} else {
	$pdf->SetFillColor(139,0,0);
	$pdf->SetTextColor($r_white,$g_white,$b_white);
	$pdf->SetFont($font_arial,$style_b,$tam_18);
	$pdf->Cell(10, ($alturaLinha*2), "1"	, 1, 0, 'C', 1);
	$pdf->SetTextColor(139,0,0);
	$pdf->Cell(180, ($alturaLinha*2), "IDENTIFICA��O PESSOAL"	, 1, 1, 'L');

	$pdf->SetTextColor($r_black,$g_black,$b_black);
	
	$pdf->SetFont($font_arial,$style_n,$tam_10);
	$pdf->Cell(60, $alturaLinha, "CPF: "	, 0, 0, 'R');
	$pdf->SetFont($font_arial,$style_b,$tam_11);
	$pdf->Cell(130, $alturaLinha, converterUTF8($PDF['CPF'])	, 0, 1, 'L');
	
	$pdf->SetFont($font_arial,$style_n,$tam_10);
	$pdf->Cell(60, $alturaLinha, "Nome: "	, 0, 0, 'R');
	$pdf->SetFont($font_arial,$style_b,$tam_11);
	$pdf->Cell(130, $alturaLinha, converterUTF8($PDF['NOME'])	, 0, 1, 'L');
	
}

$pdf->SetFont($font_arial,$style_n,$tam_10);
$pdf->Cell(60, $alturaLinha, "Endere�o: "	, 0, 0, 'R');
$pdf->SetFont($font_arial,$style_b,$tam_11);
$pdf->Cell(130, $alturaLinha, mb_strtoupper(trim($PDF['ENDERECO'].', '.$PDF['NUMERO'].' - '.$PDF['BAIRRO']),'UTF-8'), 0, 1, 'L');

$pdf->SetFont($font_arial,$style_n,$tam_10);
$pdf->Cell(60, $alturaLinha, "CEP: "	, 0, 0, 'R');
$pdf->SetFont($font_arial,$style_b,$tam_11);
$pdf->Cell(130, $alturaLinha, converterUTF8($PDF['CEP'])	, 0, 1, 'L');

$pdf->SetFont($font_arial,$style_n,$tam_10);
$pdf->Cell(60, $alturaLinha, "Cidade: "	, 0, 0, 'R');
$pdf->SetFont($font_arial,$style_b,$tam_11);
$pdf->Cell(130, $alturaLinha, mb_strtoupper(trim($PDF['MUNICIPIO'].' - '.$PDF['UF']),'UTF-8')	, 0, 1, 'L');

$pdf->SetFont($font_arial,$style_n,$tam_10);
$pdf->Cell(60, $alturaLinha, "Complemento: "	, 0, 0, 'R');
$pdf->SetFont($font_arial,$style_b,$tam_11);
$pdf->Cell(130, $alturaLinha, mb_strtoupper(trim($PDF['COMPLEMENTO']),'UTF-8')	, 0, 1, 'L');

$pdf->SetFont($font_arial,$style_n,$tam_10);
$pdf->Cell(60, $alturaLinha, "Contato: "	, 0, 0, 'R');
$pdf->SetFont($font_arial,$style_b,$tam_11);
$pdf->Cell(130, $alturaLinha, mb_strtoupper(converterUTF8($PDF['CONTATONOME']), 'UTF-8')	, 0, 1, 'L');

$pdf->SetFont($font_arial,$style_n,$tam_10);
$pdf->Cell(60, $alturaLinha, "Telefone: "	, 0, 0, 'R');
$pdf->SetFont($font_arial,$style_b,$tam_11);
$pdf->Cell(130, $alturaLinha, converterUTF8($PDF['TELCELULAR'].' | '.$PDF['TELFIXO'].' | '.$PDF['TELFINANCEIRO'])	, 0, 1, 'L');

$pdf->SetFont($font_arial,$style_n,$tam_10);
$pdf->Cell(60, $alturaLinha, "E-mail: "	, 0, 0, 'R');
$pdf->SetFont($font_arial,$style_b,$tam_11);
$pdf->Cell(130, $alturaLinha, mb_strtolower(trim($PDF['EMAIL']),'UTF-8')	, 0, 1, 'L');

$pdf->SetFont($font_arial,$style_n,$tam_10);
$pdf->Cell(60, $alturaLinha, "Observa��es: "	, 0, 0, 'R');
$pdf->SetFont($font_arial,$style_b,$tam_11);
$pdf->Cell(130, $alturaLinha, mb_strtoupper(trim($PDF['OBSERVACOES']),'UTF-8')	, 0, 1, 'L');

$pdf->Cell(190, $alturaLinha, "", 0, 1);

// 	/*#####################################################################################################*/

$pdf->SetFillColor(139,0,0);
$pdf->SetTextColor($r_white,$g_white,$b_white);
$pdf->SetFont($font_arial,$style_b,$tam_18);
$pdf->Cell(10, ($alturaLinha*2), "2"	, 1, 0, 'C', 1);
$pdf->SetTextColor(139,0,0);
$pdf->Cell(180, ($alturaLinha*2), "COMPRADORES AUTORIZADOS"	, 1, 1, 'L');

$pdf->Cell(190, $alturaLinha/2, "", 0, 1);

$pdf->SetTextColor($r_black,$g_black,$b_black);

$pdf->SetFont($font_arial,$style_n,$tam_10);
if (empty($PDF['COMPRADOR1NOME']) && 
	empty($PDF['COMPRADOR2NOME']) && 
	empty($PDF['COMPRADOR3NOME'])) {
	$pdf->Cell(190, $alturaLinha, "Nenhum Comprador infornado", 0, 1);
} else {
	$pdf->SetFont($font_arial,$style_n,$tam_10);
	$pdf->Cell(10, ($alturaLinha), "Ord" 			, "B", 0, 'L');
	$pdf->Cell(100, ($alturaLinha), "Nome" 			, "B", 0, 'L');
	$pdf->Cell(40, ($alturaLinha), "CPF" 			, "B", 0, 'L');
	$pdf->Cell(40, ($alturaLinha), "Tel. Celular" 	, "B", 1, 'L');
}

$pdf->SetFont($font_arial,$style_b,$tam_11);

if (!empty($PDF['COMPRADOR1NOME'])) {
	$pdf->Cell(10, $alturaLinha, mb_strtoupper("1",'UTF-8')	, 0, 0);
	$pdf->Cell(100, $alturaLinha, mb_strtoupper(trim($PDF['COMPRADOR1NOME']),'UTF-8')	, 0, 0);
	$pdf->Cell(40, $alturaLinha, $PDF['COMPRADOR1CPF']		, 0, 0);
	$pdf->Cell(40, $alturaLinha, $PDF['COMPRADOR1CELULAR']	, 0, 1);
}

if (!empty($PDF['COMPRADOR2NOME'])) {
	$pdf->Cell(10, $alturaLinha, mb_strtoupper("2",'UTF-8')	, 0, 0);
	$pdf->Cell(100, $alturaLinha, mb_strtoupper(trim($PDF['COMPRADOR2NOME']),'UTF-8')	, 0, 0);
	$pdf->Cell(40, $alturaLinha, $PDF['COMPRADOR2CPF']		, 0, 0);
	$pdf->Cell(40, $alturaLinha, $PDF['COMPRADOR2CELULAR']	, 0, 1);
}

if (!empty($PDF['COMPRADOR3NOME'])) {
	$pdf->Cell(10, $alturaLinha, mb_strtoupper("3",'UTF-8')	, 0, 0);
	$pdf->Cell(100, $alturaLinha, mb_strtoupper(trim($PDF['COMPRADOR3NOME']),'UTF-8')	, 0, 0);
	$pdf->Cell(40, $alturaLinha, $PDF['COMPRADOR3CPF']		, 0, 0);
	$pdf->Cell(40, $alturaLinha, $PDF['COMPRADOR3CELULAR']	, 0, 1);
}

$pdf->Cell(190, $alturaLinha*1.5, "", 0, 1);

// 	/*#####################################################################################################*/

$pdf->SetFillColor(139,0,0);
$pdf->SetTextColor($r_white,$g_white,$b_white);
$pdf->SetFont($font_arial,$style_b,$tam_18);
$pdf->Cell(10, ($alturaLinha*2), "3"	, 1, 0, 'C', 1);
$pdf->SetTextColor(139,0,0);
$pdf->Cell(180, ($alturaLinha*2), "COMPROVANTES"	, 1, 1, 'L');

$pdf->Cell(190, ($alturaLinha/2), "", 0, 1);
$pdf->SetTextColor($r_black,$g_black,$b_black);

if (!empty($PDF['COMPDOCUMENTO1'])) {
	$pdf->SetFont($font_arial,$style_n,$tam_10);
	$pdf->Cell(60, $alturaLinha, "Comprovante de Documento: "	, 0, 0, 'R');
	$pdf->SetFont($font_arial,$style_b,$tam_11);
	$pdf->Cell(130, $alturaLinha, converterUTF8($PDF['COMPDOCUMENTO1'])	, 0, 1, 'L');
}

if (!empty($PDF['COMPDOCUMENTO2'])) {
	$pdf->SetFont($font_arial,$style_n,$tam_10);
	$pdf->Cell(60, $alturaLinha, "Comprovante de Documento: "	, 0, 0, 'R');
	$pdf->SetFont($font_arial,$style_b,$tam_11);
	$pdf->Cell(130, $alturaLinha, converterUTF8($PDF['COMPDOCUMENTO2'])	, 0, 1, 'L');
}

if (!empty($PDF['COMPENDERECO1'])) {
	$pdf->SetFont($font_arial,$style_n,$tam_10);
	$pdf->Cell(60, $alturaLinha, "Comprovante de Endere�o: "	, 0, 0, 'R');
	$pdf->SetFont($font_arial,$style_b,$tam_11);
	$pdf->Cell(130, $alturaLinha, converterUTF8($PDF['COMPENDERECO1'])	, 0, 1, 'L');
}

if (!empty($PDF['COMPENDERECO2'])) {
	$pdf->SetFont($font_arial,$style_n,$tam_10);
	$pdf->Cell(60, $alturaLinha, "Comprovante de Endere�o: "	, 0, 0, 'R');
	$pdf->SetFont($font_arial,$style_b,$tam_11);
	$pdf->Cell(130, $alturaLinha, converterUTF8($PDF['COMPENDERECO2'])	, 0, 1, 'L');
}

$pdf->SetFont($font_arial,$style_n,$tam_10);
if (empty($PDF['COMPDOCUMENTO1']) && 
	empty($PDF['COMPDOCUMENTO2']) && 
	empty($PDF['COMPENDERECO1']) && 
	empty($PDF['COMPENDERECO2'])) {
	$pdf->Cell(190, $alturaLinha, "Nenhum Comprovante enviado", 0, 1);
}


// 	/*#####################################################################################################*/
$pdf->setXY(10,270);

$pdf->SetFont('arial','b',8);
$pdf->Cell(95, ($alturaLinha*0.7), "Data: ".@date("d-m-Y H:i:s")		, 'T', 0, 'L');
$pdf->Cell(95, ($alturaLinha*0.7), "Endere�o IP: ".$PDF['ENDERECOIP'] 				, 'T', 1, 'R');


// 	/*#####################################################################################################*/
// 	/*#####################################################################################################*/
### TIPO DO PDF GERADO ###
//I-> envia o arquivo embutido para o navegador. O visualizador de PDF � usado, se dispon�vel.
//D-> enviar para o navegador e for�ar o download de um arquivo com o nome fornecido pelo nome.
//F-> salvar em um arquivo local com o nome dado pelo nome (pode incluir um caminho).
//S-> retorna o documento como uma string.
$tipo_pdf = "F";

### NOME DO PDF GERADO ###
if (!empty($PDF['CNPJ'])) {
	$name = somenteNumeros($PDF['CNPJ']).'_FICHACADASTRAL'.'.pdf';
} else {
	$name = somenteNumeros($PDF['CPF']).'_FICHACADASTRAL'.'.pdf';
}
$filename = "../../".@DIR_UPLOAD.$name;

if($debug){
	varDump2("tipo_pdf: ".$tipo_pdf);
	varDump2("filename: ".$filename);
	die();
} else {
	if ($tipo_pdf == "F") {
		$pdf->Output($tipo_pdf, $filename);
		if (file_exists($filename)) {
			// exibeMensagem("Ficha cadastral gerada com sucesso!\\n".$name);

			header('Content-type: application/pdf');
			header('Content-Disposition: inline; filename="' . $name . '"');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: ' . filesize($filename));
			header('Accept-Ranges: bytes');

			@readfile($filename);
		} else {
			exibeMensagem("ERRO ao gerar o arquivo!");
		}
	} else {
		$pdf->Output($tipo_pdf, $filename);
	}
}
