<?php

namespace App\Controllers;

use App\Models\PlaceModel;
helper('common');

class Place extends BaseController
{
    public function index(): string
    {
        return view('map-test');
    }

    public function info($placeId): array
    {
        $placeModel = new PlaceModel();
        $res = $placeModel->getByPlaceId($placeId);

        if(!$res) 
        {
            returnError("No such place");
        }

        $photos = $placeModel->getPhotosByPlaceNo($res['no']);
        $res['photos'] = $photos;

        $placeModel->addStatistics($res['no'], 0, 1);

        returnData($res);
    }

    public function list(): array
    {
        $placeModel = new PlaceModel();
        $res = $placeModel->getList();

        // Loop through each result and perform another query
        for($i=0; $i<sizeof($res); $i++) 
        {   
            $res[$i]['name'] = html_entity_decode($res[$i]['name']);

            $photos = $placeModel->getPhotosByPlaceNo($res[$i]['no']);
            
            $res[$i]['photos'] = $photos;
            $res[$i]['policyText'] = ($res[$i]['policy']=='noTip')?"No Tip":"Fair Tip";
        }

        returnData($res);
    }

    public function regionalList(): array
    {
        $get = $this->request->getGet();
        $longitude = filter_var($get['lng'], FILTER_VALIDATE_FLOAT);
        $latitude = filter_var($get['lat'], FILTER_VALIDATE_FLOAT);
        $radius = filter_var($get['center'] ?? 5, FILTER_VALIDATE_FLOAT);

        $placeModel = new PlaceModel();

        // Start time measurement
        $startTime = microtime(true);
        $places = $placeModel->getListWithinRadius($latitude, $longitude, $radius);
        $queryEndTime = microtime(true);

        // Loop through each result and perform another query
        for($i=0; $i<sizeof($places); $i++) 
        {   
            $places[$i]['name'] = html_entity_decode($places[$i]['name']);

            $photos = $placeModel->getPhotosByPlaceNo($places[$i]['no']);
            
            $places[$i]['photos'] = $photos;
            $places[$i]['policyText'] = ($places[$i]['policy']=='noTip')?"No Tip":"Fair Tip";
        }

        $processEndTime = microtime(true);

        $res['queryTime'] = $queryEndTime - $startTime;
        $res['totalTime'] = $processEndTime - $startTime;
        $res['places'] = $places;

        returnData($res);
    }

    public function save(): array
    {
        $user = getUser();
        if(!$user) 
        {
            // returnFalse("Login to save new place!");
        }

        $post = $this->request->getPost();

        $googlePlaceId = filter_var($post['google-place-id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $name = filter_var($post['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $address = filter_var($post['address'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $type = filter_var($post['type'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $policy = filter_var($post['policy'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $state = filter_var($post['state'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $city = filter_var($post['city'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $longitude = filter_var($post['longitude'], FILTER_VALIDATE_FLOAT);
        $latitude = filter_var($post['latitude'], FILTER_VALIDATE_FLOAT);
        $zipcode = filter_var($post['zipcode'], FILTER_VALIDATE_FLOAT);
        
        $policyNotaccept = filter_var($post['policy-notaccept'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $policyMin10 = filter_var($post['policy-min10'] ?? false ?? false, FILTER_VALIDATE_BOOLEAN);
        $policyMax20 = filter_var($post['policy-max20'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $policyNosuggest = filter_var($post['policy-nosuggest'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $policyGuarantee = filter_var($post['policy-guarantee'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $basedonExperience = filter_var($post['basedon-experience'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $basedonSource = filter_var($post['basedon-source'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $basedonOfficial = filter_var($post['basedon-official'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $sourcePage = filter_var($post['source-page'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $officialPage = filter_var($post['official-page'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $experience = filter_var($post['experience'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        $placeVo = array();
        $placeVo['placeId'] = randByteHash();
        $placeVo['googlePlaceId'] = $googlePlaceId;
        $placeVo['name'] = $name;
        $placeVo['state'] = $state;
        $placeVo['city'] = $city;
        $placeVo['address'] = $address;
        $placeVo['type'] = $type;
        $placeVo['policy'] = $policy;
        $placeVo['latitude'] = $latitude;
        $placeVo['longitude'] = $longitude;
        $placeVo['zipcode'] = $zipcode;
        $placeVo['sourcePage'] = $sourcePage;
        $placeVo['officialPage'] = $officialPage;
        $placeVo['experience'] = $experience;
        $placeVo['created_by'] = 0;
        $placeVo['register_ip'] = getUserIp();

        $res = [];

        $db = \Config\Database::connect();
        $db->transStart();

        $placeModel = new PlaceModel();
        $res['addPlace'] = $placeModel->addPlace($placeVo);

        $error = $db->error();
        if ($error['code'] != 0) {
            $res['addPlaceError'] = $error; // Place 추가 에러 기록
            $db->transRollback();
            returnError($res); // 에러 반환 후 종료
        }

        if($policyNotaccept) {
            $res['addPolicyNotaccept'] = $placeModel->addPolicyDetail($res['addPlace']["insertID"], 'notaccept');

            $error = $db->error();
            if ($error['code'] != 0) {
                $res['addPolicyNotacceptError'] = $error; // Notaccept 추가 에러 기록
                $db->transRollback();
                returnError($res);
            }
        }

        if($policyMin10) {
            $res['addPolicyMin10'] = $placeModel->addPolicyDetail($res['addPlace']["insertID"], 'min10');

            $error = $db->error();
            if ($error['code'] != 0) {
                $res['addPolicyMin10Error'] = $error; // Min10 추가 에러 기록
                $db->transRollback();
                returnError($res);
            }
        }

        if (!$db->transComplete()) {
            returnError('An error occured while register a new place');
        }

        $newPlaceId = $placeModel->getPlaceIdByNo($res['addPlace']['insertID']);

        returnData($newPlaceId['place_id']);
    }

    public function search(): string
    {
        return view('search-test');
    }
}

