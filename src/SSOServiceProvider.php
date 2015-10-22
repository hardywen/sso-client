<?php

namespace Hardywen\SSOClient;


use Hardywen\SSOClient\Middleware\CORSResponse;
use Hardywen\SSOClient\Middleware\SSOAuthenticate;
use Illuminate\Support\ServiceProvider;

class SSOServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $config = realpath(__DIR__ . '/../config/sso.php');

        $this->mergeConfigFrom($config, 'sso');

        $this->publishes([
            $config => config_path('sso.php')
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $router = $this->app['router'];

        $router->middleware('auth', SSOAuthenticate::class);
        $router->middleware('cors', CORSResponse::class);

        $router->get('sso/login', ['as'=>'sso.login','uses'=>'Hardywen\SSOClient\SSOController@login']);
        $router->get('sso/logout', ['as'=>'sso.logout','uses'=>'Hardywen\SSOClient\SSOController@logout']);
        $router->get('sso/clear', ['as'=>'sso.clear','middleware'=>'cors','uses'=>'Hardywen\SSOClient\SSOController@clear']);

    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel()
    {
        $class = '\\' . ltrim($this->app['config']['auth.model'], '\\');

        return new $class;
    }
}