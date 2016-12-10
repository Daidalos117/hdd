<?php
/**
 * User: romanrajchert
 * Date: 10.12.16
 * Time: 17:05
 * Project: hdd
 */

$dir = "/Volumes/Seagate Backup Plus Drive/";
if(!dir($dir)){
    die();
}
require_once "Databaze.php";
Databaze::pripoj('localhost', 'root', 'root', 'hdd');

$query = Databaze::dotaz("SELECT * FROM directories");
$moviesDirectories = $query->fetchAll(PDO::FETCH_ASSOC);


foreach ($moviesDirectories as $key => $directory){
    $scanDir = scandir($dir.$directory["directory"]);

    foreach ($scanDir as $file){

        $query = Databaze::dotaz("SELECT * FROM files WHERE file = ? AND directory = ?",[$file,$directory["id"]]);
        $exists = ;



    }


}