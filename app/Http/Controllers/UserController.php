<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthController;

class UserController extends Controller 
{
    private $userModel;
    private $viewData;
    
    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
        $this->viewData  = array();
    }
    
    
    /**
     * 登录页面
     */
    public function login(Request $request)
    {
        $viewData = [];
        
        if($request->method() == 'POST')
        {
           $auth = new AuthController();
           
           $result = $auth->loginVerify($request->all());
           
           if($result === true) 
           {
               return redirect('/');
           }
           
           $this->viewData['error'] = $result;
        }
        
        return view('login', $this->viewData);
    }
}