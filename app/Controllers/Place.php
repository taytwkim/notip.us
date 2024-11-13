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

        returnData($res);
    }

    public function list(): array
    {
        $placeModel = new PlaceModel();
        $res = $placeModel->getList();

        // Loop through each result and perform another query
        for($i=0; $i<sizeof($res); $i++) 
        {
            $photos = $placeModel->getPhotosByPlaceNo($res[$i]['no']);
            
            $res[$i]['photos'] = $photos;
        }

        returnData($res);
    }

    public function save(): array
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
        $placeVo['created_by'] = '1';
        $placeVo['register_ip'] = $_SERVER['REMOTE_ADDR'];

        $placeModel = new PlaceModel();
        $res = $placeModel->addPlace($placeVo);

        returnData($res);
    }

    public function search(): string
    {
        return view('search-test');
    }
}
