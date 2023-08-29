<?php
define("PROJECT_ROOT_PATH", __DIR__ . "/../");
define("CONTROLLER_API_PATH", "https://indeliblelearning.com/electionlabonline/Controller/Api/");
 
// include main configuration file
require_once PROJECT_ROOT_PATH . "/inc/config.php";
 
// include the base controller file
require_once PROJECT_ROOT_PATH . "/Controller/Api/BaseController.php";
 
// include the use model file
require_once PROJECT_ROOT_PATH . "/Model/UserModel.php";