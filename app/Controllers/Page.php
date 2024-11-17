<?php

namespace App\Controllers;

class Page extends BaseController
{
    public function index(): string
    {
        echo view('templates/head');
        echo view('map-test');
        echo view('templates/foot');
    }

    public function map(): void
    {
        echo view('templates/head');
        echo view('map');
        echo view('templates/foot');
    }

    public function list(): void
    {
        echo view('templates/head');
        echo view('list');
        echo view('templates/foot');
    }
}
