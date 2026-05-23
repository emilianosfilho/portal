<?php
require_once "../../pages/conf/define.php";
require_once "../..//pages/conf/functions.php";
require_once "../..//pages/conf/conectaOracle.php";
require_once "../..//pages/logistica/function.php";

if(isset($_POST) && !empty($_POST)){
  $dados = $_POST;
} else {
  $dados = $_GET;
}

// varDump2($dados);

if (isset($dados['acao']) && !empty($dados['acao'])) {
    switch ($dados['acao']) {
        case 'listaSubcategorias':
            $ret = listarSubcategorias($dados['categoria_id']);
            if ($ret) {
                echo json_encode(['status' => 'sucesso', 'response' => 'Consulta realizada com sucesso', 'data' => $ret]);
            } else {
                echo json_encode(['status' => 'erro', 'response' => 'Erro ao realizar a Consulta', 'data' => false]);
            }
            break;

        case 'buscar_cliente':
            if (!$dados['codcli'] || $dados['codcli'] == "") {
                echo json_encode(['status' => 'erro', 'response' => 'Erro ao realizar a Consulta', 'data' => false]);
            } else {
                
                $sql = "SELECT   c.codcli, c.cliente FROM   pcclient c WHERE   c.codcli = {$dados['codcli']}";
                $ret = selectOracle($sql);
                if ($ret) {
                    echo json_encode(['status' => 'sucesso', 'response' => 'Consulta realizada com sucesso', 'data' => reset($ret)]);
                } else {
                    echo json_encode(['status' => 'erro', 'response' => 'Erro ao realizar a Consulta', 'data' => false]);
                }
            }
            break;

        default:
            echo json_encode(['status' => 'erro', 'response' => 'Erro acao inválida: '.$dados['acao'] ]);
            break;
        
    }
}
exit;