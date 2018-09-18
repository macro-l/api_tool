<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH.'libraries/REST_Controller.php');

/**
 * 管理插件首页
 *
 * @AUTHOR  Berserker-hong\
 *
 */

class Index extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        // $this->template_patch=REST_Controller::MANAGER_TEMPLATE_PATH;
        // $this->patch=REST_Controller::MANAGER_PATH;
    }
    public function index_get(){
        require_once('api.php');
        $api = new api();
        $list = $api->get_List();
        $DocComment = $api->get_Doc();
        // $api->test($DocComment,0);
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
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="">
        <style>
            a:link {color:#000;}		/* 未被访问的链接 */
            a:visited {color:#000;}	/* 已被访问的链接 */
            a:hover {color:blue;}	/* 鼠标指针移动到链接上 */
            a:active {color:#000;}	/* 正在被点击的链接 */
        </style>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <h1 align="center">接口列表</h1>
        <table border="1" style="margin: 0 auto 0 auto">
        <tr>
                <td style="padding:5px;width:280px">
                    服务名称
                </td>
                <td style="padding:5px;width:260px">
                    接口名称
                </td>
                <td style="padding:5px;width:100px; text-align:center">
                    上传方式
                </td>
                <td style="padding:5px;width:400px">
                    接口简介
                </td>
                <td style="padding:5px;width:80px; text-align:center">
                    接口状态
                </td>
                <td style="padding:5px;width:500px">
                    接口备注
                </td>
            </tr>
        <?php
        foreach($DocComment as $key => $value) {
            $href = (@$value['info_url'] ? $value['info_url'] : '') . '?file_url=' . (@$value['file_url'] ? $value['file_url'] : '') . '&method=' . (@$value['method'] ? $value['method'] : '');
            // var_dump($value);die;
            ?>
            <tr>
                <td style="padding:5px;">
                <a href="<?php echo $href;?>" style="font-family:Arial;text-decoration:none"><?php echo $key;?></a>
                </td>
                <td style="padding:5px;">
                    <?php echo @$value['name'] ? $value['name'] : '无';?>
                </td>
                <td style="padding:5px; text-align:center">
                    <?php echo @$value['httpmethod'] ? $value['httpmethod'] : '无';?>
                </td>
                <td style="padding:5px;">
                    <?php echo @$value['desc'] ? $value['desc'] : '无';?>
                </td>
                <td style="padding:5px; text-align:center; color:<?php if(@$value['status'] == "完成"){echo 'green';}else if(@$value['status'] == '未完成'){echo 'red';}else if(@$value['status'] == '修改中'){echo 'orange';}else {echo 'blue';}?>">
                    <?php echo @$value['status'] ? $value['status'] : '无';?>
                </td>
                <td style="padding:5px;">
                    <?php echo @$value['note'] ? $value['note'] : '无';?>
                </td>
            </tr>
        <?php
        }
        ?>
        </table>
        <script src="" async defer></script>
    </body>
</html>

<?php
    }
}
?>