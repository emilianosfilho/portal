<?php
if (isset($_POST) && !empty($_POST)) {
  $dados = $_POST;
} else {
  $dados = $_GET;
}


function varDump2(mixed $var, bool $die = false)
{
  // Opcional: Adicionar trava de segurança para não exibir em produção
  // if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') return;

  echo '<pre style="
        background-color: #212529; 
        color: #0dcaf0; 
        padding: 15px; 
        border-radius: 8px; 
        border: 1px solid #084298; 
        font-size: 13px; 
        line-height: 1.5; 
        overflow: auto; 
        max-height: 500px;
        margin: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        text-align: left;
        white-space: pre-wrap;
        word-break: break-all;
    ">';

  // Identificador visual para facilitar localização no código
  echo '<strong style="color: #ffc107;">DEBUG VEMAP:</strong><br><hr style="border-color: #444;">';

  var_dump($var);

  echo '</pre>';

  if ($die) {
    die('<div style="text-align:center; font-family:sans-serif; color:#dc3545;"><b>Execução interrompida pelo varDump2.</b></div>');
  }
}

function prompt(string $msg)
{
  echo '<pre class="prompt">portal~> ' . sanitizeOracleString($msg, 0, false) . '</pre>';
}

/*###############################################*/
function redireciona(string $location): void
{
  // Usamos json_encode para garantir que a string seja segura para JS
  // e para que contenha as aspas necessárias automaticamente.
  $locationSafe = json_encode($location, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

  echo "<script type=\"text/javascript\">
            window.location.href = $locationSafe;
          </script>";

  // Boa prática: interromper a execução do PHP para evitar processamento desnecessário
  exit;
}

function exibeModalEspecifico(string $nomeModal)
{
  echo "<script LANGUAGE=\"JavaScript\" TYPE=\"text/javascript\">
						$(document).ready(function () {
							$('#" . $nomeModal . "').modal('show');
							});
					</script>";
}

function validaNavegador()
{
  $ip = $_SERVER['REMOTE_ADDR'];
  $u_agent = $_SERVER['HTTP_USER_AGENT'];
  $bname = 'Unknown';
  $platform = 'Unknown';
  $version = "";
  if (preg_match('/linux/i', $u_agent)) {
    $platform = 'Linux';
  } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
    $platform = 'Mac';
  } elseif (preg_match('/windows|win32/i', $u_agent)) {
    $platform = 'Windows';
  }
  if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
    $bname = 'Internet Explorer';
    $ub = "MSIE";
  } elseif (preg_match('/Firefox/i', $u_agent)) {
    $bname = 'Mozilla Firefox';
    $ub = "Firefox";
  } elseif (preg_match('/Chrome/i', $u_agent)) {
    $bname = 'Google Chrome';
    $ub = "Chrome";
  } elseif (preg_match('/AppleWebKit/i', $u_agent)) {
    $bname = 'AppleWebKit';
    $ub = "Opera";
  } elseif (preg_match('/Safari/i', $u_agent)) {
    $bname = 'Apple Safari';
    $ub = "Safari";
  } elseif (preg_match('/Netscape/i', $u_agent)) {
    $bname = 'Netscape';
    $ub = "Netscape";
  } else if ($bname == 'Unknown') {
    $bname = 'Internet Explorer';
  }
  $Browser = array(
    'userAgent' => $u_agent,
    'name' => $bname,
    'version' => $version,
    'platform' => $platform
  );
  // varDump2($Browser['name']);
  if (($Browser['name'] <> 'Mozilla Firefox') && ($Browser['name'] <> 'Google Chrome')) {
    insereAlerta('danger', 'Seu navegador [' . $Browser['name'] . '] não é compatível com esta aplicação. Favor utilizar o [<a target="_blanck" href="http://br.mozdev.org/firefox/download/">Mozilla Firefox</a>] ou o [<a target="_blanck" href="https://www.google.com/chrome/">Google Chrome</a>]');
  }
}

function insereAlerta(string $type, string $msg): void
{
  if (!isset($_SESSION['ALERTA'])) {
    $_SESSION['ALERTA'] = [];
  }
  $_SESSION['ALERTA'][] = [
    'type' => sanitizeOracleString($type),
    'msg' => sanitizeOracleString($msg, 0, false)
  ];
}

function exibeAlerta()
{
  $alertas = (isset($_SESSION['ALERTA'])) ? $_SESSION['ALERTA'] : [];

  foreach ($alertas as $key => $alerta) {
    varDump2($alerta);

    switch ($alerta["type"]) {
      case 'DANGER':
        $titulo = 'ERRO';
        $bgcolor = 'danger';
        $textcolor = 'ligth';
        break;
      case 'WARNING':
        $titulo = 'ATENÇÃO';
        $bgcolor = 'warning';
        $textcolor = 'dark';
      case 'SUCCESS':
        $titulo = 'SUCESSO';
        $bgcolor = 'success';
        $textcolor = 'ligth';
        break;
        break;
      default:
        $titulo = 'INFO';
        $bgcolor = 'info';
        $textcolor = 'dark';
        break;
    }
    echo "<div class='alert alert-" . $bgcolor . " alert-dismissible'>
          <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
          <h3>" . $titulo . "</h3>
          <h5 class='text-" . $textcolor . "'>" . $alerta["msg"] . "</h5>
        </div>";
  }
  unset($_SESSION['ALERTA']);
}


function insereToastr(string $type, string $msg): void
{
  if (!isset($_SESSION['TOASTR'])) {
    $_SESSION['TOASTR'] = [];
  }
  $_SESSION['TOASTR'][] = [
    'type' => sanitizeOracleString($type),
    'msg' => sanitizeOracleString($msg, 0, false)
  ];
}

function exibeToastr()
{
  if (isset($_SESSION["TOASTR"]) && !empty($_SESSION["TOASTR"])) {
    echo PHP_EOL . '<div class="position-fixed top-0 start-50 translate-middle-x p-5" style="z-index: 11">';
    foreach ($_SESSION["TOASTR"] as $key => $toastr) {
      if ($toastr['type'] == 'success') {
        $textColor = "text-white";
      } else {
        $textColor = "text-dark";
      }
      echo PHP_EOL . '<div id="toast' . $key . '" class="toast align-items-center text-white bg-' . $toastr['type'] . ' border-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
			  <div class="d-flex">
			    <div class="toast-body">
			      ' . $toastr['msg'] . '
			    </div>
			    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
			  </div>
			</div>';
    }
    echo '</div>';
    unset($_SESSION["TOASTR"]);
  }
}

function insereModal(string $type, string $msg): void
{
  if (!isset($_SESSION['MODAL'])) {
    $_SESSION['MODAL'] = [];
  }
  $_SESSION['MODAL'][] = [
    'type' => mb_strtolower(sanitizeOracleString($type)),
    'msg' => sanitizeOracleString($msg, 0, false)
  ];
}

function exibeModal()
{
  if (isset($_SESSION["MODAL"]) && !empty($_SESSION["MODAL"])) {
    foreach ($_SESSION["MODAL"] as $key => $modal) {
      if ($modal['type'] == 'success') {
        $textColor = 'white';
        $bgcollor = 'green';
        $modal['title'] = '<i class="fa fa-check"></i> OK';
      } else
        if ($modal['type'] == 'warning') {
          $textColor = 'dark';
          $bgcollor = 'yellow';
          $modal['title'] = '<i class="fa fa-warning"></i> ATENÇÃO';
        } else
          if ($modal['type'] == 'danger') {
            $textColor = 'white';
            $bgcollor = 'red';
            $modal['title'] = '<i class="fa fa-warning"></i> ERRO';
          } else
            if ($modal['type'] == 'secondary') {
              $textColor = 'dark';
              $bgcollor = 'grey';
              $modal['title'] = '<i class="fa fa-info-circle"></i> AVISO';
            } else {
              $textColor = 'dark';
              $bgcollor = 'white';
              $modal['title'] = '<i class="fa fa-check"></i> Info:';
            }
      // varDump2($modal);
      echo PHP_EOL . '<div class="modal" tabindex="-1" id="myModal' . $key . '">
			  <div class="modal-dialog">
			    <div class="modal-content">
			      <div class="modal-header bg-' . $modal['type'] . ' text-' . $textColor . '">
			        <h2 class="modal-title">' . $modal['title'] . '</h2>
			      </div>
			      <div class="modal-body text-dark" style="font-size: 16px">
			       	' . $modal['msg'] . '
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn  btn-' . $modal['type'] . ' text-' . $textColor . '" data-bs-dismiss="modal">Fechar</button>
			      </div>
			    </div>
			  </div>
			</div>';
    }
    unset($_SESSION["MODAL"]);
  }
}

function inserePopover(string $type, string $msg): void
{
  if (!isset($_SESSION['POPOVER'])) {
    $_SESSION['POPOVER'] = [];
  }
  $_SESSION['POPOVER'][] = [
    'type' => sanitizeOracleString($type, 0, false),
    'msg' => sanitizeOracleString($msg, 0, false)
  ];
}

function exibePopover()
{
  if (isset($_SESSION['POPOVER'])) {
    foreach ($_SESSION['POPOVER'] as $key => $value) {
      echo '<div class="alert alert-' . $value['type'] . ' alert-dismissible fade in" role="alert">
				  <strong>' . $value['title'] . '</strong><br /><br />' . $value['msg'] . '
				</div>';
    }
    unset($_SESSION['POPOVER']);
  }
}

function voltaPagina(int $nr)
{
  echo "<script LANGUAGE=\"JavaScript\" TYPE=\"text/javascript\">
								history.go(-$nr);
					 </script>";
}

function exibeMensagem(string $msg)
{
  $msg = sanitizeOracleString($msg);
  echo "<script LANGUAGE=\"JavaScript\" TYPE=\"text/javascript\">
								window.alert (\" $msg \");
					 </script>";
}

function abreNova(string $url, array|bool $dados = false)
{
  if ($dados) {
    $_SESSION['dados'] = $dados;
  }
  echo "<script LANGUAGE=\"JavaScript\" TYPE=\"text/javascript\">
								window.open(\"$url\",\"_blank\");
					 </script>";
}

function fechaAba()
{
  echo "<script LANGUAGE=\"JavaScript\" TYPE=\"text/javascript\">
				  var tab = window.open('','_self');
				  tab.close();
			  </script>";
}

function moeda(mixed $valor, int $decimais = 2)
{
  if (is_null($valor) || $valor == "") {
    $valor = 0.00;
  } else {
    if (is_string($valor))
      $valor = str_replace(",", ".", $valor);
    $valor = floatval($valor);
    $valor = number_format($valor, $decimais, ',', '.');
  }
  return ($valor);
}
/*###############################################*/
function moedaPHP(mixed $valor)
{
  if (is_null($valor)) {
    return  0.00;
  }

  $valor = (string) $valor;
  if (strripos($valor, ".") && strripos($valor, ",")) {
    $valor = str_replace(".", "", $valor);
  }
  $valor = str_replace(",", ".", $valor);
  return $valor;
}


/*###############################################*/
function Percentual(string $valor, int $decimais = 2)
{
  $valor = number_format(($valor * 100), $decimais, ',', '.') . " %";
  return ($valor);
}

function formataDataEUA(string $data)
{
  $data = explode("/", $data);
  $dia = $data['0'];
  $mes = $data['1'];
  $ano = $data['2'];
  $date = date_create($ano . "-" . $mes . "-" . $dia);
  return date_format($date, 'Y-m-d 00:00:00');
  ;
}

function formataDataBRtoOracle(string $data)
{
  if (!is_null($data)) {
    if (strripos($data, '/')) {
      $data = explode("/", $data);
    } else {
      $data = explode("-", $data);
    }
    $dia = $data['0'];
    $ano = $data['2'];
    $mes = '';

    if (is_numeric($data['1'])) {
      switch ($data['1']) {
        case '01':
          $mes = 'JAN';
          break;
        case '02':
          $mes = 'FEB';
          break;
        case '03':
          $mes = 'MAR';
          break;
        case '04':
          $mes = 'APR';
          break;
        case '05':
          $mes = 'MAY';
          break;
        case '06':
          $mes = 'JUN';
          break;
        case '07':
          $mes = 'JUL';
          break;
        case '08':
          $mes = 'AUG';
          break;
        case '09':
          $mes = 'SEP';
          break;
        case '10':
          $mes = 'OCT';
          break;
        case '11':
          $mes = 'NOV';
          break;
        case '12':
          $mes = 'DEC';
          break;
      }
    } else {
      $mes = $data['1'];
    }

    return "to_date('" . $dia . "/" . $mes . "/" . $ano . "', 'DD/MM/YYYY')";
  } else {
    return false;
  }
}

function formataDataOracletoBr(string|null $data)
{
  if (is_null($data)) {
    return "";
  } else {
    // varDump2(array $data); 
    $tmp1 = explode(" ", $data);
    // varDump2($tmp1); 

    $tmp2 = explode("-", $tmp1[0]);
    if (count($tmp2) < 3) {
      $tmp2 = explode("/", $tmp1[0]);
    }
    $dia = $tmp2['0'];
    $mes = '';
    if (is_numeric($tmp2['1'])) {
      $mes = $tmp2['1'];
    } else {
      switch ($tmp2['1']) {
        case 'JAN':
          $mes = '01';
          break;
        case 'FEB':
          $mes = '02';
          break;
        case 'MAR':
          $mes = '03';
          break;
        case 'APR':
          $mes = '04';
          break;
        case 'MAY':
          $mes = '05';
          break;
        case 'JUN':
          $mes = '06';
          break;
        case 'JUL':
          $mes = '07';
          break;
        case 'AUG':
          $mes = '08';
          break;
        case 'SEP':
          $mes = '09';
          break;
        case 'OCT':
          $mes = '10';
          break;
        case 'NOV':
          $mes = '11';
          break;
        case 'DEC':
          $mes = '12';
          break;
      }
    }
    if (intval($tmp2['2']) < 1999) {
      $ano = "20" . $tmp2['2'];
    } else {
      $ano = $tmp2['2'];
    }

    if (isset($tmp1[1]) && !empty($tmp1[1])) {
      return $dia . "/" . $mes . "/" . $ano . " " . $tmp1[1];
    } else {
      return $dia . "/" . $mes . "/" . $ano;
    }
  }
}

function formataDataTrayToBR(string $data)
{
  // '2025-08-19 14:09:26' FORMATO DE ENTRADA
  if (is_null($data)) {
    return "-";
  } else {
    // varDump2(array $data); 
    $tmp1 = explode(" ", $data);
    $t_data = explode("-", $tmp1[0]);
    $dia = $t_data[2];
    $mes = $t_data[1];
    $ano = $t_data[0];

    if (isset($tmp1[1]) && !empty($tmp1[1])) {
      return $dia . "/" . $mes . "/" . $ano . " " . $tmp1[1];
    } else {
      return $dia . "/" . $mes . "/" . $ano;
    }
  }
}

function formataDataVidetoBr(string $data)
{
  $dia = '';
  $mes = '';
  $ano = '';
  if (is_null($data)) {
    return false;
  } else {
    // varDump2(array $data); die();
    $tmp = str_replace("/", "", $data);
    $tmp = str_replace("-", "", $data);
    $tmp = str_replace(".", "", $data);
    if (strlen($tmp) == 6) {
      $dia = $tmp[0] . $tmp[1];
      $mes = $tmp[2] . $tmp[3];
      $ano = "20" . $tmp[4] . $tmp[5];
    }
    return $dia . "/" . $mes . "/" . $ano;
  }
}

function decodeExcelColuna(int $key)
{
  $array = array(1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S', 20 => 'T', 21 => 'U', 22 => 'V', 23 => 'X', 24 => 'W', 25 => 'Y', 26 => 'Z', 27 => 'AA', 28 => 'AB', 29 => 'AC', 30 => 'AD', 31 => 'AE', 32 => 'AF', 33 => 'AG', 34 => 'AH', 35 => 'AI', 36 => 'AJ', 37 => 'AK', 38 => 'AL', 39 => 'AM', 40 => 'AN', 41 => 'AO', 42 => 'AP', 43 => 'AQ', 44 => 'AR', 45 => 'AS', 46 => 'AT', 47 => 'AU', 48 => 'AV', 49 => 'AX', 50 => 'AW', 51 => 'AY', 52 => 'AZ');
  return $array[$key];
}

function decodeSN(string $var)
{
  if (!empty($var) && mb_strtoupper($var) == 'S') {
    return 'SIM';
  } else {
    return 'NÃO';
  }
}

function limpaSession()
{
  if (!empty($_SESSION)) {
    foreach ($_SESSION as $key => $value) {
      unset($_SESSION[$key]);
    }
  }
}

function limpaCGCENT(string $value)
{
  if (!empty($value)) {
    $value = trim($value);
    $value = str_replace(".", "", $value);
    $value = str_replace("/", "", $value);
    $value = str_replace("-", "", $value);
    return $value;
  } else {
    return null;
  }
}

function formatCpf(string $value)
{
  if ($value == "") {
    return "";
  } else {
    $cpf = preg_replace("/\D/", '', $value);
    return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cpf);
  }
}

function formatCnpj(string $value): string|bool
{
  if ($value == "") {
    return false;
  } else {
    $cnpj = preg_replace("/\D/", '', $value);
    return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj);
  }
}

function formataCEP(string $cep)
{
  // Remove qualquer caractere que não seja número
  $cep = preg_replace("/[^0-9]/", "", $cep);

  // Verifica se tem 8 dígitos
  if (strlen($cep) == 8) {
    return substr($cep, 0, 5) . '-' . substr($cep, 5);
  }
  return $cep; // Retorna original se não tiver 8 dígitos
}

function enviaArquivos(array $FILES)
{
  if (empty($FILES)) {
    return false;
  } else {
    $arquivos = array();
    foreach ($FILES as $key => $file) {
      if ($file["error"] <> 0) {
        switch ($file["error"]) {
          case 1:
            varDump2('O arquivo enviado excede a diretiva upload_max_filesize em php.ini');
            break;
          case 2:
            varDump2('O arquivo enviado excede a diretiva MAX_FILE_SIZE especificada no formulário HTML');
            break;
          case 3:
            varDump2('O arquivo enviado foi carregado apenas parcialmente');
            break;
          case 4: /*varDump2('Nenhum arquivo foi enviado');*/
            break;
          case 6:
            varDump2('Faltando uma pasta temporária');
            break;
          case 7:
            varDump2('Falha ao gravar o arquivo no disco.');
            break;
          case 8:
            varDump2('Uma extensão PHP interrompeu o upload do arquivo');
            break;
          default:
            varDump2('Arquivo não identificado ou com erro.');
            break;
        }
      } else {
        $dir = @DIR_UPLOAD;
        if (!is_dir($dir)) {
          varDump2('ERRO Não encontrado Diretório ' . $dir);
        }
        $separador = ".";
        $name = $file["name"];
        $nometmp = $file["tmp_name"];
        $filename = $dir . $name;
        $extensao = explode($separador, $name);
        $extensao = end($extensao);
        $extensao = strtolower($extensao);
        $inputFileName = @date('Ymd_His_') . $key . '.' . $extensao;
        if (!move_uploaded_file($nometmp, $dir . $inputFileName)) {
          varDump2("nometmp: " . $nometmp);
          varDump2("inputFileName: " . $inputFileName);
          varDump2("filename: " . $filename);
          varDump2('ERRO ao importar o Arquivo ' . $filename);
        } else {
          // varDump2("Arquivo enviado com sucesso: ".$inputFileName);
          array_push($arquivos, $inputFileName);
        }
      }
    }
    return $arquivos;
  }
}

function somenteNumeros(string $string): string
{
  $string = strval($string);
  $string = str_replace(" ", "", $string);
  $string = str_replace(".", "", $string);
  $string = str_replace("_", "", $string);
  $string = str_replace("-", "", $string);
  $string = str_replace("/", "", $string);
  $string = str_replace("\\", "", $string);
  $string = str_replace("?", "", $string);
  $string = str_replace("'", "", $string);
  return $string;
}

function diferencaTempo(string|null $dataInicial, ?string $dataFinal = null): string
{
  // var_dump($dataInicial);
  if (!empty($dataInicial)) {
    $inicio = new DateTime($dataInicial);
    $fim = $dataFinal ? new DateTime($dataFinal) : new DateTime();

    $fim->sub(new DateInterval('PT8H'));

    // Garante ordem correta
    if ($inicio > $fim) {
      [$inicio, $fim] = [$fim, $inicio];
    }

    $diff = $inicio->diff($fim);

    $map = [
      'y' => ['ano', 'anos'],
      'm' => ['mês', 'meses'],
      'd' => ['dia', 'dias'],
      'h' => ['hora', 'horas'],
      'i' => ['minuto', 'minutos'],
      's' => ['segundo', 'segundos'],
    ];

    foreach ($map as $campo => [$singular, $plural]) {
      if ($diff->$campo > 0) {
        $valor = $diff->$campo;
        return $valor . ' ' . ($valor === 1 ? $singular : $plural);
      }
    }

    return '0 segundos';
  } else {
    return 'nunca';
  }
}


function sanitizeOracleString(?string $valor, int $tamanhoMax = 0, bool $upper = true): ?string
{
  if ($valor === null) {
    return null;
  }

  // 1. Garante UTF-8 válido para evitar erros de charset no OCI8
  $valor = mb_convert_encoding($valor, 'UTF-8', 'UTF-8');

  // 2. Converte para maiúsculas se solicitado (Mantendo UTF-8)
  if ($upper) {
    $valor = mb_strtoupper($valor, 'UTF-8');
  }

  // 3. Remove caracteres de controle ASCII perigosos (NULL byte, etc)
  // Mantém: \n (10) e \r (13). Remove outros de 0 a 31 e o 127 (DEL)
  $valor = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/u', '', $valor);

  // 4. Normaliza quebras de linha (converte \r\n ou \r para \n)
  $valor = str_replace(["\r\n", "\r"], "\n", $valor);

  // 5. Remove comentários SQL que podem ser usados em ataques ou causar erros de execução
  $valor = preg_replace('/(--.*?$)|\/\*.*?\*\//m', '', $valor);

  // 6. Normaliza múltiplos espaços horizontais (sem afetar a quebra de linha)
  $valor = preg_replace('/[ \t]{2,}/u', ' ', $valor);

  // 7. Escapa aspas simples (Obrigatório para Oracle sem Prepared Statements)
  // No Oracle, uma aspa simples é escapada com outra aspa simples ('')
  $valor = str_replace("'", "''", $valor);

  // 8. Limita o tamanho da string (Truncate seguro para multibyte)
  if ($tamanhoMax > 0) {
    $valor = mb_substr($valor, 0, $tamanhoMax, 'UTF-8');
  }

  return trim($valor);
}

/**
 * Formatação de tempo relativo (Backend)
 */
function calcular_tempo_relativo(string $data_timestamp)
{
  $agora = new DateTime();
  $evento = new DateTime($data_timestamp);
  $diff = $agora->diff($evento);

  if ($diff->d > 0)
    return $diff->d == 1 ? "Ontem" : $diff->d . " dias atrás";
  if ($diff->h > 0)
    return $diff->h . " hora" . ($diff->h > 1 ? "s" : "") . " atrás";
  if ($diff->i > 0)
    return $diff->i . " min atrás";
  return "Agora";
}

function obterPrimeiroEUltimoNome(?string $nome_completo): ?string
{
  if (empty($nome_completo)) {
    return null;
  }

  // Limpa espaços extras no início/fim e remove múltiplos espaços internos
  $nome_limpo = trim($nome_completo);
  $nome_limpo = preg_replace('/\s+/', ' ', $nome_limpo);

  $partes = explode(' ', $nome_limpo);
  $quantidade = count($partes);

  // Se houver apenas um nome, retorna ele mesmo
  if ($quantidade === 1) {
    return $partes[0];
  }

  // Retorna o primeiro (índice 0) e o último (último índice do array)
  return $partes[0] . ' ' . $partes[$quantidade - 1];
}

function converterUTF8(string $texto){
  $texto_corrigido = trim($texto);
  $texto_corrigido = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $texto_corrigido);
  return $texto_corrigido;
}

/**
 * Retorna o primeiro nome a partir de uma string completa.
 *
 * @param string $nomeCompleto
 * @return string
 */
function obterPrimeiroNome(string $nomeCompleto): string 
{
    // Remove espaços extras no início e no fim da string
    $nomeLimpo = trim($nomeCompleto);
    
    // Divide a string em partes separadas por espaço
    $partesDoNome = explode(' ', $nomeLimpo);
    
    // Retorna a primeira parte (índice 0)
    return $partesDoNome[0];
}