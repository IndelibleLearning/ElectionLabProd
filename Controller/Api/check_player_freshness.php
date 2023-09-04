<?php
require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');

    $player_name = urldecode($_GET["player_name"]);
    $room_code = $_GET["room_code"];

    $room_model = new RoomModel();
    $player_model = new PlayerModel();


    $room_results = $room_model->validate_room_by_code($room_code);
    if ($room_results->get_has_errors())
    {
        echo $room_results->get_json_error();
        die();
    }
    $room_id = $room_results->get_data()["id"];

    $player_results = $player_model->validate_player_in_room($room_id, $player_name);
    if ($player_results->get_has_errors())
    {
        echo $player_results->get_json_error();
        die();
    }
    $player_id = $player_results->get_data()["id"];

    echo $player_model->get_freshness_difference($player_id);

   // echo ApiResponse::success_data($result)->get_json();
    
    