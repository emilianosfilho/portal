<br><br><br>
<?php  
require_once("pages/compras/function.php");
require_once("pages/compras/controller.php");
// varDump2($_SESSION['pedidos']);

$SCRIPTS_SQL = array();
$qtPedidosImportados = 0;
$NUMSUGESTAO = buscaUltimoNUMSUGESTAO();

// varDump2($ARQUIVO); die();
if ($_SESSION['pedidos']) {
    
    // BLOCO PARA ARRUMAR OS DADOS
    // $ARQUIVO = unique_multidim_array($ARQUIVO, 'CODPROD');

	$NUMPEDIDO_ATUAL = "";

	foreach ($_SESSION['pedidos'] as $keyPedido => $valueArquivo) {
        // varDump2($valueArquivo); die();
		
        foreach ($_SESSION['pedidos'][$keyPedido] as $keyItem => $valueItem) {
    
            if ($keyPedido <> $NUMPEDIDO_ATUAL) {
                $contador=0;
                $qtPedidosImportados++;
        		$NUMSUGESTAO++;
                $NUMEROITEMLICIT = intval(1);
                $NUMPEDIDO_ATUAL = $keyPedido;

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
                . "'".$NUMSUGESTAO."', "
                . "'".$valueItem["CODFORNEC"]."', "
                . "'".$valueItem["CODFILIAL"]."',"
                . "'".$_SESSION['login']['MATRICULA']."', "
                . "TRUNC(SYSDATE), "
                . "'1', "//CODEDITAL - 
                . "'1', "//TIPODESCARGA - ( 1 - Normal / 5 - Bonificado )
                . "'V')";//TIPOEMBALAGEMPEDIDO - ( M - embalagem Master / V - unidade de Venda )
            }

            $SCRIPTS_SQL[] = "INSERT INTO PCSUGESTAOCOMPRAI( "
            . "NUMSUGESTAO, "
            . "NUMEROITEMLICIT, "
            . "CODPROD, "
            . "QTSUGERIDA, "
            . "QTPEDIDO, "
            . "PCOMPRALIQSUGERIDO) 
            VALUES ( "
            . "" . $NUMSUGESTAO . ", "
            . "" . ($NUMEROITEMLICIT++) . ", "
            . "" . $valueItem["CODPROD"] . ", "
            . "" . $valueItem["QTPEDIDO"] . ", "
            . "" . $valueItem["QTPEDIDO"] . ", "
            . "" . str_replace(',', '.', TRIM($valueItem["PCOMPRA"])) . ") ";
        }

        $contador++;
	}
}

// varDump2($SCRIPTS_SQL); die();

if ($SCRIPTS_SQL) {
	foreach ($SCRIPTS_SQL as $sql) {
		if (!executarOracle($sql)) {
			varDump2($sql);
			exibeMensagem("Erro ao executar o script sql");
			die();
		}
	}
}

exibeMensagem($qtPedidosImportados." Pedidos de Compra processados com sucesso!");

?>