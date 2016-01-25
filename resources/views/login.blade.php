@extends('layouts.master')

@section('title', '起点自动发布系统用户登录')


@section('content')


<div class="row">
    <div class="col-sm-4 col-sm-offset-4 form-box">
        @if(!empty($error))
        <div class="alert alert-danger" role="alert">{{$error}}</div>
        @endif
        
    	<div class="form-top">
    		<div class="form-top-left">
    			<h3>用户登录</h3>
    		</div>
    		<div class="form-top-right">
    			<i class="fa fa-key"></i>
    		</div>
        </div>
        <div class="form-bottom">
            <form role="form" action="" method="post" class="login-form">
            	<div class="form-group">
            		<label class="sr-only" for="form-username">用户名</label>
                	<input type="text" name="username" placeholder="用户名..." class="form-username form-control" id="form-username">
                </div>
                <div class="form-group">
                	<label class="sr-only" for="form-password">密码</label>
                	<input type="password" name="password" placeholder="密码..." class="form-password form-control" id="form-password">
                </div>
                <div class="checkbox">
                    <label>
                      <input type="checkbox" value='1' name='remember'> 记住登录状态30天
                    </label>
                </div>
                {!! csrf_field() !!}
                <button type="submit" class="btn btn-primary" style="width: 100%">登录</button>
            </form>
        </div>
    </div>
</div>
                    
               
@endsection