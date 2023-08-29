<?php
    require dirname(dirname(__FILE__)) . "/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";

    class TurnEventCardModel extends Database
    {
        
        public function get_turn_card($player_id, $turn_id)
        {
            $param_types = "ii";
            return $this->select("SELECT * FROM turn_event_cards WHERE player_id = ? AND turn_id = ?", $param_types, [ $player_id, $turn_id]);
        }
        
        public function register_card_played_on_turn(
            $player_id, 
            $turn_id,
            $event_card_id)
        {
            $param_types = "iii";
            return $this->insert("INSERT INTO turn_event_cards (
                player_id, 
                turn_id, 
                event_card_id) 
                VALUES (?, ?, ?)", 
                $param_types, 
                [$player_id, $turn_id, $event_card_id]);
        }
       
    }