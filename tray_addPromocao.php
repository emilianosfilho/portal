<?php 
ini_set("session.gc_maxlifetime", 10800 );// Defina o máximo de tempo da sessão
ini_set("session.cookie_lifetime", 10800 );// Defina a vida útil do cookie da sessão
ini_set("display_errors", 0);
ini_set("error_reporting", E_ALL ^ E_NOTICE ^ E_DEPRECATED);
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
clearstatcache();
require_once("pages/conf/define.php");
require_once("pages/conf/functions.php");
require_once("pages/conf/conectaOracle.php");

require_once("plugins/HTTP_Request2-2.6.0/HTTP/Request2.php");
require_once("pages/ecommerce/wint_function.php");
require_once("pages/ecommerce/tray_api_token.php");
require_once("pages/ecommerce/tray_api_produtos.php");
require_once("pages/ecommerce/tray_api_categorias.php");
require_once("pages/ecommerce/tray_api_variacoes.php");
require_once("pages/ecommerce/tray_api_imagens.php");
?>
<!doctype html>
<html lang="pt-br" class="h-100">
	<head>
		<link href="dist/css/bootstrap.css" rel="stylesheet">
    <link href="dist/css/prompt.css" rel="stylesheet">
		<link href="plugins/fontawesome-free/css/all.css" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta charset="UTF-8">
		<title>TRAY - CADASTRO DE PROMOÇÕES</title>
		<link rel="icon" href="favicon.ico" type="image/png">
		<script type="text/javascript" src="dist/js/bootstrap.bundle.min.js"></script>
		<script type="text/javascript" src="plugins/jquery/dist/jquery-3.5.1.js"></script>
		<script type="text/javascript" src="dist/js/moment.min.js"></script>

	</head>
	<body>
		<h1>TRAY - CADASTRO DE PROMOÇÕES</h1>

		<div class="terminal mt-4 mx-auto">
			<div class="terminal__titlebar">
				<span class="dot red"></span>
				<span class="dot yellow"></span>
				<span class="dot green"></span>
				<span class="terminal__title">TRAY - CADASTRO DE PROMOÇÕES</span>
			</div>

			<div class="terminal__screen">
				<?php
				$inicio = microtime(true);
				$processados = 0;

				// $debug = true;

				// 0: processa todos os registros disponíveis
				$maxRegistrosProcessar = 0;

				prompt("Iniciando processamento...");

				$listaNUMORIGINAL = buscaPromocao561();

				if (empty($listaNUMORIGINAL) || !array_is_list($listaNUMORIGINAL)) {
					
					prompt("Nenhum produtos em promoção para atualização.");

				} else {
				
					prompt(count($listaNUMORIGINAL)." Produtos em promoção para atualização.");

					foreach ($listaNUMORIGINAL as $key => $NUMORIGINAL) {

						if ($maxRegistrosProcessar > 0 && $key == $maxRegistrosProcessar) {
							break; // script de parada 
						}

						if (isset($NUMORIGINAL) && !empty($NUMORIGINAL)) {

							prompt("==================================================");
							prompt("processando registro ".($key+1)." de ".count($listaNUMORIGINAL));
							prompt("NUMORIGINAL: ".$NUMORIGINAL);

							$produto_Wint = buscaDadosProdutoWinthor($NUMORIGINAL);
							// varDump2($produto_Wint); 
							// die();
							
							if (!$produto_Wint) {
								prompt($NUMORIGINAL." Não é um produto winthor válido!");
							} else {
								prompt($NUMORIGINAL." É um produto winthor válido.");
								
								//#################################################################################
								//VALIDA CATEGORIA
								$category_id 		= '';
								$category_name	= '';
								if (!empty($produto_Wint["PRODUTO"]["CATEGORIA"])) {
									$category_name = ucfirst(strtolower(trim($produto_Wint["PRODUTO"]["CATEGORIA"])));
									$category = tray_categoriaConsultar($category_name);
									if (isset($category["Categories"]["Category"]) && count($category["Categories"]["Category"])>0 ) {
										$category_id 	= $category["Categories"]["Category"][0]['id'];
									}
								}
								if ($category_id == '') {
									$category_id = '25';
									$category_name = 'Estoque';
								}
								prompt("Categoria: " . $category_id.'-'.$category_name);

								$categoryPromo_id = 27; // CATEGORIA: Black Friday 2025
								prompt("Categoria da promoção a ser adicionada aos produtos: ".$categoryPromo_id);

								$product = array(
									"name" => mb_strtoupper($produto_Wint["PRODUTO"]["NOME"], 'UTF-8'),
									"description" => trim($produto_Wint["PRODUTO"]["DESCRICAO"]),
									"description_small" => trim($produto_Wint["PRODUTO"]["meta_description"]),
									"reference" => mb_strtoupper($produto_Wint["PRODUTO"]["NUMORIGINAL"], 'UTF-8'),
									"weight" => moedaPHP($produto_Wint["PRODUTO"]["PESOBRUTO"]),
									"length" => moedaPHP($produto_Wint["PRODUTO"]["COMPRIMENTOM3"]),
									"width" => moedaPHP($produto_Wint["PRODUTO"]["LARGURAM3"]),
									"height" => moedaPHP($produto_Wint["PRODUTO"]["ALTURAM3"]),
									"category_id" => $category_id,
									"related_categories" => $categoryPromo_id,
									"price" => moedaPHP($produto_Wint["PRODUTO"]["PVENDA"]),
								);
								// if($debug) varDump2($product);

								/*VALIDA SE O NUMORIGINAL JÁ POSSUI CADASTRO NA TRAY*/
								$produto_Tray = tray_produto_buscaReference($product['reference']);
								if ($produto_Tray === false) {
									$ProdNovo = true;
									tray_produto_Cadastrar($product);
								} else {
									$ProdNovo = false;
									tray_produto_atualizar($produto_Tray["id"], $product);
								}
								$produto_Tray = tray_produto_buscaID($produto_Tray["id"]);

								if (!isset($produto_Tray["id"])) {
									prompt("ERRO product_id inválido");
									die();
								} else {
									$product_id = $produto_Tray["id"];
									prompt("product_id: {$product_id}");
									
									$saldoTotal = 0;
									if ($produto_Wint["VARIANTES"]) {
										foreach ($produto_Wint["VARIANTES"] as $keyVar => $valueVar) {
											$saldoTotal += moeda($valueVar["SALDO"]);
										}
									}

									tray_resetarImagensProduto($product_id, $saldoTotal);	

									tray_variacao_atualizar(
										$product_id, 
										$produto_Wint["VARIANTES"], 
										$produto_Tray["Variant"]
									);

									$field_log = array(
										"product_id" => $product_id,
										"reference" => $produto_Tray['reference'],
										"name" => $produto_Tray["name"]
									);
									TRAY_PRODUCT_insert($field_log);
									
								}
							}
						}
						$processados++;

					} // end foreach $listaNUMORIGINAL
				}

				$fim = microtime(true);

				// Calcula o tempo total em segundos
				$tempoTotal = $fim - $inicio;

				// Converte para horas, minutos e segundos
				$horas   = floor($tempoTotal / 3600);
				$minutos = floor(($tempoTotal % 3600) / 60);
				$segundos = $tempoTotal % 60;

				// Exibe formatado
				$tempo = "";
				if ($horas > 0) {
				    $tempo .= $horas . " horas ";
				}
				if ($minutos > 0 || $horas > 0) {
				    $tempo .= $minutos . " minutos ";
				}
				$tempo .= number_format($segundos, 0) . " segundos";
				
				prompt("==================================================");
				prompt("Tempo de execução Total: ".$tempo);
				prompt("Qtde Peças processadas: ".$processados);
				prompt("Tempo de processamento por peça por minuto: ".(floor(60/($tempoTotal/$processados))));

				?>
			</div>
		</div>

	</body>
</html>





