<?php
/**
 * User: romanrajchert
 * Date: 14.01.17
 * Time: 19:33
 * Project: hdd
 */


require_once "Databaze.php";
Databaze::pripoj('localhost', 'root', 'root', 'hdd');
extract($_POST);

if($type === "check") {
	$checked = ( $checked == "true" ) ? 1 : 0;
	$query   = Databaze::dotaz( "UPDATE `files` SET `checked`=? WHERE id=?", [ $checked, $id ], 0 );
}
if($type === "hide") {
	$query   = Databaze::dotaz( "UPDATE `files` SET `hidden`=1 WHERE id=?", [ $id ], 0 );
}