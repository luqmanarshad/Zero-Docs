<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Don't forget to import the Request facade
use App\Http\Controllers\Saas\FrontendController;
use Illuminate\Support\Facades\Artisan;

class CommonController extends Controller
{
    public function index(Request $request) // Add Request $request as an argument
    {
        return view('welcome');
//        Artisan::call('config:clear');
//        Artisan::call('cache:clear');
//        Artisan::call('view:clear');
//        Artisan::call('route:clear');
//        Artisan::call('optimize:clear');
//        exec('composer dump-autoload');
//        dd('ads');
        // Check if the request is coming from 'app.trashytenant.com'
        if ($request->getHost() === config('app.backend')) {
            return redirect()->to('https://' . config('app.frontend'));
        }

        if (isAddonInstalled('PROTYSAAS') > 1) {
            $frontendController = new FrontendController;
            return $frontendController->index();
        }

        return redirect()->route('login');
    }
}
