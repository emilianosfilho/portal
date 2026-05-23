<?php  


if (isset($_SESSION['INVENTARIO'])) {
  unset($_SESSION['INVENTARIO']);
}
$LOCACAO = (isset($dados['LOCACAO'])) ? sanitizeOracleString($dados['LOCACAO']) :'';
if($debug) varDump2($LOCACAO);


if (validaLocacao($LOCACAO)) {

  if($debug) varDump2($LOCACAO." - LOCACAO VALIDA");
  
  $inventarios = pesquisaInventarioAbertoLocacao($LOCACAO);
  if($debug) varDump2($inventarios);

} else {

  if($debug) varDump2($LOCACAO." - LOCACAO INVALIDA");
  exibeMensagem('A locação informada '.$LOCACAO.' não é considerada uma locação válida.');
  // redireciona("index.php");
}

