<?php
    session_start();
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/UserModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $user_name = $data["user_name"];
    $token = $data["token"];
    
    $user_model = new UserModel();
    $room_model = new RoomModel();

    // validate user
    $user_results = $user_model->validate_user_by_username($user_name);
    if($user_results->get_has_errors())
    {
        echo $user_results->get_json_error();
        die();
    }
    $user = $user_results->get_data();
    
    // validate token
    $token_results = $user_model->validate_login_token($user["id"], $token);
    if ($token_results->get_has_errors())
    {
        echo $token_results->get_json_error();
        die();
    }
    
    
    $rooms = $room_model->get_room_by_user_id($user["id"]);
    echo ApiResponse::success_data($rooms)->get_json();