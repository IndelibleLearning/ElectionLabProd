<?php
    session_start();
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $room_code = $_GET["room_code"];
    $player_name = urldecode($_GET["player_name"]);
    $game_id = $_GET["game_id"];
    
    $room_model = new RoomModel();
    $player_model = new PlayerModel();
    $game_model = new GameModel();
    
    $room_results = $room_model->get_room_by_code($room_code);
    $room = $room_results[0];
    
    if (!$room)
    {
        echo "Could not find room associated with code " . $room_code;
        die();
    }
    
    $player_results = $player_model->get_player_in_room($room["id"], $player_name);
    $player = $player_results[0];

    if (!$player_results)
    {
        echo "Could not find player " . $player_name . " in room " . $room_code;
        die();
    }
    
    $game_results = $game_model->get_game_by_id($game_id);

    if (!$game_results || count($game_results) <= 0)
    {
        echo "Could not find game with id " . $game_id;
        die();
    }
    
    echo $player_model->join_game($player["id"], $game_id);