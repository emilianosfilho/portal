<?php 
// $debug = true;
if ($debug) varDump2("ARQUIVO ENVIAR");
if ($debug) varDump2($dados);

///////////////////////////////////////////////////////////////////////////////////////////
// SALVA O ARQUIVO NO SERVIDOR
if (isset($_FILES) && !empty($_FILES)) {
	// varDump2($_FILES);
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

			$dir = @DIR_UPLOAD;
			if (!is_dir($dir)) {
				die('ERRO Não encontrado Diretório '.$dir);
			} else {
				if($debug) varDump2($dir . " pasta encontrada com sucesso");
			}

			$tmp_name  = $valueFiles["tmp_name"];
			$name      = str_replace(" ", "_", mb_strtolower(trim($valueFiles["name"]), 'UTF-8'));
			$filename  = $dir . $name;

			$inputFileName = $dir .@date('Ymd_His_').$name;
			if(!move_uploaded_file($tmp_name,  $inputFileName)){
				varDump2($tmp_name);
				varDump2($inputFileName);
				exibeMensagem('ERRO ao importar o Arquivo '.$filename);
				varDump2("ERRO Arquivo ".$inputFileName." Não foi movido para o servidor!");
				die();
			} else {
				if (!file_exists($inputFileName)) {
					exibeMensagem("Não foi possível encontrar o arquivo " . $inputFileName . " no servidor");
					die();
				} else {
					if ($debug) varDump2("Arquivo ".$inputFileName." enviado para o servidor com sucesso!");


				}
			}
		}  
	}
}