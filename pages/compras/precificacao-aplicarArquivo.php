<?php 

if (!isset($_SESSION['PRECIFICACAO'])) {
	exibeMensagem("_SESSION PRECIFICACAO não identificada");
	die();
} else {

	if (validaPCTABPR() == false){
		exibeMensagem("ERRO ao executar validaPCTABPR");
		die();
	} else {

		$_SESSION['RESULTADO']['ERRO'] = [];
		$_SESSION['RESULTADO']['UPDATE'] = [];		

		foreach ($_SESSION['PRECIFICACAO'] as $key => $value) {

			if (!isset($value['PVENDA_NEW'])) {
				$value['PVENDA_NEW'] = $value['PVENDA'];
			}

			if (precificacao_editarPreco($value) == false) {
				$_SESSION['RESULTADO']['ERRO'][] = $value;
			} else {
				$_SESSION['RESULTADO']['UPDATE'][] = $value;
			}
			
		}
	
	}
	
}
