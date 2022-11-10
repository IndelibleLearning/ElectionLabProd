<?php
    session_start();
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/ElectionYearModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/EventDeckModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/EventDeckCardModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerEventCardModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $color_id = $_GET["color_id"];
    $election_year = $_GET["election_year"];
    $game_id = $_GET["game_id"];
    $player_name = $_GET["player_name"];
    $turn_created = $_GET["turn_created"];
    
    $game_model = new GameModel();
    $player_model = new PlayerModel();
    $election_year_model = new ElectionYearModel();
    $event_deck_model = new EventDeckModel();
    $event_deck_card_model = new EventDeckCardModel();
    $player_event_card_model = new PlayerEventCardModel();
    
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

    // check election year
    $election_year_results = $election_year_model->validate_election_by_year($election_year);
    if ($election_year_results->get_has_errors())
    {
        echo $election_year_results->get_json_error();
        die();
    }
    $election_year_id = $election_year_results->get_data()["id"];
    
    // check we haven't already gotten a card for this turn
    // TODO: MIGHT NEED TO CHANGE THIS IF WE CAN DRAW MORE THAN ONE CARD
    $cards_for_turn = $player_event_card_model->validate_card_not_drawn_on_turn($player_id, $turn_num);
    if ($cards_for_turn->get_has_errors())
    {
        echo $event_deck_results->get_json_error();
        die();
    }
    
    // get event deck
    $event_deck_results = $event_deck_model->validate_event_deck($election_year_id, $color_id);
    if ($event_deck_results->get_has_errors())
    {
        echo $event_deck_results->get_json_error();
        die();
    }
    $event_deck_id = $event_deck_results->get_data()["id"];
    
    // get event deck cards
    $event_deck_cards_results = $event_deck_card_model->validate_event_deck_cards($event_deck_id);
    if ($event_deck_cards_results->get_has_errors())
    {
        echo $event_deck_cards_results->get_json_error();
        die();
    }
    $event_deck_cards = $event_deck_cards_results->get_data();
    
    $random_card = $event_deck_cards[array_rand($event_deck_cards)];
    
    $player_event_card_model->create_player_card($player_id, $game_id, $random_card["event_card_id"], $turn_created);
    //$player_event_card_model->create_player_card($player_id, $game_id, 2, $turn_created);
    
    echo ApiResponse::success()->get_json();
    