<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH.'libraries/REST_Controller.php');

/**
 * api页
 *
 * @AUTHOR  Berserker-hong
 */

class Info extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        // $this->template_patch=REST_Controller::MANAGER_TEMPLATE_PATH;
        // $this->patch=REST_Controller::MANAGER_PATH;
    }
    public function index_get(){
        require_once('api.php');
        $api = new api();
        $file_url = $_GET['file_url'];
        $method = $_GET['method'];
        $list = $api->get_Detail_DocComment($file_url, $method);
?>


<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta type="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="">
        <style>
        span {font-size:15px;}
        </style>
    </head>
    <body>
    <div style="width:20%; min-height:940px; float:left; border-right: 2px solid #ccc; border-bottem: 2px solid #ccc">
        <h2 align="center">参数列表</h2>
        <?php
        foreach($list as $k => $v) {
            if($k == 'param') {
                foreach($v as $kk => $vv) {
                    $rn = '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    $vv['name'] = isset($vv['name']) ? $vv['name'] : '';
                    $vv['type'] = isset($vv['type']) ? $vv['type'] : '';
                    $vv['desc'] = isset($vv['desc']) ? $vv['desc'] : '';
                    $vv['other'] = isset($vv['other']) ? $vv['other'] : '';
                    if($vv['other'] != '') {
                        $vv['other'] = $rn . str_replace(' ', $rn, $vv['other']);
                    }
                    echo "<div style=\"width:80%;margin:5px 0 15px 7%;padding:5px 5px 5px 5px;border-left:5px solid #ccc;border-top:2px solid #ccc\">";
                    echo "<span align=\"left\">参数名：{$vv['name']} </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    echo "<span align=\"left\">参数类型：{$vv['type']} </span><br />";
                    echo "<span align=\"left\">功能简介：{$vv['desc']}{$vv['other']}</span><br />";
                    echo "</div>";
                }
            }
        }
        
        ?>
        </div>
        
    </div>
    <div style="width:20%; min-height:940px; float:right; border-left: 2px solid #ccc">
        <h2 align="center">返值列表</h2>
        <?php
        foreach($list as $k => $v) {
            if($k == 'return') {
                foreach($v as $kk => $vv) {
                    $rn = '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    $vv['name'] = isset($vv['name']) ? $vv['name'] : '';
                    $vv['type'] = isset($vv['type']) ? $vv['type'] : '';
                    $vv['desc'] = isset($vv['desc']) ? $vv['desc'] : '';
                    $vv['other'] = isset($vv['other']) ? $vv['other'] : '';
                    if($vv['other'] != ''){
                        $vv['other'] = $rn . str_replace(' ', $rn, $vv['other']);
                    }
                    echo "<div style=\"width:80%;margin:5px 0 15px 7%;padding:5px 5px 5px 5px;border-left:5px solid #ccc;border-top:2px solid #ccc\">";
                    echo "<span align=\"left\">参数名：{$vv['name']} </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    echo "<span align=\"left\">参数类型：{$vv['type']} </span><br />";
                    echo "<span align=\"left\">功能简介：{$vv['desc']}{$vv['other']}</span><br />";
                    echo "</div>";
                }
            }
        }
        
        ?>
    </div>
    <h1 align="center">测试链接</h1>
    <p align="center">-----------------------------------------</P>
    <p align="center">---&nbsp;&nbsp;测试链接是否成功区域 - 待开发&nbsp;&nbsp;---</P>
    <p align="center">-----------------------------------------</P>
    <br />
    <div style="width:400px;margin: 0 auto 0 auto">
    <?php var_dump($list); ?>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    </div>
        <script src="" async defer></script>
    </body>
</html>

<?php
    }
}


?>