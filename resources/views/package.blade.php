@extends('layouts.master')

@section('title', '起点PHP代码打包')

@section('content')

<div class="row">
    <div class="col-sm-8 col-sm-offset-2 form-box">
        @if(!empty($error))
        <div class="alert alert-danger" role="alert">{{$error}}</div>
        @endif
        
    	<form class="form-horizontal" action="/zip" method="post">
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">文件路径</label>
            <div class="col-sm-10">
              <textarea class="form-control" rows="15" name="path_list"></textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">包名</label>
            <div class="col-sm-10">
              <input class="form-control" id="inputPassword3" placeholder="" name="package_name">
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              {!! csrf_field() !!}
              <button type="submit" class="btn btn-default">提交</button>
            </div>
          </div>
    </form>
    </div>
</div>

@endsection