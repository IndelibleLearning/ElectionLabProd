<?php
session_start();
require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
require_once PROJECT_ROOT_PATH . "/Model/TurnModel.php";
require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
require_once PROJECT_ROOT_PATH . "/Model/TurnEventCardModel.php";
header('Content-Type: application/json; charset=utf-8');

$game_id = $_GET["game_id"];
$player_name = $_GET["player_name"];
$turn_num = $_GET["turn_num"];

$game_model = new GameModel();
$turn_model = new TurnModel();
$player_model = new PlayerModel();
$turn_event_card_model = new TurnEventCardModel();

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

// check turn
$turn_response = $turn_model->validate_get_turn($game_id, $turn_num);
if ($turn_response->get_has_errors())
{
    echo $turn_response->get_json_error();
    die();
}
$turn = $turn_response->get_data();

$card_played_on_turn = $turn_event_card_model->get_turn_card($player_id, $turn["id"]);

// return whether or not we have played a card this turn
$result = $card_played_on_turn != null && count($card_played_on_turn) > 0;

echo ApiResponse::success_data($result)->get_json();