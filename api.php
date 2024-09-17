<?php
//请不要随意更改该文档
error_reporting(0);
define("ONINDEX",1);
require_once "conf.php";

function output_msg($erron,$msg,$data,$em = 0){
    if($em==1){
        die(json_encode(array("erron"=>$erron,"msg"=>$msg,"data"=>$data)));
    } else {
        die('<div class="weui-panel__bd">
<div class="weui-flex__item">
    <div class="placeholder">'.$msg.'</div>
</div>
</div>');
    }
}

$type = $_GET['t'];
if(!$type){
    output_msg(-5,"后端异常 请联系网站管理员","",$em);
}
$em = $_GET['em'];

if($type == "list"){
    $path = $_POST['path'];
    if(!$path){
        $path = "/";
    }
    $path = urldecode($path);
    $url = 'http://110.242.69.43/api/list?order=time&dir='.urlencode($path);
    $data = get($url,array('user-Agent: netdisk','Cookie: BDUSS='.BDUSS));
      if(!$data){
        output_msg(-1,"百度网关错误","",$em);
    }
    $d = json_decode($data);
    if(!$d){
        output_msg(-2,"数据获取异常","",$em);
    } 
    $errno = $d->errno;
    if($errno != 0){
        output_msg($errno,"百度返回错误 错误号 $errno","",$em);
    }
    
    echo '<div class="page__bd page__bd_spacing">
<div class="weui-flex">
    <div class="weui-flex__item">
        <div class="placeholder">
         <a href="javascript:openDir(\'%2F\');" role="button" class="weui-btn weui-btn_mini weui-btn_primary weui-wa-hotarea" title="全部文件">全部文件</a>
         </div>
    </div>'; 
    if(!empty($path) and $path!='/'){
        $dirs = explode('/',$path);
        $dir_path = '';
        $dir_paths = [null,];
        $lastdir = $dirs[count($dirs)-1];
        for($i=1;$i<count($dirs);$i++){
            $dir_path.='/';
            $dir_path.=$dirs[$i];
            $dir_paths[$i] = $dir_path;
            if(mb_strlen($dirs[$i])>5){
            $dirname = mb_substr($dirs[$i],0,5).'...';  
            } else {
                $dirname = $dirs[$i];
            }
            echo '<div class="weui-flex__item">
        <div class="placeholder">
         <a href="javascript:openDir(\''.urlencode($dir_path).'\');" role="button" class="weui-btn weui-btn_mini weui-btn_default weui-wa-hotarea" title="'.$dirs[$i].'">'.$dirname.'</a>
         </div>
         </div>
            ';
        }
    }
    echo '</div>    
    <div class="weui-cells" id="filelist">
        <div class="weui-panel weui-panel_access">
            <div class="weui-panel__hd">文件列表</div> 
        </div> 
    </div> 
</div> ';
   for($i=0;$i<count($d->list);$i++){
        $fname = $d->list[$i]->server_filename;
        if(mb_strlen($fname)>32){
            $ffname = mb_substr($fname,0,40).'...';
        } else {
            $ffname = $fname;
        }
        if(!empty($fname)){
            $isdir = $d->list[$i]->isdir;
            $size = $d->list[$i]->size;
            $fsid = $d->list[$i]->fs_id;
            $ctime = $d->list[$i]->local_ctime;
            $path_info = $d->list[$i]->path;
            $pcs = $d->list[$i]->dlink;
            $category = $d->list[$i]->category;
            if($category=='1'){
            $filetype = '视频'; 
            } elseif($category=='2'){
            $filetype = '音乐';    
            } elseif($category=='3'){
            $filetype = '图片';       
            } elseif($category=='4'){
            $filetype = '文档';       
            } elseif($category=='5'){
            $filetype = '应用';       
            } elseif($category=='6'){
            $filetype = '其他';       
            } elseif($category=='7'){
            $filetype = '种子';       
            } else {
            $filetype = '未知类型';       
            }
            $ctime = date('Y年m月d日',$ctime);
            if($isdir==0){
            $uio = formatSize($size);
            } else {
            $uio = '--'; 
            };
            if($isdir==0){
            $nulldir = 0;    
            $cm+=1;
            echo '<div class="weui-panel__bd">
                <a aria-labelledby="js_p1m1_bd" href="javascript:openMeue(\''.$cm.'\');" class="weui-media-box weui-media-box_appmsg" title="'.$fname.'">
                <div role="option" class="weui-media-box_text">
                                    <strong class="weui-media-box__title">'.$ffname.'</strong>
                                    <p class="weui-media-box__desc">'.$filetype.'  文件大小:'.$uio.' 创建时间: '.$ctime.' 点击打开菜单</p>
                </div></a>
                <div id= "Info_'.$cm.'" hidden>
                <div id="File_Type_'.$cm.'" value="'.$filetype.'"></div>
                <div id="File_Fsid_'.$cm.'" value="'.$fsid.'"></div>
                <div id="File_Size_'.$cm.'" value="'.$size.'"></div>
                <div id="File_Path_'.$cm.'" value="'.$path_info.'"></div>
                <div id="File_Filename_'.$cm.'" value="'.$fname.'"></div>
                </div>
            </div>';
            }
            if($isdir==1){
            $cm+=1;
            $nulldir = 0; 
            echo '<div class="weui-panel__bd">
                <a aria-labelledby="js_p1m1_bd" href="javascript:openDir(\''.urlencode($path_info).'\');" class="weui-media-box weui-media-box_appmsg" title="'.$fname.'">
                <div role="option" class="weui-media-box_text">
                                    <strong class="weui-media-box__title">'.$ffname.'</strong>
                                    <p class="weui-media-box__desc">文件夹 文件大小:'.$uio.' 创建时间: '.$ctime.' 点击打开文件夹</p>
                                </div></a>
                <div id= "Info_'.$cm.'" hidden>
                <div id="File_Type_'.$cm.'" value="'.$filetype.'"></div>
                <div id="File_Path_'.$cm.'" value="'.$path_info.'"></div>
                <div id="File_Filename_'.$cm.'" value="'.$fname.'"></div>
                </div>
            </div>';
            }
        }
    }
    if($nulldir==1){
        echo '<div class="weui-panel__bd">
            <div class="weui-flex__item">
                <div class="placeholder">什么也没有呢...检查下文件夹是否正确吧~</div>
            </div>
        </div>';
    }
} elseif ($type==".baidu.com"){
    $fid = $_GET['f'];
    if(!empty($fid)){
        header("Content-Type: text/json;charset=utf-8");
        $ua = $_SERVER['HTTP_USER_AGENT'];
        if(ACLINK==""){
            $url = 'http://pan.baidu.com/api/gettemplatevariable?clienttype=web&app_id=250528&web=1&fields=[%22sign1%22,%22sign2%22,%22sign3%22,%22timestamp%22,%22uk%22,%22is_vip%22,%22is_svip%22]';
            $data = get($url,array("User-Agent: netdisk","Cookie: BDUSS=".BDUSS));
            $j = json_decode($data);
            $sign1 = $j->result->sign1;
            $sign3 = $j->result->sign3;
            $uk = $j->result->uk;
            $vip = "0";
            if($j->result->is_vip){
                $vip = "1";
            } elseif($j->result->is_svip){
                $vip = "2";
            }
            $sign = rc4($sign3,$sign1);
            if($sign == "" or $sign == null){
                die(json_encode(array("errno" => '-8', "msg" => "获取下载地址失败"), JSON_UNESCAPED_UNICODE));
            }
            $timestamp = $j->result->timestamp;
            $url = 'http://pan.baidu.com/api/download?clienttype=8&app_id=250528&web=1&fidlist=['.$fid.']&type=dlink&sign='.urlencode($sign).'&timestamp='.$timestamp;
            $data = get($url,array("User-Agent: ".$ua,"Cookie: BDUSS=".BDUSS));
            $j = json_decode($data);
            if (!$j or !$j->dlink[0]->dlink) {
                die(json_encode(array("errno" => '-8', "msg" => "获取下载地址失败"), JSON_UNESCAPED_UNICODE));
            }
            $dl2 = $j->dlink[0]->dlink;
            $d = parse_url($dl2);
            $parm = geturl($d["query"]);
            $url = "http://pan.baidu.com/api/report/user?action=sapi_auth&timestamp=".$time."&clienttype=8&app_id=250528&web=1";
            $data = get($url,array("User-Agent: netdisk","Cookie: BDUSS=".$bduss));
            $j = json_decode($data);
            $sk = $j->uinfo;
            $fid = GetSubStr($parm["fid"]."{}","-","{}");
            $md5 = str_ireplace("/file/","",$d["path"]);
            $url = 'http://pcs.baidu.com/rest/2.0/pcs/file?method=locatedownload';
            $pdata = 'tls=1&app_id=250528&es=1&esl=1&ver=4.0&dtype=3&err_ver=1.0&ehps=0&open_pflag=0&clienttype=8&channel=weixin&version=7.42.0.5&vip='.$vip.'&wp_retry_num=2&tdt=1&gsl=0&gtchannel=0&gtrate=0&gsl=0&gtchannel=0&gtrate=0&'.makerand($sk,$uk,$time).'&path='.$md5.'&'.$d["query"];
            if($vip=="2"){
                $pdata = 'tls=1&app_id=250528&es=1&esl=1&ver=4.0&dtype=3&err_ver=1.0&ehps=0&orgin=dlna&open_pflag=0&clienttype=8&channel=0&version=7.42.0.5&vip='.$vip.'&wp_retry_num=2&tdt=1&gsl=0&gtchannel=0&gtrate=0&gsl=0&gtchannel=0&gtrate=0&'.makerand($sk,$uk,$time).'&path='.$md5.'&'.$d["query"];
            
            }
            $data = post($url,$pdata,array("User-Agent: ".$ua,"Cookie: BDUSS=".BDUSS));
            $j = json_decode($data);
            if (!$j or !$j->urls[0]->url) {
                die(json_encode(array("errno" => '-116', "msg" => "获取下载地址失败"), JSON_UNESCAPED_UNICODE));
            }
            $dlink = $j->urls[0]->url;
        } else {
            $url = ACLINK;
            if($url != "" && $url != null){
                $data = get($url,array(""));
                $j = json_decode($data);
                $code = $j->code;
                if($j->code == "0"){
                    $pdata = "bduss=".BDUSS."&fid=".$fid."&ua=".base64_encode($ua);
                    $data = post($url,$pdata,array("User-Agent: KinhWeb/".VERSION));
                    $j = json_decode($data);
                    if (!$j or !$j->dlink) {
                        die(json_encode(array("errno" => '-116', "msg" => "获取下载地址失败"), JSON_UNESCAPED_UNICODE));
                    }
                    $dlink = $j->dlink;
                }
            }    
        }
        if(!$dlink){
            die(json_encode(array("errno" => '-8', "msg" => "获取下载地址失败"), JSON_UNESCAPED_UNICODE));
        }
        echo(json_encode(array("errno" => -302, "msg" => "302 moved temporarily"), JSON_UNESCAPED_UNICODE));
        header('HTTP/1.1 302 moved temporarily');
        header("Location:".$dlink);
        die();
    }    
} else {
    output_msg(500,"异常请求","",$em);
}