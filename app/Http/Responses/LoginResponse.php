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
        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : back();
    }
}
