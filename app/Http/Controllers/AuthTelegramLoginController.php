<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AuthTelegramLoginController extends Controller
{
    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function show(Request $request): Application|Factory|View
    {
        if ($request->has('return_url')) {
            $request->session()->flash('return_url', $request->get('return_url'));
        }

        return view('auth.telegram-login');
    }
}
