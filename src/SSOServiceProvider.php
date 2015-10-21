<?php

namespace Hardywen\SSOClient;


use Carbon\Carbon;
use Hardywen\SSOClient\Middleware\SSOAuthenticate;
use Illuminate\Support\Facades\Auth;
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
        $request = $this->app['request'];

        $router = $this->app['router'];

        $router->get('sso/login', function () use ($request) {

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
        });

        $router->get('sso/clear', function () {
            Auth::logout();

            return response('', 204);
        });

        $router->middleware('auth', SSOAuthenticate::class);
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