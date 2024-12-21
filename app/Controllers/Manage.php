<?php

namespace App\Controllers;

use App\Models\PlaceModel;

helper('common');

class Manage extends BaseController
{
    public function index(): string
    {
        echo view('templates/head');
        echo view('templates/foot');
    }

    public function dashboard(): void
    {
        echo view('manage/templates/head');
        echo view('manage/dashboard');
        echo view('manage/templates/foot');
    }

    public function register($placeId=null): void
    {
        $data = array();

        $placeModel = new PlaceModel();
        $res = $placeModel->getByPlaceId($placeId);

        if(!$res) 
        {
            returnError("No such place");
        }

        $photos = $placeModel->getPhotosByPlaceNo($res['no']);
        $res['photos'] = $photos;

        $data['place'] = $res;

        echo view('templates/head');
        echo view('manage', $data);
        echo view('templates/foot');
    }

    public function list(): void
    {
        echo view('templates/head');
        echo view('list');
        echo view('templates/foot');
    }

    public function about(): void
    {
        echo view('templates/head');
        echo view('about');
        echo view('templates/foot');
    }
}
