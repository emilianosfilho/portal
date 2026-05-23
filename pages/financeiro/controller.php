<?php 
// varDump2($dados);
if(isset($dados['acao'])){

	switch ($dados['acao']) {

		case 'gerarFichaCadastral':
			include('pages/financeiro/gera_ficha_cadastral.php');
			break;

		case 'validarFichaPJ':
			include('pages/financeiro/validarFichaPJ.php');
			break;

		case 'validarFichaPF':
			include('pages/financeiro/validarFichaPF.php');
			break;

		case 'validarCGCENT':
			if (validarCGCENT($dados)){
				redireciona("ficha.php?op=identificacao&TIPOFJ=".$dados['TIPOFJ']);
			}
			break;

		case 'atualizaDadosEmpresariais':
			// varDump2($dados);
			if (atualizaDadosEmpresariais($dados)){
				$_SESSION['FICHA'] = buscaDadosFichaID($dados['IDFICHACADASTRAL']);
				redireciona("ficha.php?op=contatos");
			}
			break;

		case 'atualizarContatos':
			if (atualizarContatos($dados)){
				$_SESSION['FICHA'] = buscaDadosFichaID($dados['IDFICHACADASTRAL']);
				redireciona("ficha.php?op=compradores");
			}
			break;

		case 'atualizarCompradores':
			if (atualizarCompradores($dados)){
				$_SESSION['FICHA'] = buscaDadosFichaID($dados['IDFICHACADASTRAL']);
				redireciona("ficha.php?op=comprovantes");
			}
			break;

		case 'atualizarComprovantes':
			if (atualizarComprovantes($dados)){
				$_SESSION['FICHA'] = buscaDadosFichaID($dados['IDFICHACADASTRAL']);
				redireciona("ficha.php?op=fichaCadastralPreview");
			}
			break;

		case 'exportaFicha':
			abreNova('ficha.php?op=fichaCadastral'.(($dados['IDFICHACADASTRAL']<>"")?'&IDFICHACADASTRAL='.$dados['IDFICHACADASTRAL']:'').(($dados['TIPOFJ']<>"")?'&TIPOFJ='.$dados['TIPOFJ']:'').(($dados['CGCENT']<>"")?'&CGCENT='.$dados['CGCENT']:''));
			break;

		case 'validaHome':
			if ($dados['TIPOFJ']=="F") {
				redireciona("ficha.php?op=dadosPessoais&TIPOFJ=".$dados['TIPOFJ']."&CGCENT=".$dados['CGCENT']);
			} else {
				redireciona("ficha.php?op=dadosEmpresariais&TIPOFJ=".$dados['TIPOFJ']."&CGCENT=".$dados['CGCENT']);
			}
			break;

		case 'validaDadosPessoais':
			if (is_array($dados)) {
				foreach ($dados as $key => $value) {
					$_SESSION['FICHA'][$key] = mb_strtoupper($value, 'UTF-8');
				}
			}
			// varDump2($_SESSION['FICHA']);
			redireciona('ficha.php?op=contatos');
			break;
			
		case 'validaContatos':
			if (is_array($dados)) {
				foreach ($dados as $key => $value) {
					$_SESSION['FICHA'][$key] = $value;
				}
			}
			redireciona('ficha.php?op=compradores');
			break;

		case 'validaCompradores':
			if (is_array($dados)) {
				foreach ($dados as $key => $value) {
					$_SESSION['FICHA'][$key] = $value;
				}
			}
			redireciona('ficha.php?op=documentos');
			break;


		case 'validaDocumentos':
			if (isset($_FILES)) {
				foreach ($_FILES as $keyFiles => $file) {
					if($file["name"] <> ""){
						if ($file["error"]<>0) {
							switch ($_FILES[$keyFiles]["error"]) {
								case 1: exibeMensagem('O arquivo enviado excede a diretiva upload_max_filesize em php.ini'); break;
								case 2: exibeMensagem('O arquivo enviado excede a diretiva MAX_FILE_SIZE especificada no formulário HTML'); break;
								case 3: exibeMensagem('O arquivo enviado foi enviado apenas parcialmente'); break;
								case 4: exibeMensagem('Nenhum arquivo foi carregado'); break;
								case 6: exibeMensagem('Faltando uma pasta temporária'); break;
								case 7: exibeMensagem('Falha ao gravar o arquivo no disco.'); break;
								case 8: exibeMensagem('Uma extensão PHP interrompeu o upload do arquivo.'); break;
							}
						} else {

							// varDump2($file);
							$dir = @DIR_UPLOAD;
							if (!is_dir($dir)) {
									die('ERRO Não encontrado Diretório '.$dir);
							} else {
									if($debug) varDump2($dir . " pasta encontrada com sucesso");
							}


							$separador          = ".";
							$file               = $_FILES[$keyFiles]['name'];
							$nometmp            = $_FILES[$keyFiles]['tmp_name'];
							$filename           = $dir . $file;
							$extensao           = strtolower(end(explode($separador, $file)));
							$inputFileName 			= $keyFiles . @date('_Ymd_His').'.'.$extensao;
							if(move_uploaded_file($nometmp,  $dir . $inputFileName)){
								$_SESSION['FICHA'][$keyFiles] = $inputFileName;
							} else {
								die("ERRO Arquivo ".$inputFileName." Não foi movido para o servidor!");
							}

						}  
					}
				}
			}
			if( salvarFicha($_SESSION['FICHA']) ){
				exibeMensagem("Ficha Cadastral preenchida com sucesso!");
				redireciona('ficha.php?op=fichaCadastral&TIPOFJ='.$_SESSION['FICHA']['TIPOFJ'].'&CGCENT='.$_SESSION['FICHA']['CGCENT']);
			}
			break;

		case 'AdicionarClienteAliquotaRR':
			AdicionarClienteAliquotaRR($dados);
			break;


		case 'enviarEmail':
			enviarFichaEmail($dados);
			break;
		
	}	
}