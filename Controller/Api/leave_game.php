<?php
    session_start();
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $room_code = $_GET["room_code"];
    $player_name = $_GET["player_name"];
    
    $room_model = new RoomModel();
    $player_model = new PlayerModel();
    
    // check room
    $room_response = $room_model->validate_room_by_code($room_code);
    if ($room_response->get_has_errors())
    {
        echo $room_response->get_json_error();
        die();
    }
    $room = $room_response->get_data();
    
    // get players
    $player_response = $player_model->validate_player_in_room($room["id"], $player_name);
    if ($player_response->get_has_errors())
    {
        echo $player_response->get_json_error();
        die();
    }
    $player = $player_response->get_data();
    
    // reset player's game data
    $player_model->leave_game($player["id"]);
    
    echo ApiResponse::success()->get_json();