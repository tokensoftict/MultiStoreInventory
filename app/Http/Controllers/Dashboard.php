<?php

namespace App\Http\Controllers;

use App\Classes\Settings;
use App\Classes\Dashboard as DashboardService;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    protected $settings;
    protected $dashboard;

    public function __construct(Settings $_settings, DashboardService $dashboard){
        $this->settings = $_settings;
        $this->dashboard = $dashboard;
    }

    public function index(){
        $data = [
            'dashboard' => $this->dashboard
        ];
        return setPageContent('dashboard', $data);
    }
}
