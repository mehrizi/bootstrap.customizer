<?php
$T = microtime (true);
include "customizer.php";
$sample = new Mehrizi\Bootstrap\Customizer(['primary'=>"#0000FF"]);
$sample->build ("../test.css");
echo microtime (true) - $T;