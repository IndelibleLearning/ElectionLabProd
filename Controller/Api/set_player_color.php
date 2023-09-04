<?php
require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ColorModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $player_id = $_GET["player_id"];
    $color_name = $_GET["color_name"];

    $player_model = new PlayerModel();
    $color_model = new ColorModel();
    
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
    
    
    $player_model->set_color($player_id, $color["id"]);
    
    