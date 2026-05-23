<?php

if (isset($_SESSION['ARQUIVO'])) {
	unset($_SESSION['ARQUIVO']);
}
$_SESSION['ARQUIVO'] = array();

if ($debug) varDump2($_FILES["arquivo"]);

///////////////////////////////////////////////////////////////////////////////////////////
// SALVA O ARQUIVO NO SERVIDOR
if (!isset($_FILES["arquivo"]) || $_FILES["arquivo"]["error"] <> 0) {
	switch ($_FILES["arquivo"]["error"]) {
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

	$dir = @DIR_UPLOAD;
	if (!is_dir($dir)) {
		die('ERRO Não encontrado Diretório '.$dir);
	} else {
		if($debug) varDump2($dir . " pasta encontrada com sucesso");
	}


	$NOMEARQUIVO        = $dados['NOMEARQUIVO'];
	$separador          = ".";
	$file               = $_FILES["arquivo"]["name"];
	$nometmp            = $_FILES["arquivo"]["tmp_name"];
	$filename           = $dir . $file;

	$extensao           = explode($separador, $file);
	$extensao           = end($extensao);
	$extensao           = strtolower($extensao);
	$extecoesPermitidas = array('csv', 'xls', 'xlsx');
	//apagaArquivos($dir); 
	if(!in_array($extensao, $extecoesPermitidas)){
		exibeMensagem('ERRO Somente arquivos com a extensão .xlsx ou xls será aceito!  A extenção do arquivo atual é: '.$extensao);
		die;
	}

	$inputFileName = $dir .@date('Ymd_His_').$NOMEARQUIVO.'.'.$extensao;
	if(!move_uploaded_file($nometmp,  $inputFileName)){
		varDump2($nometmp);
		varDump2($inputFileName);
		exibeMensagem('ERRO ao importar o Arquivo '.$filename);
		varDump2("ERRO Arquivo ".$inputFileName." Não foi movido para o servidor!");
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

		$cabecalho = [];
		$ARQUIVO   = [];

		foreach ($sheet->getRowIterator() as $rowIndex => $row) {

		    $cellIterator = $row->getCellIterator();
		    $cellIterator->setIterateOnlyExistingCells(false);

		    $linha = [];

		    foreach ($cellIterator as $cellIndex => $cell) {
		        $valor = mb_strtoupper(trim((string) $cell->getValue()), 'UTF-8');

		        // Linha de cabeçalho
		        if ($rowIndex === 1) {
		            $cabecalho[$cellIndex] = $valor;
		        } else {
		            $linha[$cellIndex] = $valor;
		        }
		    }

		    // Ignora cabeçalho vazio
		    if ($rowIndex === 1) {
		        continue;
		    }

		    // Remove pares onde cabeçalho e valor são ""
		    foreach ($linha as $idx => $valor) {
		        $header = $cabecalho[$idx] ?? '';

		        if ($header === '' && $valor === '') {
		            unset($linha[$idx], $cabecalho[$idx]);
		        }
		    }

		    // Ignora linha totalmente vazia
		    if (!array_filter($linha, fn($v) => $v !== '')) {
		        continue;
		    }

		    // Garante alinhamento correto
		    $ARQUIVO[] = array_combine($cabecalho, $linha);
		}

		$_SESSION['ARQUIVO'] = $ARQUIVO;

		if($debug) varDump2($_SESSION['ARQUIVO']);
	}
}