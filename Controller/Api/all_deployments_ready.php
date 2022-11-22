<?php
require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
require_once PROJECT_ROOT_PATH . "/Model/DeploymentModel.php";
require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
header('Content-Type: application/json; charset=utf-8');

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$room_code = $data["room_code"];
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

// check game
$game_response = $game_model->validate_game_by_id($game_id);
if ($game_response->get_has_errors())
{
    echo $game_response->get_json_error();
    die();
}
$game = $game_response->get_data();
$game_id = $game["id"];

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
    $deployments = $deployment_model->get_deployments($player_id, $game_id);

    if (sizeof($deployments) <= 0)
    {
        $all_players_deployed = false;
    }

}

echo ApiResponse::success_data($all_players_deployed)->get_json();