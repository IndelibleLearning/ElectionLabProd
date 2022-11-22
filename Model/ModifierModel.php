<?php
    require dirname(dirname(__FILE__)) . "/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";

    class ModifierModel extends Database
    {
        
        public function get_modifiers($state_id, $game_id, $player_id)
        {
            $param_types = "iii";
            return $this->select("SELECT * FROM modifiers where state_id = ? AND game_id = ? AND player_id = ?", $param_types, [$state_id, $game_id, $player_id]);
        }
        
        public function create_modifier(
            $turn_id, 
            $type, 
            $value, 
            $player_id,
            $state_id,
            $game_id)
        {
            $param_types = "isiiii";
            return $this->insert("INSERT INTO modifiers (
                turn_id, 
                type, 
                value,
                player_id,
                state_id,
                game_id) 
                VALUES (?, ?, ?, ?, ?, ?)", 
                $param_types, 
                [$turn_id, $type, $value, $player_id, $state_id, $game_id]);
        }
        
        function get_players_modifications($players, $game_id, $state_id)
        {
            $modifiers = [];
            foreach ($players as $player)
            {
                $modifiers[$player["id"]] = $this->get_player_modifications($player["id"], $game_id, $state_id);
            }
            
            return $modifiers;
        }
       
        function get_player_modifications($player_id, $game_id, $state_id)
        {
            $total_mod_for_state = 0;
            
            // grab players mods for state
            $player_mods = $this->get_modifiers($state_id, $game_id, $player_id);
            if ($player_mods && count($player_mods) > 0)
            {
                $total_mod_for_state = $this->total_mods($player_mods);
            }
            return $total_mod_for_state;
        }
        
        function total_mods($mods_array)
        {
            $total_mods = 0;
            foreach($mods_array as $mod)
            {
                $total_mods += $mod["value"];
            }
            
            return $total_mods;
        }
    }