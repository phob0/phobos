<?php

use Illuminate\Foundation\Auth\AuthenticatesUsers;

trait PhobosLogin
{
    protected $oauthClientName = 'App Password Grant Client';

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'apiLogout']);
    }

    public function apiLogin(Request $request)
    {
        $this->validateLogin($request);

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($authResponse = $this->attemptApiLogin($request)) {
            $this->clearLoginAttempts($request);

            Cookie::queue('app_auth', $authResponse['access_token'], $authResponse['expires_in'] / 60);
            Cookie::queue('app_reauth', $authResponse['refresh_token'], $authResponse['expires_in'] / 60);

            return $this->apiSuccess($authResponse);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function attemptApiLogin(Request $request)
    {
        $http = new Client;

        $apiClient = \Cache::tags(['auth'])->remember('oauth_password_client', 86400, function() {
            return (array)\DB::table('oauth_clients')
                ->whereName($this->oauthClientName)
                ->first(['id', 'secret']);
        });

        try {
            $response = $http->post(url('/oauth/token'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => $apiClient['id'],
                    'client_secret' => $apiClient['secret'],
                    'username' => $request->get('email'),
                    'password' => $request->get('password'),
                    'scope' => '',
                ],
            ]);
        } catch (ClientException $ex) {
            return false;
        }

        return json_decode((string)$response->getBody(), true);
    }

    public function apiLogout(Request $request)
    {
        \DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $request->user()->token()->id)
            ->update([
                'revoked' => true,
            ]);
        $request->user()->token()->revoke();

        Cookie::queue(Cookie::forget('app_auth'));
        Cookie::queue(Cookie::forget('app_reauth'));

        return $this->apiSuccess();
    }
}
