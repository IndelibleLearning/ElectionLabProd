<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";
     
    class ElectionYearModel extends Database
    {
        public function get_election_by_year($election_year)
        {
            $param_types = "i";
            return $this->select("SELECT * FROM election_years where year = ?", $param_types, [$election_year]);
        }
        
        public function validate_election_by_year($election_year)
        {
            $error_code = "no_year_found";
            $error_msg = "Could not find election year associated with $election_year";
            $results = $this->get_election_by_year($election_year);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
        
        public function get_election_by_id($id)
        {
            $param_types = "i";
            return $this->select("SELECT * FROM election_years where id = ?", $param_types, [$id]);
        }
        
        public function validate_election_by_id($id)
        {
            $error_code = "no_year_id_found";
            $error_msg = "Could not find election year associated with id $id";
            $results = $this->get_election_by_id($id);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
    }