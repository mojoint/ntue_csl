<?php
function debugger($key, $val){
    $fh = fopen('/tmp/csl_'. $key. '.log','a+');
    fwrite($fh,$val."\n");
    fclose($fh);
}




