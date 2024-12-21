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
        if($this->request->getMethod() == 'GET') 
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
        else if($this->request->getMethod() == 'POST') 
        {
            $post = $this->request->getPost();

            $placeId = filter_var($placeId, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $firstName = filter_var($post['first-name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $lastName = filter_var($post['last-name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $description = filter_var($post['address'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $placeModel = new PlaceModel();
            $place = $placeModel->getByPlaceId($placeId);
            $user = getUser();

            if(!$user) 
            {
                returnError("To sign up as a manager, you must be logged in.");
            }

            $res = $placeModel->addPlaceManager($place['no'], $user['no'], 'manager', 'apply', $description);

            if($res) 
            {
                returnData("Registered");
            }
        }
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
