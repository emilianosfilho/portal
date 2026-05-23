<?php  
if (isset($_SESSION['ARQUIVO'])) {
	unset($_SESSION['ARQUIVO']);
}
// $debug = true;

if (isset($_FILES) && !empty($_FILES)) {
	
	foreach ($_FILES as $keyFiles => $valueFiles) {
		if ($debug) varDump2($valueFiles);

		for ($i=0; $i < count($valueFiles['name']); $i++) { 
			
			if ($valueFiles["error"][$i] <> 0) {
				switch ($valueFiles["error"][$i]) {
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
					if($debug) varDump2("SUCESSO diretório: " . $dir . " é válido");

					$file['size'] = ($valueFiles["size"][$i]/1024);
					$file['tmp_name'] = $valueFiles["tmp_name"][$i];
					$file['filename'] = mb_strtolower(md5(@date('YmdHis').$valueFiles["name"][$i]).".".end(explode(".", $valueFiles["name"][$i])));
					$file['inputFileName'] = $dir . $file['filename'];

					if($debug) varDump2($file);

					if ($file['size'] > 350) {
						exibeMensagem('ERRO a imagem '.$valueFiles["name"][$i].' excede o tamanho máximo permitido de 350 Kb');
					} else {
						if(!move_uploaded_file($file['tmp_name'],  $file['inputFileName'])){
							varDump2($file['tmp_name']);
							varDump2($file['inputFileName']);
							exibeMensagem('ERRO ao importar o Arquivo '.$file['filename']);
							varDump2("ERRO Arquivo ".$file['inputFileName']." Não foi movido para o servidor!");
						} else {
							if (file_exists($file['inputFileName'])) {
								if($debug) exibeMensagem("SUCESSO ao mover o Arquivo: ".$file['filename']);
								$_SESSION['ARQUIVO'][] = $file['filename'];
							} else {
								exibeMensagem("ERRO Nãomfoi possível localizar o Arquivo: ".$file['filename']);
							}
						}
					}
				}
			}  
		}

		if (count($_SESSION['ARQUIVO']) == count($valueFiles['name'])) {
			if($debug) exibeMensagem("SUCESSO ao enviar ".count($valueFiles['name'])." Arquivos");
		}

	}
}

