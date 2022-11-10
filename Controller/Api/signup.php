<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/UserModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $user_name = $data["user_name"];
    $password = $data["password"];
    $email = $data["email"];
    
    $validate_user_name = ApiResponse::validate_not_null_or_blank($user_name, "no_user_name", "No user name provided");
    if ($validate_user_name->get_has_errors())
    {
        echo $validate_user_name->get_json_error();
        die();
    }
    
    $validated_password = ApiResponse::validate_not_null_or_blank($password, "no_pass_word", "No password provided");
    if ($validated_password->get_has_errors())
    {
        echo $validated_password->get_json_error();
        die();
    }
    
    $validated_email = ApiResponse::validate_not_null_or_blank($email, "no_email", "No email provided");
    if ($validated_email->get_has_errors())
    {
        echo $validated_email->get_json_error();
        die();
    }
    
    $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
    
    $userModel = new UserModel();

    $statement = $userModel->create_user($user_name, $hashed_pw, $email);
    
    echo($statement);
    