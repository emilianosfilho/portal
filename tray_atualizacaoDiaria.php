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
		<title>VEMAP ATUALIZAÇÃO DIÁRIA - TRAY</title>
		<link rel="icon" href="favicon.ico" type="image/png">
		<script type="text/javascript" src="dist/js/bootstrap.bundle.min.js"></script>
		<script type="text/javascript" src="plugins/jquery/dist/jquery-3.5.1.js"></script>
		<script type="text/javascript" src="dist/js/moment.min.js"></script>

	</head>
	<body style="background-color: #000; color: #fff;">

			<div class="terminal__screen">
				<?php
				$inicio = microtime(true);
				$processados = 0;

				// $debug = true;

				prompt("Iniciando processamento...");

				$listaNUMORIGINAL = buscaProdutosDesatualizados();
				prompt(moeda(count($listaNUMORIGINAL),0)." produtos a serem atualizados.");

				/******************************************************/
				/* DEFINE QUANTOS REGISTROS SERÃO PROCESSADOS */
				/******************************************************/
				$maxRegistrosProcessar = 0;
				if ($maxRegistrosProcessar == 0) {
					prompt("Todos os registros devem ser processados.");
				} else {
					prompt("Máximo de registros que devem ser processados {$maxRegistrosProcessar}.");
				}
				/******************************************************/

				// die();

				if (!empty($listaNUMORIGINAL) && array_is_list($listaNUMORIGINAL)) {

					foreach ($listaNUMORIGINAL as $key => $NUMORIGINAL) {

						if ($key == $maxRegistrosProcessar && $maxRegistrosProcessar > 0) {
							break; // script de parada 
						}

						prompt("==================================================");
						prompt("processando registro ".($key+1)." de ".count($listaNUMORIGINAL));

						if (!isset($NUMORIGINAL) || empty($NUMORIGINAL)) {
							prompt("NUMORIGINAL é obrigatório, não pode ser nulo");

						} else {
							prompt("NUMORIGINAL: ".$NUMORIGINAL);

							$produto_Wint = buscaDadosProdutoWinthor($NUMORIGINAL);
							// varDump2($produto_Wint); 
							// die();
							
							if (!$produto_Wint) {
								/*******************************************************/
								/* CASO O PRODUTO WINTHOR NÃO SEJA VÁLIDO */
								/* verifica se existe produto tray se existir, exclui todos */
								/*******************************************************/
								prompt($NUMORIGINAL." Não é um produto winthor válido!");
								if ($produto_Tray_tmp = tray_produto_buscaReference($NUMORIGINAL) ){
									foreach ($produto_Tray_tmp as $product_old) {
										$product_id = $product_old["Product"]["id"];
										tray_produto_excluir($product_id);
									}
								}

							} else {

								prompt(mb_strtoupper($produto_Wint["PRODUTO"]["NOME"], 'UTF-8'));
								
								//#################################################################################
								//VALIDA CATEGORIA
								$category_id 		= '25';
								$category_name 	= 'Estoque';
								if (!empty($produto_Wint["PRODUTO"]["CATEGORIA"])) {
									$category_name = ucfirst(strtolower(trim($produto_Wint["PRODUTO"]["CATEGORIA"])));
									$category_name = ($category_name == 'Eletrico')?'Elétrica':$category_name;
									$category_name = ($category_name == 'Filtragem')?'Filtros':$category_name;
									$category_name = ($category_name == 'Vedacoes')?'Vedações':$category_name;
									$category_name = ($category_name == 'Hidraulica')?'Hidráulica':$category_name;
									$category_name = ($category_name == 'Mecanica')?'Mecânica':$category_name;
									$category_name = ($category_name == 'Transmissao')?'Transmissão':$category_name;
									$category = tray_categoriaConsultar($category_name);
									if (isset($category["id"]) && !empty($category["id"]) ) {
										$category_id 		= $category["id"];
										$category_name 	= $category["name"];
									} else {
										$category_id 		= '25';
										$category_name 	= 'Estoque';
									}
								}
								prompt("category: " . $category_id.' - '.$category_name);
								// die();

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
									"price" => moedaPHP($produto_Wint["PRODUTO"]["PVENDA"]),
								);
								if (isset($categoryPromo_id)) {
									$product["related_categories"] = $categoryPromo_id;
								}

								/*******************************************************/
								/* CASO O PRODUTO WINTHOR SEJA VÁLIDO */
								/* verifica se existe produto tray */
								/* se simo primeiro será atualizado */
								/* 		se existir mais de um os excedentes serão excluidos */
								/* senao cadastra o produto na tray */
								/*******************************************************/
								if ($produto_Tray_tmp = tray_produto_buscaReference($NUMORIGINAL) ){
									// varDump2($produto_Tray_tmp);
									foreach ($produto_Tray_tmp as $key => $product_old) {
										$product_id = $product_old["Product"]["id"];
										prompt("product_id: " . $product_id);
										if ($key == 0) {
											tray_produto_atualizar($product_id, $product);
										} else {
											tray_produto_excluir($product_id);
										}
									}
								} else {
									$product_id = tray_produto_Cadastrar($product);
								}

								$tray = tray_produto_buscaID($product_id);
								$produto_Tray = $tray[0]['Product'];
								// varDump2($produto_Tray);
								// die();

								$saldoTotal = 0;
								if ($produto_Wint["VARIANTES"]) {
									foreach ($produto_Wint["VARIANTES"] as $keyVar => $valueVar) {
										$saldoTotal += moeda($valueVar["QTSALDO"]);
									}
								}	
								prompt("saldoTotal: " . $saldoTotal);

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
						$processados++;
						// die();

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

	</body>
</html>





