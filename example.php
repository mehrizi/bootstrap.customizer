<?php
$T = microtime (true);
include "customizer.inc.php";
$sample = new Mehrizi\Bootstrap\Customizer(['primary'=>"#0000FF"]);
$sample->build (true,true,false);
echo microtime (true) - $T;