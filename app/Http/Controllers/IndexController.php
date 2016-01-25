<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use qidian\gitphp\AGitRepository;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    private $gitRepostiory;   
    private $gitJsPath;  //git js project path
    private $svnJsPath;  //svn js project path
    private $request;
    private $session; 
    
    /**
     * 构造函数初始化
     */
    public function __construct(Request $request)
    {
        $this->gitJsPath     = config("qidian.git_repository_js_path");
        $this->gitRepostiory = new AGitRepository($this->gitJsPath);
        $this->svnJsPath     = config("qidian.svn_repository_js_path");
        $this->request       = $request;
        $this->session       = $request->session();
    }
    
    /**
     * 预发页面主入口
     * @author Zengfanwei <zengfanwei@yuewen.com>
     */
    public function index()
    {       
        /*初始化*/
        $ciLog      = array();
        $differList = array();  
        $svnlog     = array();
        $svnerror   = array();
        $errorMsg   = $this->session->get('index.error');
        $successMsg = $this->session->get('index.success');
        
        $this->session->forget('index');

        $this->gitRepostiory->fetch("");
        $this->updateCode();

        //获取当前预发分支
        $yufaBranch = $this->gitRepostiory->checkReleaseBranch("release");
        
        if($yufaBranch->name != "noBranch")
        {
            $this->gitRepostiory->checkout($yufaBranch->name);
            $this->gitRepostiory->pull($yufaBranch->name);
            
            //获取提交历史
            $ciLog = $this->gitRepostiory->getBranchLog($yufaBranch->name,10);
            //获取差异列表
            $differList = $this->gitRepostiory->getDiffFiles($yufaBranch->name,"origin/master");
        }

        $log   = $this->gitRepostiory->getLog();
        $error = $this->gitRepostiory->getErrorLog();
        
        $viewData = [
            'log'        => $log,
            'error'      => $error,
            'svnlog'     => $svnlog,
            'svnerror'   => $svnerror,
            'branchName' => $yufaBranch->name,
            'ciLog'      => $ciLog,
            'differList' => $differList,
            'errorMsg'   => $errorMsg,
            'successMsg' => $successMsg
        ];
        
        /*输出页面内容无缓存*/
        return response()->view('index', $viewData)
        ->header("Expires", gmdate("D, d M Y H:i:s", time()))
        ->header("Cache-Control", 'no-store, must-revalidate')
        ->header("Pragma", 'no-cache');
    }
    
    /**
     * 提交预发布
     * @author Zengfanwei <zengfanwei@yuewen.com>
     */
    public function postRelease()
    {      
        require_once  APP_PATH .'/Lib/SvnPeer.php';
        
        $releaseBranch = "release/" . date("Ymd");
        $this->gitRepostiory->checkout($releaseBranch, true);
        $this->gitRepostiory->push("origin", $releaseBranch, $releaseBranch);
             
        $svn = new \Lib\svn\SvnPeer($this->svnJsPath);
        
        $svn->update();
        
        //移动文件到svn js目录
        $this->gitRepostiory->copyfiles("ResourceWeb/src", $this->svnJsPath);
        $this->gitRepostiory->copyfiles("ResourceWeb/style", $this->svnJsPath);
        
        //构建
        $svn->gruntCommand("build-all");
        
        //添加&删除文件
        $svn->checkIn();
        
        //提交文件
        $svn->ci("test Autosystem publish:" . $releaseBranch);
        
        $svnlog = $svn->getLog();
        $svnerror = $svn->getErrorLog();
        
        $error   = trans('index.post_svn_error');
        $success = '';
        
        foreach ($svnlog as $value) 
        {
            if(strpos($value,"Committed") !== false)
            {
                $success = trans('index.post_release_success');
                break;
            }
        }
        
        foreach ($svnerror as $value) 
        {
            if(strpos($value,"*RUN* svn ci -m") !== false)
            {
                $error = trans('index.post_release_faile');
                break;
            }
        }
        
        $this->session->set("index.error", $error);
        $this->session->set("index.success", $success);
        
        return redirect('/');
    }
    
    /**
     * 完成预发
     * @author Zengfanwei <zengfanwei@yuewen.com>
     */    
    public function completeRelease()
    {
        $svnlog     = array();
        $svnerror   = array();
        
        //获取当前预发分支
        $yufaBranch = $this->gitRepostiory->checkReleaseBranch("release");
        
        if($yufaBranch->name != "noBranch")
        {
            $this->updateCode();                    
            //删除本地分支
            $this->gitRepostiory->deleteBranch($yufaBranch->name,true);
        }
        
        $log = $this->gitRepostiory->getLog();
        $error = $this->gitRepostiory->getErrorLog();
        
        $errorMsg = $successMsg =  '';
        
        if(sizeof($error) > 0) $successMsg = trans('index.complete_release_error');
        else  $successMsg = trans('index.complete_release_success');
        
        $this->session->set("index.error", $errorMsg);
        $this->session->set("index.success", $successMsg);
        
        return redirect('/');
    }
    
    /**
     * 删除预发
     * @author zengfanwei <zengfanwei@yuwen.com>
     */
    public function deleteRelease()
    {    
        require_once  APP_PATH .'/Lib/SvnPeer.php';
        
        //获取当前预发分支
        $yufaBranch = $this->gitRepostiory->checkReleaseBranch("release");
        
        if($yufaBranch->name != "noBranch")
        {
            $this->updateCode();
            //删除本地分支
            $this->gitRepostiory->deleteBranch($yufaBranch->name);
        }
        
        $log = $this->gitRepostiory->getLog();
        $error = $this->gitRepostiory->getErrorLog();
        
        $svn = new \Lib\svn\SvnPeer($this->svnJsPath);
        $svn->cleanup();
        
        $this->session->set("index.success", trans('index.delete_release_success'));
        
        return redirect('/');        
    }
    
    /**
     * 更新git代码
     */
    protected function updateCode()
    {
        $this->gitRepostiory->checkout("develop");
        $this->gitRepostiory->pull("develop");
        $this->gitRepostiory->checkout("master");
        $this->gitRepostiory->pull("master");
    }
}