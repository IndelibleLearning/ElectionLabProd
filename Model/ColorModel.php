<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";
     
    class ColorModel extends Database
    {
        public function get_color_by_name($color_name)
        {
            $param_types = "s";
            return $this->select("SELECT * FROM colors where name = ?", $param_types, [$color_name]);
        }
        
        public function validate_color_by_name($color_name)
        {
            $error_code = "color_not_found";
            $error_msg = "Could not find color associated with name: $color_name";
            $results = $this->get_color_by_name($color_name);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
        
        public function get_color_by_id($id)
        {
            $param_types = "i";
            return $this->select("SELECT * FROM colors where id = ?", $param_types, [$id]);
        }
        
        public function validate_color_by_id($id)
        {
            $error_code = "color_not_found";
            $error_msg = "Could not find color associated with id: $id";
            $results = $this->get_color_by_id($id);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
    }