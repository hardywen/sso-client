<?php

namespace Hardywen\SSOClient;


<<<<<<< HEAD
use Hardywen\SSOClient\Middleware\CORSResponse;
use Hardywen\SSOClient\Middleware\SSOAuthenticate;
=======
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
>>>>>>> 63c8fbe112da9cdaf48af1a33c7e3b3d02015128
use Illuminate\Support\ServiceProvider;

class SSOServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->package('hardywen/sso-client');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $router = $this->app['router'];

<<<<<<< HEAD
        $router->middleware('auth', SSOAuthenticate::class);
        $router->middleware('cors', CORSResponse::class);

        $router->get('sso/login', ['as'=>'sso.login','uses'=>'Hardywen\SSO\SSOController@login']);
        $router->get('sso/logout', ['as'=>'sso.logout','uses'=>'Hardywen\SSO\SSOController@logout']);
        $router->get('sso/clear', ['as'=>'sso.clear','middleware'=>'cors','uses'=>'Hardywen\SSO\SSOController@clear']);

=======
        $config = $this->app->config->get('sso-client::config');

        $router->get('sso/login', function () use ($request,$config) {

            $sign = $request->get('sign');
            $time = $request->get('time');
            $ssoToken = $request->get('sso_token');

            $encryptKey = $config['sso_token_encrypt_key'];

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
        });

        $router->get('sso/clear', function () {
            Auth::logout();

            return Response::make('', 204);
        });

        $router->filter('auth', function() use ($request,$config){
            if (Auth::guest()) {

                if ($request->ajax()) {
                    return Response::make('Unauthorized.', 401);
                } else {
                    return Redirect::guest($config['sso.sso_login_url'] . '?redirect_url=' . urlencode(url('sso/login')));
                }
            }
        });
>>>>>>> 63c8fbe112da9cdaf48af1a33c7e3b3d02015128
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel()
    {
        $class = '\\' . ltrim($this->app['config']->get('auth.model'), '\\');

        return new $class;
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('sso-client');
    }
}