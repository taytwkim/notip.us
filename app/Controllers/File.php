<?php

namespace App\Controllers;

use App\Models\FileModel;
helper('common');

class File extends BaseController
{
    public function index(): string
    {
        return 'Access Denied.';
    }

    public function download($uuid)
    {
        $fileModel = new FileModel();
        $res = $fileModel->getByUUID($uuid);
        $rootPath = "/home/notip";

        if (!$res || !$res['success']) 
        {
            die('No file or you can\'t access');
        }
        
        $resData = $res['data'];

        $filePath = $rootPath.$resData['file_path'];
        $fileName = $resData['origin_name'];

        // Check if the file path is valid
        if (!isset($resData['file_path']) || !file_exists($filePath)) 
        {
            // Return a 404 error if the file is not found
            die('No file or you can\'t access');
        }

        // Use CodeIgniter's download function to initiate file download
        return $this->response->download($filePath, null)->setFileName($fileName);
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
