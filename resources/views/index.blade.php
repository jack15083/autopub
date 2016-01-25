@extends('layouts.master')

@section('title', '起点自动发布系统')


@section('content')
<script type="text/javascript">
	function showCommitLog() {
		if($("#commit-log").css("display") == "none"){
			$("#commit-log").show();
			$("#diff-log").hide();
			$("#yufa-log").hide();
			$("#commit-btn").html("关闭日志");
		}
		else{
			$("#commit-log").hide();
			$("#diff-log").show();
			$("#commit-btn").html("查看日志");
		}
	}
	function showBranchLog() {
		if($("#yufa-log").css("display") == "none"){
			$("#yufa-log").show();
			$("#diff-log").hide();
			$("#commit-log").hide();
			$("#yufa-btn").html("关闭分支提交历史");
		}
		else{
			$("#yufa-log").hide();
			$("#diff-log").show();
			$("#yufa-btn").html("查看分支提交历史");
		}
	}
</script>
<div class="container-fluid">
<div class="row">
    <div class="col-md-8">
        @if($errorMsg)
        <div class="alert alert-danger" role="alert">{{$errorMsg}}</div>
        @endif
        
        @if($successMsg)
        <div class="alert alert-success" role="alert">{{$successMsg}}</div>
        @endif
        <form id="w0" action="/release" method="post" class="form-inline">
            {!! csrf_field() !!}
            <div class="form-group">
                <button type="submit" class="btn btn-primary" style="margin-right:10px;">提交预发</button>        
                <a href="/complete" class="btn btn-primary" style="margin-right:10px;">完成预发</a>
                <a href="/delete" class="btn btn-primary" style="margin-right:10px;">删除预发</a>
                <button id="commit-btn" type="button" class="btn btn-primary" style="margin-right:10px;" onclick="showCommitLog()">查看日志</button>
                <button id="yufa-btn" type="button" class="btn btn-primary" style="margin-right:10px;" onclick="showBranchLog()">查看分支提交历史</button>
            </div>
        </form>
        
        <div style="margin:10px 0;">
        	当前预发分支：{{$branchName}}
        </div>
        
        <div id="diff-log" style="">
        	<div>
        	当前分支与线上环境的差异文件列表：
        		<lu>
        		@foreach ($differList as $value)
        		<li>
        		{{$value}}
        		</li>
        		@endforeach
        		</lu>
        	</div>
        </div>
        <div id="yufa-log" style="display:none;">
        	<div>
        	当前分支提交历史：
        		<lu>
        		@foreach ($ciLog as $value)
        		<li>
        		{{$value}}
        		</li>
        		@endforeach
        		</lu>
        	</div>
        </div>
        <div id="commit-log" style="display:none;">
        	<div>
        	错误日志：
        		<lu>
        		@foreach ($error as $value) 
        		<li>
        		{{$value}}
        		</li>
        		@endforeach
        		
        		@foreach ($svnerror as $value)
        		<li>
        		{{$value}}
        		</li>
        		@endforeach
        		</lu>
        	</div>
        	<p>
        	<div>
        	操作日志：
        		<lu>
        		@foreach ($log as $value)
        		<li>
        		{{$value}}
        		</li>
        		@endforeach
        		
        		@foreach ($svnlog as $value)
        		<li>
        		{{$value}}
        		</li>
        		@endforeach
        		</lu>
        	</div>
        </div>
    </div>
    
    <div id="help" class="col-md-4">
    	<p>提交预发：</p>
    	<p>生成预发分支，并自动发布到预发环境。<br>
    	生成的预发分支按照日期命名。<br>
    	如已有预发分支则不再生成，而更新此分支并预发。<br>
    	<span style="color:red;font-weight:bold;">请不要重复提交，否则会发布多次</span>
    	</p>
    	<br>
    	<p>完成预发：</p>
    	<p>把预发分支的内容汇入develop与master，并删除预发分支。</p>
    	<br>
    	<p>删除预发：</p>
    	<p>删除预发分支，并把预发环境回滚至线上版本。<br>
    	此动作同时会修复发布错误
    	</p>
    
    </div>
</div>
</div>
@endsection