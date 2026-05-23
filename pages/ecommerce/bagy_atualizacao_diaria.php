<?php 
require_once 'HTTP/Request2.php';
require_once("../conf/define.php");
require_once("../conf/functions.php");
require_once("../conf/conectaOracle.php");
require_once("wint_function.php");
require_once("bagy_api_produto.php");
require_once("bagy_api_categoria.php");
require_once("bagy_api_imagem.php");
require_once("bagy_api_variante.php");

// Captura o tempo de início da execução
$start = microtime(true);

$prompt = [];
$dias    = $_GET['dias']    ?? null;
$inicial = $_GET['inicial'] ?? null;

$valido = false;
if (!isset($dias)) {
	$prompt[] = "ERRO Dias a atualizar não informado corretamente.";
} else {
	$valido = true;
	$dias = intval($dias);
	$prompt[] = "SUCESSO Dias a atualizar: " . $dias;
	if (!isset($inicial)) {
		$prompt[] = "ERRO Inicial a atualizar não informado corretamente.";
	} else {
		$valido = true;
		$inicial = trim(mb_strtoupper($inicial));
		$prompt[] = "SUCESSO Inicial a atualizar: " . $inicial;
	}
}


$elegiveis = bagy_buscaProdutosMovimentadosInicial($dias, $inicial);
if ($valido == false || empty($elegiveis) ){
	$prompt[] = "Nenhum produto elegível para atualização.";

} else {

	$prompt[] = count($elegiveis)." Produtos elegível para atualização.";
	foreach ($elegiveis as $key => $NUMORIGINAL) {

		if ($NUMORIGINAL == 'A11822' ) {
			$prompt[] = "++++++++++++++++++++++++++++++++++++++++++++++++++++";
			$prompt[] = "Registro ".($key+1)." de ".count($elegiveis);
			$prompt[] = "validando o NUMORIGINAL: {$NUMORIGINAL}";

			$prod_wint = buscaDadosProdutoWinthor($NUMORIGINAL);
			// $prompt[] = json_encode ($prod_wint);

			if ($prod_wint==false || empty($prod_wint)) {

				$prompt[] = "ERRO Nenhum produto Wint válido encontrado para o NUMORIGINAL: {$NUMORIGINAL}";
			} else {
				
				$prompt[] = "INFO nome Produto Winthor: ".$prod_wint['PRODUTO']['NOME'];

				$prod_bagy = bagy_produto_existe($NUMORIGINAL);

				if ($prod_bagy['data'] == false || empty($prod_bagy['data'])) {
					
					$novoProd_bagy = bagy_produto_cadastrar($prod_wint['PRODUTO']);
					$prompt[] = json_encode($novoProd_bagy);

					// if($novoProd_bagy){
					// 	$prod_bagy['data'][0] = $novoProd_bagy;
					// 	$prompt[] = "SUCESSO ao executar bagy_produto_cadastrar. Product_id: ".$prod_bagy['data'][0]['id'];
					// } else {
					// 	$prompt[] = "ERRO ao executar bagy_produto_cadastrar.";
					// }

				} 

				$prompt[] = "INFO ".count($prod_bagy['data'])." produtos BAGY encontrado para o NUMORIGINAL: {$NUMORIGINAL}";
				
				$data_inicio = new DateTime(@date('Y-m-d'));

				// foreach ($prod_bagy['data'] as $keyPB => $produto_bagy) {
				// 	if ($keyPB == 0) {
				// 		$prompt[] = "INFO Produto Bagy ID: ".$produto_bagy['id'];
				// 		$data_fim = new DateTime($produto_bagy['updated_at']);
				// 		$intervalo = $data_inicio->diff($data_fim);

				// 		if ($intervalo->days > 2) {
				// 			$prompt[] = "INFO Produto Bagy atualizado a mais de 2 dias";

				// 			$prompt[] = bagy_produto_atualizar($produto_bagy['id'], $prod_wint['PRODUTO']);

				// 		} else {
				// 				$prompt[] = "INFO Produto Bagy atualizado a menos de 2 dias";
				// 		}
				// 	} else {
				// 			$prompt[] = "ERRO Produto Bagy duplicado. Product_id: ".$produto_bagy['id'];
				// 	}
				// }

			}

		}

	}

}

// Captura o tempo de fim da execução
$end = microtime(true);

// Calcula a diferença e obtém o tempo de execução em segundos
$duracao_segundos = $end - $start;

$horas = str_pad(floor($duracao_segundos / 3600) , 2 , '0' , STR_PAD_LEFT);
$minutos = str_pad(floor(($duracao_segundos / 60) % 60) , 2 , '0' , STR_PAD_LEFT);
$segundos = str_pad($duracao_segundos % 60 , 2 , '0' , STR_PAD_LEFT);

// Exibe o tempo de execução
$prompt[] = "++++++++++++++++++++++++++++++++++++++++++++++++++++";
$prompt[] = "INFO O script foi executado em: {$horas}:{$minutos}:{$segundos}";

echo json_encode($prompt);

?>