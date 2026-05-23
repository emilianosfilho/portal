<?php 
$debug = true;

$PRODUTOS = tray_listarProdutos($dados);
if($debug) varDump2(count($PRODUTOS)." produtos a serem excluídos na Tray.");


$erro = 0;
foreach ($PRODUTOS as $key1 => $product) {
	if (isset($product["Product"]["id"]) && !empty($product["Product"]["id"])) {
		if($debug) varDump2("Excluíndo produto ID: ".$product["Product"]["id"]);
		if (!tray_excluirProduto($product["Product"]["id"])) {
			$erro++;
		}
	}
	// die();
}

if ($erro==0) {
	exibeMensagem(count($PRODUTOS)." foram excluídos da tray com sucesso!");
}