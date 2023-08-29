<?php
    session_start();
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModeModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $game_id = $_GET["game_id"];

    $game_model = new GameModel();
    $player_model = new PlayerModel();

    $game_response = $game_model->validate_game_by_id($game_id);
    if ($game_response->get_has_errors())
    {
        echo $game_response->get_json_error();
        die();
    }
    $game = $game_response->get_data();

    // get players in game
    $player_response = $player_model->validate_players_in_game($game_id);
    if ($player_response->get_has_errors())
    {
        echo $player_response->get_json_error();
        die();
    }
    $players = $player_response->get_data();

    // check if all players have deployments
    $all_players_deployed = true;
    foreach ($players as $player)
    {
        $player_id = $player["id"];
        $player_model->leave_game($player_id);
    }

    echo ApiResponse::success()->get_json();
    