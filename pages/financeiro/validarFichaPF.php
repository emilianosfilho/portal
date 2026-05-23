<?php  
if (!empty($_FILES)) {
	if($arquivos = enviaArquivos($_FILES)){
		foreach ($arquivos as $arquivo) {
			if (strpos($arquivo, 'COMPDOCUMENTO1')) 
				$dados['COMPDOCUMENTO1'] = $arquivo;
			if (strpos($arquivo, 'COMPDOCUMENTO2')) 
				$dados['COMPDOCUMENTO2'] = $arquivo;
			if (strpos($arquivo, 'COMPENDERECO1')) 
				$dados['COMPENDERECO1'] = $arquivo;
			if (strpos($arquivo, 'COMPENDERECO2')) 
				$dados['COMPENDERECO2'] = $arquivo;
		}
	}
}
// varDump2($dados); 

$erros = "";

if (empty($dados["CPF"])) {
	$erros = "O campo CPF é de preenchimento obrigatório.<br/>";	
} else {
	if (!validar_cpf($dados['CPF'])) {
		$erros = "CPF informado {$dados['CPF']} é inválido!";
	} else {

		if($ficha = buscaFichaCPF($dados['CPF'])){
			// varDump2("atualizarFicha");
			$dados['IDFICHACADASTRAL'] = $ficha['IDFICHACADASTRAL'];
			atualizarFicha($dados);
		} else {
			// varDump2("criaNovaFicha");
			criaNovaFicha($dados);
		}

	}
}

if ($erros <> "") {
	insereModal("danger", $erros);
} else {
	insereModal("success", "Ficha cadastral registrada com sucesso!");

	if($ficha = buscaFichaCPF($dados['CPF'])){
		abreNova("pages/financeiro/gera_ficha_cadastral.php?IDFICHACADASTRAL={$ficha['IDFICHACADASTRAL']}");
		// enviarFichaEmail($ficha);
	}
}

?>