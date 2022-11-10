<?php
session_start();
require __DIR__ . '/../../inc/bootstrap.php';
require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
require_once PROJECT_ROOT_PATH . "/Model/TurnModel.php";
require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
require_once PROJECT_ROOT_PATH . "/Model/TurnEventCardModel.php";
require_once PROJECT_ROOT_PATH . "/Controller/Api/RestHelper.php";
header('Content-Type: application/json; charset=utf-8');

$game_id = $_GET["game_id"];
$player_name = $_GET["player_name"];
$turn_num = $_GET["turn_num"];

$game_model = new GameModel();
$player_model = new PlayerModel();

// validate game
$game_response = $game_model->validate_game_by_id($game_id);
if ($game_response->get_has_errors())
{
    echo $game_response->get_json_error();
    die();
}
$game = $game_response->get_data();

// check player
$player_response = $player_model->validate_player_in_game($game_id, $player_name);
if ($player_response->get_has_errors())
{
    echo $player_response->get_json_error();
    die();
}
$player_id = $player_response->get_data()["id"];

// get opponent
$opponent = $player_model->get_opponent($game_id, $player_id)[0]["player_name"];

$encoded_opponent = urlencode($opponent);
// check opponent has cards
$check_play_url = CONTROLLER_API_PATH . "check_played_card.php?game_id=$game_id&player_name=$encoded_opponent&turn_num=$turn_num";
$has_opponent_played = RestHelper::rest_call($check_play_url)["data"];

echo ApiResponse::success_data($has_opponent_played)->get_json();