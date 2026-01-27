<?php
namespace App\Configs;

use MongoDB\Client;

class Database
{
	private static $client = null;
	private static $database = null;

	public static function getConnection()
	{
		if (self::$client === null) {
			$mongoUri = $_ENV['MONGODB_URI'];
			self::$client = new Client($mongoUri);
		}
		return self::$client;
	}

	public static function getDatabase($dbName = null)
	{
		if (self::$database === null) {
			$client = self::getConnection();
			$dbName = $dbName ?: $_ENV['MONGODB_DATABASE'];
			self::$database = $client->selectDatabase(databaseName: $dbName);
		}
		return self::$database;
	}
}
?>