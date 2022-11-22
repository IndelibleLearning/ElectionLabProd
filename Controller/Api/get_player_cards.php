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
    
    $game_model = new GameModel();
    $player_model = new PlayerModel();
    $election_year_model = new ElectionYearModel();
    $event_deck_model = new EventDeckModel();
    $player_event_card_model = new PlayerEventCardModel();
    $event_card_model = new EventCardModel();
    
    // validate game
    $game_response = $game_model->validate_game_by_id($game_id);
    if ($game_response->get_has_errors())
    {
        echo $game_response->get_json_error();
        die();
    }
    
     // check player
    $player_response = $player_model->validate_player_in_game($game_id, $player_name);
    if ($player_response->get_has_errors())
    {
        echo $player_response->get_json_error();
        die();
    }
    $player_id = $player_response->get_data()["id"];
    
    // get event cards for the player
    $player_cards = $player_event_card_model->get_player_cards($player_id, $game_id);
    
    $hand = [];
    // get info for each individual card
    foreach($player_cards as $card)
    {
        $card_response = $event_card_model->validate_event_card($card["event_card_id"]);
        if ($card_response->get_has_errors())
        {
            continue;
        }
        $card_result["event_card_data"] = $card_response->get_data();
        $card_result["player_event_card_data"] = $card;
        array_push($hand, $card_result);
    }
    
    echo ApiResponse::success_data($hand)->get_json();
    