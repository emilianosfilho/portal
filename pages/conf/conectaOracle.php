<?php
function conectaOracle() {
    $host = "192.168.10.245";
    $port = "1521";
    $user = "vemap";
    $pass = "v3m4p";
    $inst = "WINT";
    
    $dbstr = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$host.")(PORT = ".$port."))
              (CONNECT_DATA = (SERVER = DEDICATED) (SERVICE_NAME = ".$inst.") ) )";
              
    // AL32UTF8 é essencial para evitar problemas de acentuação no Winthor
    $conn = @oci_connect($user, $pass, $dbstr, 'AL32UTF8');
    
    if ($conn) {
        return $conn;
    } else {
        $e = oci_error();
        // Logamos o erro no servidor, nunca no output em chamadas AJAX
        error_log("Erro Conexão Oracle: " . $e['message']);
        return false;
    }
}

function selectOracle($sql) {
    $conn = conectaOracle();
    if (!$conn) return false;

    $stid = oci_parse($conn, $sql);
    if (!$stid) {
        oci_close($conn);
        return false;
    }

    $exec = oci_execute($stid);
    if ($exec) {
        $retorno = [];
        while (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
            $retorno[] = $row;
        }
        oci_free_statement($stid);
        oci_close($conn);
        return $retorno;
    } else {
        oci_free_statement($stid);
        oci_close($conn);
        return false;
    }
}

function executarOracle($sql) {
    $conn = conectaOracle();
    if (!$conn) return false;

    $stid = @oci_parse($conn, $sql);
    if (!$stid) {
        $e = oci_error($conn);
        $msg = isset($e['message']) ? $e['message'] : 'Erro no parse do SQL';
        $sqltext = isset($e['sqltext']) ? $e['sqltext'] : $sql;
        
        oci_close($conn);
        throw new Exception("Erro Oracle: " . $msg . " | " . $sqltext);
    } 

    $exec = @oci_execute($stid);
    if (!$exec) {
        $e = oci_error($stid);
        $msg = isset($e['message']) ? $e['message'] : 'Erro na execução';
        $sqltext = isset($e['sqltext']) ? $e['sqltext'] : $sql;
        
        oci_free_statement($stid);
        oci_close($conn);
        throw new Exception("Erro Oracle: " . $msg . " | " . $sqltext);
    }
    
    oci_free_statement($stid);
    oci_close($conn);
    return true;
}
?>