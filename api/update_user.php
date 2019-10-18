<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

include_once '../config/database.php';
include_once '../objects/user.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));
$jwt = isset($data->jwt) ? $data->jwt : "";
if($jwt){
    try{
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        
        $user->name = $data->name;
        $user->email = $data->email;
        $user->image = $data->image;
        $user->password = $data->password;
        $user->id = $decoded->data->id;

        if($user->update()){
            $token = array(
                "iss" => $iss,
                "aud" => $aud,
                "iat" => $iat,
                "nbf" => $nbf,
                "data" => array(
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                    "image" => $user->image
                )
             );
             $jwt = JWT::encode($token, $key);
              
             // set response code
             http_response_code(200);
            echo json_encode(array(
                "message" => "User updated",
                "jwt" => $jwt));
        }
        else{
            http_response_code(401);
            echo json_encode(array("message" => "Cannot update user."));
        }
    }
    catch (Exception $e){
        echo json_encode(array(
            "message" => "Access denied",
            "error"=> $e->getMessage()
        ));
    }
}
else{
    http_response_code(401);
    echo json_encode(array("message" => "Access denied."));
}

?>