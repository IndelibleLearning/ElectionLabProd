<?php
    session_start();
    require dirname(dirname(dirname(dirname(__FILE__)))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/EventCardModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/StateModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ModifierModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerEventCardModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/TurnEventCardModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $DESIRED_KEY = "plus";
    
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $game_id = $data["game_id"];
    $player_name = $data["player_name"];
    $player_event_card_id = $data["player_event_card_id"];
    $state_abbrev = $data["state_abbrev"];
    $turn_num = $data["turn_num"];
    
    $player_model = new PlayerModel();
    $game_model = new GameModel();
    $event_card_model = new EventCardModel();
    $state_model = new StateModel();
    $turn_model = new TurnModel();
    $modifier_model = new ModifierModel();
    $player_event_card_model = new PlayerEventCardModel();
    $turn_event_card_model = new TurnEventCardModel();
    
     // check player
    $player_response = $player_model->validate_player_in_game($game_id, $player_name);
    if ($player_response->get_has_errors())
    {
        echo $player_response->get_json_error();
        die();
    }
    $player_id = $player_response->get_data()["id"];
    
    // check game
    $game_response = $game_model->validate_game_by_id($game_id);
    if ($game_response->get_has_errors())
    {
        echo $game_response->get_json_error();
        die();
    }
    $game = $game_response->get_data();

    // validate state
    $state_response = $state_model->validate_state_by_abbrev($state_abbrev);
    if ($state_response->get_has_errors())
    {
        echo $state_response->get_json_error();
        die();
    }
    $state = $state_response->get_data();
    
    // validate turn
    $turn_results = $turn_model->validate_turn($game_id, $turn_num);
    if ($turn_results->get_has_errors())
    {
        echo $turn_results->get_json_error();
        die();
    }
    $turn = $turn_results->get_data();
    
    // validate card is in player's hand
    $player_event_card_response = $player_event_card_model->validate_player_card($player_event_card_id, $player_id);
    if ($player_event_card_response->get_has_errors())
    {
        echo $player_event_card_response->get_json_error();
        die();
    }
    $player_event_card = $player_event_card_response->get_data();
    
    // validate card exists
    $event_card_id = $player_event_card["event_card_id"];
    $card_response = $event_card_model->validate_event_card($player_event_card["event_card_id"]);
    if ($card_response->get_has_errors())
    {
        echo $card_response->get_json_error();
        die();
    }
    $event_card = $card_response->get_data();
        
    // validate card is the right key
    $card_key_response = $event_card_model->validate_card_has_key($event_card, $DESIRED_KEY);
    if ($card_key_response->get_has_errors())
    {
        echo $card_key_response->get_json_error();
        die();
    }
    
    // add modifiers
    $modifier_model->create_modifier(
        $turn["id"], 
        $DESIRED_KEY, 
        1, 
        $player_id,
        $state["id"],
        $game_id);
    
    // mark card as used on this turn
    $player_event_card_model->set_turn_played($player_event_card["id"], $turn_num);
    
    // update turn card
    $turn_event_card_model->register_card_played_on_turn($player_id, $turn["id"], $event_card["id"]);
    
    echo ApiResponse::success()->get_json();
    