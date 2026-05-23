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

$erros = "<ul>";

if (empty($dados["CNPJ"])) {
	$erros = "<li>O campo CNPJ é obrigatório.</li>";	
} else {
	if (!validar_cnpj($dados['CNPJ'])) {
		$erros = "<li>CNPJ informado {$dados['CNPJ']} é inválido!";
	} else {
		if($ficha = buscaFichaCNPJ($dados['CNPJ'])){
			// varDump2("atualizarFicha");
			$dados['IDFICHACADASTRAL'] = $ficha['IDFICHACADASTRAL'];
			atualizarFicha($dados);
		} else {
			// varDump2("criaNovaFicha");
			criaNovaFicha($dados);
		}
	}
}
if (empty($dados["RAZAOSOCIAL"]))
	$erros .= "<li>O campo RAZAOSOCIAL é obrigatório.</li>";	
if (empty($dados["INSCESTADUAL"]))
	$erros .= "<li>O campo INSCESTADUAL é obrigatório.</li>";	
if (empty($dados["ENDERECO"]))
	$erros .= "<li>O campo ENDERECO é obrigatório.</li>";	
if (empty($dados["NUMERO"]))
	$erros .= "<li>O campo NUMERO é obrigatório.</li>";	
if (empty($dados["BAIRRO"]))
	$erros .= "<li>O campo BAIRRO é obrigatório.</li>";	
if (empty($dados["CEP"]))
	$erros .= "<li>O campo CEP é obrigatório.</li>";	
if (empty($dados["UF"]))
	$erros .= "<li>O campo UF é obrigatório.</li>";	
if (empty($dados["MUNICIPIO"]))
	$erros .= "<li>O campo MUNICIPIO é obrigatório.</li>";	
if (empty($dados["EMAIL"]))
	$erros .= "<li>O campo EMAIL é obrigatório.</li>";		
if (empty($dados["TELCELULAR"]))
	$erros .= "<li>O campo TELCELULAR é obrigatório.</li>";		
if (empty($dados["COMPRADOR1NOME"]))
	$erros .= "<li>O campo Nome do comprador é obrigatório.</li>";		
if (empty($dados["COMPRADOR1CELULAR"]))
	$erros .= "<li>O campo Tel Celular do Comprador é obrigatório.</li>";		
if (empty($dados["COMPENDERECO1"]))
	$erros .= "<li>O campo Comprovante de Endereço é obrigatório.</li>";	


if ($erros <> "<ul>") {
	$erros .= "</ul>";
	insereModal("danger", $erros);
} else {
	insereModal("success", "Ficha cadastral registrada com sucesso!");

	if ($ficha = buscaFichaCNPJ($dados['CNPJ'])) {
		abreNova("pages/financeiro/gera_ficha_cadastral.php?IDFICHACADASTRAL={$ficha['IDFICHACADASTRAL']}");
		enviarFichaEmail($ficha);
	}
}

?>