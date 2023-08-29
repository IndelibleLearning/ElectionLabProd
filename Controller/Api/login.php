<?php
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/UserModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $user_name = $data["user_name"];
    $password = $data["password"];
    
    $userModel = new UserModel();

    $user_results = $userModel->get_user_by_username($user_name);
    $user = $user_results[0];
    
    $password_result = password_verify($password, $user["password"]);
    
    $validated_password = ApiResponse::validate_not_null_or_blank($password_result, "incorrect_password", "Incorrect Username/Password");
    if ($validated_password->get_has_errors())
    {
        echo $validated_password->get_json_error();
        die();
    }
    
    //Generate a random string.
    $token = openssl_random_pseudo_bytes(16);
    
    //Convert the binary data into hexadecimal representation.
    $token = bin2hex($token);
    
    $userModel->set_login_token($user["id"], $token);
    
    $result = [];
    $result["login_token"] = $token;
    
    echo ApiResponse::success_data($result)->get_json();
