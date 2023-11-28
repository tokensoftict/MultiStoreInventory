<?php

namespace App\Http\Controllers;

use App\Classes\Settings;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    protected $settings;

    public function __construct(Settings $_settings){
        $this->settings = $_settings;
    }

    public function index(){

        return setPageContent('dashboard');

    }
}
