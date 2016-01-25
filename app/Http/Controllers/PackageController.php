<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Alchemy\Zippy\Zippy;
use Illuminate\Http\Request;

/**
 * 代码库打包类
 * @author zengfanwei
 *
 */
class PackageController extends Controller {
    
    private $request;
    
    public function __construct(Request $request) 
    {
        $this->session = $request->session();
    }
    
    public function index()
    {       
        $errorMsg   = $this->session->get('package.error');
        $this->session->forget('package');
        
        return response()->view('package', array(
            'error'      => $errorMsg,
        ));
    }
    
    public function zip()
    {
        $pathList    = request('path_list');
        $packageName = request('package_name');
        
        if(empty($packageName)) $packageName = date("Y-m-d_His");
               
        $pathList = explode("\n", $pathList);
                    
        $myCodeStoragePath = config('qidian.php_code_storage_path');
        
        $newList = array();
        foreach ($pathList as $key => $path)
        {
            if(empty($path)) continue;
            $path = trim(preg_replace('/^\//', '', $path));
            
            
            $source = $myCodeStoragePath .'/' . $path;
                       
            //echo $myCodeStoragePath .'/' . $path;die();
            if(!is_dir($source) && is_file($source))
            {
                $basename = basename($source);                              
                $pathname = dirname($path);
                $dest = PUBLIC_PATH . '/files/' . $packageName . '/' . $pathname ;
                
                if(!is_dir($dest)) mkdir($dest, 0777, true);
                copy($source, $dest . '/' . $basename);
            }
            else if(is_dir($source))
            {
                $pathname = dirname($path);
                $dest = PUBLIC_PATH . '/files/' . $packageName . '/' . $path ;
                if(!is_dir($dest)) mkdir($dest, 0777, true);
                $this->copyDir($source, $dest);
            }
            
            $newList[] = $path;
        }
        
        if(empty($newList))
        {
            $this->session->set("package.error", trans('package.empty_path_list'));
            return redirect('/package');
        }
        
        $zip = Zippy::load();
        $zipPath = PUBLIC_PATH . '/files/' . $packageName . '.zip';
        $zip->create($zipPath, PUBLIC_PATH . '/files/' . $packageName);
        
        $this->downloadFile($zipPath);
        
        unlink($zipPath);
        $this->delDir(PUBLIC_PATH . '/files/' . $packageName);
    }
    
    
    public function copyDir($source, $dest)
    {        
        $dir = opendir($source);
        if (!$dir) 
        {
            return false;
        }
        
        
        while (false !== ($file = readdir($dir))) 
        {
            if (($file!='.') && ($file!='..')) 
            {
                if (is_dir($source. '/' . $file) ) 
                {
                    if (!$this->copyDir($source . '/' . $file, $dest . '/' . $file)) 
                    {
                        return false;
                    }
                } 
                else 
                {
                    if (!copy($source . '/' . $file, $dest . '/' . $file)) 
                    {
                        return false;
                    }
                }
            }
        }
        
        closedir($dir);
        return true;
    }
     
    public function downloadFile($file)
    {
        global $log;
    
        if (!is_file($file)) { die("<b>404 File not found!</b>"); }
    
        $len = filesize($file);
        $filename = basename($file);
        $file_extension = strtolower(substr(strrchr($filename,"."),1));
    
        switch( $file_extension )
        {
            case "apk":
                $ctype="application/vnd.android.package-archive";
                break;
            case 'zip':
            case 'plist':
            case 'ipa':
                $ctype="application/force-download";
                break;
            default:
                $log->logError("Invalid file_extension: " . $file_extension);
                exit;
        }
    
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        //Use the switch-generated Content-Type
        header("Content-Type: $ctype");
        //Force the download
        header("Content-Disposition: attachment; filename=".$filename.";");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".$len);
        $fp = @fopen($file, "r");
        while(!feof($fp))
        {
            echo fgets($fp, 4096);
        }
        fclose($fp);
    }
    
    public function delDir($dir) 
    {    
        //先删除目录下的文件：  
        $dh = opendir($dir);
    
        while ($file = readdir($dh))
        {
    
            if($file != "." && $file != "..") 
            { 
                $fullpath = $dir . "/" . $file;
    
                if(!is_dir($fullpath)) 
                {
                    unlink($fullpath);
    
                } 
                else 
                {    
                   $this->delDir($fullpath);   
                }
    
            }
    
        }   
    
        closedir($dh);    
        //删除当前文件夹：  
        if(rmdir($dir))   return true;
    
        return false;
    
    }
}