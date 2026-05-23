<?php 
// Configura o cookie para expirar ao fechar o navegador
session_set_cookie_params(0); 

session_start();
ini_set("display_errors", 1);
date_default_timezone_set('America/Manaus');
ini_set('error_reporting', E_ALL ^ E_NOTICE);
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE));
clearstatcache();
require __DIR__."/pages/conf/define.php";
require __DIR__."/pages/conf/functions.php";
require __DIR__."/pages/conf/conectaOracle.php";
?>
<!DOCTYPE html>
<html lang="pt-br" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta http-equiv="Expires" content="-1">
    <meta http-equiv="Cache-control" content="no-cache">
    <title><?= @APP_APELIDO.' - '.@APP_NOME; ?></title>
    <link rel="icon" href="favicon.ico" type="image/png">
    <meta name="theme-color" content="#7952b3">
    <link href="dist/css/bootstrap.css" rel="stylesheet">
    <link href="plugins/fontawesome-pro/css/all.css" rel="stylesheet">
    <script type="text/javascript" src="plugins/jquery/dist/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="dist/js/p5.min.js"></script>
    <style>
    #painel{
        font-family:"Arial";
        width:100%;
        background-color: #222;
        color: #fff;
        font-size: 24px;
        padding: 0px !important;
        margin: 0px !important;
        text-align: center;
        min-height: 100%;
    }
    #cabecalho{
        font-family:"Arial";
        width:100%;
        background-color: #222;
        color: #fff;
        font-size: 45px;
        padding: 0px !important;
        margin: 0px !important;
        text-align: center;
        min-height: 100%;
    }

    #painel th{
        background-color: #f98404;
        color: #000;
        font-weight: bold;
        font-size: 24px;
    }
    #painel td{
        background-color: #222;
        color: #fff;
        font-size: 24px;
    }
    .img-sobreposta {
        position: absolute;
        top: 130px; /* Posição */
        height: 400px;
        left: 50%;
        transform: translateX(-50%); /* Centraliza horizontalmente */
        z-index: 10; /* Garante que fique por cima */
    }
    </style>
  </head>

  <body style="background-color: #222; color: #fff; ">

    <img src="dist/img/img-painel.jpg" id="img-painel" class="img-sobreposta">

    <div id="painel">
        <!-- exibe o nome dos 5 ultimos -->
        <div id="cabecalho" class="row mt-1">
            <div class="col-9">
                <i class="fas fa-list-check"></i> SEPARAÇÃO DE PEDIDOS
            </div>
            <div class="col-3">
                <div id="clock" class="text-warning"></div>
            </div>
        </div>

      	<table class="table table-bordered">
      		<thead>
        		<tr>
        			<th>NUMPED</th>
        			<th>CLIENTE</th>
        			<th>VEND.</th>
        			<th>BALCÃO</th>
        			<th>STATUS</th>
        			<th>CONF.</th>
        			<th>TEMPO</th>
        		</tr>
      		</thead>
      		<tbody id="pedidos">
      		</tbody>
      	</table>

        <audio id="myAudio" autoplay="" loop="" muted="" playsinline="" autobuffer="">
            <source src="dist/mp3/call.mp3" type="audio/mpeg">
        </audio>

    </div>

<script>
(function () {
    'use strict';

    /* ======================
       CONFIGURAÇÕES
       ====================== */
    var INTERVALO_OK   = 1000; // 1 segundo
    var INTERVALO_ERRO = 60000; // 1 minuto
    var FETCH_TIMEOUT = 5000; // 5 segundos

    /* ======================
       ELEMENTOS
       ====================== */
    var audioElem   = document.getElementById('myAudio');
    var pedidosElem = document.getElementById('pedidos');
    var clockElem   = document.getElementById('clock');
    var imgpainel   = document.getElementById("img-painel");


    /* ======================
       ESTADOS
       ====================== */
    var carregando = false;
    var timerLoop  = null;

    // trava lógica do ciclo 0 → >0
    var haviaPedidosNovos = false;

    /* ======================
       INICIALIZAÇÃO DO ÁUDIO
       (autoplay silencioso)
       ====================== */
    function inicializarAudio() {
        if (!audioElem) return;

        audioElem.muted = true;
        audioElem.loop = false;

        var p = audioElem.play();
        if (p && typeof p.catch === 'function') {
            p.catch(function () {
                // alguns navegadores ignoram, mas tentamos novamente depois
            });
        }
    }

    /* ======================
       LIMPEZA
       ====================== */
    window.addEventListener('beforeunload', function () {
        if (audioElem) {
            audioElem.pause();
            audioElem.currentTime = 0;
        }
        if (timerLoop) clearTimeout(timerLoop);
    });



    /* ======================
       FETCH COM TIMEOUT
       ====================== */
    function fetchComTimeout(url, options, timeout) {
        return Promise.race([
            fetch(url, options),
            new Promise(function (_, reject) {
                setTimeout(function () {
                    reject(new Error('Timeout'));
                }, timeout);
            })
        ]);
    }

    /* ======================
       TOCAR SOM (REGRA FINAL)
       ====================== */
    function tocarSom() {
        if (!audioElem) return;

        try {
            audioElem.pause();
            audioElem.currentTime = 0;
            audioElem.muted = false;
            audioElem.play();
        } catch (e) {}
    }

    /* ======================
       CARREGAR PEDIDOS
       ====================== */
    function carregarPedidos() {

        if (carregando) return;
        carregando = true;

        fetchComTimeout(
            'pages/logistica/painel_json.php?acao=checkout',
            { method: 'GET', cache: 'no-store' },
            FETCH_TIMEOUT
        )
        .then(function (response) {
            if (!response.ok) throw new Error('Erro HTTP');
            return response.json();
        })
        .then(function (json) {

            var pedidosNovosAtual = parseInt(json.pedidosNovos || 0, 10);

            /* ======================
               REGRA DEFINITIVA
               ====================== */
            if (
                pedidosNovosAtual > 0 &&
                haviaPedidosNovos === false
            ) {
                tocarSom();
            }

            // rearma quando não houver pedidos novos
            haviaPedidosNovos = pedidosNovosAtual > 0;

            /* ======================
               TABELA
               ====================== */
            var html = [];

            if (json.pedidos && json.pedidos.length) {
                imgpainel.style.display = "none";
                json.pedidos.forEach(function (p) {
                    html.push('<tr>');
                    html.push('<td>' + p.NUMPED + '</td>');
                    html.push('<td>' + (p.CLIENTE || '').substring(0, 15) + '...</td>');
                    html.push('<td>' + p.VENDEDOR + '</td>');
                    
                    // CORREÇÃO AQUI: 
                    // 1. Usamos 'text-dark' para garantir o contraste.
                    // 2. A classe 'bg-warning' do Bootstrap 5 aplica o fundo amarelo.
                    if (p.CLIENTEBALCAO === 'SIM') {
                        html.push('<td class="text-warning fw-bold">SIM</td>');
                    } else {
                        html.push('<td>NÃO</td>');
                    }

                    if (p.STATUSCHECKOUT === 'PENDENTE') {
                        html.push('<td class="text-info">PENDENTE</td>');
                        html.push('<td class="text-info">PENDENTE</td>');
                    } else {
                        html.push('<td>' + p.STATUSCHECKOUT + '</td>');
                        html.push('<td>' + p.CONFERENTE + '</td>');
                    }
                    html.push('<td>' + p.DECORRIDO + '</td>');
                    html.push('</tr>');
                });
            } else {
                imgpainel.style.display = "block";
            }

            pedidosElem.innerHTML = html.join('');

            agendarProximo(INTERVALO_OK);
        })
        .catch(function () {
            agendarProximo(INTERVALO_ERRO);
        })
        .finally(function () {
            carregando = false;
        });
    }

    /* ======================
       LOOP CONTROLADO
       ====================== */
    function agendarProximo(intervalo) {
        if (timerLoop) clearTimeout(timerLoop);
        timerLoop = setTimeout(carregarPedidos, intervalo);
    }

    /* ======================
       START
       ====================== */
    inicializarAudio();
    carregarPedidos();

})();


function atualizarRelogio() {
    const agora = new Date();

    const hora = agora.toLocaleTimeString('pt-BR');

    document.getElementById('clock').innerHTML = hora;
}

// Atualiza imediatamente
atualizarRelogio();

// Atualiza a cada 1 segundo
setInterval(atualizarRelogio, 1000);

</script>




  </body>
</html>
