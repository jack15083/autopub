<?php
namespace Lib\svn;

/**
 *
 * This class for execute the external program of svn
 *
 * @auth Seven Yang <qineer@gmail.com>
 *
 */
class SvnPeer
{

    protected $_path;

    protected $_logs = array();


    protected $_errorlogs = array();


    protected $_fileslogs=array();

    public function __construct($path = null)
    {
        $this->setPath($path);
    }

    public function getPath()
    {
        return $this->_path;
    }

    public function getLog(){
        return $this->_logs;
    }

    public function getErrorLog(){
        return $this->_errorlogs;
    }

    public function setPath($path)
    {
        if (!($realPath = realpath($path))) {
            throw new \Exception("The specified path does not exist: ".$path);
        }
        $this->_path = $realPath;
    }

    public function update()
    {
        return $this->run('update');
    }

    public function cleanup()
    {
        return $this->run('cleanup');
    }

    public function ci($msg)
    {
        return $this->run('ci -m "'. $msg .'"');
    }
    public function getModifyLogs($publishDate,$nowDate)
    {

        $returnMlogFiles=$this->run("log -v -r {".$publishDate."}:{".$nowDate."} -q | awk '{if ( $1 == 'M') { print $2}}");
        return  $returnMlogFiles;
    }
    /**
     ** 根据选择站点发布时间获取当前文件提交列表(不显示提交的日志)
     */
    public function getfileslog($publishDate,$nowDate)
    {
        //svn log -v -r {2015-9-20}:{2015-9-29}
        //$retMesage=$this->run("log -v -r  {2015-9-25}:{2015-9-25}");
        $retMesage=$this->run("log -v -r {".$publishDate."}:{".$nowDate."} -q");
        $this->_fileslogs=array_merge($this->_fileslogs,explode("------------------------------------------------------------------------",$retMesage));
        return   $this->_fileslogs;
    }

    /*
     public function processSvnFilesLog($publishDate,$nowDate)
     {
     $filesLog=self::getfileslog($publishDate,$nowDate);
     $newfileslog=array();
     if(count($filesLog)>0)
     {
     for($j=0;$j<count($filesLog)-1;$j++ )
     {
     if($filesLog[$j]!="")
     {
     $newfileslog=array_merge($newfileslog,explode("Changed paths:",trim($filesLog[$j])));
     }
     }
     }
     return $newfileslog;
     }
     */

    public function  mkDirs($dir)
    {
        if(!is_dir($dir)){ //判断当前目录是否存在
            if(!self::mkDirs(dirname($dir))){
                return false;
            }
            if(!mkdir($dir,0777)){
                return false;
            }
        }
        return true;
    }


    public function run($command)
    {
        $descriptor = array(
                1 => array('pipe', 'w'),
                2 => array('pipe', 'w'),
        );
        $pipes = array();

        $resource = proc_open("svn ".$command,$descriptor,$pipes,$this->getPath());
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        foreach($pipes as $pipe) {
            fclose($pipe);
        }
        //Yii::warning("proc_open:"."svn ".$command);
        $this->_logs = array_merge($this->_logs,explode("\n","*RUN* svn ".$command.":\n".$stdout));
        if($stderr) $this->_errorlogs = array_merge($this->_errorlogs,explode("\n","*RUN* svn ".$command.":\n".$stderr));
        if (trim(proc_close($resource)) && $stderr) {
            //Yii::error($stderr);
        }
        return trim($stdout.$stderr);
    }

    public function gruntCommand($command)
    {
        $descriptor = array(
                1 => array('pipe', 'w'),
                2 => array('pipe', 'w'),
        );
        $pipes = array();

        $resource = proc_open("grunt ".$command,$descriptor,$pipes,$this->getPath());
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        foreach($pipes as $pipe) {
            fclose($pipe);
        }
        //Yii::warning("proc_open:"."grunt ".$command);
        $this->_logs = array_merge($this->_logs,explode("\n","*RUN* grunt ".$command.":\n".$stdout));
        if($stderr) $this->_errorlogs = array_merge($this->_errorlogs,explode("\n","*RUN* grunt ".$command.":\n".$stderr));
        if (trim(proc_close($resource)) && $stderr) {
            //Yii::error($stderr);
        }
        return trim($stdout.$stderr);
    }

    public function checkIn()
    {
        //添加文件
        $this->run("st | awk '{if ( $1 == \"?\") { print $2}}' | xargs svn add");


        $this->run("st | awk '{if ( $1 == \"!\") { print $2}}' | xargs svn delete");

        return 0;
    }

    /**
     *压缩文件夹格式为zip
     **/
    public function  compressionPackageFiles($zipFileName, $floderPathName)
    {
        // 参数 $floderPathName="/home/wwwroot/xahot"
        $this->run("zip –q –r ".$zipFileName.".zip ".$floderPathName."");

    }

    /**
     ** 复制文件
     **/
    public function copyfiles($path1,$path2)
    {
        $descriptor = array(
                1 => array('pipe', 'w'),
                2 => array('pipe', 'w'),
        );
        $pipes = array();

        $resource = proc_open("cp -f -R ".$path1." ".$path2,$descriptor,$pipes,$this->getPath());
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        foreach($pipes as $pipe) {
            fclose($pipe);
        }
        //Yii::warning("proc_open: cp -f ".$path1." ".$path2);
        $this->_logs = array_merge($this->_logs,explode("\n","*RUN* cp:\n".$stdout));
        if($stderr) $this->_errorlogs = array_merge($this->_errorlogs,explode("\n","*RUN* cp:\n".$stderr));
        if (trim(proc_close($resource)) && $stderr) {
            //Yii::error($stderr);
        }
        return trim($stdout.$stderr);
    }

}