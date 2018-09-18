<?php
// codeigniter 防跨站攻击
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class api
 *
 * @AUTHOR  Berserker-hong
 */
class api {
    // api目录
    private $dir;
    // 详情页的url
    private $info_url;
    // api目录结构模式   0：无目录模式  |  1：目录模式
    private $mode;
    // 自定义文件命名标识
    private $file_tag;
    // 自定义文件后缀
    private $file_suffix;
    // 自定义api后缀标识
    private $api_suffix;
    // 类方法名的后缀标识、
    private $method_suffix;
    // api列表
    private $list;
    // 文档分隔符
    private $DS;
    // 路径分隔符
    private $PS;
    // 系统环境   1-linux系统  |  0-windows系统
    private $system;
    // 错误信息
    private $error;
    // 网站域名
    private $host;
    // 注释中方法名的行号
    private $DC_name_line;
    // 注释中方法名的标识
    private $DC_name_tag;
    // 注释中HTTP请求方法的行号
    private $DC_httpmethod_line;
    // 注释中HTTP请求方法的标识
    private $DC_httpmethod_tag;
    // 注释中功能作用简单说明的标识
    private $DC_simpleaccount_tag;
    // 注释中功能作用详细说明的标识
    private $DC_detailaccount_tag;
    // 注释中状态的标识
    private $DC_status_tag;
    // 注释中备注的标识
    private $DC_Note_tag;
    // 注释中上传变量的标识
    private $DC_param_tag;
    // 注释中返回变量的标识
    private $DC_return_tag;

    function __construct() {
        // 加载配置文件
        include_once("config.php");

        // 网站路径类变量初始化
        $this->host = $_SERVER['HTTP_HOST'];
        $this->mode = $api_mode;
        $this->dir =  $api_boot.$api_dir;
        $this->info_url =  $info_url;
        $this->file_tag = $file_tag;
        $this->file_suffix = $file_suffix;
        $this->api_suffix = $api_suffix;
        $this->method_suffix = $method_suffix;
        $this->list = array();
        $this->DS = DIRECTORY_SEPARATOR;
        $this->PS = PATH_SEPARATOR;
        $this->ini_Dir();
        $this->ini_System();

        //接口注释类变量初始化
        $this->DC_name_line = $name_line;
        $this->DC_name_tag = $name_tag;
        $this->DC_httpmethod_line = $httpmethod_line;
        $this->DC_httpmethod_tag = $httpmethod_tag;
        $this->DC_simpleaccount_tag = $simpleaccount_tag;
        $this->DC_detailaccount_tag = $detailaccount_tag;
        $this->DC_status_tag = $status_tag;
        $this->DC_note_tag = $note_tag;
        $this->DC_param_tag = $param_tag;
        $this->DC_return_tag = $return_tag;
    }
    // （测试）获取api列表
    public function get_List() {
        $this->api_Dir_List($this->dir);
        return $this->list;
    }

    /**
     * (测试)传入指定的api接口列
     * 
     * @param string $file_url api文件路径（不包含网站api文件所在目录的信息）
     * @param string $method 具体的类方法（不包含网站api文件所在目录的信息）
     */
    public function get_Detail_DocComment($file_url, $method) {
        // 路径裂变成信息表
        $filenames = explode($this->DS,$file_url);
        // 得到文件名
        $filename = end($filenames);
        // 得到类名
        $classname = $this->get_ClassName($filename);
        
        // 获取文件路径
        if(substr($this->dir,-1) != $this->DS) {
            $list = $this->dir . $this->DS . $file_url;
        } else {
            $list = $this->dir . $file_url;
        }

        // 导入类文件
        require_once($list);
        // 得到注解文本
        $DocComments = $this->get_DocComment($classname, $method);
        // 生成注解列表
        $DocComment = $this->creat_All_DocComment($DocComments);
        // 数据回调
        return $DocComment;        
    }

    // （测试）获取api注释数据
    public function get_Doc() {
        return $this->api_List_DocComment('lists');
    }

    //  获取api文档列表
    private function api_Dir_List($dir) {
        // 获取目录列表信息数组
        $dirlists = glob($dir . $this->DS . '*');
        // 轮询文章并处理
        foreach($dirlists as $dirlist) {
            if(is_dir($dirlist)) {
                // 目录模式才递归目录信息
                if($this->mode) {
                    $this->api_Dir_List($dirlist);
                }
            } else {
                $this->list[] = $dirlist;
            }
        }
        // return $this->list;
    }

    // 生成api列表的信息
    private function api_List_DocComment() {
        // 检查是否有api列表
        if(!isset($this->list)) {
            $this->error = '未有api列表';
            return FALSE;
        }

        //列表注释数组变量 初始化
        $listdoccomment =array();
        foreach($this->list as $key => $list) {
        require_once($list);
            // 路径裂变成信息表
            $lists = explode($this->DS,$list);
            // 得到文件名
            $filename = end($lists);
            // 得到类名
            $classname = $this->get_ClassName($filename);
            // 得到含有方法信息的父类
            $Parent_Methods = $this->get_Parent_Methods($classname);
            // 得到类里的方法列表
            $method_lists = get_class_methods($classname);
            // 轮询类方法得到数据
            foreach($method_lists as $method_list) {
            // 关于存在方法的归属状态（1-父类，0-子类）
                $status = 0;
                // 跳过父类的方法
                if($Parent_Methods){
                    foreach($Parent_Methods as $Parent_Method) {
                        if($method_list == $Parent_Method->name) {
                            $status = 1;
                            break;
                        }
                    }
                }
                if($status == 1) {
                    continue;
                }
                // 接口文件所在的目录
                $dir = '';
                // api文件目录（用于组合链接文件路径）
                $dir_url = '';
                // 反射类方法注解
                $doccomment = $this->get_DocComment($classname, $method_list);
                // 组合接口名
                $path = explode($this->DS, $list); //文件路径
                $pathlen = count($path); // 文件路径深度
                $apiroot = explode($this->DS, $this->dir); // api文件夹路径
                $apirootlen = count($apiroot); // api文件路径深度
                // 组合出目录名
                for($i=$apirootlen; $i<$pathlen; $i++) {
                    $dir .= $path[$i-1].$this->DS;
                }
                // 组合出api文件目录
                for($i=$apirootlen+1; $i<$pathlen; $i++) {
                    $dir_url .= $path[$i-1].$this->DS;
                }
                $name = $classname; // 类名 / 文件名
                $method = $this->DS . $method_list; // 类方法
                 // 组合出链接类文件路径
                $file_url = $dir_url . $filename;
                 // 处理特殊方法名
                if($this->method_suffix !== NULL) {
                    $tags = explode('|',$this->method_suffix);
                    foreach($tags as $tag) {
                        if(substr($method,-strlen($tag)) == $tag) {
                            $method = substr($method, 0 ,-strlen($tag));
                            break;
                        }
                    }
                }
                // 组合出接口名
                $service = $dir . $name . $method;
                // 聚变成信息列表
                $listdoccomment[$service] = $this->create_List_DocComment($doccomment);
                // 判断是否需要填充详情页url
                $listdoccomment[$service]['info_url'] = $this->info_url;
                // 填充链接类文件路径
                $listdoccomment[$service]['file_url'] = $file_url;
                // 填充链接类方法名
                $listdoccomment[$service]['method'] = $method_list;
            }
        }
        return $listdoccomment;
    }

    /**
     * 处理注释，生成api全部信息
     * 
     * @param string $doccomment 方法的所有注释信息
     */
    private function creat_All_DocComment($doccomment) {
        $class_doccomment = array();
        // 裂变注释行生成数组(""包含起来的才是换行符)
        $httpmethod = explode("\n", $doccomment);
        // 轮询注释行
        foreach($httpmethod as $key => $doccomment_line) {
            // 提取方法名
            $name = $this->get_DC_name($key, $doccomment_line);
            if($name !== NULL) {
                $class_doccomment['name'] = $name;
            }
            // 提取注释中HTTP请求方法的行号
            $httpmethod = $this->get_DC_httpmethod($key, $doccomment_line);
            if($httpmethod !== NULL) {
                $class_doccomment['httpmethod'] = $httpmethod;
            }
            // 提取状态
            $status = $this->get_DC_status($key, $doccomment_line);
            if($status !== NULL) {
                $class_doccomment['status'] = $status;
            }
            // 提取简介说明
            $desc = $this->get_DC_simpleaccount($key, $doccomment_line);
            if($desc !== NULL) {
                $class_doccomment['desc'] = $desc;
            }
            // 提取详细说明
            $detail = $this->get_DC_detail($key, $doccomment_line);
            if($detail !== NULL) {
                $class_doccomment['detail'] = $detail;
            }
            // 提取备注
            $note = $this->get_DC_note($key, $doccomment_line);
            if($note !== NULL) {
                $class_doccomment['note'] = $note;
            }
            // 提取上传变量
            $param = $this->get_DC_param($key, $doccomment_line);
            if($param !== NULL) {
                $class_doccomment['param'][] = $param;
            }
            // 提取返回变量
            $return = $this->get_DC_return($key, $doccomment_line);
            if($return !== NULL) {
                $class_doccomment['return'][] = $return;
            }
        }
        return $class_doccomment;
        
    }

    // 处理注释，生成列表信息
    private function create_List_DocComment($doccomment) {
        $class_doccomment = array();
        // 裂变注释行生成数组(""包含起来的才是换行符)
        $httpmethod = explode("\n", $doccomment);
        // 轮询注释行
        foreach($httpmethod as $key => $doccomment_line) {
            // 提取方法名
            $name = $this->get_DC_name($key, $doccomment_line);
            if($name !== NULL) {
                // 去除左右字符
                $class_doccomment['name'] = trim($name);
            }
            // 提取注释中HTTP请求方法的行号
            $httpmethod = $this->get_DC_httpmethod($key, $doccomment_line);
            if($httpmethod !== NULL) {
                // 去除左右字符
                $class_doccomment['httpmethod'] = trim($httpmethod);
            }
            // 提取状态
            $status = $this->get_DC_status($key, $doccomment_line);
            if($status !== NULL) {
                // 去除左右字符
                $class_doccomment['status'] = trim($status);
            }
            // 提取简介说明
            $desc = $this->get_DC_simpleaccount($key, $doccomment_line);
            if($desc !== NULL) {
                // 去除左右字符
                $class_doccomment['desc'] = trim($desc);
            }
            // 提取备注
            $note = $this->get_DC_note($key, $doccomment_line);
            if($note !== NULL) {
                // 去除左右字符
                $class_doccomment['note'] = trim($note);
            }
        }
        return $class_doccomment;
        
    }

    // 在注释中提取类方法名
    private function get_DC_name($key, $doccomment_line) {
        $name = NULL;
        if($this->DC_name_tag !== NULL){
            // 查找是否存在和定位位置
            $tagpos = strpos($doccomment_line, $this->DC_name_tag);
            if($tagpos !== false){
                $name_tag_len = strlen($this->DC_name_tag);
                // 提取变量
                $name = substr($doccomment_line, $tagpos+$name_tag_len);
            }
        } else if($this->DC_name_line !== NULL) {
            // 判断行数条件
            if($key === $this->DC_name_line-1) {
                // 去除左边空格
                $name = trim($doccomment_line);
                // 提取变量
                $name = substr($name , 2);
                // 再次去除左边空格
                $name = trim($name);
            }

        } else if($key === 1) {
            // 提取变量
            // 去除左边空格
            $name = trim($doccomment_line);
            // 提取变量
            $name = substr($name , 2);
            // 再次去除左边空格
            $name = trim($name);
        }
        return $name;
    }

     // 在注释中提取类方法名
    private function get_DC_httpmethod($key, $doccomment_line) {
        // 初始化$httpmethod
        $httpmethod = NULL; 
        if($this->DC_httpmethod_tag !== NULL){
            // 查找是否存在和定位位置
            $tagpos = strpos($doccomment_line, $this->DC_httpmethod_tag);
            if($tagpos !== false){
                $httpmethod_tag_len = strlen($this->DC_httpmethod_tag);
                // 提取变量
                $httpmethod= substr($doccomment_line, $tagpos+$httpmethod_tag_len);
            }
        } else if($this->DC_httpmethod_line !== NULL) {
            // 判断行数条件
            if($key === $this->DC_httpmethod_line-1) {
                // 去除左边空格
                $httpmethod = trim($doccomment_line);
                // 提取变量
                $httpmethod = substr($httpmethod , 2);
                // 再次去除左边空格
                $httpmethod = trim($httpmethod);
            }
        } else if($key === 2) {
            // 提取变量
            // 去除左边空格
            $httpmethod = trim($doccomment_line);
            // 提取变量
            $httpmethod = substr($httpmethod , 2);
            // 再次去除左边空格
            $httpmethod = trim($httpmethod);
            
        }
        return $httpmethod;
    }

    // 在注释中提取类方法状态
    private function get_DC_status($key, $doccomment_line) {
        // 初始化$status
        $status = NULL;
        // 判断是否自定义了状态标签
        if($this->DC_status_tag !== NULL) {
            $tagpos = strpos($doccomment_line, $this->DC_status_tag);
            if($tagpos !== false){
                $status_tag_len = strlen($this->DC_status_tag);
                // 提取变量
                $status= substr($doccomment_line, $tagpos+$status_tag_len);
            }
        }
        return $status;
    }

    // 在注释中提取类方法简介
    private function get_DC_simpleaccount($key, $doccomment_line) {
        // 初始化$status
        $simpleaccount = NULL;
        if($this->DC_simpleaccount_tag !== NULL) {
            $tagpos = strpos($doccomment_line, $this->DC_simpleaccount_tag);
            if($tagpos !== false){
                $simpleaccount_tag_len = strlen($this->DC_simpleaccount_tag);
                // 提取变量
                $simpleaccount= substr($doccomment_line, $tagpos+$simpleaccount_tag_len);
            }
        }
        return $simpleaccount;
    }

    // 在注释中提取类方法详细介绍
    private function get_DC_detail($key, $doccomment_line) {
        // 初始化$detailaccount
        $detailaccount = NULL;
        if($this->DC_detailaccount_tag !== NULL) {
            $tagpos = strpos($doccomment_line, $this->DC_detailaccount_tag);
            if($tagpos !== false){
                $detailaccount_tag_len = strlen($this->DC_detailaccount_tag);
                // 提取变量
                $detailaccount= substr($doccomment_line, $tagpos+$detailaccount_tag_len);
            }
        }
        return $detailaccount;
    }

    // 在注释中提取类方法备注
    private function get_DC_note($key, $doccomment_line) {
        // 初始化$detailaccount
        $note = NULL;
        if($this->DC_note_tag !== NULL) {
            $tagpos = strpos($doccomment_line, $this->DC_note_tag);
            if($tagpos !== false){
                $note_tag_len = strlen($this->DC_note_tag);
                // 提取变量
                $note= substr($doccomment_line, $tagpos+$note_tag_len);
            }
        }
        return $note;
    }

    // 在注释中提取类方法传入变量
    private function get_DC_param($key, $doccomment_line) {
        // 初始化$detailaccount
        $param = NULL;
        if($this->DC_param_tag !== NULL) {
            $tagpos = strpos($doccomment_line, $this->DC_param_tag);
            if($tagpos !== false){
                $param_tag_len = strlen($this->DC_param_tag);
                // 提取变量
                $params = trim(substr($doccomment_line, $tagpos+$param_tag_len));
                $param_list = explode(' ',$params);
                foreach($param_list as $key => $value) {
                    $tage = NULL;
                    switch($key) {
                        case 0:
                            $tage = 'type';
                            break;
                        case 1:
                            $tage = 'name';
                            break;
                        case 2:
                            $tage = 'desc';
                            break;
                        default:
                            $tage = 'other';
                            break;
                    }
                    if(!isset($param[$tage])) {
                        if($key==1){
                            if(substr($value,0,1)=='$')
                            $value = substr($value,1);
                        }
                        $param[$tage] = $value;
                    } else {
                        $param[$tage] .= ' ' . $value;
                    }
                }
            }
        }
        return $param;
    }

    // 在注释中提取类方法返回函数
    private function get_DC_return($key, $doccomment_line) {
        // 初始化$detailaccount
        $return = NULL;
        if($this->DC_return_tag !== NULL) {
            $tagpos = strpos($doccomment_line, $this->DC_return_tag);
            if($tagpos !== false){
                $return_tag_len = strlen($this->DC_return_tag);
                // 提取变量
                $returns= trim(substr($doccomment_line, $tagpos+$return_tag_len));

                $return_list = explode(' ',$returns);
                foreach($return_list as $key => $value) {
                    $tage = NULL;
                    switch($key) {
                        case 0:
                            $tage = 'type';
                            break;
                        case 1:
                            $tage = 'name';
                            break;
                        case 2:
                            $tage = 'desc';
                            break;
                        default:
                            $tage = 'other';
                            break;
                    }
                    if(!isset($param[$tage])) {
                        $return[$tage] = $value;
                    } else {
                        $return[$tage] .= ' ' . $value;
                    }
                }
            }
        }
        return $return;
    } 

    // 得到类方法的注解信息
    private function get_DocComment($classname, $method_list) {
        // 实例化  类方法 的反射
        $Reflectionmethod = new Reflectionmethod($classname, $method_list);
        // 判断类方法是否是公有属性
        if(!$Reflectionmethod->isPublic()) {
            continue;
        }
        // 获取类方法的注释
        $doccomment = $Reflectionmethod->getDocComment();

        return $doccomment;
    }


    // 去除文件名后缀与标识，得到类名
    private function get_ClassName($filename) {
        // 去除文件后缀
        if($this->file_suffix === NULL) {
            $classname = substr($filename , 0, -4);
        } else {
            $classname = substr($filename , 0, -strlen($this->file_suffix));
        }
        //去除文件命名标识
        if($this->file_tag !== NULL) {
            $classname = substr($classname , 0, -strlen($this->file_tag));
        }
        // 去除api后缀标识
        if($this->api_suffix !== NULL) {
            $classname = substr($classname , 0, -strlen($this->api_suffix));
        }
        return $classname;
    }

    // 格式化api路径
    private function ini_Dir() {
        // 确保文档分隔符符合系统
        if($this->system) {
            $this->dir = str_replace('\\', $this->DS, $this->dir);
        } else {
            $this->dir = str_replace('/', $this->DS, $this->dir);
        }

        // 路径最后一字符不加文档分隔符
        $endstr = substr($this->dir, -1);
        if($endstr == $this->DS) {
            $dirlen = strlen($this->dir);
            $this->dir = substr($this->dir, 0, $dirlen-1);
        }
    }

    private function ini_System() {
        if($this->DS == '/' && $this->PS == ':') {
            $this->system = 1;
        } else if($this->DS == '\\' && $this->PS == ';') {
            $this->system = 0;
        } else if ($this->DS == '/') {
            $this->system = 1;
        } else if ($this->DS == '\\') {
            $this->system = 1;
        }
    }

    /**
     * 获父类所有的方法
     * @param string $classname
     */
    private function get_Parent_Methods($classname) {
        // 实例子类
        $class =new ReflectionClass($classname);
        // 获取父类
        $parent = $class->getParentclass();
        if($parent) {
            // 获取父类的方法（一个反射类）
            $methods = $parent->getMethods();
        return $methods;
        }
        return NULL;
    }
    // test fucntion 
    public function test($args, $type=1) {
        echo "<pre>";
        var_dump($args);
        echo "</pre>";
        if($type != 0) {
            die;
        }
    }
}

// $api = new api();
// $list = $api->get_List();
// $api->test($list,0);
// $DocComment = $api->get_Doc();
// $api->test($DocComment,0);
?>