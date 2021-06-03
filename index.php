<?php
/**
* Translator
* @auth: Monzurul Hasan
* @file: index.php
* @date: 02/06/2021
*/

error_reporting(0);
require_once("Translator.php");

$trans = new Translator();
$trans->setSource("bn");
$trans->setTarget("en");
$trans->setQuery("আমার সোনার বাংলা, আমি তোমায় ভালোবাসি");

echo "<pre>";
print_r($trans->getTranslation());
echo "</pre>";
