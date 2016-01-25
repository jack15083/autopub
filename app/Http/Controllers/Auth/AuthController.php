<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Validator;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @param  bool   $isCreate is 
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data, $isCreate = false)
    {        
        if($isCreate)
        {
            $rules['email']    = 'required|max:20|unique:users';
            $rules['username'] = 'required|email|max:100|unique:users';
            $rules['password'] = 'required|confirmed|min:6';
        }
        else 
        {
            $rules = [
                'username' => 'required|max:20',
                'password' => 'required|min:6',
            ];
        }
        
        return Validator::make($data, $rules ,trans('validation.custom'));
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
    
    public function checkLogin()
    {
        return false;
    }
    
    public function loginVerify($data)
    {
        $validator = $this->validator($data);
        
        if($validator->fails())
        {
            $message = $validator->errors();
            
            return $message->first();
        }
        
        
        
    }
}
