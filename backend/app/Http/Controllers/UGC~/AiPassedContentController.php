<?php

namespace App\Http\Controllers\UGC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Breadcrumbs;

class AiPassedContentController extends Controller
{
    public function index()
    {
        Breadcrumbs::add('Dashboard', route('dashboard'));
        Breadcrumbs::add('ContentManagement');
        Breadcrumbs::add('AiPassedContent');  
        return view('ugc.ai_passed.index');
    }
}
