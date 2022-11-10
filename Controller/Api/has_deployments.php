<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/DeploymentModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $room_code = $data["room_code"];
    $player_name = $data["player_name"];
    $game_id = $data["game_id"];

    $room_model = new RoomModel();
    $player_model = new PlayerModel();
    $game_model = new GameModel();
    $deployment_model = new DeploymentModel();
    
     // check room
    $room_response = $room_model->validate_room_by_code($room_code);
    if ($room_response->get_has_errors())
    {
        echo $room_response->get_json_error();
        die();
    }
    $room = $room_response->get_data();
    
    // check player
    $player_response = $player_model->validate_player_in_room($room["id"], $player_name);
    if ($player_response->get_has_errors())
    {
        echo $player_response->get_json_error();
        die();
    }
    $player_id = $player_response->get_data()["id"];
    
    // check game
    $game_response = $game_model->validate_game_by_id($game_id);
    if ($game_response->get_has_errors())
    {
        echo $game_response->get_json_error();
        die();
    }
    $game = $game_response->get_data();
    $game_id = $game["id"];

    // get deployments
    $deployments = $deployment_model->get_deployments($player_id, $game_id);
    
    echo ApiResponse::success_data(sizeof($deployments) > 0)->get_json();
    
    