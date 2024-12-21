<?php

namespace App\Controllers;

class Page extends BaseController
{
    public function index(): string
    {
        echo view('templates/head');
        echo view('templates/foot');
    }

    public function map($placeId=null): void
    {
        $data = array();
        $data['placeId'] = $placeId;
        
        echo view('templates/head');
        echo view('map', $data);
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
