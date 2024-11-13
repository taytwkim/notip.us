<?php

namespace App\Controllers;
require '/home/notip/vendor/autoload.php'; // Google API Client Library
use Google\Client as Google_Client;  

use App\Models\UserModel;
helper('common');

class User extends BaseController
{
    public function index(): string
    {
        return 'Access Denied.';
    }

    public function auth(): array
    {
        $client = new Google_Client(['client_id' => '633588665707-p58qf2t6g18mcuo0rm738pjg2hsmo3kj.apps.googleusercontent.com']); 
        $payload = $client->verifyIdToken($_POST['credential']);

        //echo "payload";
        //print_r($payload);

        if ($payload) 
        {
            $googleId = $payload['sub'];
            $name = $payload['name'];
            $email = $payload['email'];
            $profilePicture = $payload['picture'];

            $userModel = new UserModel();

            $res = $userModel->getByGoogleId($googleId);

            $userNo = null;
            if($res['data']) 
            {
                $userNo = $res['data']['no'];    
            }        

            if($userNo) 
            {
                // Update if the user already exists
                $userModel->updateGoogleUser($name, $email, $profilePicture, $userNo);
            } 
            else 
            {
                // Add if the user is new
                $res = $userModel->addUser($name, $email, $profilePicture, $googleId);

                if($res['success']) 
                {
                    $userNo = $res['data'];
                }
                else
                {
                    returnError('Unknown error occurred during new user registration.');
                }
            }

            if(!$userNo) returnError('Unknown error occurred during login.');

            // The following 3 values are always required together, as they are used in the common getUser() function for session data
            $_SESSION['user_no'] = $userNo;
            $_SESSION['user_name'] = $name;
            $_SESSION['profile_image'] = $profilePicture;
        } 
        else 
        {
            returnError('No authentication credentials provided.');
        }

        returnData($userNo);
    }

    public function signOut(): array 
    {
        session_destroy();

        returnData();
    }

    public function savePlace(): array
    {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, true);
        
        $placeVo = array();
        $placeVo['placeId'] = $input['placeId'];
        $placeVo['name'] = $input['name'];
        $placeVo['alias'] = $input['alias'] ?? '';
        $placeVo['keyword'] = $input['keyword'] ?? '';
        $placeVo['state'] = $input['state'];
        $placeVo['city'] = $input['city'];
        $placeVo['address'] = $input['address'];
        $placeVo['latitude'] = $input['latitude'];
        $placeVo['longitude'] = $input['longitude'];
        $placeVo['zipcode'] = $input['zipcode'];
        $placeVo['type'] = $input['type'];
        $placeVo['reg_user'] = '1';
        $placeVo['reg_ip'] = $_SERVER['REMOTE_ADDR'];

        $placeModel = new PlaceModel();
        $res = $placeModel->addPlace($placeVo);

        returnData($res);
    }

    public function search(): string
    {
        return view('search-test');
    }
}
