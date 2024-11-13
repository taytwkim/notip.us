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
        echo view('main');
        echo view('templates/foot');
    }

    public function search(): string
    {
        return view('search-test');
    }
}
