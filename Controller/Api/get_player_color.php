<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ColorModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $room_code = $_GET["room_code"];
    $player_name = $_GET["player_name"];
    $game_id = $_GET["game_id"];
    
    $player_model = new PlayerModel();
    $color_model = new ColorModel();
    $room_model = new RoomModel();
    $game_model = new GameModel();

     // check room
    $room_response = $room_model->validate_room_by_code($room_code);
    if ($room_response->get_has_errors())
    {
        echo $room_response->get_json_error();
        die();
    }
    $room = $room_response->get_data();
    
    // check game
    $game_response = $game_model->validate_game_by_id($game_id);
    if ($game_response->get_has_errors())
    {
        echo $game_response->get_json_error();
        die();
    }
    $game = $game_response->get_data();
    
    // check player
    $player_response = $player_model->validate_player_in_room($room["id"], $player_name);
    if ($player_response->get_has_errors())
    {
        echo $player_response->get_json_error();
        die();
    }
    $color_id = $player_response->get_data()["color_id"];

    
    // check color
    $color_response = $color_model->validate_color_by_id($color_id);
    if ($color_response->get_has_errors())
    {
        echo $color_response->get_json_error();
        die();
    }
    $color = $color_response->get_data();
    
    $results = [];
    $results["color_id"] = $color_id;
    $results["color_name"] = $color["name"];
    $results["player_name"] = $player_name;
    
    echo ApiResponse::success_data($results)->get_json();
    
    