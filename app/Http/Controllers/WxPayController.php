<?php

namespace App\Http\Controllers;

use App\Http\Helper\EasyWeChat;
use App\VideoBuyList;
use App\WxOrder;
use App\WxSignUp;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class WxPayController extends Controller
{
    /**
     * 微信扫码支付回調函數
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function payNotifyUrl()
    {
        $app = new Application(EasyWeChat::getPayOptions());
        $response = $app->payment->handleScanNotify(function ($productId, $openId, $notify) {
//            Log::info('payNotifyUrl', ['notify' => json_encode($notify)]);

            $body = '课程费用';
            $detail = '课程费用';
            $tradeNo = date('YmdHis') . substr($openId, strlen($openId) - 4);
            $totalFee = '32800';
//            $totalFee = '1';

            /**
             * 订单入库
             */
            $newOrder = [
                'open_id' => $openId,
                'out_trade_no' => $tradeNo,
                'total_fee' => $totalFee,
                'body' => $body,
                'detail' => $detail,
                'type' => '',
                'time_start' => date('Y-m-d H:i:s'),
                'ret_native_notify' => json_encode($notify),
                'status' => 'start' //start:开始; end:结束
            ];
            WxOrder::create($newOrder);

            return EasyWeChat::newNativeOrder($body, $detail, $tradeNo, $totalFee, $openId, $productId);
        });
//        Log::info('payNotifyUrl', ['response' => $response]);
        return $response;
    }

    /**
     * 微信支付回調函數
     */
    public function notifyUrl()
    {
        $app = new Application(EasyWeChat::getPayOptions());
        $response = $app->payment->handleNotify(function ($notify, $successful) {
            Log::info('notifyUrl', ['notify' => json_encode($notify), 'status' => $successful]);

            $wxOrder = WxOrder::where('out_trade_no', $notify->out_trade_no)->first();
            if (!$wxOrder) { // 如果订单不存在
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            if ($wxOrder->time_expire) { // 假设订单字段“支付时间”不为空代表已经支付
                return true; // 已经支付成功了就不再更新了
            }

            // 用户是否支付成功
            if ($successful) {
                // 不是已经支付状态则修改为已经支付状态
                $wxOrder->time_expire = date('Y-m-d H:i:s'); // 更新支付时间为当前时间
                $wxOrder->ret_notify = json_encode($notify);
                $wxOrder->status = 'paid';

                /**
                 * 更新报名信息
                 */
                $signUpInfo = WxSignUp::where('openid', $wxOrder->open_id)->first();
                if ($signUpInfo) {
                    $signUpInfo->status = 'paid';
                    $signUpInfo->save();
                }
            } else { // 用户支付失败
                $wxOrder->status = 'paid_fail';
            }
            $wxOrder->save(); // 保存订单
            return true; // 返回处理完成
        });

        return $response;
    }

    /**
     * 购买视频的微信支付回調函數
     */
    public function videoBuyNotifyUrl()
    {
        $app = new Application(EasyWeChat::getPayOptions());
        $response = $app->payment->handleNotify(function ($notify, $successful) {
            Log::info('notifyUrl', ['notify' => json_encode($notify), 'status' => $successful]);

            $wxOrder = WxOrder::where('out_trade_no', $notify->out_trade_no)->first();
            if (!$wxOrder) { // 如果订单不存在
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            if ($wxOrder->time_expire) { // 假设订单字段“支付时间”不为空代表已经支付
                return true; // 已经支付成功了就不再更新了
            }

            // 用户是否支付成功
            if ($successful) {
                // 不是已经支付状态则修改为已经支付状态
                $wxOrder->time_expire = date('Y-m-d H:i:s'); // 更新支付时间为当前时间
                $wxOrder->ret_notify = json_encode($notify);
                $wxOrder->status = 'paid';

                /**
                 * 更新购买信息
                 */
                $info = VideoBuyList::where('open_id', $wxOrder->open_id)->first();
                if ($info) {
                    $info->start_time = date('Y-m-d H:i:s');
                    $info->end_time = ($info->type == 'all') ? date('Y-m-d H:i:s', strtotime("+1 year")) : date('Y-m-d H:i:s', strtotime("+6 month"));
                    $info->status = 'paid';
                    $info->save();
                }
            } else { // 用户支付失败
                $wxOrder->status = 'paid_fail';
            }

            $wxOrder->save(); // 保存订单
            return true; // 返回处理完成
        });

        return $response;
    }

    /**
     * 获取Qr Code
     */
    public function getQrCode()
    {
        $data = $this->getSign();

        $str = 'weixin://wxpay/bizpayurl?';
        $str .= 'appid=' . $data['appid'] . '&';
        $str .= 'mch_id=' . $data['mch_id'] . '&';
        $str .= 'nonce_str=' . $data['nonce_str'] . '&';
        $str .= 'product_id=' . $data['product_id'] . '&';
        $str .= 'sign=' . $data['sign'] . '&';
        $str .= 'time_stamp=' . $data['time_stamp'];

        QrCode::format('png')->size(200)->generate($str, 'qrcode/20170525.png');

        dd($str);
    }

    /**
     * 生成签名
     */
    public function getSign()
    {
        $data['appid'] = env('WECHAT_APPID');
        $data['mch_id'] = env('WECHAT_MCH_ID');
        $data['nonce_str'] = 'lajiaolvyou2017liyingxuan';
        $data['product_id'] = '20170525';
        $data['time_stamp'] = '1495691065';
        $data['sign'] = $this->wxMd5Sign($data);

        return $data;
    }

    /**
     * MD5
     *
     * @param $data
     * @return string
     */
    public function wxMd5Sign($data)
    {
        $content = '';
        foreach ($data as $key => $value) {
            if ($content != '') {
                $content .= '&' . $key . '=' . $value;
            } else {
                $content = $key . '=' . $value;
            }
        }

        try {
            if (is_null($content)) {
                throw new \Exception('财付通签名内容不能为空');
            }
            $signStr = $content . '&key=' . env('WECHAT_API_KEY');

            return strtoupper(md5($signStr));
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Array to xml.
     *
     * @param null $parameters
     * @return string
     */
    public function wxArrayToXml($parameters = NULL)
    {
        if (is_null($parameters)) {
            $parameters = $this->parameters;
        }

        if (!is_array($parameters) || empty($parameters)) {
            die('参数不为数组无法解析');
        }

        $xml = "<xml>";
        foreach ($parameters as $key => $val) {
            $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";

        return $xml;
    }

    /**
     * Random.
     *
     * @param $length
     * @param int $numeric
     * @return string
     */
    public function random($length, $numeric = 0)
    {
        if ($numeric) {
            $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
        } else {
            $hash = '';
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
            $max = strlen($chars) - 1;
            for ($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
        }

        return $hash;
    }
}
