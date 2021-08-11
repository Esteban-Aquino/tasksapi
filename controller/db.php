<?php
class DB {
    private static $writeDBConnection;
    private static $readDBConnection;
    private static $host = 'localhost';
    private static $dbName = 'tasksdb';
    private static $dbUser = 'root';
    private static $dbPassword = 'root';

    private static function getConnectionString() {
        return 'mysql:host='.self::$host.';dbname='.self::$dbName.';charset=utf8';
    }

    private static function setConnectionAttributes() {
        self::$writeDBConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$writeDBConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public static function connectWriteDB() {
        if(self::$writeDBConnection === null) {
            self::$writeDBConnection = new PDO(self::getConnectionString(),self::$dbUser,self::$dbPassword);
            self::setConnectionAttributes();
        }

        return self::$writeDBConnection;
    }

    public static function connectReadDB() {
        if(self::$readDBConnection === null) {
            self::$readDBConnection = new PDO(self::getConnectionString(),self::$dbUser,self::$dbPassword);
            self::setConnectionAttributes();
        }

        return self::$readDBConnection;
    }



}