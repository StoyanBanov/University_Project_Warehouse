<?php
	class DbHelper {
		private static $_dbUsername = "root";
		private static $_dbPassword = "";
		private static $_dbHost = "localhost";
		private static $_dbName = "wsp_2023_warehouse_project";
		private static $_dbOptions = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
		
		public static function GetConnection() {
			try {
				return new PDO(
					"mysql:host=" . self::$_dbHost . ";dbname=" . self::$_dbName,
					self::$_dbUsername,
					self::$_dbPassword,
					self::$_dbOptions);
			} catch(PDOException $ex) {
				die("Възникна грешка: " . $ex->getMessage());
			}
		}
	}