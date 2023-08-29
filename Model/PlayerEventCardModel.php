<?php
    require dirname(dirname(__FILE__)) . "/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";

    class PlayerEventCardModel extends Database
    {
        
        public function get_player_cards($player_id, $game_id)
        {
            $param_types = "ii";
            return $this->select("SELECT * FROM player_event_cards where player_id = ? AND game_id = ?", $param_types, [$player_id, $game_id]);
        }
        
        public function validate_player_cards($player_id, $game_id)
        {
            $error_code = "player_cards_not_found";
            $error_msg = "Could not find cards for player $player_id in $game_id";
            $results = $this->get_player_cards($player_id, $game_id);
            return ApiResponse::validate_array_not_empty($results, $error_code, $error_msg);
        }
        
        public function get_player_card($id, $player_id)
        {
            $param_types = "ii";
            return $this->select("SELECT * FROM player_event_cards where id = ? AND player_id = ?", $param_types, [$id, $player_id]);
        }
        
        public function validate_player_card($id, $player_id)
        {
            $error_code = "player_event_card_not_found";
            $error_msg = "Could not find cards with id $id for player with id $player_id";
            $results = $this->get_player_card($id, $player_id);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
        
        public function get_player_card_on_turn($player_id, $turn_created)
        {
            $param_types = "ii";
            return $this->select("SELECT * FROM player_event_cards where player_id = ? AND turn_created = ?", $param_types, [$id, $turn_created]);
        }
        
        public function validate_card_not_drawn_on_turn($player_id, $turn_created)
        {
            $error_code = "player_event_card_already_drawn";
            $error_msg = "Already drew card on turn $turn_created for player with id $player_id";
            $results = $this->get_player_card_on_turn($player_id, $turn_num);
            return ApiResponse::validate_is_empty($results, $error_code, $error_msg);
        }
        
        public function create_player_card(
            $player_id, 
            $game_id, 
            $event_card_id,
            $turn_created)
        {
            $param_types = "iiii";
            return $this->insert("INSERT INTO player_event_cards (
                player_id, 
                game_id, 
                event_card_id,
                turn_created) 
                VALUES (?, ?, ?, ?)", 
                $param_types, 
                [$player_id, $game_id, $event_card_id, $turn_created]);
        }
        
        public function set_turn_played($player_event_card_id, $turn_num)
        {
            $param_types = "ii";
            return $this->update("UPDATE player_event_cards SET turn_played=? where id=?", $param_types, [$turn_num, $player_event_card_id]);
        }
       
    }