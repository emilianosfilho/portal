<?php 
session_start();

ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ (E_WARNING|E_NOTICE|E_DEPRECATED));
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
clearstatcache();
require "../../pages/conf/define.php";
require "../../pages/conf/functions.php";
require "../../pages/conf/conectaOracle.php";
require "../../pages/vendas/function.php";

// $debug=true;

if(isset($_GET) && !empty($_GET)){
   $dados = $_GET;
} else {
	if(isset($_POST) && !empty($_POST)){
	   $dados = $_POST;
	} else {
	  $dados = false;
	}
}
if ($debug) varDump2($dados);

if ($debug) varDump2($_FILES);

$ARQUIVO = array();

///////////////////////////////////////////////////////////////////////////////////////////
// SALVA O ARQUIVO NO SERVIDOR
if (isset($_FILES) && !empty($_FILES)) {
	foreach ($_FILES as $keyFiles => $valueFiles) {
		if ($debug) varDump2($valueFiles);

		if ($valueFiles["error"] <> 0) {
			switch ($valueFiles["error"]) {
				case 1: varDump2('The uploaded file exceeds the upload_max_filesize directive in php.ini'); break;
				case 2: varDump2('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form'); break;
				case 3: varDump2('The uploaded file was only partially uploaded'); break;
				case 4: varDump2('No file was uploaded'); break;
				case 6: varDump2('Missing a temporary folder'); break;
				case 7: varDump2('Failed to write file to disk.'); break;
				case 8: varDump2('A PHP extension stopped the file upload.'); break;
			}
			die("ERRO - Arquivo não identificado ou com erro");

		} else {

			$dir = "../../".@DIR_UPLOAD;
			if (!is_dir($dir)) {
				die('ERRO Não encontrado Diretório '.$dir);
			} else {
				if($debug) varDump2($dir . " pasta encontrada com sucesso");
			}

			$delimiter          = ".";
			$name               = $valueFiles["name"];
			$tmp_name           = $valueFiles["tmp_name"];
			$filename           = $dir . $name;

			$extensao           = explode($delimiter, $name);
			$extensao           = end($extensao);
			$extensao           = strtolower($extensao);
			$extecoesPermitidas = array('xls', 'xlsx');
			//apagaArquivos($dir); 
			if ($debug) varDump2("extensao: ".$extensao);
			if ($debug) varDump2($extecoesPermitidas);

			if(!in_array($extensao, $extecoesPermitidas)){
				exibeMensagem('ERRO Somente arquivos com a extensão .xlsx ou xls será aceito!  A extenção do arquivo atual é: '.$extensao);
				die;
			}

			$inputFileName = $dir .@date('Ymd_His_').$name;
			if(!move_uploaded_file($tmp_name,  $inputFileName)){
				varDump2($tmp_name);
				varDump2($inputFileName);
				exibeMensagem('ERRO ao importar o Arquivo '.$filename);
				varDump2("ERRO Arquivo ".$name." Não foi movido para o servidor!");
				die();
			} else {
				
				/////////////////////////////////////////////////////////////////////////////////////////////
				if (file_exists($inputFileName)) {
					if ($debug) varDump2("Arquivo ".$inputFileName." movido para o servidor com sucesso!");
				} else {
					exibeMensagem("Não foi possível encontrar o arquivo " . $inputFileName . " no servidor");
				}
			
				// include the autoloader, so we can use PhpSpreadsheet
				require_once(__DIR__ . '/../../vendor/autoload.php');

				/**  Identify the type of $inputFileName  **/
				$inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
				/**  Create a new Reader of the type that has been identified  **/
				$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
				/**  Load $inputFileName to a Spreadsheet Object  **/
				$spreadsheet = $reader->load($inputFileName);
				//aba da planilha
				$sheet = $spreadsheet->getActiveSheet();

				$cabecalho  = array();

				foreach ($sheet->getRowIterator() as $keyRow => $row) {
					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(FALSE);
					$item[] = array();
					$contCab = 0;
					foreach ($cellIterator as $keyCell => $cell) {
						$value = $cell->getValue();
						if ($keyRow == 1) {
							$cabecalho[] = $value;
						} else {
							$tmp[$keyRow][] = trim($value);
						}
					}
				}

				foreach ($tmp as $key2 => $value2) {
					$ARQUIVO[] = array_combine($cabecalho, $value2);
				}

				if($debug) varDump2($ARQUIVO);

			}
		}  
	}
}


if (!empty($ARQUIVO)) {

	foreach ($ARQUIVO as $key => $value) {
		
		if (isset($value["CODPECA"]) && !empty($value["CODPECA"])) {
			// varDump2($value); 
		  
		  $CODPECA      = mb_strtoupper(trim($value["CODPECA"]), 'UTF-8');
		  $QUANTIDADE   = intval($value["QUANTIDADE"]);
			// varDump2("CODPECA: ".$CODPECA); 
			// varDump2("QUANTIDADE: ".$QUANTIDADE); 
			// die();

		  if (strlen($CODPECA) < 2) {
				if($debug) varDump2('ERRO O campo Pesquisa deve conter ao menos 2 dígitos');
				insereModal('danger', "O campo Pesquisa deve conter ao menos 2 dígitos");
		  } else {

				$IDORCAMENTO  = intval($_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']);
				$CODCLI       = intval($_SESSION['ORCAMENTO']['CAB']['CODCLI']);
				$CODUSUR      = intval($_SESSION['ORCAMENTO']['CAB']['CODUSUR']);
				$CODPLPAG     = intval($_SESSION['ORCAMENTO']['CAB']['CODPLPAG']);
				$NUMPR        = intval($_SESSION['ORCAMENTO']['PLPAG']['NUMPR']);
				$UFDESTINO    = strval($_SESSION['ORCAMENTO']['CLIENTE']['UF']);
				$CODFILIALNF  = strval($_SESSION['ORCAMENTO']['CLIENTE']['CODFILIALNF']);
				if ($CODFILIALNF == "") {
				  $CODFILIALNF = "1";
				}
				$PERCDESC     = $_SESSION['ORCAMENTO']['CLIENTE']['FAST'];
				if ($PERCDESC == "" || $PERCDESC == null || $PERCDESC == false) {
				  $PERCDESC = 0;
				}

				$NUMREGIAO  = $_SESSION['ORCAMENTO']['CLIENTE']['NUMREGIAO'];
				if ($NUMREGIAO == "" || $NUMREGIAO == null || $NUMREGIAO == false) {
				  $NUMREGIAO = 1;
				}

				$sql = "SELECT DISTINCT BASE.*, 
							(CASE WHEN (BASE.CODPROD IS NOT NULL) THEN 
							  (SELECT MAX(ROUND((A.PVENDAATUAL - (A.PVENDAATUAL * (A.PERCDESCAUTOR / 100))),2))
								FROM PCAUTORI A
							   WHERE DATA_UTILIZACAO IS NULL
								 AND CODFILIAL = ".$CODFILIALNF."
								 AND CODUSUR = ".$CODUSUR."
								 AND CODPLPAG = ".$CODPLPAG."
								 AND CODCLI = ".$CODCLI."
								 AND CODPROD = BASE.CODPROD)
							  ELSE 0 END) AS PVENDA_301
			  FROM (SELECT 0 AS ORD,
						   '".$CODPECA."' AS CODPECA,
						   '".$QUANTIDADE."' AS QTPEDIDA,
						   TO_CHAR('WINTHOR') AS ORIGEM,
						   P.NUMORIGINAL,
						   TRUNC(P.CODPROD) || '-' || TRUNC(P.DV) AS WINTHOR,
						   TRUNC(P.CODPROD) AS CODPROD,
						   TRUNC(P.DV) AS DV,
						   TRIM(P.DESCRICAO) AS DESCRICAO,
						   NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA), P.MARCA) AS MARCA,
						   TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
						   TO_CHAR(DECODE(P.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
						   NULL AS VIDE,
						   (SELECT GREATEST(E1.QTESTGER, 0) FROM PCEST E1 WHERE E1.CODPROD = P.CODPROD AND E1.CODFILIAL = ".$CODFILIALNF.") AS QTESTGER,
						   (SELECT GREATEST(E1.QTRESERV, 0) FROM PCEST E1 WHERE E1.CODPROD = P.CODPROD AND E1.CODFILIAL = ".$CODFILIALNF.") AS QTRESERV,
						   (SELECT GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(P.CODPROD, ".$CODFILIALNF.", 'VP', TRUNC(SYSDATE)),0) AS QTSALDO from dual) AS SALDO,
						   (SELECT ROUND(DECODE('".$NUMPR."',
												'1',
												PR.PVENDA1,
												'2',
												PR.PVENDA2,
												'3',
												PR.PVENDA3,
												'4',
												PR.PVENDA4,
												'5',
												PR.PVENDA5,
												'6',
												PR.PVENDA6,
												'7',
												PR.PVENDA7,
												PR.PVENDA) *
										 (1 - ((NVL(".$PERCDESC.", 0) * (-1)) / 100)),
										 2) + 0.01
							  FROM PCTABPR PR
							 WHERE PR.CODPROD = P.CODPROD
							   AND PR.NUMREGIAO = '".$NUMREGIAO."') AS PTABELA,
						   (SELECT TT.CODTRIBPISCOFINS
							  FROM PCTABTRIB TT
							 WHERE TT.CODPROD = P.CODPROD
							   AND TT.CODFILIALNF = '".$CODFILIALNF."'
							   AND TT.UFDESTINO = '".$UFDESTINO."') AS CODTRIBPISCOFINS
					FROM PCPRODUT P
					WHERE P.DTEXCLUSAO IS NULL
					  AND P.NUMORIGINAL LIKE '".$CODPECA."%' OR 'W'||P.CODPROD = '".$CODPECA."' OR P.DESCRICAO LIKE '%".$CODPECA."%'

					UNION
					SELECT 0 AS ORD,
						  '".$CODPECA."' AS CODPECA,
						  '".$QUANTIDADE."' AS QTPEDIDA,
						 DECODE(TRIM(V2.APLICMARCA), 'OPCAO', 'OPCAO', 'INFO', 'INFO', 'VIDE') AS ORIGEM,
						 V2.CODPECA AS NUMORIGINAL,
						 NULL AS WINTHOR,
						 NULL AS CODPROD,
						 NULL AS DV,
						 TO_CHAR(NVL(V2.DESCRICAO, 'SOB CONSULTA')) AS DESCRICAO,
						 TO_CHAR(NVL(NVL(V2.APLICMARCA, V2.MARCA),  'SOB CONSULTA')) AS MARCA,
						 NULL AS LOCACAO,
						 TO_CHAR(DECODE(V2.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
						 V2.VIDE AS VIDE,
						 TO_NUMBER(0) AS QTESTGER,
						 TO_NUMBER(0) AS QTRESERV,
						 TO_NUMBER(0) AS SALDO,
						 TO_NUMBER(V2.PRECO) AS PTABELA,
						 NULL AS CODTRIBPISCOFINS
					 FROM ORCVIDE V2
					WHERE V2.DTEXCLUSAO IS NULL
					  AND V2.VIDE IS NOT NULL
					  AND V2.CODPECA IS NOT NULL
					  AND V2.CODPECA LIKE '".$CODPECA."%' 

					UNION
					SELECT 0 AS ORD,
						  '".$CODPECA."' AS CODPECA,
						  '".$QUANTIDADE."' AS QTPEDIDA,
						 DECODE(TRIM(V2.APLICMARCA), 'OPCAO', 'OPCAO', 'INFO', 'INFO', 'VIDE') AS ORIGEM,
						 V2.VIDE AS NUMORIGINAL,
						 NULL AS WINTHOR,
						 NULL AS CODPROD,
						 NULL AS DV,
						 TO_CHAR(NVL(V2.DESCRICAO, 'SOB CONSULTA')) AS DESCRICAO,
						 TO_CHAR(NVL(NVL(V2.APLICMARCA, V2.MARCA),  'SOB CONSULTA')) AS MARCA,
						 NULL AS LOCACAO,
						 TO_CHAR(DECODE(V2.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
						 V2.CODPECA AS VIDE,
						 TO_NUMBER(0) AS QTESTGER,
						 TO_NUMBER(0) AS QTRESERV,
						 TO_NUMBER(0) AS SALDO,
						 TO_NUMBER(V2.PRECO) AS PTABELA,
						 NULL AS CODTRIBPISCOFINS
					 FROM ORCVIDE V2
					WHERE V2.DTEXCLUSAO IS NULL
					  AND V2.VIDE IS NOT NULL
					  AND V2.CODPECA IS NOT NULL
					  AND V2.CODPECA IS NOT NULL 
					  AND V2.VIDE LIKE '".$CODPECA."%' 

					UNION
					SELECT 1 AS ORD,
						   '".$CODPECA."' AS CODPECA,
						   '".$QUANTIDADE."' AS QTPEDIDA,
						   TO_CHAR('VIDE') AS ORIGEM,
						   P.NUMORIGINAL,
						   TRUNC(P.CODPROD) || '-' || TRUNC(P.DV) AS WINTHOR,
						   TRUNC(P.CODPROD) AS CODPROD,
						   TRUNC(P.DV) AS DV,
						   TRIM(P.DESCRICAO) AS DESCRICAO,
						   NVL((SELECT M.MARCA FROM PCMARCA M WHERE M.CODMARCA = P.CODMARCA), P.MARCA) AS MARCA,
						   TRIM(P.INFORMACOESTECNICAS) AS LOCACAO,
						   TO_CHAR(DECODE(P.IMPORTADO, 'N', 'NAC.', 'IMP.')) AS PROCEDENCIA,
						   P.NUMORIGINAL AS VIDE,
						   (SELECT GREATEST(E1.QTESTGER, 0) FROM PCEST E1 WHERE E1.CODPROD = P.CODPROD AND E1.CODFILIAL = ".$CODFILIALNF.") AS QTESTGER,
						   (SELECT GREATEST(E1.QTRESERV, 0) FROM PCEST E1 WHERE E1.CODPROD = P.CODPROD AND E1.CODFILIAL = ".$CODFILIALNF.") AS QTRESERV,
						   (SELECT GREATEST(PKG_ESTOQUE.ESTOQUE_DISPONIVEL(P.CODPROD, ".$CODFILIALNF.", 'VP', TRUNC(SYSDATE)),0) AS QTSALDO from dual) AS SALDO,
						   (SELECT ROUND(DECODE(".$NUMPR.",
												1,
												PR.PVENDA1,
												2,
												PR.PVENDA2,
												3,
												PR.PVENDA3,
												4,
												PR.PVENDA4,
												5,
												PR.PVENDA5,
												6,
												PR.PVENDA6,
												7,
												PR.PVENDA7,
												PR.PVENDA) *
										 (1 - ((NVL(".$PERCDESC.", 0) * (-1)) / 100)),
										 2) + 0.01
							  FROM PCTABPR PR
							 WHERE PR.CODPROD = P.CODPROD
							   AND PR.NUMREGIAO = ".$NUMREGIAO.") AS PTABELA,
						   (SELECT TT.CODTRIBPISCOFINS
							  FROM PCTABTRIB TT
							 WHERE TT.CODPROD = P.CODPROD
							   AND TT.CODFILIALNF = ".$CODFILIALNF."
							   AND TT.UFDESTINO = '".$UFDESTINO."') AS CODTRIBPISCOFINS
					FROM PCPRODUT P
					WHERE P.DTEXCLUSAO IS NULL
					  AND P.NUMORIGINAL IN
						 (SELECT V.VIDE AS PECA
							FROM ORCVIDE V
						   WHERE V.DTEXCLUSAO IS NULL
							 AND V.CODPECA LIKE '".$CODPECA."%'
							 AND V.VIDE IS NOT NULL
							 AND V.APLICMARCA NOT LIKE '%INFO%'
							 AND V.APLICMARCA NOT LIKE '%OPCAO%'
						  UNION
						  SELECT V.CODPECA AS PECA
							FROM ORCVIDE V
						   WHERE V.DTEXCLUSAO IS NULL
							 AND V.VIDE LIKE '".$CODPECA."%'
							 AND V.CODPECA IS NOT NULL
							 AND V.APLICMARCA NOT LIKE '%INFO%'
							 AND V.APLICMARCA NOT LIKE '%OPCAO%')) BASE
			ORDER BY BASE.SALDO DESC, BASE.ORD ASC";

			  $consulta = selectOracle($sql);

				if($debug) varDump2($consulta);
				
				if (empty($consulta)) {
					$dados['CODPECA']    = mb_strtoupper(trim($value["CODPECA"]), 'UTF-8');
			  		$dados['QTPEDIDA']   = intval($value["QUANTIDADE"]);
					if ($dados["somenteDisponiveis"] == "N") {
						adicionaPecaGenerica($dados);
					}
				} else {

					foreach ($consulta as $keyConsulta => $valueConsulta) {

						$valueConsulta['DESCRICAO'] = (!is_null($valueConsulta['DESCRICAO']))?str_replace("'", " ", trim($valueConsulta['DESCRICAO'])):"";
						$valueConsulta['MARCA'] = (!is_null($valueConsulta['MARCA']))?str_replace("'", " ", trim($valueConsulta['MARCA'])):"";
						$valueConsulta['LOCACAO'] = (!is_null($valueConsulta['LOCACAO']))?str_replace("'", " ", trim($valueConsulta['LOCACAO'])):"";
						$valueConsulta['ICMS'] = ((!is_null($valueConsulta['ICMS']))?$valueConsulta['ICMS']:0);

						if (intval($valueConsulta['SALDO']) >= intval($QUANTIDADE)) {
						  $valueConsulta['DISPONIBILIDADE'] = 'IMEDIATA';
						} else {
						  if (intval($valueConsulta['SALDO']) <= 0) {
								$valueConsulta['DISPONIBILIDADE'] = 'SOB CONSULTA';
						  } else {
								$valueConsulta['DISPONIBILIDADE'] = 'IMEDIATA ('.$valueConsulta['SALDO'].')';
						  }
						}

						if (($valueConsulta['PVENDA_301'] <> '') && ($valueConsulta['PVENDA_301'] < $valueConsulta['PTABELA'])) {
						  $valueConsulta['PTABELA'] = $valueConsulta['PVENDA_301'];
						}

						$sql = "INSERT INTO ORCORCAMENTOI (IDORCAMENTO, VALIDACAO,  CODPECA,  NUMORIGINAL, STATUS,  CODVIDE,  CODPROD,  DV,  DESCRICAO, MARCA, QTPEDIDA,   QTDISPPECA, QTDISPONIVEL,  DISPONIBILIDADE, ICMS,   PTABELA,  PVENDA,  PROCEDENCIA, OBSERVACAO, LOCACAO)
						  VALUES
							(/*IDORCAMENTO*/'".$_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']."',
							 /*VALIDACAO*/'".$valueConsulta['ORIGEM']."',
							 /*CODPECA*/'".$CODPECA."',
							 /*NUMORIGINAL*/'".TRIM($valueConsulta['NUMORIGINAL'])."',
							 /*STATUS*/'A',
							 /*CODVIDE*/'".$valueConsulta['CODVIDE']."',
							 /*CODPROD*/'".$valueConsulta['CODPROD']."',
							 /*DV*/'".$valueConsulta['DV']."',
							 /*DESCRICAO*/'".$valueConsulta['DESCRICAO']."',
							 /*MARCA*/'".$valueConsulta['MARCA']."',
							 /*QTPEDIDA*/'".$QUANTIDADE."',
							 /*QTDISPPECA*/FUN_ORC_CONSULTA_SALDO_PECA('".$CODPECA."%'),
							 /*QTDISPONIVEL*/'".$valueConsulta['SALDO']."',
							 /*DISPONIBILIDADE*/'".$valueConsulta['DISPONIBILIDADE']."',
							 /*ICMS*/'".$valueConsulta['ICMS']."',
							 /*PTABELA*/'".$valueConsulta['PTABELA']."',
							 /*PVENDA*/'".$valueConsulta['PTABELA']."',
							 /*PROCEDENCIA*/'".$valueConsulta['PROCEDENCIA']."',
							 /*OBSERVACAO*/'".$valueConsulta['OBSERVACAO']."',
							 /*LOCACAO*/'".$valueConsulta['LOCACAO']."'
						  )";

						if($debug) varDump2($sql); 
						if ($dados["somenteDisponiveis"] == "N"){
							executarOracle($sql);
						} else {
							if ($valueConsulta['SALDO'] > 0) {
								executarOracle($sql);
							}
						}

					}// end foreach ($consulta as $keyConsulta => $valueConsulta)
				}// end if (empty($consulta))
			}// end if (strlen($CODPECA) < 2)
		}// end if (isset($value["CODPECA"]) && !empty($value["CODPECA"]))
	}// end foreach ($ARQUIVO as $key => $value)
}// end if (!empty($ARQUIVO))



redireciona("../../index.php?op=62");