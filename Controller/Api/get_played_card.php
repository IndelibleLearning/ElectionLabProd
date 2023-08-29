<?php
    session_start();
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/ElectionYearModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/EventDeckModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerEventCardModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/EventCardModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $game_id = $_GET["game_id"];
    $player_name = $_GET["player_name"];
    $turn_num = $_GET["turn_number"];
    
    // get all the player cards
    $get_cards_url = CONTROLLER_API_PATH . "get_player_cards.php?&game_id=$game_id&player_name=$player_name";
    $player_cards = RestHelper::rest_call($get_cards_url);
    
    // filter out the cards that are available during the turn
    $results = null;
    foreach($player_cards as $card)
    {
        if ($turn_num == $card["turn_played"])
        {
            $results = $card;
        }
    }
    
    echo ApiResponse::success_data($results)->get_json();