<?php  
if (isset($_SESSION['PRECIFICACAO'])) 
	unset($_SESSION['PRECIFICACAO']);
$_SESSION['PRECIFICACAO'] = array();

if (!isset($_SESSION['ARQUIVO']) || empty($_SESSION['ARQUIVO'])) {
	exibeMensagem("Arquivo PRECIFICACAO não identificado pelo sistema");
	die();
} else {
	// varDump2("Entrou no VALIDA ARQUIVO");
	// varDump2($_SESSION['ARQUIVO']); 
}

foreach ($_SESSION['ARQUIVO'] as $key => $value) {

	//////////////////////////////////////////////////////////////////////
	$value['CODPROD'] = intval($value['CODPROD']);
	if ($value['CODPROD'] > 0) {

		// varDump2($value); die();
		if (!isset($_SESSION['PRECIFICACAO'][$value['CODPROD']])) {

			if ($value['DV'] <> "") {
				$value['DV'] = intval($value['DV']);
			} else {
				$value['DV'] = "";
			}

			if ($produto = validaPrecoProdut($value['CODPROD'])){
				foreach ($produto as $keyProd => $valueProd) {
					$value[$keyProd] = trim($valueProd);
				}
			}

			$value['PVENDA'] = moedaPHP($value['PVENDA']);
			if ($value['PVENDA'] < 0.0001) {
					$value['V_PVENDA'] = "O CAMPO PVENDA É OBRIGATÓRIO E NÃO PODE SER VAZIO E NEM NEGATIVO";
			} else {
				if ($value['PVENDA'] == 0.0001) {
					$value['PVENDA'] = round($value['PVENDA'], 4);
				} else {
					$value['PVENDA'] = round($value['PVENDA'], 2);
				}
				$_SESSION['PRECIFICACAO'][$value['CODPROD']] = $value;
			}
		}
	}
}

// varDump2($_SESSION['PRECIFICACAO']);