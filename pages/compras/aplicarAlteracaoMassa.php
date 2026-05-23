<main>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <?php  
				$debug = false;
				// $debug = true;
        include_once("pages/compras/function.php");

				if($debug) varDump2("Entrou no aplicarAlteracaoMassa");

				foreach ($_SESSION['PRODUTOS'] as $key => $value) {
					if($debug) varDump2($value);

					$aplicar = false;
					//////////////////////////////////////////////////////////////////////
					if ($value['CODPROD'] != "") {

						$sql = "UPDATE PCPRODUT SET ";

						if ( !empty($value['NUMORIGINAL']) && empty($value['V_NUMORIGINAL']) ){
							$aplicar = true;
							$sql .= PHP_EOL."NUMORIGINAL = '".$value['NUMORIGINAL']."',";
						}
						if ( !empty($value['CODMARCA']) && empty($value['V_CODMARCA']) ){
							$aplicar = true;
							$sql .= PHP_EOL."CODMARCA = '".$value['CODMARCA']."',";
							$sql .= PHP_EOL."MARCA = '".$value['MARCA']."',";
						}
						if ( !empty($value['DESCRICAO']) && empty($value['V_DESCRICAO']) ){
							$aplicar = true;
							$sql .= PHP_EOL."DESCRICAO = '".$value['DESCRICAO']."',";
						}
						if ( !empty($value['LOCACAO']) && empty($value['V_LOCACAO']) ){
							$aplicar = true;
							$sql .= PHP_EOL."INFORMACOESTECNICAS = '".$value['LOCACAO']."',";
						}
						if ( !empty($value['CODEPTO']) && empty($value['V_CODEPTO']) ){
							$aplicar = true;
							$sql .= PHP_EOL."CODEPTO = '".$value['CODEPTO']."',";
						}
						if ( !empty($value['CODSEC']) && empty($value['V_CODSEC']) ){
							$aplicar = true;
							$sql .= PHP_EOL."CODSEC = '".$value['CODSEC']."',";
						}

						if ($aplicar) {
							$sql = substr($sql, 0, -1);
							$sql .= PHP_EOL."WHERE CODPROD = '".$value['CODPROD']."'";
							if($debug) {
								varDump2($sql); 
							} else {
								executarOracle($sql);
							}
						}
					}
				}
				exibeMensagem("Alteração em massa aplicado com sucesso!");
				redireciona('index.php?op=116');
				?>
		  </div>
		</div>
  </div>
</main>
