<?php

namespace Hardywen\SSOClient;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class SSOController extends Controller
{

    public function login(Request $request)
    {
        $sign = $request->get('sign');
        $time = $request->get('time');
        $ssoToken = $request->get('sso_token');

        $encryptKey = config('sso.sso_token_encrypt_key');

        if ($sign && $time && $ssoToken) {
            if ($sign == md5($encryptKey . $time . $ssoToken)) {
                $merchant = $this->createModel();

                $merchant = $merchant->where('sso_token', $ssoToken)->where('sso_token_created_at', '>=',
                    Carbon::now()->subMinute())->first();

                if ($merchant) {
                    Auth::login($merchant);

                    return redirect()->intended();
                }
            }
        }
    }

    public function logout()
    {
        $url = config('sso.sso_logout_url');
        return redirect($url.'&redirect_url='. urlencode(route('sso.login')));
    }

    public function clear()
    {
        Auth::logout();

        return response('');

    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel()
    {
        $class = '\\' . ltrim(config('auth.model'), '\\');

        return new $class;
    }

}