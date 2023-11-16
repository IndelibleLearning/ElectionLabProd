<?php
require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
require_once PROJECT_ROOT_PATH . "/inc/vendor/autoload.php";
require_once PROJECT_ROOT_PATH . "/Model/UserModel.php";

$userModel = new UserModel();

$clientId = "887227153285-7ol0k70995346nef307c9ckesrai7rft.apps.googleusercontent.com";

$client = new Google_Client(['client_id' => $clientId]);  // Specify your client ID
$json = file_get_contents('php://input');
$data = json_decode($json, true);

$id_token = $data['google_id'];

try {
    $payload = $client->verifyIdToken($id_token);

    if ($payload) {
        $userid = $payload['sub'];
        $email = $payload['email'];
        // If request specified a G Suite domain:
        //$domain = $payload['hd'];

        // TODO: make this standard function with login
        $token = openssl_random_pseudo_bytes(16);

        //Convert the binary data into hexadecimal representation.
        $token = bin2hex($token);

        // TODO: Find or create the user
        $validated_user = $userModel->validate_or_create_google_user($userid, $email);
        if ($validated_user->get_has_errors())
        {
            echo $validated_user->get_json_error();
            die();
        }
        $user = $validated_user->get_data()[0];

        $userModel->set_login_token($user["id"], $token);

        $result = [];
        $result["login_token"] = $token;
        $result["username"] = $user["user_name"];
        echo ApiResponse::success_data($result)->get_json();
    } else {
        // Invalid ID token
        echo ApiResponse::error("google_signin_fail", "Failed to sign in with google")->get_json();
    }

} catch (Exception $e)
{
    //error_log('Exception when verifying Google ID token: ' . $e->getMessage());
    echo $e->getMessage();
}