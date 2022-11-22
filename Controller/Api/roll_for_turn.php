<?php
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
    require_once PROJECT_ROOT_PATH . "/Model/PlayerModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RoomModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/GameModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/TurnModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/DeploymentModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/RollModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ModifierModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/DiceModel.php";
    require_once PROJECT_ROOT_PATH . "/Model/ElectionYearModel.php";
    require_once PROJECT_ROOT_PATH . "/Controller/Api/RestHelper.php";
    header('Content-Type: application/json; charset=utf-8');
    
    $EVENTS_GAME_MODE = 2;
    
    $room_code = $_GET["room_code"];
    $game_id = $_GET["game_id"];
    $turn_num = $_GET["turn_num"];
    
    $room_model = new RoomModel();
    $player_model = new PlayerModel();
    $game_model = new GameModel();
    $turn_model = new TurnModel();
    $deployment_model = new DeploymentModel();
    $election_year_model = new ElectionYearModel();
    $modifier_model = new ModifierModel();
    
    // validate room
    $room_response = $room_model->validate_room_by_code($room_code);
    if ($room_response->get_has_errors())
    {
        echo $room_response->get_json_error();
        die();
    }
    
    // validate game
    $game_response = $game_model->validate_game_by_id($game_id);
    if ($game_response->get_has_errors())
    {
        echo $game_response->get_json_error();
        die();
    }
    $game = $game_response->get_data();
    
    // get turn
    $turn_results = $turn_model->validate_turn($game_id, $turn_num);
    if ($turn_results->get_has_errors())
    {
        echo $turn_results->get_json_error();
        die();
    }
    $turn = $turn_results->get_data();
    $turn_id = $turn["id"];
    $state_id_for_turn = $turn["state_id"];
    
    // make sure we haven't already rolled for this turn
    $already_rolled_results = $turn_model->validate_turn_not_rolled_yet($turn);
    if ($already_rolled_results->get_has_errors())
    {
        showRolls($room_code, $game_id, $turn_num);
        die();
    }
    
    // get election year
    $election_year_response = $election_year_model->validate_election_by_id($game["election_year_id"]);
    if ($election_year_response->get_has_errors())
    {
        echo $election_year_response->get_json_error();
        die();
    }
    $election_year = $election_year_response->get_data()["year"];
    
    // get players
    $player_response = $player_model->validate_players_in_game($game_id);
    if ($player_response->get_has_errors())
    {
        echo $player_response->get_json_error();
        die();
    }
    $players = $player_response->get_data();
    
    $player_color_map = [];
    
    // get each players deployments
    $deployments = [];
    foreach ($players as $player)
    {
        $player_deployment = $deployment_model->get_deployment($player["id"], $game_id, $state_id_for_turn);
        if ($player_deployment && count($player_deployment) > 0)
        {
            array_push($deployments, $player_deployment[0]);
        }
        
        $color_map_entry = [];
        $color_map_entry["color_id"] = $player["color_id"];
        $color_map_entry["player_name"] = $player["player_name"];
        $player_color_map[$player["id"]] = $color_map_entry;
    }
    
    // get each players modifications from event cards on this state
    $modifications = $modifier_model->get_players_modifications($players, $game_id, $state_id_for_turn);
    
    // calculate total deployments to see if there is no pieces
    $total_deployments = get_total_deployments($deployments, $modifications);
    
    // nobody deployed so everyone gets one die
    if ($total_deployments <= 0)
    {
        foreach ($players as $player)
        {
            $player_deployment = [];
            $player_deployment["player_id"] = $player["id"];
            $player_deployment["num_pieces"] = 1;
            $modifications[$player["id"]] = 0;
            array_push($deployments, $player_deployment);
        }
    }
    
    $all_rolls = [];
    
    // create map of players to their number of pieces
    $first_dice_map = [];
    foreach ($deployments as $deployment)
    {
        $player_id = $deployment["player_id"];
        $first_dice_map[$player_id] = $deployment["num_pieces"] + $modifications[$player_id];
    }
    
    // the first roll processes a little different
    //echo "--Roll 1\n";
    $roll_round = 0;
    $rolls_map = do_roll($first_dice_map, $roll_round, $turn_id);
    array_push($all_rolls, $rolls_map);
    $dice_map = process_rolls($rolls_map);
    $roll_round++;
    
    // process the rest
    while(check_one_winner($dice_map))
    {
        //echo "\n--Roll " . ($roll_round + 1) . "\n";
        $rolls_map = do_roll($dice_map, $roll_round, $turn_id);
        array_push($all_rolls, $rolls_map);
        $dice_map = process_rolls($rolls_map);
        $roll_round++;
    }
    
    $results = get_winner_and_loser($dice_map);
    $winner_id = $results["winner_id"];
    $loser_id = $results["loser_id"];
    
    // update the winner/num turns
    $turn_model->set_winner($turn_id, $winner_id, $roll_round);
    
    // check if we are in events game mode
    if ($game["game_mode"] == $EVENTS_GAME_MODE)
    {
         // loser gets a card
        $loser_color_id = $player_color_map[$loser_id]["color_id"];
        $loser_name = urlencode($player_color_map[$loser_id]["player_name"]);
        $create_card_url = CONTROLLER_API_PATH . "create_player_card.php?color_id=$loser_color_id&game_id=$game_id&turn_created=$turn_num&election_year=$election_year&player_name=$loser_name";
        $create_card_response = RestHelper::rest_call($create_card_url);
        
        if($create_card_response["has_errors"])
        {
            echo json_encode($create_card_response);
            die();
        }
    }

    showRolls($room_code, $game_id, $turn_num);
    
    // -------------- FUNCTIONS --------------
    
    // takes in a map of player ids to their number of dice
    // returns a map of player ids to arrays of their dice for this roll sorted
    function do_roll($player_dice_map, $roll_round, $turn_id)
    {
        $roll_model = new RollModel();
        $dice_model = new DiceModel();

        // process all deployments 
        $player_rolls = [];
        foreach ($player_dice_map as $player_id => $num_dice)
        {
            // create db entry for this roll
            $roll_model->create_roll($turn_id, $num_dice, $roll_round, $player_id);

            // roll all of this player's dice
            $dice = [];
            for ($i = 0; $i < $num_dice; $i++)
            {
                $this_roll = rand(1,6);
                array_push($dice, $this_roll);
                
                // create db entry for this die
                $roll_results = $roll_model->get_roll_for_round($turn_id, $roll_round, $player_id);
                $roll_id = $roll_results[0]["id"];
                $dice_model->create_dice($this_roll, $roll_id, $player_id);
            }

            // sort $dice descending
            rsort($dice);
            
            // DEBUG PURPOSES PRINT
            /*echo "Player " . $player_id . ":";
            foreach($dice as $die)
            {
                echo $die . " ";
            }
            echo "\n";*/
            
            // store to player id
            $player_rolls[$player_id] = $dice;
        }
        
        return $player_rolls;
    }
    
    // takes in a list of the players ids mapped to their rolls
    // compares and returns a new map of player id to their number
    // of dice for their next roll
    function process_rolls($player_roll_map)
    {
        // most number of dice for easy looping purposes
        $biggest_num_dice = -1;
        // final map with all of the players to their dice values
        // start them as their previous values
        $final_dice_map = [];
        foreach($player_roll_map as $player_id => $dice_values)
        {
            if (count($dice_values) > $biggest_num_dice)
            {
                $biggest_num_dice = count($dice_values);
            }
            
            $final_dice_map[$player_id] = count($dice_values);
        }
        
        // highest dice value amongst the players for each dice
        $max_dice_values = [];
        // find the highest dice for each player's dice when comparing in descending order
        for($i=0; $i<$biggest_num_dice; $i++)
        {
            $max_value = -1;
            foreach($player_roll_map as $player_id => $dice_values)
            {
                if (count($dice_values) > $i && $dice_values[$i] > $max_value)
                {
                    $max_value = $dice_values[$i];
                }
            }
            array_push($max_dice_values, $max_value);
        }
        
        // loop through all the maps and if the player isn't the max then
        // reduce their number of dice for next round
        foreach($player_roll_map as $player_id => $dice_values)
        {
            for($i=0; $i < count($dice_values); $i++)
            {
                $this_dice = $dice_values[$i];
                

                if ($this_dice < $max_dice_values[$i])
                {
                    $final_dice_map[$player_id]--;
                }
            }
        }
        
        return $final_dice_map;
    }
    
    // returns true at most one player has dice remaining
    function check_one_winner($dice_map)
    {
        $num_losers = 0;
        foreach($dice_map as $player_id => $num_dice)
        {
            if ($num_dice <= 0)
            {
                $num_losers++;
            }
        }
        
        return $num_losers < count($dice_map) - 1;
    }
    
    // returns the winner of the dice map based on who has dice left
    function get_winner_and_loser($dice_map)
    {
        $results = [];

        foreach($dice_map as $player_id => $num_dice)
        {
            if($num_dice > 0)
            {
                $results["winner_id"] = $player_id;
            }
            else
            {
                $results["loser_id"] = $player_id;
            }
        }
        
        return $results;
    }
    
    
    // returns number of total deployments for all players
    function get_total_deployments($deployments, $modifications)
    {
        $total_deployments = 0;
        foreach ($deployments as $deployment)
        {
            $pieces = $deployment["num_pieces"];
            $mod = $modifications[$deployment["player_id"]];
            $total_deployments += $pieces + $mod;
        }
        
        return $total_deployments;
    }
    
    function showRolls($room_code, $game_id, $turn_num)
    {
        $rolls_url = CONTROLLER_API_PATH . "get_rolls_for_turn.php?room_code=$room_code&game_id=$game_id&turn_num=$turn_num";
        $rolls_response = RestHelper::rest_call($rolls_url);
        
        if($rolls_response["has_errors"])
        {
            echo json_encode($rolls_response);
            die();
        }
        
        echo ApiResponse::success_data($rolls_response["data"])->get_json();
    }
    
    