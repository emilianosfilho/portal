<?php  
include "pages/relatorios/function.php";
if (isset($_SESSION['msg'])) {
  echo $_SESSION['msg'];
  unset($_SESSION['msg']);
}              

// varDump2($_POST); 
// die();

$_SESSION['DATAS'] = $_POST;

switch ($_POST["relatorio"]) {
  case 'emitirRelProdutosSemEstoque':
    redireciona('pages/relatorios/ExcelProdutosSemEstoque.php');
    break;
  case 'emitirRelProdutosCotados':
    redireciona('pages/relatorios/ExcelProdutosCotados.php');
    break;
  case 'emitirRelProdutosDuplicados':
    redireciona('pages/relatorios/ExcelProdutosDuplicados.php');
    break;
  case 'emitirRelAlteracaoPreco':
    redireciona('pages/relatorios/ExcelAlteracaoPreco.php');
    break;
  case 'emitirRelAcessoNegado':
    redireciona('pages/relatorios/ExcelAcessoNegado.php');
    break;
  case 'emitirRelAcessoPermitidos':
    redireciona('pages/relatorios/ExcelAcessoPermitidos.php');
    break;
  case 'emitirRelLogPecasCliente':
    redireciona('pages/relatorios/ExcelLogPecasCliente.php');
    break;
  case 'emitirRelLogContatos':
    redireciona('pages/relatorios/ExcelLogContatos.php');
    break;
  case 'emitirRelLogVides':
    redireciona('pages/relatorios/ExcelLogVide.php');
    break;
}
?>
