# bootstrap.customizer
Bootstrap v4 PHP customizer
<br>This is a simple library to allow customize Bootstrap 4 and get css file out of it.

## Installing
###Composer:
```
composer require mehrizi/bootstrap.customizer`
```
### Package:
download the .zip package and extract
### Usage:
Include (composer): `vendor/mehrizi/bootstrap.customizer/src/customizer.php`<br>
or `bootstrap.customizer/src/customizer.php`
<br>
and you can use it as follows:<br>
```
$sample = new Mehrizi\Bootstrap\Customizer(['primary'=>"#0000FF"]);
$sample->build (true,true,false);
```
The build function parameters are:
```
* 1st: true/false or path string =>default:false(return)
* 2nd: bool => default:true(minified)
* 3rd: bool => default:true(use caching)
```
