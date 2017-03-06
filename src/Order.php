<?php
/**
 * @see https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_1
 * Author: ChenGuanQun
 * Date: 2017/3/2
 * Time: 11:55
 */
namespace chenkby\wechat\pay;

use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Object;
use Yii;

class Order
{


    public $appid;

    public $mch_id;

    /**
     * @var string 商户支付密钥
     */
    public $key;


    /**
     * @var Order|null Order实例
     */
    private static $_order = null;

    private function __construct(array $config = [])
    {
        if (!empty($config)) {
            Yii::configure($this, $config);
        }
    }

    /**
     * 返回Order对象
     * @param array $config
     * @return Order
     */
    public static function getInstance(array $config = [])
    {
        if (!self::$_order instanceof  self) {
            self::$_order = new self($config);
        }
        return self::$_order;
    }

    /**
     * 创建订单接口地址
     */
    const URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    public function create()
    {

    }

    /**
     * 查询订单接口地址
     */
    const QUERY_URL = 'https://api.mch.weixin.qq.com/pay/orderquery';


    public function query($transactionId)
    {
        $params = [
            'appid' => $this->appid,
            'mch_id' => $this->mch_id,
            'transaction_id' => $transactionId,
            'nonce_str' => Helper::generateNonceStr(),
        ];
    }

    /**
     * 关闭订单接口地址
     */
    const CLOSE_URL = 'https://api.mch.weixin.qq.com/pay/closeorder';

    /**
     * 关闭订单
     * @param string $outTradeNo 商户订单号
     * @return bool
     * @throws Exception
     */
    public function close($outTradeNo)
    {
        $params = [
            'appid' => $this->appid,
            'mch_id' => $this->mch_id,
            'out_trade_no' => $outTradeNo,
            'nonce_str' => Helper::generateNonceStr(),
        ];

        $sign = Helper::generateSign($params, $this->key);
        if (!1) {
            throw new Exception('');
        }
        return true;
    }


    /**
     * 检查订单参数是否正确
     * @param array $orderParams
     * @return bool
     * @throws InvalidConfigException
     */
    private function verifyParams(array $orderParams = [])
    {
        $checkParams = ['appid', 'mch_id', 'body', 'out_trade_no', 'total_fee', 'notify_url', 'trade_type'];

        foreach ($checkParams as $param) {
            if (empty($orderParams[$param])) {
                throw new InvalidConfigException($param . '参数不能为空！');
            }
        }

        return true;
    }

    /**
     * FBI Warning
     * @throws Exception
     */
    public function __clone()
    {
        throw new Exception('不要吃饱了没事找事！');
    }
}