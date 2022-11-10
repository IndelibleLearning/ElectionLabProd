<?php
    session_start();
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Controller/Api/RestHelper.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
    require_once PROJECT_ROOT_PATH . "/Model/TurnModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/StateModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/StateEVModel.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $game_id = $_GET["game_id"];
    $player_name = $_GET["player_name"];
    $turn_num = $_GET["turn_num"];
    
    $turn_model = new TurnModel();
    $state_model = new StateModel();
    $state_ev_model = new StateEVModel();
    
    $encoded_player_name = urlencode($player_name);
 
    // get all the player cards
    $get_cards_url = CONTROLLER_API_PATH . "get_player_cards.php?&game_id=$game_id&player_name=$encoded_player_name";
    $player_cards = RestHelper::rest_call($get_cards_url)["data"];
    
    // filter out the cards that are available during the turn
    $results = [];
    $cards = [];
    foreach($player_cards as $card)
    {
        $card_data = $card["player_event_card_data"];
        if ($card_data["turn_created"] < $turn_num && 
           ($card_data["turn_played"] == null || $turn_num <= $card_data["turn_played"]))
        {
            array_push($cards, $card);
        }
    }
    
    $results["cards"] = $cards;
   
    echo ApiResponse::success_data($results)->get_json();