<?php 
ini_set( "session.gc_maxlifetime", 10800 );// Defina o máximo de tempo da sessão
ini_set( "session.cookie_lifetime", 10800 );// Defina a vida útil do cookie da sessão
ini_set("display_errors", 0);
ini_set('error_reporting', E_ALL ^ E_NOTICE ^ E_DEPRECATED);
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
clearstatcache();
require "pages/conf/define.php";
require "pages/conf/functions.php";
require "pages/conf/conectaOracle.php";
?>
<!doctype html>
<html lang="pt-br" class="h-100">
	<head>
		<link href="dist/css/bootstrap.css" rel="stylesheet">
		<link href="plugins/fontawesome-free/css/all.css" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
		<meta name="generator" content="Hugo 0.88.1">
		<meta charset="UTF-8">
		<title>VEMAP - ATUALIZAÇÃO DIÁRIA BAGY</title>
		<link rel="icon" href="favicon.ico" type="image/png">
		<script type="text/javascript" src="dist/js/bootstrap.bundle.min.js"></script>

	</head>
	<body>
		<center>
			<h1>VEMAP - ATUALIZAÇÃO DIÁRIA BAGY</h1>
			<div class="progress">
				<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
			</div>
		</center>
	</body>


	<script type="text/javascript" src="plugins/jquery/dist/jquery-3.5.1.js"></script>
	<script type="text/javascript" src="dist/js/moment.min.js"></script>
	<script type="text/javascript" src="pages/bagy/produto_ajax.js"></script>
	<script type="text/javascript" src="pages/bagy/atributo_ajax.js"></script>
	<script type="text/javascript" src="pages/bagy/produto_function.js"></script>
	<script type="text/javascript" src="pages/bagy/categorias_ajax.js"></script> 

	<script type="text/javascript">
	$(window).on("load", function(){

		var antes = Date.now();
		console.log('<?=@date('Y-m-d H:i:s')?> Iniciando ATUALIZAÇÃO DIÁRIA');

		var hoje = '<?=@date('Y-m-d')?>';

		if (typeof TOKEN == "undefined") {
		  const TOKEN = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzaG9wX2lkIjoxMzQxNTcsInR5cGUiOiJhcGkiLCJlbWFpbCI6IjU4NzgyNjQ0OTYzMTYyNTQwMDAwLmJhZ3lAYXBpLmNvbS5iciIsImZpcnN0X25hbWUiOiJWRU1BUCBXSU5USE9SIiwiYWN0aXZlIjp0cnVlLCJpYXQiOjE2OTMzNDEyNzF9.8KTYG7TjnpFtIzZnFnwNVC5emGksKt8-JPex0LhrDV0";
		}



		bagy_atributo = bagy_atributo_listar();
		bagyAtributo = bagy_atributo.responseJSON["values"];
		console.log(bagyAtributo);


		bagy_categoria = bagy_categoria_listar();
		console.log(bagy_categoria);
		die();

		elegiveis = buscaProdutosElegiveis("A");
		if (typeof elegiveis.responseJSON.data != "undefined") {
			for (var i = 0; i < elegiveis.responseJSON.data.length; i++) {
				NUMORIGINAL = elegiveis.responseJSON.data[i]["NUMORIGINAL"];
				console.log("###################################################");
				console.log("NUMORIGINAL: "+NUMORIGINAL);

				produto_wint = buscaDadosProdutoWinthor(NUMORIGINAL);
				if (typeof produto_wint.responseJSON.data != "undefined") {
					produtoWint = produto_wint.responseJSON.data;
					console.log(produtoWint);

					if (typeof produtoWint['PRODUTO'] != "undefined") {

						produto_bagy = bagy_consultarProdutoExternalID (NUMORIGINAL);
						if (typeof produto_bagy.responseJSON.data != "undefined") {
							produtoBagy = produto_bagy.responseJSON.data;
							console.log(produtoBagy.length+" registros bagy encontrados");
							if (produtoBagy.length > 0) {
								for (var b = 0; b < produtoBagy.length; b++) {
									bagy_deletarProduto (produtoBagy[b]["id"]);
								}
							}
						}

						field_produto = {
							"name":           	produtoWint['PRODUTO']["NOME"].substring(0, 254),
							"description":    	produtoWint['PRODUTO']["DESCRICAO"].substring(0, 65535),
							"external_id":    	produtoWint['PRODUTO']["NUMORIGINAL"].substring(0, 254),
							"weight":         	parseFloat(produtoWint['PRODUTO']["PESOBRUTO"]).toFixed(2), 
							"depth":          	parseFloat(produtoWint['PRODUTO']["COMPRIMENTOM3"]).toFixed(2),
							"width":          	parseFloat(produtoWint['PRODUTO']["LARGURAM3"]).toFixed(2),
							"height":         	parseFloat(produtoWint['PRODUTO']["ALTURAM3"]).toFixed(2),
							"meta_title":       produtoWint['PRODUTO']["NOME"].substring(0, 254).toLowerCase(),
							"meta_description": produtoWint['PRODUTO']["meta_description"].substring(0, 254),
							"meta_keywords":    produtoWint['PRODUTO']["meta_keywords"].substring(0, 254),
							"video":            "https://youtu.be/CmCoAzhr71U?si=qF0d9fF4_sLX6tE4"
						};
						if (produtoWint['PRODUTO']["DEPARTAMENTO"] != null) {
							categoria_bagy = bagy_categoria_buscaName (produtoWint['PRODUTO']["DEPARTAMENTO"]);
							console.log(categoria_bagy);
							die();
							// field_produto["categoria_id"] = categoria_bagy;
						} else {
							field_produto["categoria_id"] = false;
						}
						produto_bagy = bagy_criarProduto(field_produto);
						product_id = produto_bagy.responseJSON["id"];

						estoqueTotal = 0;
						menorPreco = 0;
						if (typeof produtoWint['VARIANTES'] != "undefined") {
							for (var w = 0; w < produtoWint['VARIANTES'].length; w++) {
								if (produtoWint['VARIANTES'][w]["SALDO"] > 0 && 
										produtoWint['VARIANTES'][w]["PVENDA"] > 0.1) {

									estoqueTotal +=produtoWint['VARIANTES'][w]["SALDO"];
									if (menorPreco == 0 || menorPreco > produtoWint['VARIANTES'][w]["PVENDA"]) {
										menorPreco = produtoWint['VARIANTES'][w]["PVENDA"];
									}
								}

								console.log("MARCA: "+produtoWint['VARIANTES'][w]["MARCA"]);

								attribute_value_id = false;
								for (var a = 0; a < bagyAtributo.length; a++) {
									if(bagyAtributo[a]["name"] == produtoWint['VARIANTES'][w]["MARCA"]){
										attribute_value_id = bagyAtributo[a]["id"];
									}
								}
								if (attribute_value_id == false) {
									novoAtributo = bagy_atributo_criar(produtoWint['VARIANTES'][w]["MARCA"]);
									attribute_value_id = novoAtributo.responseJSON["id"];
									
									bagy_atributo = bagy_atributo_listar();
									bagyAtributo = bagy_atributo.responseJSON.data["values"];
									// console.log(bagyAtributo);
								}
								console.log("attribute_value_id: "+attribute_value_id);

								field_variacao = {
									"attribute_value_id": attribute_value_id,
									"balance": produtoWint['VARIANTES'][w]["SALDO"],
									"price": produtoWint['VARIANTES'][w]["PVENDA"],
									"product_id": product_id,
								};
								bagy_criarVariacao(field_variacao);
							}
						}

						bagy_adicionarImagensBatch(product_id, estoqueTotal);

					}

				}

				if (i == 5) { 
					die(); 
				}

			}
		}

	}); // FIM DA FUNCTION load
			
	</script>
</html>