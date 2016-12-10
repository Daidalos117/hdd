<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 29. 3. 2015
 * Time: 15:42
 */

class Databaze {

    /**
     * @var PDO
     */
    private static $spojeni;

    private static $nastaveni = Array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //na produkcnim serveru vypnout !!

    );


    public static function pripoj($host, $uzivatel, $heslo, $databaze) {
        if (!isset(self::$spojeni)) {
            self::$spojeni = @new PDO(
                "mysql:host=$host;dbname=$databaze",
                $uzivatel,
                $heslo,
                self::$nastaveni
            );
        }
        return self::$spojeni;
    }

    /**
     * @param $sql
     * @param array $parametry
     * @return PDOStatement
     */
    public static function dotaz($sql, $parametry = array()) {
        $dotaz = self::$spojeni->prepare($sql);
        $dotaz->execute($parametry);
        return $dotaz;
    }

    /**
     * Get last error
     * @return array
     */
    public static function getError(){
        return self::$spojeni->errorInfo();
    }

    /**
     * Get last inserted ID
     * @return string
     */
    public static function getLastID(){
        return self::$spojeni->lastInsertId();
    }
}