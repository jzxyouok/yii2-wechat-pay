<?php
/**
 * Author: ChenGuanQun
 * Date: 2017/3/3
 * Time: 19:37
 */
namespace chenkby\wechat\pay;

use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class Helper
{
    /**
     * 验证参数是否为空
     * @param array $params 完整的参数
     * @param array $checkParams 要检查的参数
     * @return bool
     * @throws InvalidConfigException
     */
    public static function verifyParams(array $params = [], array $checkParams = [])
    {
        foreach ($checkParams as $param) {
            if (empty($params[$param])) {
                throw new InvalidConfigException($param . '参数不能为空！');
            }
        }

        return true;
    }

    /**
     * 生成32位随机字符串
     * @return string
     */
    public static function generateNonceStr()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $str = '';
        for ($i = 0; $i < 32; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 生成签名
     * @param array $params 待签名参数
     * @param string $key 商户支付密钥
     * @return string
     */
    public static function generateSign(array $params = [], $key)
    {
        ksort($params);
        $string = http_build_query($params);
        $string = $string . '&key=' . $key;
        $string = md5($string);
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 数组转为XML
     * @param array $params 待转的数组
     * @return string
     * @throws ErrorException
     */
    public static function arrayToXml(array $params = [])
    {
        if (!is_array($params) || count($params) <= 0) {
            throw new ErrorException("数组数据异常！");
        }

        $xml = "<xml>";
        foreach ($params as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * XML转为数组
     * @param string $xml 待转的xml内容
     * @return array
     * @throws ErrorException
     */
    public static function xmlToArray($xml)
    {
        if (!$xml) {
            throw new ErrorException("xml数据异常！");
        }
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * POST提交数据
     * @param array $data 要提交的数据
     * @param string $url URL
     * @param null $certPath cert证书路径
     * @param null $keyPath key证书路径
     * @param int $timeout 超时时间(s)
     * @return array|mixed
     * @throws ErrorException
     */
    public static function post(array $data = [], $url, $certPath = null, $keyPath = null, $timeout = 30)
    {
        $ch = curl_init($url);

        $curlOptions = [
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data
        ];

        // ssl证书
        if (isset($certPath) && isset($keyPath)) {
            $curlOptions = ArrayHelper::merge($curlOptions, [
                CURLOPT_SSLCERTTYPE => 'PEM',
                CURLOPT_SSLCERT => $certPath,
                CURLOPT_SSLKEYTYPE => 'PEM',
                CURLOPT_SSLKEY => $keyPath
            ]);
        }

        $data = curl_exec($ch);
        curl_close($ch);

        if ($data) {
            return $data;
        } else {
            $error = curl_errno($ch);
            throw new ErrorException("curl出错，错误码:$error");
        }
    }
}