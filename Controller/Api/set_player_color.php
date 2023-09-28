<?php
require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ColorModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $player_id = $_GET["player_id"];
    $color_name = $_GET["color_name"];
    $game_id = $_GET["game_id"];

    $player_model = new PlayerModel();
    $color_model = new ColorModel();
    $game_model = new GameModel();

    // validate player
    $player_response = $player_model->validate_player_by_id($player_id);
    if ($player_response->get_has_errors())
    {
        echo $player_response->get_json_error();
        die();
    }

    // check color
    $color_response = $color_model->validate_color_by_name($color_name);
    if ($color_response->get_has_errors())
    {
        echo $color_response->get_json_error();
        die();
    }
    $color = $color_response->get_data();

    // validate player
    $game_response = $game_model->validate_game_by_id($game_id);
    if ($game_response->get_has_errors())
    {
        echo $game_response->get_json_error();
        die();
    }
    $game = $game_response->get_data();
    
    
    $player_model->set_color($player_id, $color["id"]);

    if ($color_name == "red")
    {
        $game_model->set_red_player($game_id, $player_id);
    } else if ($color_name == "blue")
    {
        $game_model->set_blue_player($game_id, $player_id);
    }

    