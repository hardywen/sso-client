<?php
/**
 * Created by PhpStorm.
 * User: hardywen
 * Date: 15/10/21
 * Time: 下午6:45
 */

namespace Hardywen\SSOClient;


use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class SSOController extends Controller
{
    public function login()
    {
        $config = Config::get('sso-client::config');

        $sign = Input::get('sign');
        $time = Input::get('time');
        $ssoToken = Input::get('sso_token');

        $encryptKey = $config['sso_token_encrypt_key'];

        if ($sign && $time && $ssoToken) {
            if ($sign == md5($encryptKey . $time . $ssoToken)) {
                $merchant = $this->createModel();

                $merchant = $merchant->where('sso_token', $ssoToken)->where('sso_token_created_at', '>=',
                    Carbon::now()->subMinute())->first();

                if ($merchant) {
                    Auth::login($merchant);

                    return Redirect::intended();
                }
            }
        }
    }


    public function clear()
    {

        Auth::logout();

        return Response::make('', 204);
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel()
    {
        $class = '\\' . ltrim(Config::get('auth.model'), '\\');

        return new $class;
    }
}