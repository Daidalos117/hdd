<?php
/**
 * User: romanrajchert
 * Date: 10.12.16
 * Time: 17:05
 * Project: hdd
 */

//$dir = "/Volumes/Seagate Backup Plus Drive/";
$dir = "smb://Samba Shares on bambi._smb._tcp.local/Seagate Backup Plus Drive";
if(!dir($dir)){
    die();
}
require_once "Databaze.php";
Databaze::pripoj('localhost', 'root', 'root', 'hdd');


$query = Databaze::dotaz("SELECT * FROM directories");
$moviesDirectories = $query->fetchAll(PDO::FETCH_ASSOC);

$filesExceptions = [".", ".."];
foreach ($moviesDirectories as $key => $directory){
    $newDir = $dir.$directory["directory"];
    $scanDir = scandir($newDir);

    foreach ($scanDir as $file){
        if(in_array($file,$filesExceptions)) continue;

        $newFile = $newDir."/".$file;
        $fileSize = filesize($newFile);
        $time = filemtime($newFile);

        $query = Databaze::dotaz("SELECT * FROM files WHERE file = ? AND directory_id = ?",[$file,$directory["id"]]);
        $exists = $query->fetch();

        $year = null;
        if (preg_match('/\b\d{4}\b/', $file, $matches)) {
            $year = $matches[0];
        }
        if($exists){
            Databaze::dotaz("UPDATE `files` set last_seen = NOW(), file_edited = FROM_UNIXTIME(?) WHERE file = ? AND directory_id = ?",[$time,$file,$directory["id"]]);
        }else{
            $qu = Databaze::dotaz("INSERT INTO `files`( `file`, `last_seen`, `directory_id`, `year`, `size_bytes`,  `file_edited`) VALUES (?,NOW(),?,?,?,FROM_UNIXTIME(?))",
                [$file,$directory["id"], $year, $fileSize, $time]);

        }

    }


}