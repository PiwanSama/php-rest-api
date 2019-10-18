<?php

class Database{
	//Database credentials
	private $host = "localhost";
	private $db_name = "api_db";
	private $username = "root";
	private $password = "";

	//Database connection
	public function getConnection(){
		$this->conn = null;
		try{
			$this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name, $this->username, $this->password);
        }
		catch(PDOException $exception){
			echo "Connection error: ". $exception->getMessage();
		}
		return $this->conn;
	}
}
?>