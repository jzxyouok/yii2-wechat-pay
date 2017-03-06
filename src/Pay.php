<?php
/**
 * 微信支付
 * Author: ChenGuanQun
 * Date: 2017/3/2
 * Time: 11:51
 */
namespace chenkby\wechat\pay;

use yii\base\Object;

class Pay extends Object
{
    public $appid;

    public $mch_id;

    public $key;

    public function createOrder()
    {

    }

    public function queryOrder()
    {

    }

    public function queryRefund()
    {

    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return Order::getInstance([
            'appid' => $this->appid,
            'mch_id' => $this->mch_id,
            'key' => $this->key
        ]);
    }
}