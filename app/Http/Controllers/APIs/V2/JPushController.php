<?php

namespace Zhiyi\Plus\Http\Controllers\APIs\V2;

use Illuminate\Http\Request;
use JPush\Client as JPush;
use Log;
use DB;
class JPushController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //推送给全部用户
    public function all() {
        if (isset($_POST['message'])){
            $client = new JPush(config('jpush.app_key'), config('jpush.master_secret'));
            $push_payload = $client->push()
                ->setPlatform('all')
                ->addAllAudience()
                ->setNotificationAlert($_POST['message']);
            try {
                $response = $push_payload->send();
                print_r($response);
            } catch (\JPush\Exceptions\APIConnectionException $e) {
                // try something here
                print $e;
            } catch (\JPush\Exceptions\APIRequestException $e) {
                // try something here
                print $e;
            }
        }
    }
    //推送到指定用户
    public function one() {
        if (isset($_POST["alias"])) {
            $client = new JPush(config('jpush.app_key'), config('jpush.master_secret'));
            $result = $client->push()
                ->setPlatform('all')
                ->addAlias($_POST["alias"])
                ->androidNotification('Hello Android', array(
                    'builder_id' => 2,
                    'extras' => array(
                        'thirdAddress' => 'TCBF2MNm17AHmWTU3TVCL5uYjAvzf4N7Ubgq',
                        'thirdAmount' => 0.1,
                        'thirdMoneyType' => 'IPC',
                        'thirdUrl' => 'https://www.bibipay.net',
                        'thirdTokenAccur' => 2
                    ),
                ))
                ->message('message content', array(
                    'title' => 'hello jpush',
                    'content_type' => 'text',
                    'extras' => array(
                        'thirdAddress' => 'TCBF2MNm17AHmWTU3TVCL5uYjAvzf4N7Ubgq',
                        'thirdAmount' => 0.1,
                        'thirdMoneyType' => 'IPC',
                        'thirdUrl' => 'https://www.bibipay.net',
                        'thirdTokenAccur' => 2
                    ),
                ))
                ->options(array(
                    "apns_production" => true  //true表示发送到生产环境(默认值)，false为开发环境
                ));
            try {
                $response = $result->send();
                return $response;
            } catch (\JPush\Exceptions\APIConnectionException $e) {
                // try something here
                print $e;
            } catch (\JPush\Exceptions\APIRequestException $e) {
                // try something here
                print $e;
            }
        }
    }
    //推送到指定用户
    public function beiyong_alias() {
        $jpush['alias'] = 'A1B38B2EEB1E4B9A8A0C9B38E08ABB43';
        $client = new JPush(config('jpush.app_key'), config('jpush.master_secret'));
        $platform = array('ios', 'android');
        $ios_alert = array(
            'aps' => array(
                'thirdAddress' => 'ZCBP5hLGxdxtsVo9SijL8j1NuHyRSUtkiJns',
                'thirdAmount' => 0.1,
                'thirdMoneyType' => 'IPC',
                'thirdUrl' => 'https://www.bibipay.net',
                'thirdTokenAccur' => 2
            ),
        );
        $ios_notification = array(
            'aps' => array(
                'thirdAddress' => 'ZCBP5hLGxdxtsVo9SijL8j1NuHyRSUtkiJns',
                'thirdAmount' => 0.01,
                'thirdMoneyType' => 'IPC',
                'thirdUrl' => 'https://www.bibipay.net',
                'thirdTokenAccur' => 2
            ),
            'sound' => 'jpush.caf',
            'badge' => 2,
            'content-available' => true,
            'category' => 'jiguang',
        );
        $android_notification = array(
            'builder_id' => 2,
            'extras' => array(
                'thirdAddress' => 'ZCBP5hLGxdxtsVo9SijL8j1NuHyRSUtkiJns',
                'thirdAmount' => 1,
                'thirdMoneyType' => 'NBA',
                'thirdUrl' => 'https://www.bibipay.net',
                'thirdTokenAccur' => 0  //精度
            ),
        );
        $message = array(
            'title' => 'hello jpush',
            'content_type' => 'text',
            'extras' => array(
                'thirdAddress' => 'ZCBP5hLGxdxtsVo9SijL8j1NuHyRSUtkiJns',
                'thirdAmount' => 1,
                'thirdMoneyType' => 'NBA',
                'thirdUrl' => 'https://www.bibipay.net',
                'thirdTokenAccur' => 0
            ),
        );
        $result = $client->push()
            ->setPlatform($platform)
            ->addAlias($jpush['alias'])
            ->iosNotification(array(
                'thirdAddress' => 'ZCBP5hLGxdxtsVo9SijL8j1NuHyRSUtkiJns',
                'thirdAmount' => 0.01,
                'thirdMoneyType' => 'IPC',
                'thirdUrl' => 'https://www.bibipay.net',
                'thirdTokenAccur' => 2
            ) , $ios_notification)
            ->androidNotification('Hello Android' , $android_notification)
            ->message($message)
            ->options(array(
                "apns_production" => false  //true表示发送到生产环境(默认值)，false为开发环境
            ));
        try {
            $response = $result->send();
            return $response;
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            // try something here
            print $e;
        } catch (\JPush\Exceptions\APIRequestException $e) {
            // try something here
            print $e;
        }
    }
    //推送到指定用户
    public function alias($jpush) {
        $client = new JPush(config('jpush.app_key'), config('jpush.master_secret'));
        $platform = array('ios', 'android');
        $ios_alert = array(
            'aps' => array(
                'thirdAddress' => $jpush['thirdAddress'],
                'thirdAmount' => $jpush['thirdAmount'],
                'thirdMoneyType' => $jpush['type'],
                'thirdUrl' => 'https://www.bibipay.net',
                'thirdTokenAccur' => $jpush['thirdTokenAccur']
            ),
        );
        $ios_notification = array(
            'aps' => array(
                'thirdAddress' => $jpush['thirdAddress'],
                'thirdAmount' => $jpush['thirdAmount'],
                'thirdMoneyType' => $jpush['type'],
                'thirdUrl' => 'https://www.bibipay.net',
                'thirdTokenAccur' => $jpush['thirdTokenAccur']
            ),
            'sound' => 'jpush.caf',
            'badge' => 2,
            'content-available' => true,
            'category' => 'jiguang',
        );
        $android_notification = array(
            'builder_id' => 2,
            'extras' => array(
                'thirdAddress' => $jpush['thirdAddress'],
                'thirdAmount' => $jpush['thirdAmount'],
                'thirdMoneyType' => $jpush['type'],
                'thirdUrl' => 'https://www.bibipay.net',
                'thirdTokenAccur' => $jpush['thirdTokenAccur']
            ),
        );
        $message = array(
            'title' => 'hello jpush',
            'content_type' => 'text',
            'extras' => array(
                'thirdAddress' => $jpush['thirdAddress'],
                'thirdAmount' => $jpush['thirdAmount'],
                'thirdMoneyType' => $jpush['type'],
                'thirdUrl' => 'https://www.bibipay.net',
                'thirdTokenAccur' => $jpush['thirdTokenAccur']
            ),
        );
        $result = $client->push()
            ->setPlatform($platform)
            ->addAlias($jpush['alias'])
            ->iosNotification(array(
                'thirdAddress' => $jpush['thirdAddress'],
                'thirdAmount' => $jpush['thirdAmount'],
                'thirdMoneyType' => $jpush['type'],
                'thirdUrl' => 'https://www.bibipay.net',
                'thirdTokenAccur' => $jpush['thirdTokenAccur']
            ) , $ios_notification)
            ->androidNotification('Hello Android' , $android_notification)
            ->message($message)
            ->options(array(
                "apns_production" => false  //true表示发送到生产环境(默认值)，false为开发环境
            ));
        try {
            $response = $result->send();
            return $response;
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            // try something here
            print $e;
        } catch (\JPush\Exceptions\APIRequestException $e) {
            // try something here
            print $e;
        }
    }

    public function testlog(){
        $token = DB::table('token')
            ->get();
        //return $token;
        Log::info('token相关信息: '.$token);
        return $token;
    }

}
