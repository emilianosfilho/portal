<?php 
// echo md5('vemap');
// validaNavegador();


##############################################################################
$ram = shell_exec('free');
// varDump2($ram);
$ram = explode(' ', $ram);
$array_tmp = array();
foreach ($ram as $key => $value) {
  if (trim($value) <> "") {
    array_push($array_tmp, $value);
  }
}
// varDump2($array_tmp);
$array_ram = array('TOTAL' => $array_tmp[6], 
                   'USADA' => $array_tmp[7], 
                   'LIVRE' => $array_tmp[8], 
                   'COMPARTILHADA' => $array_tmp[9], 
                   'CACHE' => $array_tmp[10], 
                   'DISPONIVEL' => trim(str_replace('Swap:', '', $array_tmp[11])) );
// varDump2($array_ram);
$TOTAL = ($array_ram['TOTAL']);
$LIVRE = ($array_ram['LIVRE']);
$USADA = ($array_ram['USADA']+$array_ram['CACHE']);
$ram_perc_uso   = round((($USADA/$TOTAL)*100),0);

if ($ram_perc_uso < 0.8) {
  $bg_ram_perc_uso = "bg-primary";
} else {
  $bg_ram_perc_uso = "bg-danger";
}

##############################################################################
$hdd_total = disk_total_space("/");
$hdd_livre = disk_free_space("/");
$hdd_uso   = ($hdd_total - $hdd_livre);
$si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
$base = 1024;
$class_uso    = min((int)log($hdd_uso , $base) , count($si_prefix) - 1);
$class_livre  = min((int)log($hdd_livre , $base) , count($si_prefix) - 1);
$class_total  = min((int)log($hdd_total , $base) , count($si_prefix) - 1);
$hdd_perc_uso = ($hdd_uso/$hdd_total);
if ($hdd_perc_uso < 0.8) {
  $bg_hdd_perc_uso = "bg-primary";
} else {
  $bg_hdd_perc_uso = "bg-danger";
}
##############################################################################


 ?>