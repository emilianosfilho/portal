<?php
header('Content-Type: application/json');
require_once 'pages/chamados/function.php';

// Em um cenário real, o ID viria da $_SESSION
$idLogado = $_SESSION['login']['IDUSUARIO']; 
$acao = $_GET['acao'] ?? '';

switch ($acao) {
    case 'badge':
        echo json_encode(['total' => get_contagem_notificacoes($idLogado)]);
        break;

    case 'listar':
        echo json_encode(get_lista_notificacoes($idLogado));
        break;

    case 'marcar_lida':
        $input = json_decode(file_get_contents('php://input'), true);
        $sucesso = false;
        if (!empty($input['ids'])) {
            $sucesso = set_notificacoes_lidas($idLogado, $input['ids']);
        }
        echo json_encode(['status' => $sucesso]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Acao invalida']);
        break;
}