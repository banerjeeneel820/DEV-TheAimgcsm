<?php
    class DB {
        public static $connected = false;
        public static $WRITELINK;
        public static function connect() {
            self::$WRITELINK = mysqli_connect(HOST,MYSQL_USER,MYSQL_PASS) or die("Error 1");
            mysqli_select_db(self::$WRITELINK, DB_AIMGCSM);
            mysqli_query(self::$WRITELINK, 'SET NAMES "utf8"'); 
            DB::$connected = true;
            return self::$WRITELINK;
        }
    }
?>
