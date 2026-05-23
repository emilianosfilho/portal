<?php  
$resultado = [
	'ERRO'=>[], 
	'UPDATE'=>[], 
	'INSERT'=>[]
];

if ($_SESSION['ARQUIVO']) {
	foreach ($_SESSION['ARQUIVO'] as $key => $value) {

		if ($value['IDVIDE']) {
			if(salvarAlteracaoVide($value) == false){
				$resultado['ERRO'][] = $value;
			} else {
				$resultado['UPDATE'][] = $value;
			}
		} else {
			if(salvarNovoVide($value) == false){
				$resultado['ERRO'][] = $value;
			} else {
				$resultado['INSERT'][] = $value;
			}
		}
	}
}