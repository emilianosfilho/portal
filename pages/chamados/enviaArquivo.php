<?php
$debug = false;
// $debug = true;

if ($debug) varDump2($_FILES["anexo"]);

///////////////////////////////////////////////////////////////////////////////////////////
// SALVA O ARQUIVO NO SERVIDOR
if (!isset($_FILES["anexo"]) || $_FILES["anexo"]["error"] <> 0) {
	switch ($_FILES["anexo"]["error"]) {
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
	$file               = $_FILES["anexo"]["name"];
	$nometmp            = $_FILES["anexo"]["tmp_name"];
	$filename           = $dir . $file;

	$extensao           = explode($separador, $file);
	$extensao           = end($extensao);
	$extensao           = strtolower($extensao);

	$inputFileName			= $file;

	// $extecoesPermitidas = array('csv', 'xls', 'xlsx');
	// //apagaArquivos($dir); 
	// if(!in_array($extensao, $extecoesPermitidas)){
	// 	exibeMensagem('ERRO Somente arquivos com a extensão .xlsx ou xls será aceito!  A extenção do arquivo atual é: '.$extensao);
	// 	die;
	// }

	try {
    // Tenta mover o arquivo para o destino
    if (!move_uploaded_file($nometmp, $filename)) {
      // Se falhar, lançamos uma exceção com detalhes para o catch
      throw new Exception("Falha ao mover o arquivo para o servidor. Verifique permissões de diretório.");
    }

    // Verifica se o arquivo realmente existe após a operação
    if (!file_exists($filename)) {
      throw new Exception("O arquivo foi movido, mas não pôde ser encontrado no destino: " . $inputFileName);
    }

    // Sucesso
    if ($debug) {
      varDump2("Arquivo " . $inputFileName . " movido para o servidor com sucesso!");
    }
    
    return $filename;

	} catch (Exception $e) {
    // Tratamento de erro centralizado
    if ($debug) {
      varDump2("Origem (TMP): " . $nometmp);
      varDump2("Destino: " . $filename);
      varDump2("Mensagem de Erro: " . $e->getMessage());
    }

    exibeMensagem('ERRO ao importar o Arquivo: ' . ($inputFileName));
    
    // Em contextos procedurais de processamento de arquivos, o die() 
    // pode ser substituído por um return false dependendo do fluxo da sua aplicação.
    die("Execução interrompida devido a erro crítico no upload.");
	}
}