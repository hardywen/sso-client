<?php

namespace Hardywen\SSOClient;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
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
        $request = $this->app['request'];

        $router = $this->app['router'];

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