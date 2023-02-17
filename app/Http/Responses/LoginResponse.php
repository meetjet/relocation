<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;

class LoginResponse implements LoginResponseContract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        // Default
//        return $request->wantsJson()
//            ? response()->json(['two_factor' => false])
//            : redirect()->intended(Fortify::redirects('login'));
        if ($request->wantsJson()) {
            return response()->json(['two_factor' => false]);
        }

        return $request->session()->has('return_url')
            ? redirect($request->session()->get('return_url'))
            : redirect()->intended(Fortify::redirects('login'));
    }
}
