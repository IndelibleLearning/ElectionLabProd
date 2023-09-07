<?php
require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
header('Content-Type: application/json; charset=utf-8');

$player_name = urldecode($_GET["player_name"]);
$room_code = $_GET["room_code"];
$game_id = $_GET["game_id"];

$room_model = new RoomModel();
$player_model = new PlayerModel();
$game_model = new GameModel();

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
$player = $player_results->get_data();

$game_results = $game_model->validate_game_by_id($game_id);
if ($game_results->get_has_errors())
{
    echo $game_results->get_json_error();
    die();
}
$game_room_id = $game_results->get_data()["room_id"];

$still_running =
    $player["room_id"] == $room_id &&
    $player["game_id"] == $game_id &&
    $room_id == $game_room_id;

echo ApiResponse::success_data($still_running)->get_json();

    