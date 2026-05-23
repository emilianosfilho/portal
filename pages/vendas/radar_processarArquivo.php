<?php
// varDump2($arquivo);
foreach ($arquivo as $key => $value) {
    // $qtPedida = $value['QTPEDIDA'];

  if (!$value['CODPECA'] || empty($value['CODPECA'])) {
      return;
  }

  $array_pecas = explode(" ", $value['CODPECA']);
  // varDump2($array_pecas);

  if ($array_pecas) {

    if (!isset($_SESSION['RADAR'])) {
      $_SESSION['RADAR'] = [];
    }

    foreach ($array_pecas as $codpeca) {

      if (strlen($codpeca) < 3) {
        return;
      }

      $retorno = radar_consultaPeca($codpeca);

      if (empty($retorno)) {
        $map = "DIGITACAO|{$codpeca}|||";
        if (!isset($_SESSION['RADAR'][$map])) {
          $_SESSION['RADAR'][$map] = [
            'ORD' => 0,
            'ORIGEM' => 'DIGITACAO',
            'NUMORIGINAL' => $codpeca,
            'VIDE' => '',
            'WINT' => '',
            'DESCRICAO' => '**********',
            'MARCA' => '**********',
            'QTDISPONIVEL' => 0,
            'QTPEDIDA' => $value['QTPEDIDA'],
            'PVENDA' => 0
          ];
        }
      } 
      else {
        foreach ($retorno as $value) {
          $map = ($value['ORIGEM'] ?? '') . "|" .
                  ($value['NUMORIGINAL'] ?? '') . "|" .
                  ($value['VIDE'] ?? '') . "|" .
                  ($value['MARCA'] ?? '') . "|" .
                  ($value['WINT'] ?? '');

          if (!isset($_SESSION['RADAR'][$map])) {
              $_SESSION['RADAR'][$map] = $value;
              $_SESSION['RADAR'][$map]['QTPEDIDA'] = $value['QTPEDIDA'];
          }
        }
      }
    }
  }
}