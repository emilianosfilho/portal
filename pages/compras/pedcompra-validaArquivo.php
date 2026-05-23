<?php  
// $debug = true;

$CONTA_ERROS = 0;

$ARQUIVO = $_SESSION['ARQUIVO'];

foreach ($ARQUIVO as $key => $value) {
	if($debug) varDump2($value); 

	if (empty($value['CODPROD']) || trim($value['CODPROD']) == "") {
	  unset($ARQUIVO[$key]);
	} else {

		$ARQUIVO[$key]['CODPROD'] = intval($value['CODPROD']);
		
		if (trim($value['DV']) == "") {
			$CONTA_ERROS++;
			$ARQUIVO[$key]['V_DV'] = "DV É OBRIGATÓRIO E NÃO PODE SER VAZIO";
		} else {
			$ARQUIVO[$key]['DV'] = intval($value['DV']);
			
			$produto = buscaDadosPCPRODUT($value['CODPROD'], $value['CODFORNEC']);
			if($debug) varDump2($produto); 
			if($debug) die();
			if (intval($produto['DV']) != intval($value['DV'])) {
				$CONTA_ERROS++;
				$ARQUIVO[$key]['V_DV'] = "DV INFORMADO ESTÁ INCORRETO";
			} else {
				if ($produto['VALIDACAO'] != "OK") {
					$CONTA_ERROS++;
					$ARQUIVO[$key]['V_CODPROD'] = $produto['VALIDACAO'];
				}
				if (!empty($value['DTEXCLUSAO'])) {
					$CONTA_ERROS++;
					$ARQUIVO[$key]['V_CODPROD'] = "PRODUTO CADASTRADO, PORÉM ESTÁ EXCLUÍDO!";
				} else {
					$ARQUIVO[$key]['DESCRICAO'] 	= str_replace("'", " ", mb_strtoupper(trim($produto['DESCRICAO']),'UTF-8'));
					$ARQUIVO[$key]['CODMARCA'] 		= str_replace("'", " ", mb_strtoupper(trim($produto['CODMARCA']),'UTF-8'));
					$ARQUIVO[$key]['MARCA'] 		= str_replace("'", " ", mb_strtoupper(trim($produto['MARCA']),'UTF-8'));
					$ARQUIVO[$key]['NUMORIGINAL'] 	= str_replace("'", " ", mb_strtoupper(trim($produto['NUMORIGINAL']),'UTF-8'));
					$ARQUIVO[$key]['CODFAB'] 		= str_replace("'", " ", mb_strtoupper(trim($produto['CODFAB']),'UTF-8'));
					$ARQUIVO[$key]['FORNECEDOR'] 	= str_replace("'", " ", mb_strtoupper(trim($produto['FORNECEDOR']),'UTF-8'));
				}
			}
		}

		if (empty($value['NUMPEDIDO']) || trim($value['NUMPEDIDO']) == "") {
			$ARQUIVO[$key]['NUMPEDIDO'] = intval(1);
		}
		$ARQUIVO[$key]['NUMPEDIDO'] = intval($value['NUMPEDIDO']);

		if (empty($value['CODFILIAL']) || trim($value['CODFILIAL']) == "") {
			$ARQUIVO[$key]['CODFILIAL'] = intval(1);
		}
		$ARQUIVO[$key]['CODFILIAL'] = intval($value['CODFILIAL']);

		if (empty($value['CODFORNEC']) || trim($value['CODFORNEC']) == "") {
			$CONTA_ERROS++;
			$ARQUIVO[$key]['V_CODFORNEC'] = "CODFORNEC É OBRIGATÓRIO E NÃO PODE SER VAZIO";
		} else {
			if ($value['FORNECEDOR_EXCLUIDO'] == 'S') {
				$CONTA_ERROS++;
				$ARQUIVO[$key]['V_CODFORNEC'] = "FORNECEDOR CADASTRADO, PORÉM ESTÁ EXCLUÍDO!";
			} else {
				$ARQUIVO[$key]['CODFORNEC'] = intval($value['CODFORNEC']);
				$ARQUIVO[$key]['FORNECEDOR'] = buscaNomeFornecedor($value['CODFORNEC']);
				if (!$ARQUIVO[$key]['FORNECEDOR']) {
					$CONTA_ERROS++;
					$ARQUIVO[$key]['V_CODFORNEC'] = "FORNECEDOR INVÁLIDO";
				}					
			}
		}

		if (empty($value['QTPEDIDO']) || trim($value['QTPEDIDO']) == "") {
			$CONTA_ERROS++;
			$ARQUIVO[$key]['V_QTPEDIDO'] = "QTPEDIDO É OBRIGATÓRIO E NÃO PODE SER VAZIO";
		} else {
			$ARQUIVO[$key]['QTPEDIDO'] = moedaPHP($value['QTPEDIDO']);
		}

		if (empty($value['PCOMPRA']) || trim($value['PCOMPRA']) == "") {
			$CONTA_ERROS++;
			$ARQUIVO[$key]['V_PCOMPRA'] = "PCOMPRA É OBRIGATÓRIO E NÃO PODE SER VAZIO";
		} else {
			$ARQUIVO[$key]['PCOMPRA'] = moedaPHP($value['PCOMPRA']);
		}

		if (empty($value['PCOMINT1']) || trim($value['PCOMINT1']) == "" || moedaPHP($value['PCOMINT1']) <> 0) {
			$CONTA_ERROS++;
			$ARQUIVO[$key]['V_PCOMINT1'] = "PCOMINT1 É OBRIGATÓRIO E NÃO PODE SER VAZIO";
		} else {
			$ARQUIVO[$key]['PCOMINT1'] = moedaPHP($value['PCOMINT1']);
		}
	}
}

if (isset($_SESSION['PEDIDOS'])) {
	unset($_SESSION['PEDIDOS']);
}
$_SESSION['PEDIDOS'] = array();
foreach ($ARQUIVO as $key => $value) {
	$_SESSION['PEDIDOS'][$value['NUMPEDIDO']][] = $value;
}
// varDump2($_SESSION['PEDIDOS']);
// die();
