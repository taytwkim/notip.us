<?php

namespace App\Controllers;

use App\Models\PlaceModel;

helper('common');

class Brand extends BaseController
{
    public function index(): void
    {
        echo view('templates/head');
        echo view('brand');
        echo view('templates/foot');
    }
}
