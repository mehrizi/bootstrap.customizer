<?php
$T = microtime (true);
include "src/customizer.php";
$sample = new Mehrizi\Bootstrap\Customizer(['primary'=>"#0000FF"]);
$sample->build (true,true,false);
echo microtime (true) - $T;