<?php  
$debug = false;

if($debug) varDump2("Entrou no validaArquivoAlteracaoMassa");

$PRODUTOS = $_SESSION['ARQUIVO'];

foreach ($PRODUTOS as $key => $value) {

	//////////////////////////////////////////////////////////////////////
	if ($value['CODPROD'] == "") {
		unset($PRODUTOS[$key]);
	} else {
		if ($CODPROD = validaCODPROD($value['CODPROD'])) {
			$PRODUTOS[$key]['V_CODPROD'] = false;
			$PRODUTOS[$key]['CODPROD'] 	= $CODPROD;
		} else {
			$PRODUTOS[$key]['V_CODPROD'] = "CODPROD informado não é válido pois não existe no winthor";
		}
	}


	//////////////////////////////////////////////////////////////////////
	if ($value['NUMORIGINAL'] == "") {
		$PRODUTOS[$key]['NUMORIGINAL'] = null;
		$PRODUTOS[$key]['V_NUMORIGINAL'] = "Sem Alteração";
	} else {
		$PRODUTOS[$key]['NUMORIGINAL'] = trim($value['NUMORIGINAL']);
		$PRODUTOS[$key]['NUMORIGINAL'] = mb_strtoupper($PRODUTOS[$key]['NUMORIGINAL'], 'UTF-8');
		$tamanhoNUMORIGINAL = strlen($PRODUTOS[$key]['NUMORIGINAL']);
		if ($tamanhoNUMORIGINAL > 20) {
			$PRODUTOS[$key]['V_NUMORIGINAL'] = "O campo NUMORIGINAL deve ter no máximo 20 caracteres e atualmente possui ".$tamanhoNUMORIGINAL." caracteres";
		} else {
			$PRODUTOS[$key]['V_NUMORIGINAL'] = false;
		}
	}


	//////////////////////////////////////////////////////////////////////
	if (is_null($value['MARCA'])) {
		$PRODUTOS[$key]['V_CODMARCA'] = "Sem Alteração";
		$PRODUTOS[$key]['MARCA'] = null;
	} else {
		$PRODUTOS[$key]['MARCA'] = trim($value['MARCA']);
		$PRODUTOS[$key]['MARCA'] = mb_strtoupper($PRODUTOS[$key]['MARCA'],'UTF-8');
		if ($MARCA = validaMARCA($PRODUTOS[$key]['MARCA'])) {
			if ($MARCA['ATIVO'] == 'N') {
				$PRODUTOS[$key]['V_CODMARCA'] = "O CAMPO MARCA NO ARQUIVO DE PRODUTO É INVÁLIDO POIS EXISTE MAS ESTA COMO INATIVA NO WINTHOR";
			} else {
				$PRODUTOS[$key]['V_CODMARCA'] 	= false;
				$PRODUTOS[$key]['CODMARCA'] = $MARCA['CODMARCA'];
			}
		} else {
			$PRODUTOS[$key]['V_CODMARCA'] = "A MARCA ".$MARCA." É INVÁLIDA POIS NÃO EXISTE NA TABELA DE MARCA DO WINTHOR";
		}
	}

	//////////////////////////////////////////////////////////////////////
	if ($value['DESCRICAO'] == "") {
		$PRODUTOS[$key]['DESCRICAO'] = false;
		$PRODUTOS[$key]['V_DESCRICAO'] = "Sem Alteração";
	} else {
		$PRODUTOS[$key]['DESCRICAO'] = mb_strtoupper(trim($value['DESCRICAO']) , 'UTF-8');
		if (strlen($value['DESCRICAO']) > 40) {
			$PRODUTOS[$key]['V_DESCRICAO'] = "O campo DESCRICAO deve ter no máximo 40 caracteres e atualmente possui ".strlen($value['DESCRICAO'])." caracteres";
		} else {
			$PRODUTOS[$key]['V_DESCRICAO'] = false;
		}
	}

	//////////////////////////////////////////////////////////////////////
	if ($value['LOCACAO'] == "" || $value['LOCACAO'] == ".") {
		$PRODUTOS[$key]['LOCACAO'] = "9999";
	} else {
		$PRODUTOS[$key]['LOCACAO'] = trim($value['LOCACAO']);
		$PRODUTOS[$key]['LOCACAO'] = mb_strtoupper($PRODUTOS[$key]['LOCACAO'],'UTF-8');
		if (strlen($PRODUTOS[$key]['LOCACAO']) < 2) {
			$PRODUTOS[$key]['V_LOCACAO'] = "O campo LOCACAO deve ter no mínimo 2 caracteres e atualmente possui ".strlen($value['LOCACAO'])." caracteres";
		}
	}

	//////////////////////////////////////////////////////////////////////
	if ($value['CODEPTO'] == "") {
		$PRODUTOS[$key]['V_CODEPTO'] = "Sem Alteração";
	} else {
		$PRODUTOS[$key]['CODEPTO'] = intval($value['CODEPTO']);
		if ($PRODUTOS[$key]['DEPARTAMENTO'] = validaCODEPTO($PRODUTOS[$key]['CODEPTO'])) {
			$PRODUTOS[$key]['V_CODEPTO'] = false;
		} else {
			$PRODUTOS[$key]['V_CODEPTO'] = "CODEPTO informado é INVÁLIDO pois não existe no Winthor";
		}
	}

	//////////////////////////////////////////////////////////////////////
	if ($value['CODSEC'] == "") {
		$PRODUTOS[$key]['V_CODSEC'] = "Sem Alteração";
	} else {
		$PRODUTOS[$key]['CODSEC'] 	= intval($value['CODSEC']);
		
		if ($PRODUTOS[$key]['SECAO'] = validaCODEPTO($value['CODSEC'])) {
			$PRODUTOS[$key]['V_CODSEC'] = false;
		} else {
			$PRODUTOS[$key]['V_CODSEC'] = "CODSEC informado é INVÁLIDO pois não existe no Winthor";
		}
	}


}

if (isset($_SESSION['PRODUTOS'])) {
	unset($_SESSION['PRODUTOS']);
}
$_SESSION['PRODUTOS'] = $PRODUTOS;
// varDump2($_SESSION['PRODUTOS']);