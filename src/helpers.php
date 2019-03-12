<?php

use whh\yii2repositories\Container;
use yii\web\Response;
use whh\yii2repositories\BaseRepository;

if (!function_exists('app')) {
    /**
     * 只创建对象，防止重复多次创建，节省资源
     * @param $class
     * @return mixed|BaseRepository|\common\tool\XuanWu
     */
    function app($class)
    {
        return Container::creation($class);
    }
}


if (!function_exists('toW')) {
    /**
     * 单位转换-元----》万元
     * @param $price
     * @return float
     */
    function toW($price)
    {
        $price = round($price / 10000,2);
        return $price;
    }
}

if (!function_exists('toJson')) {
    /**
     * 返回json
     * @param array $data
     * @param string $message
     * @param int $code
     * @return \yii\console\Response|Response
     */
    function toJson(array $data, $message = '', $code = 400)
    {
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        $response->data = compact('data', 'message', 'code');
        return $response;
    }
}

if (!function_exists('random')) {
    /**
     * 生成随机数
     * @param int $length
     * @param string $type
     * @param int $convert
     * @return string
     */
    function random($length = 6, $type = 'string', $convert = 0)
    {
        $config = array(
            'number' => '1234567890',
            'letter' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'string' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
            'all' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        );

        if (!isset($config[$type]))
            $type = 'all';
        $string = $config[$type];

        $code = '';
        $strlen = strlen($string) - 1;
        for ($i = 0; $i < $length; $i++) {
            $code .= $string{mt_rand(0, $strlen)};
        }
        if (!empty($convert)) {
            $code = ($convert > 0) ? strtoupper($code) : strtolower($code);
        }
        return $code;
    }
}

if (!function_exists('uploadImagePath')){
    /**
     * 上传图片地址
     * @param $filePath
     * @return bool|string
     */
    function uploadImagePath($filePath){
        return \Yii::getAlias('@frontend/web/uploads/'.date('Y-m-d') . '/' .$filePath);
    }
}

if (!function_exists('routeBelong')) {
    /**
     * 判断路由归属
     * @param $pattern
     * @return false|int
     */
    function routeBelong($pattern)
    {
        return preg_match($pattern, \Yii::$app->request->pathInfo);
    }
}
if (!function_exists('routeBelongMatch')) {
    /**
     * 判断路由归属
     * @param $pattern
     * @return false|int
     */
    function routeBelongMatch($pattern)
    {
        $pattern = preg_replace('/^\//','^',$pattern);
        $pattern = str_replace('/','\/',$pattern);
        $pattern = '/' . $pattern . '/';
        return routeBelong($pattern);
    }
}

if (!function_exists('interceptStr')){
    /**
     * 字符截取
     * @param $string
     * @param $length
     * @param string $etc
     * @return string
     */
    function interceptStr($string, $length, $etc = '...'){
        $result = '';
        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
        $strlen = strlen($string);
        for ($i = 0; (($i < $strlen) && ($length > 0)); $i++) {
            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                if ($length < 1.0) {
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            } else {
                $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
        }
        $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        if ($i < $strlen) {
            $result .= $etc;
        }
        return $result;
    }
}

if (!function_exists('encodeMobile')){
    /**
     * 手机号-马赛克
     * @param $str
     * @return null|string|string[]
     */
    function encodeMobile($str){
        return preg_replace('/^(\d{3})\w+(\d{4})/','$1***$2',$str);
    }
}

if (!function_exists('encodeBankCard')) {
    /**
     * 银行卡-马赛克
     * @param $str
     * @return null|string|string[]
     */
    function encodeBankCard($str)
    {
        return preg_replace('/^(\d{3})\w+(\d{4})/', '$1***$2', $str);
    }
}

if (!function_exists('toT')){
    /**
     * 单位转换-元或者万----》千位分隔符
     * @param $price
     * @return $isW
     */
    function toT($price,$isW = false)
    {
        if ($isW){
            $price = floatval($price * 10000);
        }
        return  number_format($price,2,".",",");
    }
}
if (!function_exists('ajaxPage')) {
//    /**
//     * @param $num  integer 总数量
//     * @param $perpage integer 一页多小条数据
//     * @param $curpage  integer 当前页
//     * @param $mpurl string
//     * @param int $maxpages
//     * @return mixed
//     */
    /**
     * @param \yii\data\Pagination $pagination
     * @return mixed
     */
    function ajaxPage($pagination)
    {
        //$num, $perpage, $curpage, $mpurl, $maxpages = 0
        $num = $pagination->totalCount;
        $perpage = $pagination->limit;
        $curpage = $pagination->page + 1;
        $mpurl = '';
        $maxpages = 0;
        if ($num > $perpage) {
            $page = 11;
            $offset = 5;
            $realpages = @ceil($num / $perpage);
            $pages = $maxpages && $maxpages < $realpages ? $maxpages : $realpages;
            if ($page > $pages) {
                $from = 1;
                $to = $pages;
            } else {
                $from = $curpage - $offset;
                $to = $from + $page - 1;
                if ($from < 1) {
                    $to = $curpage + 1 - $from;
                    $from = 1;
                    if ($to - $from < $page) {
                        $to = $page;
                    }
                } elseif ($to > $pages) {
                    $from = $pages - $page + 1;
                    $to = $pages;
                }
            }
            $multipage['total_page'] = $realpages;
            $multipage['cur_page'] = $curpage;
            if (empty($mpurl)) {
                $multipage['prev'] = $curpage > 1 ? $curpage - 1 : 1;
                $multipage['next'] = $curpage < $realpages ? $curpage + 1 : 1;
            } else {
                $multipage['prev_url'] = $curpage > 1 ? $mpurl . '&page=' . ($curpage - 1) : "";
                $multipage['next_url'] = $curpage < $realpages ? $mpurl . '&page=' . ($curpage + 1) : "";
            }
            return $multipage;
        } else {
            $multipage['total_page'] = 1;
            $multipage['cur_page'] = 1;
            $multipage['prev'] = 1;
            $multipage['next'] = 1;
            return $multipage;
        }
    }
}

if (!function_exists('isTouch')){
    function isTouch()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('toZhDate')){
    /**
     * 转换成 2019-01-01  -------> 2019年01月01日
     * @param $date
     * @return false|string
     */
    function toZhDate($date){
        return date('Y年m月d日',strtotime($date));
    }
}

