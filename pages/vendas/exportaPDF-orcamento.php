<?php 
session_start();
date_default_timezone_set('America/Manaus');
error_reporting(E_ALL ^E_NOTICE ^E_WARNING ^E_DEPRECATED ); 
ini_set('error_reporting', E_ALL ^E_NOTICE ^E_WARNING ^E_DEPRECATED );
ini_set("display_errors", 1);
require_once "../../pages/conf/define.php";
require_once "../../pages/conf/functions.php";
require_once "../../pages/conf/conectaOracle.php";
require_once '../../pages/vendas/function.php';
//PREPARA PARA GERAR O PDF
define("FPDF_FONTPATH", "../../plugins/fpdf/font/");
require("../../plugins/fpdf/fpdf.php");
require("../../plugins/fpdf/stylesheet.php");

// $debug = true;
$dados = $dados??[];
if ($debug) varDump2($dados);

if (!isset($dados['IDORCAMENTO'])) {
	exibeMensagem("IDORCAMENTO não informado ou inválido!.");
	fechaAba();
	die();
}


$ARRAY_EXIBE = array();
if (isset($dados['somenteDisponiveis']) && $dados['somenteDisponiveis'] == "on") {
	$existeRestricao = true;
	foreach ($_SESSION['ORCAMENTO']['ITENS'] as $key => $item) {
		if (intval($item["QTDISPONIVEL"]) > 0) {
			array_push($ARRAY_EXIBE, $item);
		}
	}
} else {
	$existeRestricao = false;
	$ARRAY_EXIBE = $_SESSION['ORCAMENTO']['ITENS'];
}

if($debug) varDump2($ARRAY_EXIBE);



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

$pdf = new FPDF();
$pdf = new FPDF('P','mm','A4');

$pdf->AliasNbPages();

for ($paginaAtual=1; $paginaAtual <= $totalPaginas; $paginaAtual++) { 

	$pdf->AddPage();

	$pdf->Image($logo_header,10,10,36,10);


	/*#####################################################################################################*/
	if (stripos($_SESSION['ORCAMENTO']['CLIENTE']['CLIENTE'], 'ANALISE') || stripos($_SESSION['ORCAMENTO']['CLIENTE']['CLIENTE'], 'VEMAP')) {
		$clienteVemap  = true;
	} else {
		$clienteVemap  = false;
	}
	if ($clienteVemap === false) 
	{
		$pdf->SetFont($font_arial,$style_n,$tam_10);
		$pdf->SetTextColor($r_black,$g_black,$b_black);

		switch ($_SESSION['ORCAMENTO']['CLIENTE']['UF']) {
			case 'PA':
				$pdf->Cell(190, 5, converterUTF8('Filial Santarém / Pará'), 0, 1, 'R');
				$pdf->Cell(190, 5, converterUTF8('Av. Cuiabá, 2026, Caranazal, CEP: 68040-400'), "B", 1, 'R');
				break;
			case 'RR':
				$pdf->Cell(190, 5, converterUTF8('Filial Boa Vista / Roraima'), 0, 1, 'R');
				$pdf->Cell(190, 5, converterUTF8('Av. Centenário, 470 / Cinturão Verde, CEP: 69312-377'), "B", 1, 'R');
				break;
			
			default:
				$pdf->Cell(190, 5, converterUTF8('Matriz: Manaus / Amazonas'), 0, 1, 'R');
				$pdf->Cell(190, 5, converterUTF8('Av. Max Teixeira, 1.057 / Col. Santo Antônio, CEP: 69093-770'), "B", 1, 'R');
				break;
		}

	}
			
	if ($existeRestricao) {
		$pdf->SetTextColor($r_danger,$g_danger,$b_danger); /* Vermelho */
		$pdf->SetTextColor($r_danger,$g_danger,$b_danger);
		$pdf->SetFont($font_arial,$style_b,$tam_10);
		$pdf->Cell(190,7, "SOMENTE DISPONÍVEIS" , 0, 1, 'C');
	} else {
		$pdf->Cell(190,7, "" , 0, 1, 'C');
	}

	//MONTA O CABE�ALHO DO ORCAMENTO
	$pdf->SetFont($font_arial,$style_n,$tam_10);
	$pdf->SetFillColor($r_lightblue,$g_lightblue,$b_lightblue);
	$pdf->SetTextColor($r_white,$g_white,$b_white);
	$pdf->Cell(90, 5, "Cliente"			  , 1, 0, 'L', 1);
	$pdf->Cell(10, 5, "UF"				  , 1, 0, 'C', 1);
	$pdf->Cell(55, 5, "Vendedor(a)"		  , 1, 0, 'L', 1);
	$pdf->SetFillColor($r_warning,$g_warning,$b_warning);/* Amarelo */
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(35, 5, converterUTF8("Núm Orc.")		  , 'LTR', 1, 'C', 1);

	$pdf->SetFont($font_courier,$style_b,$tam_9);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(90, 7, converterUTF8(substr($_SESSION['ORCAMENTO']['CLIENTE']['CODCLI']."-".converterUTF8($_SESSION['ORCAMENTO']['CLIENTE']['CLIENTE']),0,40)) , 1, 0, 'L');
	$pdf->Cell(10, 7, $_SESSION['ORCAMENTO']['CLIENTE']['UF']	, 1, 0, 'C');
	$pdf->Cell(55, 7, converterUTF8(substr(obterPrimeiroEUltimoNome($_SESSION['ORCAMENTO']['VENDEDOR']['NOME']),0,25)) 		, 1, 0, 'L');
	$pdf->SetFont($font_arial,$style_b,$tam_20);
	$pdf->SetFillColor($r_warning,$g_warning,$b_warning);/* Amarelo */
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(35, 7, $dados['IDORCAMENTO'] , 'LBR', 1, 'C', 1);

	$pdf->SetFont($font_arial,$style_n,$tam_10);
	$pdf->SetFillColor($r_lightblue,$g_lightblue,$b_lightblue);
	$pdf->SetTextColor($r_white,$g_white,$b_white);
	$pdf->Cell(90, 5, "Contato" , 1, 0, 'L', 1);
	$pdf->Cell(35, 5, "Equipamento" , 1, 0, 'L', 1);
	$pdf->Cell(30, 5, "Ordem Compra"		  , 1, 0, 'L', 1);
	$pdf->Cell(35, 5, "Status" , 1, 1, 'C', 1);

	$pdf->SetFont($font_courier,$style_b,$tam_9);
	$pdf->SetTextColor($r_black,$g_black,$b_black);

	$contato = "";
	$contato .= converterUTF8(($_SESSION['ORCAMENTO']['CAB']['CONTATO'])?" ".$_SESSION['ORCAMENTO']['CAB']['CONTATO']:"");
	$contato .= ($_SESSION['ORCAMENTO']['CAB']['TELCELULAR'])?" ".$_SESSION['ORCAMENTO']['CAB']['TELCELULAR']:"";
	$contato .= ($_SESSION['ORCAMENTO']['CAB']['TELFIXO'])?" ".$_SESSION['ORCAMENTO']['CAB']['TELFIXO']:"";

	$pdf->Cell(90, 7, substr($contato, 0, 45), 1, 0, 'L');
	if ($_SESSION['ORCAMENTO']['CAB']['MAQUINA'] == 'OUTRO') {
		$pdf->Cell(35, 7, converterUTF8($_SESSION['ORCAMENTO']['CAB']['OBSERVACAO3']??''), 1, 0, 'L');
	} else {
		$pdf->Cell(35, 7, converterUTF8($_SESSION['ORCAMENTO']['CAB']['MAQUINA']??''), 1, 0, 'L');
	}
	$pdf->Cell(30, 7, converterUTF8($_SESSION['ORCAMENTO']['CAB']['ORDEMDECOMPRA']??'') , 1, 0, 'L');
	$pdf->SetFont($font_courier,$style_b,$tam_16);
	switch ($_SESSION['ORCAMENTO']['CAB']['STATUS']) {
		case 'ORCAMENTO':
			$pdf->SetFillColor($r_gray,$g_gray,$b_gray);
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
	$pdf->Cell(35, 7, $_SESSION['ORCAMENTO']['CAB']['STATUS'], 1, 1, 'C', 1);

	$pdf->Cell(1, 2, "", 0, 1); /* PULA LINHA */


	/*#####################################################################################################*/
	$tamanhoDescricao = 98;

	// //MONTA O CABE�ALHO DOS ITENS
	$pdf->SetFont($font_arial,$style_b,$tam_8);
	$pdf->SetFillColor($r_lightblue,$g_lightblue,$b_lightblue);
	$pdf->SetTextColor($r_white,$g_white,$b_white);
	$pdf->Cell(7, $alturaLinhaItem, "Item"							, 'TB', 0, 'L', 1);
	if (isset($dados['incluirCodpeca'])) {
		$pdf->Cell(20, $alturaLinhaItem, converterUTF8("Peça")					, 'TB', 0, 'L', 1);
		$tamanhoDescricao -= 20;
	}
	if (isset($dados['incluirCodprod'])) {
		$pdf->Cell(11, $alturaLinhaItem, "Wint"					, 'TB', 0, 'L', 1);
		$tamanhoDescricao -= 11;
	}
	if (isset($dados['incluirLocacao'])) {
		$pdf->Cell(13, $alturaLinhaItem, "Loc"					, 'TB', 0, 'L', 1);
		$tamanhoDescricao -= 13;
	}
	if (isset($dados['incluirCST'])) {
		$pdf->Cell(8, $alturaLinhaItem, "CST"					, 'TB', 0, 'L', 1);
		$tamanhoDescricao -= 8;
	}
	if (isset($dados['incluirNCM'])) {
		$pdf->Cell(15, $alturaLinhaItem, "NCM"					, 'TB', 0, 'L', 1);
		$tamanhoDescricao -= 15;
	}
	$pdf->Cell($tamanhoDescricao, $alturaLinhaItem, converterUTF8("Descrição")		, 'TB', 0, 'L', 1);
	$pdf->Cell(23, $alturaLinhaItem, "Marca/Linha"					, 'TB', 0, 'L', 1);
	$pdf->Cell(20, ($alturaLinhaItem/2), "Dispon."					, 'T', 0, 'C', 1);
	$pdf->Cell(7, $alturaLinhaItem, "Qtd"							, 'TB', 0, 'R', 1);
	$pdf->Cell(17, $alturaLinhaItem, converterUTF8("Preço Unit.")			   		, 'TB', 0, 'R', 1);
	$pdf->Cell(18, $alturaLinhaItem, "Sub Total"					, 'TB', 1, 'R', 1);
	$pdf->SetXY(138, 56);
	if ($_SESSION['ORCAMENTO']['CLIENTE']['UF'] == 'AM') {
		$pdf->Cell(20, ($alturaLinhaItem/2), "Manaus-AM"			, 'B', 1, 'C', 1);
	} else {
		$pdf->Cell(20, ($alturaLinhaItem/2), ""						, 'B', 1, 'C', 1);
	}


	$contador = 0;
	
	// varDump2($dados); 

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

        $tribProd['CST'] = "";
        $tribProd['NCM'] = "";
				if (isset($dados['incluirCST']) || isset($dados['incluirNCM'])) {
					if (isset($item['CODPROD']) && $item['CODPROD'] != "") {
						$tribProd = buscaTribProd($item['CODPROD'], $_SESSION['ORCAMENTO']['CLIENTE']['UF'], 1);
					}
				}

				// $pdf->SetFont($font_courier,$style_b,$tam_9);
				$pdf->SetFont($font_courier,$style_b,7.5);
				$pdf->SetTextColor($r_black,$g_black,$b_black);
				if ($registroAtual % 2) {
					$pdf->SetFillColor($r_light,$g_light,$b_light);
				} else {
					$pdf->SetFillColor($r_white,$g_white,$b_white);
				}

				$pdf->Cell(7, $alturaLinhaItem, str_pad($registroAtual, 3, '0', STR_PAD_LEFT), 'TB', 0, 'L',1);

				if (isset($dados['incluirCodpeca'])) {
					$pdf->Cell(20, $alturaLinhaItem, strtoupper(trim($item['CODPECA']))		, 'TB', 0, 'L',1);
				}

				if (isset($dados['incluirCodprod'])) {
					$pdf->Cell(11, $alturaLinhaItem, 'W'.strtoupper(trim($item['CODPROD']))		, 'TB', 0, 'L',1);
				}

				if (isset($dados['incluirLocacao'])) {
					$pdf->Cell(13, $alturaLinhaItem, ($item['LOCACAO'])?strtoupper(trim($item['LOCACAO'])):""		, 'TB', 0, 'L',1);
				}

				if (isset($dados['incluirCST'])) {
					$pdf->Cell(8, $alturaLinhaItem, strtoupper(trim($tribProd['CST']))			, 'TB', 0, 'L',1);
				}

				if (isset($dados['incluirNCM'])) {
					$pdf->Cell(15, $alturaLinhaItem, strtoupper(trim($tribProd['NCM']))		, 'TB', 0, 'L',1);
				}

				$pdf->Cell($tamanhoDescricao, $alturaLinhaItem, substr(strtoupper(trim($item['DESCRICAO'])),0,30) , 'TB', 0, 'L', 1);
				$pdf->Cell(23, $alturaLinhaItem, strtoupper(trim($item['MARCA'])), 'TB', 0, 'L',1);
				$pdf->Cell(20, $alturaLinhaItem, converterUTF8($item['DISPONIBILIDADE'])		, 'TB', 0, 'L',1);
				$pdf->Cell(7, $alturaLinhaItem, intval($item['QTPEDIDA'])					  , 'TB', 0, 'R',1);
				$pdf->Cell(17, $alturaLinhaItem, moeda(floatval($item['PVENDA']),2), 'TB', 0, 'R',1);
				$pdf->Cell(18, $alturaLinhaItem, moeda((intval($item['QTPEDIDA']) * floatval($item['PVENDA'])),2), 'TB', 1, 'R',1);
				$vltotal += (intval($item['QTPEDIDA']) * floatval($item['PVENDA']));

			}
		}

	
	}

	if ($totalPaginas == $paginaAtual) {
		$pdf->SetFont($font_courier,$style_b,$tam_12);
		$pdf->SetTextColor($r_lightblue,$g_lightblue,$b_lightblue);
		$pdf->Cell(155 , 8, "Valor total", 0, 0, 'R');
		$pdf->SetFillColor($r_lightblue,$g_lightblue,$b_lightblue);
		$pdf->SetTextColor($r_white,$g_white,$b_white);
		$pdf->Cell(35, 8, moeda($vltotal)		, 1, 1, 'R', 1);
	}


	$pdf->Image($logo_rodape,7,220,195,50);

	$pdf->SetXY(10, 272);
	$pdf->SetFont($font_arial, $style_i, $tam_9);
	$pdf->SetTextColor($r_black,$g_black,$b_black);
	$pdf->Cell(190, 4, converterUTF8("Página ".str_pad($paginaAtual, 2, "0", STR_PAD_LEFT)." de ".str_pad($totalPaginas, 2, "0", STR_PAD_LEFT)), "T", 1, 'R');
}

if (!$debug) {

	### TIPO DO PDF GERADO ###
	//I-> envia o arquivo embutido para o navegador. O visualizador de PDF � usado, se dispon�vel.
	//D-> enviar para o navegador e for�ar o download de um arquivo com o nome fornecido pelo nome.
	//F-> salvar em um arquivo local com o nome dado pelo nome (pode incluir um caminho).
	//S-> retorna o documento como uma string.
	$tipo_pdf = "I";

	//ENDERE�O ONDE SER� GERADO O PDF
	$end_final = "ORCAMENTO_".$dados['IDORCAMENTO'].".pdf";

	$pdf->Output($tipo_pdf, $end_final);
}
$pdf->Close();
?>