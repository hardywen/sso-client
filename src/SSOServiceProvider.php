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
        $this->package('hardywen/sso-client', 'sso-client', realpath(__DIR__ . '/../'));


        $router = $this->app['router'];
        $request = $this->app['request'];
        $config = $this->app['config']->get('sso-client::config');

        $router->filter('auth', function () use ($request, $config) {
            if (Auth::guest()) {
                if ($request->ajax()) {
                    return Response::make('Unauthorized.', 401);
                } else {
                    return Redirect::guest($config['sso_login_url'] . '?redirect_url=' . urlencode(url('sso/login')));
                }
            }
        });

        $router->filter('cors', function () use ($request) {
            header("Access-Control-Allow-Origin: " . $request->header('Origin'));
            header('Access-Control-Allow-Credentials: true');

            if ($request->getMethod() == "OPTIONS") {
                $headers = [
                    'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
                    'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type, X-Auth-Token, Origin, Authorization'
                ];

                return Response::make('You are connected to the API', 200, $headers);
            }
        });

        $router->get('sso/login', ['as' => 'sso.login', 'uses' => 'Hardywen\SSOClient\SSOController@login']);
        $router->get('sso/logout', ['as' => 'sso.logout', 'uses' => 'Hardywen\SSOClient\SSOController@logout']);
        $router->get('sso/clear',
            ['as' => 'sso.clear', 'before' => 'cors', 'uses' => 'Hardywen\SSOClient\SSOController@clear']);

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