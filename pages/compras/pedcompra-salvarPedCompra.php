<?php  
$CONTA_ERROS 			= 0;
$qtPedidosImportados 	= 0;
$SCRIPTS_SQL 			= array();
$NUMSUGESTAO 			= buscaUltimoNUMSUGESTAO();


if ($_SESSION['PEDIDOS']) {
	$keyPedidoAtual = "";
	
	// varDump2($_SESSION['PEDIDOS']); die();
	
	foreach ($_SESSION['PEDIDOS'] as $keyPedido => $valuePedido) {

		if ($keyPedido <> $keyPedidoAtual) {
			$qtPedidosImportados++;
					$NUMSUGESTAO++;
			$contador 				=0;
			$NUMEROITEMLICIT 	= intval(1);
			$keyPedidoAtual 	= $keyPedido;

			$SCRIPTS_SQL[] = "INSERT INTO PCSUGESTAOCOMPRAC( "
			. "NUMSUGESTAO, "
			. "CODFORNEC, "
			. "CODFILIAL, "
			. "CODUSUARIOSUGESTAO, "
			. "DATASUGESTAO, "
			. "CODEDITAL, "
			. "TIPODESCARGA, "
			. "TIPOEMBALAGEMPEDIDO) 
			VALUES ( "
			. $NUMSUGESTAO.", "
			. $valuePedido[0]["CODFORNEC"].", "
			. $valuePedido[0]["CODFILIAL"].", "
			. $_SESSION['login']['MATRICULA'].", "
			. "TRUNC(SYSDATE), "
			. moedaPHP(1) . ", "//CODEDITAL - 
			. moedaPHP(1) . ", "//TIPODESCARGA - ( 1 - Normal / 5 - Bonificado )
			. "'V')";//TIPOEMBALAGEMPEDIDO - ( M - embalagem Master / V - unidade de Venda )
		}

		foreach ($valuePedido as $keyItem => $valueItem) {
			$SCRIPTS_SQL[] = "INSERT INTO PCSUGESTAOCOMPRAI( "
			. "NUMSUGESTAO, "
			. "NUMEROITEMLICIT, "
			. "CODPROD, "
			. "QTSUGERIDA, "
			. "QTPEDIDO, "
			. "PCOMPRALIQSUGERIDO) 
			VALUES ( "
			. $NUMSUGESTAO . ", "
			. ($keyItem+1) . ", "
			. $valueItem["CODPROD"] . ", "
			. moedaPHP($valueItem["QTPEDIDO"]) . ", "
			. moedaPHP($valueItem["QTPEDIDO"]) . ", "
			. moedaPHP($valueItem["PCOMPRA"]) . ") ";
		}
	}
}

// varDump2($SCRIPTS_SQL);
// die();

if ($SCRIPTS_SQL) {
	foreach ($SCRIPTS_SQL as $sql) {
		if(!executarOracle($sql)){
			exibeMensagem('ERROS foram encontrados ao salvar os pedidos de Compra');
			die();
		}
	}
	exibeMensagem('Sugestão de Pedidos de Compra salvos no Winthor com sucesso!');
	redireciona("index.php?op=117&aba=pedido&acao=clear");
}