<?php
define("ONINDEX",1);
require_once "conf.php";
$nulldir = 1;
$fid = $_GET['f'];
if(!empty($fid)){
    header("Content-Type: text/json;charset=utf-8");
    $ua = $_SERVER['HTTP_USER_AGENT'];
    if(ACST!=1){
        $url = 'https://pan.baidu.com/api/gettemplatevariable?clienttype=web&app_id=250528&web=1&fields=[%22sign1%22,%22sign2%22,%22sign3%22,%22timestamp%22]';
        $data = get($url,array("User-Agent: netdisk","Cookie: BDUSS=".BDUSS));
        $j = json_decode($data);
        $sign1 = $j->result->sign1;
        $sign3 = $j->result->sign3;
        $sign = rc4($sign3,$sign1);
        if($sign == "" or $sign == null){
            die(json_encode(array("errno" => '-8', "msg" => "获取下载地址失败"), JSON_UNESCAPED_UNICODE));
        }
        $timestamp = $j->result->timestamp;
        $url = 'https://pan.baidu.com/api/download?clienttype=web&app_id=250528&web=1&fidlist=['.$fid.']&type=dlink&sign='.urlencode($sign).'&timestamp='.$timestamp;
        $data = get($url,array("User-Agent: ".$ua,"Cookie: BDUSS=".BDUSS));
        $j = json_decode($data);
        if (!$j or !$j->dlink[0]->dlink) {
    	    die(json_encode(array("errno" => '-8', "msg" => "获取下载地址失败"), JSON_UNESCAPED_UNICODE));
        }
        $dl2 = $j->dlink[0]->dlink;
        $d = parse_url($dl2);
        $url = 'http://pan.baidu.com/rest/2.0/xpan/file?method=locatedownload&tls=1&app_id=250528&es=1&esl=1&ver=4.0&dtype=3&err_ver=1.0&ehps=0&open_pflag=0&clienttype=web&channel=0&version=7.35.1.2&wp_retry_num=2&tdt=1&gsl=0&gtchannel=0&gtrate=0&path='.str_ireplace("/file/","",$d["path"]).'&'.$d[query];
        $data = get($url,array("User-Agent: ".$ua,"Cookie: BDUSS=".BDUSS));
        $j = json_decode($data);
        if (!$j or !$j->urls[0]->url) {
    	    die(json_encode(array("errno" => '-116', "msg" => "获取下载地址失败"), JSON_UNESCAPED_UNICODE));
        }
        $dlink = $j->urls[0]->url;
    } else {
        $url = LINK;
        $pdata = "bduss=".BDUSS."&fid=".$f;
        $data = post($url,$pdata,array("User-Agent: KinhWeb/1.0"));
        $j = json_decode($data);
        if (!$j or !$j->dlink) {
    	    die(json_encode(array("errno" => '-116', "msg" => "获取下载地址失败"), JSON_UNESCAPED_UNICODE));
        }
        $dlink = $j->dlink;
    }
    if(!$dlink){
        die(json_encode(array("errno" => '-8', "msg" => "获取下载地址失败"), JSON_UNESCAPED_UNICODE));
    }
    echo(json_encode(array("errno" => -302, "msg" => "302 moved temporarily"), JSON_UNESCAPED_UNICODE));
    header('HTTP/1.1 302 moved temporarily');
    header("Location:".$dlink);
    die();
}
$path = $_POST['path'];
if(!empty($path)){
$path = urldecode($path);
$url = 'http://110.242.69.43/api/list?order=time&dir='.urlencode($path);
$data = get($url,array('user-Agent: netdisk','Cookie: BDUSS='.BDUSS));
if(!$data){
?>
<div class="weui-panel__bd">
    <div class="weui-flex__item">
        <div class="placeholder">百度网关错误</div>
    </div>
</div>
<?php
} 
$d = json_decode($data);
if(!$d){
?>
<div class="weui-panel__bd">
    <div class="weui-flex__item">
        <div class="placeholder">百度网关错误</div>
    </div>
</div>
<?php
}
$errno = $d->errno;
if($errno != 0){
?>
<div class="weui-panel__bd">
    <div class="weui-flex__item">
        <div class="placeholder">百度返回错误 错误号 <?php echo $errno?></div>
    </div>
</div>
<?php
}
?>
<div class="page__bd page__bd_spacing">
<div class="weui-flex">
    <div class="weui-flex__item">
        <div class="placeholder">
         <a href="javascript:javascript:openDir('%2F');" role="button" class="weui-btn weui-btn_mini weui-btn_primary weui-wa-hotarea" title="全部文件">全部文件</a>
         </div>
    </div>
<?php
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
         <a href="javascript:javascript:openDir(\''.urlencode($dir_path).'\');" role="button" class="weui-btn weui-btn_mini weui-btn_default weui-wa-hotarea" title="'.$dirs[$i].'">'.$dirname.'</a>
         </div>
         </div>
            ';
        }
    }
?>
</div>    
    <div class="weui-cells" id="filelist">
        <div class="weui-panel weui-panel_access">
            <div class="weui-panel__hd">文件列表</div> 
        </div> 
    </div> 
</div> 
<?php
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
if($nulldir==1){ ?>
<div class="weui-panel__bd">
    <div class="weui-flex__item">
        <div class="placeholder">什么也没有呢...检查下文件夹是否正确吧~</div>
    </div>
</div>
<?php 
}
} else {
?>
<!DOCTYPE html>
<html lang="zh-CN" class="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="description" content="光与暗の交界处|Best File Web!"/>
        <meta name="renderer" content="webkit" />
        <meta name="referrer" content="never" />
        <meta name="viewport" content="width=device-width,initial-scale=1.0" />
        <meta http-equiv="Cache-Control" content="no-siteapp" />
        <link rel="stylesheet" href="//cdn.bootcdn.net/ajax/libs/weui/2.5.9/style/weui.min.css">
        <script src="//cdn.bootcdn.net/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="//lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/dplayer/1.26.0/DPlayer.min.js"></script>
        <style type="text/css">
        .placeholder {
        margin: 5px;
        padding: 0 10px;
        background-color: var(--weui-BG-1);
        height: 2.3em;
        line-height: 2.3;
        text-align: center;
        color: var(--weui-FG-1);
        }
        </style>
        <title><?php print(TITLE); ?></title>
    </head>    
    <body>
        <br>
        <br>
        <div class="weui-form__text-area">
            <h2 class="weui-form__title"><?php print(TITLE); ?></h2>
            <div class="weui-form__desc">上传文件  
                <a href="javascript:window.open('https://'+ window.location.host +'/upload');" role="button" class="weui-btn weui-btn_mini weui-btn_primary weui-wa-hotarea">点我</a>
            </div>
    </div>
        <div id="all">
            
            <div class="page__bd page__bd_spacing">
                <div class="weui-flex">
                    <div class="weui-flex__item">
                        <div class="placeholder">
                            <a href="javascript:javascript:openDir('%2F');" role="button" class="weui-btn weui-btn_mini weui-btn_primary weui-wa-hotarea" title="全部文件">全部文件</a>
                        </div>
                    </div>
                </div>    
                <div class="weui-cells" id="filelist">
                    <div class="weui-panel weui-panel_access">
                        <div class="weui-panel__hd">文件列表</div> 
                    </div> 
<?php
$url = 'http://110.242.69.43/api/list?order=name&dir=/';
$data = get($url,array('user-Agent: netdisk','Cookie: BDUSS='.BDUSS));
if(!$data){
?>
<div class="weui-panel__bd">
    <div class="weui-flex__item">
        <div class="placeholder">百度网关错误</div>
    </div>
</div>
<?php    
} 
$d = json_decode($data);
if(!$d){
?>
<div class="weui-panel__bd">
    <div class="weui-flex__item">
        <div class="placeholder">百度网关错误</div>
    </div>
</div>
<?php
}
$errno = $d->errno;
if($errno != 0){
?>
<div class="weui-panel__bd">
    <div class="weui-flex__item">
        <div class="placeholder">百度返回错误 错误号 <?php echo $errno?></div>
    </div>
</div>
<?php    
}
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
        <a aria-labelledby="js_p1m1_bd" href="javascript:openMeue(\''.$cm.'\');" class="weui-media-box weui-media-box_appmsg">
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
        <a aria-labelledby="js_p1m1_bd" href="javascript:openDir(\''.urlencode($path_info).'\');" class="weui-media-box weui-media-box_appmsg">
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
if($nulldir==1){ ?>
<div class="weui-panel__bd">
    <div class="weui-flex__item">
        <div class="placeholder">什么也没有呢...检查下文件夹是否正确吧~</div>
    </div>
</div>
<?php 
}
?>
                </div> 
            </div>
        </div> 
        <script>
             function Aria2DownLoad(Name, DLink, UA, Split, port) {
             	var Variable_WebSocket = new WebSocket('ws://localhost:' + port + '/jsonrpc');
             	Variable_WebSocket.onopen = function() {
             		Variable_WebSocket.send('{"jsonrpc":2,"id":"KinhWeb","method":"system.multicall","params":[[{"methodName":"aria2.addUri","params":[["' + DLink + '"],{"max-connection-per-server":"' + Split + '","split":"' + Split + '","out":"' + Name + '","user-agent":"' + UA + '","piece-length":"1M","allow-piece-length-change":"true"}]}]]}');
             	}
             	Variable_WebSocket.onclose = function() {
             		msg('error', 'aria2c未启动');
             	}
            
             	Variable_WebSocket.onerror = function() {
             		msg('error', '发送失败');
             	}
            
             	Variable_WebSocket.onmessage = function(e) {
             		if (e.data.indexOf('result') != -1) {
             			msg('error', '发送成功')
             		}
             	}
             }
            
             function getNum(str, firstStr, secondStr) {
             	if (str == "" || str == null || str == undefined) { // "",null,undefined
             		return "";
             	}
             	if (str.indexOf(firstStr) < 0) {
             		return "";
             	}
             	var subFirstStr = str.substring(str.indexOf(firstStr) + firstStr.length, str.length);
             	var subSecondStr = subFirstStr.substring(0, subFirstStr.indexOf(secondStr));
             	return subSecondStr;
             }
            
             function msg(type, text) {
             	if (type == 'error') {
             		document.getElementById('error').innerHTML = text;
             	} else {
             		document.getElementById(type + 'info').innerHTML = text;
             	}
             	var ele = '#' + type;
             	var elem = $(ele);
             	if (elem.css('display') != 'none') return;
             	elem.fadeIn(100);
             	setTimeout(function() {
             		elem.fadeOut(100);
             	}, 2000);
             }
             
             function openDir(path) {
             	document.getElementById("loadinginfo").innerHTML = '获取文件列表中';
             	$('#loading').fadeIn(100);
             	var data = {
             		'path': path
             	}
             	$.post("https://" + window.location.host + "/", data, function(r) {
             		document.getElementById("loading").style = 'display: none';
             		document.getElementById('all').innerHTML = r;
             	}).fail(function() {
             		$('#loading').fadeOut(100);
             		msg('warn', '获取失败');
             	});
             }
            
             function openMeue(num) {
             	var ft = document.getElementById('File_Type_' + num).getAttribute('value');
             	if (ft == '视频' || ft == '图片' || ft == '音乐') {
             		if (ft == '视频') {
             			document.getElementById("potplayer").style = 'display';
             		} else {
             			document.getElementById("potplayer").style = 'display: none';
             		}
             		document.getElementById("player").style = 'display';
             	} else if ($('#player').css('display') != 'none') {
             		document.getElementById("player").style = 'display: none';
             		document.getElementById("potplayer").style = 'display: none';
             	}
             	document.getElementById('meueid').setAttribute('value', num);
             	$('#meue').fadeIn(100);
             }
            
             function getDlink(type) {
                document.getElementById("loadinginfo").innerHTML = '获取下载地址中';
             	$('#loading').fadeIn(100);
             	var num = document.getElementById('meueid').getAttribute('value');
             	var fid = document.getElementById('File_Fsid_' + num).getAttribute('value');
             	var fname = document.getElementById('File_Filename_' + num).getAttribute('value');
             	var dlink = "https://" + window.location.host + "/?f=" + fid + '&s=.baidu.com';
             	if (type == 'download') {
             		window.open(dlink);
             		msg('error', '获取下载地址成功');
             	} else if (type == 'aria2c' || type == 'mo') {
             		if (type == 'mo') {
             			var port = 16800;
             			Aria2DownLoad(fname, dlink, 'netdisk', 32, port);
             		} else {
             			var port = 6800;
             			Aria2DownLoad(fname, dlink, 'netdisk', 16, port);
             		}
             	} else if (type == 'pot') {
             		window.open('potplayer://' + dlink);
             		msg('error', '获取下载地址成功');
             	} else {
             		var aux = document.createElement("input");
             		aux.setAttribute("value", dlink);
             		document.body.appendChild(aux);
             		aux.select();
             		document.execCommand("copy");
             		document.body.removeChild(aux);
             		msg('error', '已复制到粘贴板');
             	}
             	$('#loading').fadeOut(100);
             }
            
             function player() {
             	var num = document.getElementById('meueid').getAttribute('value');
                var fid = document.getElementById('File_Fsid_' + num).getAttribute('value');
                var ft = document.getElementById('File_Type_' + num).getAttribute('value');
             	var url = "https://" + window.location.host + "/?f=" + fid;
             	msg('error', '预览准备中...');
             	if (ft == '图片') {
             		document.getElementById('playerinfo').innerHTML = '<span id="galleryImg" alt="预览文件" role="img" class="weui-gallery__img" style="background-image: url(' + url + ');" tabindex="-1"></span>';
     				$('#meue').fadeOut(100);
     				$('#gallery').fadeIn(100)
             	} else if (ft == '视频') {
     				dplayer(url);
     				$('#meue').fadeOut(100);
     				$('#gallery').fadeIn(100)
             	} else if (ft == '音乐') {
     				dplayer(url);
     				$('#meue').fadeOut(100);
     				$('#gallery').fadeIn(100)
             	} else {
     				msg('error', '不支持的预览格式');
             	}
             }
            
             function dplayer(url) {
             	var type = 'normal';
             	const dp = new DPlayer({
             		container: document.getElementById('playerinfo'),
             		autoplay: true,
             		video: {
             			url: url,
             			type: type,
             			hotkey: true, // 移动端全屏时向右划动快进，向左划动快退。
             		}
             	});
             }
            
             function closeplayer() {
             	$('#gallery').fadeOut(100);
             	document.getElementById('playerinfo').innerText = 'null';
             }
            
            $(function() {
                $('#iosMask').on('click', function() {
                $('#meue').fadeOut(100)
                });
            });
        </script>
        <div role="alert" class="weui-toptips weui-toptips_warn" id="error">errorinfo</div>
        <div role="alert" id="loading" style="display: none;">
            <div class="weui-mask_transparent"></div>
            <div class="weui-toast">
            <span class="weui-primary-loading weui-icon_toast">
            <span class="weui-primary-loading__dot"></span>
            </span>
            <p class="weui-toast__content" id="loadinginfo">loadinginfo</p>
            </div>
        </div>
        <div role="alert" id="warn" style="display: none;">
            <div class="weui-mask_transparent"></div>
            <div class="weui-toast">
            <i class="weui-icon-warn weui-icon_toast"></i>
            <p class="weui-toast__content" id="warninfo">warninfo</p>
            </div>
        </div>
        <div role="alert" id="ok" style="display: none;">
            <div class="weui-mask_transparent"></div>
            <div class="weui-toast">
            <i class="weui-icon-success-no-circle weui-icon_toast"></i>
            <p class="weui-toast__content" id="okinfo">okinfo</p>
        </div>
    </div>
    <div id='meueid' value hidden></div>
    <div id='meue' style="display: none;">
        <div class="weui-mask" id="iosMask" style="opacity: 1;" wah-hotarea="click"></div>
        <div role="dialog" aria-modal="true" tabindex="0" aria-hidden="false" class="weui-actionsheet weui-actionsheet_toggle" id="iosActionsheet">
            <div class="weui-actionsheet__title">
                <p class="weui-actionsheet__title-text">选项</p>
            </div>
            <div class="weui-actionsheet__menu">
                <div id="dlink" tabindex="0" role="button" class="weui-actionsheet__cell">
                <a href="javascript:getDlink();" role="button" class="weui-btn weui-btn_mini weui-btn_default weui-wa-hotarea">获取下载地址</a>
                </div>
                <div id="download" tabindex="0" role="button" class="weui-actionsheet__cell">
                <a href="javascript:getDlink('download');" role="button" class="weui-btn weui-btn_mini weui-btn_default weui-wa-hotarea">直接下载</a>
                </div>
                <div id="aria2cdown" tabindex="0" role="button" class="weui-actionsheet__cell">
                <a href="javascript:getDlink('aria2c');" role="button" class="weui-btn weui-btn_mini weui-btn_default weui-wa-hotarea">发送到aria2c</a>
                </div>
                <div id="moritxdown" tabindex="0" role="button" class="weui-actionsheet__cell">
                <a href="javascript:getDlink('mo');" role="button" class="weui-btn weui-btn_mini weui-btn_default weui-wa-hotarea">发送到Moritx</a>
                </div>
                <div id="player" tabindex="0" role="button" class="weui-actionsheet__cell" style="display: none;">
                <a href="javascript:player();" role="button" class="weui-btn weui-btn_mini weui-btn_default weui-wa-hotarea">预览文件</a>
                <a id="potplayer" style="display: none;" href="javascript:getDlink('pot');" role="button" class="weui-btn weui-btn_mini weui-btn_default weui-wa-hotarea">在PotPlayer中播放</a>
                </div>
            <div class="weui-actionsheet__action">
                <div role="button" tabindex="0" class="weui-actionsheet__cell" id="iosActionsheetCancel" wah-hotarea="click">
                <a href="javascript:$('#meue').fadeOut(100);" role="button" class="weui-btn weui-btn_primary">取消</a>
                </div>
            </div>
            </div>
        </div>
    </div> 
    <div style="display: none;" class="weui-mask" role="dialog" aria-labelledby="galleryImg" aria-hidden="false" aria-modal="true" id="gallery">
            <div id='playerinfo' style='position:inherit;width:100%;height:90%'>
            null
            </div>
        <div style="background-color:rgba(0,0,0,.6);opacity: 1;bottom:0;position:absolute;left:0;right:0">
            <a href="javascript:closeplayer();" role="button" class="weui-btn ">关闭</a>
        </div>
    </div>
    <br>
    <br>
    <br>
    <br>
    <div class="weui-footer">
            <p class="weui-footer__text"><?php print(FOOT); ?></p>
            <p class="weui-footer__text">Copyright © 2019-2024 MoGuQAQ Powered By KinhWeb 1.0</p>
        </div>

            
    </body>
    
</html>
<?php } ?>