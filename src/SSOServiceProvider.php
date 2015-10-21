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
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->package('hardywen/sso-client','sso-client',realpath(__DIR__.'/../'));


        $router = $this->app['router'];
        $request = $this->app['request'];
        $config = $this->app['config']->get('sso-client::config');

        $router->get('sso/login',['uses'=> 'Hardywen\SSOClient\SSOController@login']);
        $router->get('sso/clear',['uses'=> 'Hardywen\SSOClient\SSOController@clear']);

        $router->filter('auth', function() use ($request,$config){
            if (Auth::guest()) {

                if ($request->ajax()) {
                    return Response::make('Unauthorized.', 401);
                } else {
                    return Redirect::guest($config['sso_login_url'] . '?redirect_url=' . urlencode(url('sso/login')));
                }
            }
        });
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