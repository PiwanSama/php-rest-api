<?php

class User{
	//database connection and table name
	private $conn;
	private $table_name = "users";

	//object properties
	public $name;
	public $email;
	public $password;
	public $image;

	//constructor with $db as database connection
	public function __construct($db){
		$this->conn = $db;
	}

	function create(){
		$query = "INSERT INTO ".$this->table_name."
				 SET name = :name, email = :email, password = :password, image = :image";
		
		//Prepare the query
		$stmt = $this->conn->prepare($query);

		//Sanitize
		$this->name=htmlspecialchars(strip_tags($this->name));
		$this->email=htmlspecialchars(strip_tags($this->email));
		$this->password=htmlspecialchars(strip_tags($this->password));
		$this->image=htmlspecialchars(strip_tags($this->image));

		//Bind the values
		$stmt->bindParam(':name', $this->name);
		$stmt->bindParam(':email', $this->email);
		$stmt->bindParam(':image', $this->image);

		//Hash the password
		$password_hash = password_hash($this->password, PASSWORD_BCRYPT);
		$stmt->bindParam(':password', $password_hash);

		if($stmt->execute()){
			return true;
		}
		return false;
	}

	function update(){
		$password_set = !empty($this->password) ? ", password = :password" : "";
 
		$query = "UPDATE ".$this->table_name."
				 SET 
				 name = :name,
				 email = :email,
				 image = :image
				 {$password_set}
				 WHERE id = :id";
		
		//Prepare the query
		$stmt = $this->conn->prepare($query);

		//Sanitize
		$this->name=htmlspecialchars(strip_tags($this->name));
		$this->email=htmlspecialchars(strip_tags($this->email));
		$this->image=htmlspecialchars(strip_tags($this->image));

		//Bind the values
		$stmt->bindParam(':name', $this->name);
		$stmt->bindParam(':email', $this->email);
		$stmt->bindParam(':image', $this->image);

		//Hash the password
		if(!empty($this->password)){
			$this->password=htmlspecialchars(strip_tags($this->password));
			$password_hash = password_hash($this->password, PASSWORD_BCRYPT);
			$stmt->bindParam(':password', $password_hash);
		}

		$stmt->bindParam(':id', $this->id);

		if($stmt->execute()){
			return true;
		}

		return false;
	}

	// check if given email exist in the database
	function emailExists(){
		$query = "SELECT id, name, email, password
				FROM " . $this->table_name . "
				WHERE email = ?
				LIMIT 0,1";
		$stmt = $this->conn->prepare($query);
		// sanitize
		$this->email=htmlspecialchars(strip_tags($this->email));
		$stmt->bindParam(1, $this->email);
		$stmt->execute();
		
		$num = $stmt->rowCount();
		if($num>0){
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$this->id = $row['id'];
			$this->name = $row['name'];
			$this->email = $row['email'];
			$this->password = $row['password'];

			return true;
		}
		return false;
	}

function read(){
	$users= array();
	$query = "SELECT * FROM ".$this->table_name.";";
	$stmt = $this->conn->prepare($query);
	$stmt->execute();
	$num = $stmt->rowCount();
	if($num>0){
		while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
			// extract row will change $row['name'] to $name 
			extract($row);	
			$user = array(
				"name" => $name,
				"email" => $email,
				"image" => $image); 		
			array_push($users, $user);
			}
		return json_encode($users);
	}
	return false;
}
}

?>