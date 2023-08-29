<?php
    session_start();
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/UserModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $user_name = $data["user_name"];
    $token = $data["token"];
    $settings = [];
    $settings["room_name"] = $data["room_name"];
    $settings["room_code"] = $data["room_code"];
    $settings["auto_assign"] = $data["auto_assign"];
    $settings["auto_assign_games_started"] = $data["auto_assign_games_started"];
    $settings["join_timer"] = $data["join_timer"];
    $settings["pair_timer"] = $data["pair_timer"];
    
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
    
    // check that the user does not already have a room with this name
    $room_results = $room_model->validate_unique_room_name($user["id"], $settings["room_name"]);
    if ($room_results->get_has_errors())
    {
        echo $room_results->get_json_error();
        die();
    }
   
    
    $room_code = generateRandomString();
    
    // validate room code
    $room_code_results = $room_model->validate_room_code_unused($user["id"], $room_code);
    if ($room_code_results->get_has_errors())
    {
        echo $room_code_results->get_json_error();
        die();
    }
    
    $room_model->create_room($user["id"], $room_code ,$settings);
    
    $results = [];
    $results["room_code"] = $room_code;
    echo ApiResponse::success_data($results)->get_json();
    
    
    function generateRandomString($length = 6) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }