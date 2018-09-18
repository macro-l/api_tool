<?php
/**
 * @AUTHOR  Berserker-hong
 */

// codeigniter 防跨站攻击
// defined('BASEPATH') OR exit('No direct script access allowed');



/* --------------------------------------------------------------
     * 网站路径类设置
     * ------------------------------------------------------------ */


/**
 * 网站的根目录
 * $boot = ""
 */
$api_boot = dirname(dirname(__FILE__));

/**
 * 详情页的url
 * $info_url = ""
 * $info_url = "http://www.w3c.com"
 */
$info_url = "http://192.168.1.229:8089/api_tool/Info";



/**
 * 无目录模式
 * -| $api_boot
 *  |---|apiname.api.php
 *  |---|apiname2.api.php
 *  |---|...
 * 
 * 目录模式
 * -| $api_boot
 *  |---|dirname
 *      |---|apiname.api.php
 *      |---|apiname2.api.php
 *      |---|...
 *  |---|dirname2
 *      |---|apiname.api.php
 *      |---|apiname2.api.php
 *      |---|...
 *  |---|...
 */

 /**
  *api目录结构模式
  *0：无目录模式  |  1：目录模式
  * $api_model
  */
$api_mode = 1;

/**
 * 根目录里的 api文件根目录
 * $api_dir = ""
 */
$api_dir = "/api";

/**
 * 自定义文件命名标识
 * apiname[$file_tags].php
 * $file_tags = ".class"
 * eg: apiname.class.php
 */
$file_tag = NULL;


/**
 * 自定义文件后缀
 * apiname[$file_suffix]
 * $file_suffix = ".h"
 * eg: apiname.h
 */
$file_suffix = NULL;

/**
 * 自定义api后缀标识
 * apiname[$api_suffix].php
 * $api_suffix = "_api"
 * eg: apiname_api.php
 */
$api_suffix = NULL;


/* --------------------------------------------------------------
     * 类方法名设置
     * ------------------------------------------------------------ */

/**
 * 类方法的后缀标识(可以传多个，用‘|’隔开)
 * function methodname[$method_suffix]
 * $method_duffix = "_api|_api2"
 * eg: function methodname_api  ||  function methodname_api2
 */
$method_suffix = "_get|_post";



/* --------------------------------------------------------------
     * 接口注释类设置
     * ------------------------------------------------------------ */

/**********************************************************************
 /* 接口注释规范说明
 /* ->     /**                      --<第一行> 第一行为注释起始，不放注释信息
 /* ->      * line1_content         --<第二行> 务必 '*' + '一个空格' + '注解' 
 /* ->      * line2_content         --<第三行> 务必 '*' + '一个空格' + '注解' 
 /* ->      * line3_content         --<第四行> 务必 '*' + '一个空格' + '注解' 
 /* ->      * line4_content         --<第五行> 务必 '*' + '一个空格' + '注解' 
 /* ->      */                      
 /* 
 *
 * 其实就是按phpDocumentor规范注解
 ************************************************************************/


/**
 * 自定义注释中方法名的行号
 * NULL 为遵守phpDocumentor规范位于第二行（具体位置请看->接口注释规范说明）
 * $name_tag是使用时，$name_line无效
 */
$name_line = NULL;

/**
 * 自定义注释中方法名的标识
 * NULL 默认为不使用标识
 * $name_tag使用时，$name_line无效
 */
$name_tag = NULL;

/**
 * 自定义注释中HTTP请求方法的行号
 * NULL 默认为位于第三行（具体位置请看->接口注释规范说明）
 * $httpmethod_tag是使用时，$httpmethod_line无效
 */
$httpmethod_line = NULL;

/**
 * 自定义注释中HTTP请求方法的标识
 * NULL 默认为不使用标识
 * $httpmethod_tag使用时，$httpmethod_line无效
 */
$httpmethod_tag = NULL;

/**
 * 自定义注释中状态的标识
 * NULL 默认为 @status
 */
$status_tag = '@status';

/**
 * 自定义注释中功能作用简单说明的标识
 * NULL 默认为 @desc
 */
$simpleaccount_tag = '@desc';

/**
 * 自定义注释中功能作用详细说明的标识
 * NULL 默认为 @detail
 */
$detailaccount_tag = '@detail';

/**
 * 自定义注释中备注的标识
 * NULL 默认为 @note
 */
$note_tag = '@note';

/**
 * 自定义注释中上传变量的标识
 * NULL 默认为 @param
 */
$param_tag = '@param';

/**
 * 自定义注释中返回变量的标识
 * NULL 默认为 @return
 */
$return_tag = '@return';


?>