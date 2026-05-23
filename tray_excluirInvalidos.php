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
		<title>VEMAP EXCLUSÃO INVÁLIDOS - TRAY</title>
		<link rel="icon" href="favicon.ico" type="image/png">
		<script type="text/javascript" src="dist/js/bootstrap.bundle.min.js"></script>
		<script type="text/javascript" src="plugins/jquery/dist/jquery-3.5.1.js"></script>
		<script type="text/javascript" src="dist/js/moment.min.js"></script>

	</head>
	<body style="background-color: #000; color: #fff;">

			<div class="terminal__screen">
				<?php
				$debug = true;
				$inicio = microtime(true);
				$excluidos = 0;


				prompt("Iniciando processamento...");

				//###########################################
				// CARREGA A LISTA TOTAL DE PRODUTOS NA TRAY
				//###########################################
				$tray_products = tray_listarProdutos();
				if ($tray_products) {
					prompt(moeda(count($tray_products),0)." produtos na tray a serem validados.");
					$min_date = strtotime('2025-12-15');
					// varDump2($min_date);
					foreach ($tray_products as $product_id => $product) {
						$modified = strtotime($product["modified"]);
						if ($modified < $min_date) {
							if (tray_produto_excluir($product_id) ){
								$excluidos++;
								varDump2("Data inválida: {$product["modified"]} tray_produto_excluir, product_id: {$product_id}");
							}
						}
					}
				}

				//###########################################
				// FINALIZA O PROCESSAMENTO
				//###########################################
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
				prompt("Qtde excluidos: ".$excluidos);
				prompt("Tempo de processamento por peça por minuto: ".(floor(60/($tempoTotal/$excluidos))));

				?>
			</div>

	</body>
</html>





