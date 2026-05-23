<?php
session_start();
error_reporting(0);
ini_set('display_errors', 0);
ini_set('error_reporting', E_ALL ^ E_NOTICE);
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE));
date_default_timezone_set('America/Manaus');
clearstatcache();
require "../../pages/conf/define.php";
require "../../pages/conf/functions.php";
require "../../pages/conf/conectaOracle.php";
require "../../pages/logistica/function.php";

$dados = $_GET;

if (isset($dados['acao'])) {
    switch ($dados['acao']) {
        case 'checkout':
            gera_Checkout();
            break;

        case 'checkout_loja':
            gera_Checkout_loja();
            break;


    }
}

#####################################################################

function gera_Checkout()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $retorno = [
        'tipo'          => 'CHECKOUT',
        'pedidosNovos'  => 0,
        'pedidos'       => [],
    ];

    $pedidos = buscaPedVendaPendentes();

    $mapPedidos    = [];
    $pedidosNovos  = 0;

    // Garante que a sessão exista
    $_SESSION['PEDIDOSCHECKOUT'] ??= [];

    // Mapeia pedidos únicos pelo NUMPED
    foreach ($pedidos as $pedido) {
        $mapPedidos[$pedido['NUMPED']] = $pedido;
    }

    // Adiciona novos pedidos ao checkout
    foreach ($mapPedidos as $numPed => $pedido) {
        if (!isset($_SESSION['PEDIDOSCHECKOUT'][$numPed])) {
            $pedidosNovos++;
        }
        $_SESSION['PEDIDOSCHECKOUT'][$numPed] = $pedido;
    }

    // Remove pedidos que não existem mais
    foreach ($_SESSION['PEDIDOSCHECKOUT'] as $numPed => $pedido) {
        if (!isset($mapPedidos[$numPed])) {
            unset($_SESSION['PEDIDOSCHECKOUT'][$numPed]);
        }
    }

    $retorno['pedidosNovos'] = $pedidosNovos;
    $retorno['pedidos']      = array_values($_SESSION['PEDIDOSCHECKOUT']);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($retorno);
    exit;
}



function gera_Checkout_loja(){
    $tipo = 'PEDIDOS';
    $pedidos = buscaPedVendaPendentes();
    $pedidosNovos = 0;
    if ($pedidos) {
        foreach ($pedidos as $key1 => $value1) {
            $existe = false;
            foreach ($_SESSION['PEDIDOSCHECKOUT'] as $key2 => $value2) {
                if ($value1['NUMPED'] == $value2['NUMPED']) {
                    $existe = true;
                    $_SESSION['PEDIDOSCHECKOUT'][$key2] = $value1;
                }
            }
            if ($existe === false) {
                $pedidosNovos++;
                $_SESSION['PEDIDOSCHECKOUT'][] = $value1;
            }
        }
    }


    foreach ($_SESSION['PEDIDOSCHECKOUT'] as $key1 => $value1) {
        $existe = false;
        foreach ($pedidos as $key2 => $value2) {
            if ($value1['NUMPED'] == $value2['NUMPED']) {
                $existe = true;
                $_SESSION['PEDIDOSCHECKOUT'][$key2] = $value1;
            }
        }
        if ($existe === false) {
            unset($_SESSION['PEDIDOSCHECKOUT'][$key1]);
        }
    }

    $retorno = array(
        'tipo' => $tipo, //CHECKIN - CHECKOUT
        'pedidos' => $pedidos,
        'pedidosNovos' => $pedidosNovos
    );
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($retorno);
    exit;
}

#####################################################################

function geraPainelCheckIN(){
    $tipo = 'CHECKIN';
    $pedidos = buscaNotaEntradaPendentes();
    $pedidosNovos = count($pedidos);

    if (!isset($_SESSION['PEDIDOSCHECKIN'])) {
        $_SESSION['PEDIDOSCHECKIN'] = array();
    }

    foreach ($pedidos as $key1 => $value1) {
        $existe = false;
        foreach ($_SESSION['PEDIDOSCHECKIN'] as $key2 => $value2) {
            if ($value1['NUMNOTA'] == $value2['NUMNOTA']) {
                $existe = true;
                $_SESSION['PEDIDOSCHECKIN'][$key2] = $value1;
            }
        }
        if (!$existe) {
            $pedidosNovos++;
            $_SESSION['PEDIDOSCHECKIN'][] = $value1;
        }
    }


    foreach ($_SESSION['PEDIDOSCHECKIN'] as $key1 => $value1) {
        $existe = false;
        foreach ($pedidos as $key2 => $value2) {
            if ($value1['NUMNOTA'] == $value2['NUMNOTA']) {
                $_SESSION['PEDIDOSCHECKIN'][$key1] = $value2;
                $existe = true;
            }
        }
        if ($existe === false) {
            unset($_SESSION['PEDIDOSCHECKIN'][$key1]);
        }
    }

    $retorno = array(
        'tipo' => $tipo, //CHECKIN - CHECKOUT
        'pedidos' => $pedidos,
        'pedidosNovos' => $pedidosNovos
    );
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($retorno);
    exit;
}


?>