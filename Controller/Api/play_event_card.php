<?php
    session_start();
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/EventCardModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/TurnModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/TurnEventCardModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $game_id = $_GET["game_id"];
    $player_name = $_GET["player_name"];
    $turn_num = $_GET["turn_num"];
    $event_card_id = $_GET["event_card_id"];
    
    $game_model = new GameModel();
    $player_model = new PlayerModel();
    $turn_model = new TurnModel();
    $event_card_model = new EventCardModel();
    $turn_event_card_model = new TurnEventCardModel();
   
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


    // check turn
    $turn_response = $turn_model->validate_get_turn($game_id, $turn_num);
    if ($turn_response->get_has_errors())
    {
        echo $turn_response->get_json_error();
        die();
    }
    $turn = $turn_response->get_data();
    
    // check event card
    // if event card is null this means they have decided to not play a card
    $validated_card_id = null;

    // if event card is NOT null we want to make sure it's valid
    if ($event_card_id != null)
    {
        $event_card_response = $event_card_model->validate_event_card($event_card_id);
        if (!$event_card_response->get_has_errors())
        {
            // only register the played card if there are no errors
            $validated_card_id = $event_card_id;
        }
    }

    $turn_event_card_model->register_card_played_on_turn( 
            $player_id, 
            $turn["id"],
            $validated_card_id);
    
    echo ApiResponse::success()->get_json();
    