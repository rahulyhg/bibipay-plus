<?php

/*
 * +----------------------------------------------------------------------+
 * |                          ThinkSNS Plus                               |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2018 Chengdu ZhiYiChuangXiang Technology Co., Ltd.     |
 * +----------------------------------------------------------------------+
 * | This source file is subject to version 2.0 of the Apache license,    |
 * | that is bundled with this package in the file LICENSE, and is        |
 * | available through the world-wide-web at the following url:           |
 * | http://www.apache.org/licenses/LICENSE-2.0.html                      |
 * +----------------------------------------------------------------------+
 * | Author: Slim Kit Group <master@zhiyicx.com>                          |
 * | Homepage: www.thinksns.com                                           |
 * +----------------------------------------------------------------------+
 */

use Zhiyi\Plus\EaseMobIm;
use Illuminate\Support\Facades\Route;
use Zhiyi\Plus\Http\Controllers\APIs\V2 as API2;
use Illuminate\Contracts\Routing\Registrar as RouteContract;

Route::any('/develop', \Zhiyi\Plus\Http\Controllers\DevelopController::class.'@index');

/*
|--------------------------------------------------------------------------
| RESTful API version 2.
|--------------------------------------------------------------------------
|
| Define the version   w of the interface that conforms to most of the
| REST ful specification.
|
*/

Route::group(['prefix' => 'v2'], function (RouteContract $api) {

    /*
    |-----------------------------------------------------------------------
    | No user authentication required.
    |-----------------------------------------------------------------------
    |
    | Here are some public routes, public routes do not require user
    | authentication, and if it is an optional authentication route to
    | obtain the current authentication user, use `$request-> user ('api')`.
    |
     */
  	//测试接口
    $api->get('/okwang' , API2\QzDetailsController::class.'@exchangRate');
    //测试myjwt接口
    $api->get('/myjwt' , API2\QzUserController::class.'@myjwt');
    //测试邮件接口
    $api->get('/myemail' , API2\QzEmailController::class.'@send');
    //权证首页显示接口
    $api->get('/qzhome' , API2\QzHomeController::class.'@index');
    //权证产品详细信息
    $api->get('/pdetail' , API2\QzHomeController::class.'@productDetail');
  	//权证用户订单详细信息
    $api->get('/odetail' , API2\QzHomeController::class.'@orderDetail');
    //调用腾讯云短信接口
    $api->get('/mysms' , API2\UserSmsController::class.'@send');
    //调用极光推送接口
    $api->post('/jpush/all' , API2\JPushController::class.'@all');
    $api->post('/jpush/alias' , API2\JPushController::class.'@alias');
    //$api->post('/jpush/testlog' , API2\JPushController::class.'@testlog');
    //$api->post('/jpush/one' , API2\JPushController::class.'@one');

    $api->post('/pingpp/webhooks', API2\PingPlusPlusChargeWebHooks::class.'@webhook');
    $api->post('/plus-pay/webhooks', API2\NewWalletRechargeController::class.'@webhook');
    $api->post('/currency/webhooks', API2\CurrencyRechargeController::class.'@webhook');
    //生成邀请二维码
    $api->post('/sharecode' , API2\ShareCodeController::class.'@index');
    $api->post('/sharecode/getPay' , API2\ShareCodeController::class.'@getPay');
    // 钱包充值验证
    $api->post('/alipay/notify', API2\PayController::class.'@alipayNotify');
    $api->post('/wechat/notify', API2\PayController::class.'@wechatNotify');

    // 积分充值验证
    $api->post('/alipayCurrency/notify', API2\CurrencyPayController::class.'@alipayNotify');
    $api->post('/wechatCurrency/notify', API2\CurrencyPayController::class.'@wechatNotify');
    /*
    | 应用启动配置.
    */

    $api->get('/bootstrappers', API2\BootstrappersController::class.'@show');

    //发送设备id
    $api->get('/phoneid' , API2\PhoneidController::class.'@index');
    // User authentication.
    $api->group(['prefix' => 'auth'], function (RouteContract $api) {
        $api->post('plogin' , API2\AuthController::class.'@phoneLogin');
        $api->post('login', API2\AuthController::class.'@login');
        $api->post('token', API2\AuthController::class.'@base_token');
        $api->post('me', API2\AuthController::class.'@me');
        $api->any('logout', API2\AuthController::class.'@logout');
        $api->any('refresh', API2\AuthController::class.'@refresh');
    });
    // Search location.
    $api->get('/locations/search', API2\LocationController::class.'@search');

    // Get hot locations.
    // @GET /api/v2/locations/hots
    $api->get('/locations/hots', API2\LocationController::class.'@hots');

    // Get Advertising space
    $api->get('/advertisingspace', API2\AdvertisingController::class.'@index');

    // Get Advertising.
    $api->get('/advertisingspace/{space}/advertising', API2\AdvertisingController::class.'@advertising');
    $api->get('/advertisingspace/advertising', API2\AdvertisingController::class.'@batch');

    // Get a html for about us.
    $api->get('/aboutus', API2\SystemController::class.'@about');

    // 注册协议
    $api->get('/agreement', API2\SystemController::class.'@agreement');

    // Get all tags.
    // @Get /api/v2/tags
    $api->get('/tags', API2\TagController::class.'@index');

    /*
    |-----------------------------------------------------------------------
    | 用户验证验证码
    |-----------------------------------------------------------------------
    |
    | 定义与用户操作相关的验证码操作
    |
    */

    $api->group(['prefix' => 'verifycodes'], function (RouteContract $api) {

        /*
        | 注册验证码
        */

        $api->post('/register', API2\VerifyCodeController::class.'@storeByRegister');

        /*
        | 已存在用户验证码
        */

        $api->post('/', API2\VerifyCodeController::class.'@store');
    });

    // 排行榜相关
    // @Route /api/v2/user/ranks
    $api->group(['prefix' => 'ranks'], function (RouteContract $api) {

        // 获取粉丝排行
        // @GET /api/v2/user/ranks/followers
        $api->get('/followers', API2\RankController::class.'@followers');

        // 获取财富排行
        // @GET /api/v2/user/ranks/balance
        $api->get('/balance', API2\RankController::class.'@balance');

        // 获取收入排行
        // @GET /api/v2/user/ranks/income
        $api->get('/income', API2\RankController::class.'@income');
    });
    /*
    | 获取文件.
    */

    tap($api->get('/files/{fileWith}', API2\FilesController::class.'@show'), function ($route) {
        $route->setAction(array_merge($route->getAction(), [
            'middleware' => ['cors-should', 'bindings'],
        ]));
    });

    /*
    |-----------------------------------------------------------------------
    | 与公开用户相关
    |-----------------------------------------------------------------------
    |
    | 定于公开用户的相关信息路由
    |
    */

    /*
    | 找人
    */
    $api->group(['prefix' => 'user'], function (RouteContract $api) {
        // @get find users by phone
        $api->post('/find-by-phone', API2\FindUserController::class.'@findByPhone');

        // @get popular users
        $api->get('/populars', API2\FindUserController::class.'@populars');

        // @get latest users
        $api->get('/latests', API2\FindUserController::class.'@latests');

        // @get recommended users
        $api->get('/recommends', API2\FindUserController::class.'@recommends');

        // @get search name
        $api->get('/search', API2\FindUserController::class.'@search');

        // @get find users by user tags
        $api->get('/find-by-tags', API2\FindUserController::class.'@findByTags');
    });

    $api->group(['prefix' => 'qzusers'] , function (RouteContract $api) {
        //创建用户
        $api->post('/' , API2\QzUserController::class.'@register');
    });
    $api->group(['prefix' => 'users'], function (RouteContract $api) {

        /*
        | 创建用户
        */

        $api->post('/', API2\UserController::class.'@store')
            ->middleware('sensitive:name');

        /*
        | 批量获取用户
        */

        $api->get('/', API2\UserController::class.'@index');

        /*
        | 获取单个用户资源
         */

        $api->get('/{user}', API2\UserController::class.'@show');

        /*
        | 用户头像
         */

        tap($api->get('/{user}/avatar', API2\UserAvatarController::class.'@show'), function ($route) {
            $route->setAction(array_merge($route->getAction(), [
                'middleware' => ['cors-should', 'bindings'],
            ]));
        });

        // 获取用户关注者
        $api->get('/{user}/followers', API2\UserFollowController::class.'@followers');
        // 获取用户关注的用户
        $api->get('/{user}/followings', API2\UserFollowController::class.'@followings');

        // Get the user's tags.
        // @GET /api/v2/users/:user/tags
        $api->get('/{user}/tags', API2\TagUserController::class.'@userTgas');
    });

    // Retrieve user password.
    $api->put('/user/retrieve-password', API2\ResetPasswordController::class.'@retrieve');

    // IAP帮助页
    $api->view('/currency/apple-iap/help', 'apple-iap-help');

    /*
    |-----------------------------------------------------------------------
    | Define a route that requires user authentication.
    |-----------------------------------------------------------------------
    |
    | The routes defined here are routes that require the user to
    | authenticate to access.
    |
    */

    /**
     * 权证相关接口
     */
    $api->group(['middleware' => 'auth:api'], function (RouteContract $api) {
        $api->group(['prefix' => 'qzusers'], function (RouteContract $api) {
            //权证平台用户购买
            $api->post('/buygoods' , API2\QzBuyController::class.'@buyGoods');
            //权证平台用户订单
            $api->get('/qzdetail' , API2\QzDetailsController::class.'@index');
            //获取token地址
            $api->get('/address' , API2\QzBuyController::class.'@getAddress');
          	//获取用户行权相关数据信息
          	$api->post('/exerdetial' , API2\QzDetailsController::class.'@exerciseDetial');
            //用户确权
            $api->post('/execute' , API2\QzDetailsController::class.'@execute');
            //强制行权
            $api->post('/qzxq' , API2\QzDetailsController::class.'@forceExecute');
            //取消订单
            $api->post('/delorder' , API2\QzDetailsController::class.'@delOrder');
          	//确权详情
          	$api->post('/detexecute' , API2\QzDetailsController::class.'@exe_detail');
          	//获取用户邀请码
          	$api->get('/getcode' , API2\QzDetailsController::class.'@getCode');
        });
    });
    $api->group(['middleware' => 'auth:api'], function (RouteContract $api) {

        /*
        |--------------------------------------------------------------------
        | Define the current authentication user to operate the route.
        |--------------------------------------------------------------------
        |
        | Define the routes associated with the current authenticated user,
        | such as getting your current user, updating user data, and so on.
        |
        */

        $api->group(['prefix' => 'user'], function (RouteContract $api) {

            /*
            | 获取当前用户
            */

            $api->get('/', API2\CurrentUserController::class.'@show');

            // Update the authenticated user
            $api->patch('/', API2\CurrentUserController::class.'@update');

            // Update phone or email of the authenticated user.
            $api->put('/', API2\CurrentUserController::class.'@updatePhoneOrMail');

            // 查看用户未读消息统计
            $api->get('/unread-count', API2\UserUnreadCountController::class.'@index');

            /*
            | 用户收到的评论
            */

            $api->get('/comments', API2\UserCommentController::class.'@index');

            /*
            | 用户收到的赞
             */

            $api->get('/likes', API2\UserLikeController::class.'@index');
            /*
            | 用户我的喜欢（点赞的帖子）
             */
            $api->get('/mylikes' , API2\UserLikeController::class.'@mylikes');
            // User certification.
            $api->group(['prefix' => 'certification'], function (RouteContract $api) {

                // Send certification.
                $api->post('/', API2\UserCertificationController::class.'@store');

                // Update certification.
                $api->patch('/', API2\UserCertificationController::class.'@update');

                // Get user certification.
                $api->get('/', API2\UserCertificationController::class.'@show');
            });


            /*
            | 用户我的资产相关
            */
            //用户资产类型
            $api->get('/capital' , API2\UserCapitalController::class.'@show_all');
            //用户资产详情
            $api->get('/capdetail' , API2\UserCapitalController::class.'@show_detail');
            /*
            | 用户通知相关
             */

            $api->group(['prefix' => 'notifications'], function (RouteContract $api) {

                /*
                | 用户通知列表
                 */

                $api->get('/', API2\UserNotificationController::class.'@index');

                /*
                | 通知详情
                 */

                $api->get('/{notification}', API2\UserNotificationController::class.'@show');

                /*
                | 阅读通知，可以使用资源模型阅读单条，也可以使用资源组形式，阅读标注多条.
                 */

                $api->patch('/{notification?}', API2\UserNotificationController::class.'@markAsRead');

                /*
                    标记所有未读消息为已读
                 */
                $api->put('/all', API2\UserNotificationController::class.'@markAllAsRead');
            });

            // send a feedback.
            $api->post('/feedback', API2\SystemController::class.'@createFeedback');

            // get a list of system conversation.
            $api->get('/conversations', API2\SystemController::class.'@getConversations');

            /*
            | 更新当前用户头像
             */

            $api->post('/avatar', API2\UserAvatarController::class.'@update');

            // Update background image of the authenticated user.
            $api->post('/bg', API2\CurrentUserController::class.'@uploadBgImage');
            /*
            | 用户喜欢
             */
            $api->group(['prefix' => 'feeds'] , function(RouteContract $api) {
                //点喜欢-用户已经点击喜欢的
                // $api->post('/' , API2\UserFeedController::class.'@getClick');
                //取消喜欢
                //喜欢的人列表
                $api->get('/' , API2\UserFeedController::class.'@getClickList');

            });
            //我的动态列表
            $api->get('/mycards' , API2\UserFeedController::class.'@getmycards');
            /*
           | 用户帖子
            */
            //我的帖子列表
//            $api->group(['prefix' => 'grouppost'] , function(RouteContract $api){
//                $api->get('/' , API2\UserGrouppostController::class.'@myCardsList');
//            });
            /*
            | 用户关注
             */

            $api->group(['prefix' => 'followings'], function (RouteContract $api) {

                // 我关注的人列表
                $api->get('/', API2\CurrentUserController::class.'@followings');

                // 关注一个用户
                $api->put('/{target}', API2\CurrentUserController::class.'@attachFollowingUser');

                // 取消关注一个用户
                $api->delete('/{target}', API2\CurrentUserController::class.'@detachFollowingUser');
            });

            $api->group(['prefix' => 'followers'], function (RouteContract $api) {
                // 获取关注我的用户
                $api->get('/', API2\CurrentUserController::class.'@followers');
            });
            //用户钱包相关操作（充值，提现）
            $api->group(['prefix' => 'wallet'] , function(RouteContract $api){
                //用户提现
                $api->post('/paywallet' , API2\PayWalletController::class.'@withdraw');
                //用户充值
                $api->post('/recharge', API2\PayWalletController::class.'@userRecharge');
            });

            // 获取相互关注的用户
            $api->get('/follow-mutual', API2\CurrentUserController::class.'@followMutual');
			//权证修改账户密码
          	$api->post('/qzpassword', API2\ResetPasswordController::class.'@setBasePassword');
            // Reset password.
            $api->put('/password', API2\ResetPasswordController::class.'@reset');

            $api->post('/paypass' , API2\ResetPasswordController::class.'@payreset');
            // The tags route of the authenticated user.
            // @Route /api/v2/user/tags
            $api->group(['prefix' => 'tags'], function (RouteContract $api) {

                // Get all tags of the authenticated user.
                // @GET /api/v2/user/tags
                $api->get('/', API2\TagUserController::class.'@index');

                // Attach a tag for the authenticated user.
                // @PUT /api/v2/user/tags/:tag
                $api->put('/{tag}', API2\TagUserController::class.'@store');

                // Detach a tag for the authenticated user.
                // @DELETE /api/v2/user/tags/:tag
                $api->delete('/{tag}', API2\TagUserController::class.'@destroy');
            });

            // 打赏用户
            $api->post('/{target}/rewards', API2\UserRewardController::class.'@store');
            // 新版打赏用户
            $api->post('/{target}/new-rewards', API2\NewUserRewardController::class.'@store');

            /*
             * 解除手机号码绑定.
             *
             * @DELETE /api/v2/user/phone
             * @author Seven Du <shiweidu@outlook.com>
             */
            $api->delete('/phone', API2\UserPhoneController::class.'@delete');

            /*
             * 解除用户邮箱绑定.
             *
             * @DELETE /api/v2/user/email
             * @author Seven Du <shiweidu@outlook.com>
             */
            $api->delete('/email', API2\UserEmailController::class.'@delete');

            $api->post('/black/{targetUser}', API2\UserBlacklistController::class.'@black');
            $api->delete('/black/{targetUser}', API2\UserBlacklistController::class.'@unBlack');
            $api->get('/blacks', API2\UserBlacklistController::class.'@blackList');
        });

        /*
        |--------------------------------------------------------------------
        | Wallet routing.
        |--------------------------------------------------------------------
        |
        | Defines routes related to wallet operations.
        |
        */

        $api->group(['prefix' => 'wallet'], function (RouteContract $api) {

            /*
            | 获取钱包配置信息
             */

            $api->get('/', API2\WalletConfigController::class.'@show');

            /*
            | 获取提现记录
             */
            $api->get('/cashes', API2\WalletCashController::class.'@show');

            /*
            | 发起提现申请
             */

            $api->post('/cashes', API2\WalletCashController::class.'@store');

            /*
            | 充值钱包余额
             */

            $api->post('/recharge', API2\WalletRechargeController::class.'@store');

            /*
            | 获取凭据列表
             */

            $api->get('/charges', API2\WalletChargeController::class.'@list');

            /*
            | 获取单条凭据
             */

            $api->get('/charges/{charge}', API2\WalletChargeController::class.'@show');
        });

        // 新版支付
        $api->group(['prefix' => 'walletRecharge'], function (RouteContract $api) {
            // 申请凭据入口
            $api->post('/orders', API2\PayController::class.'@entry');

            // 手动检测支付宝订单的支付状态
            $api->post('/checkOrders', API2\PayController::class.'@checkAlipayOrder');
        });

        $api->group(['prefix' => 'currencyRecharge'], function (RouteContract $api) {
            $api->post('/orders', API2\CurrencyPayController::class.'@entry');
            $api->post('/checkOrders', API2\CurrencyPayController::class.'@checkAlipayOrder');
        });

        // 新版钱包
        $api->group(['prefix' => 'plus-pay'], function (RouteContract $api) {

            // 获取提现记录
            $api->get('/cashes', API2\NewWalletCashController::class.'@show');

            // 发起提现申请
            $api->post('/cashes', API2\NewWalletCashController::class.'@store');

            // 发起充值
            $api->post('/recharge', API2\NewWalletRechargeController::class.'@store');

            // 钱包订单列表
            $api->get('/orders', API2\NewWalletRechargeController::class.'@list');

            // 取回凭据
            $api->get('/orders/{order}', API2\NewWalletRechargeController::class.'@retrieve');

            // 转账
            $api->post('/transfer', API2\TransferController::class.'@transfer');

            // 转换积分
            $api->post('/transform', API2\NewWalletRechargeController::class.'@transform');
        });

        /*
        | 检查一个文件的 md5, 如果存在着创建一个 file with id.
         */

        $api->get('/files/uploaded/{hash}', API2\FilesController::class.'@uploaded');

        /*
        | 上传一个文件
         */

        $api->post('/files', API2\FilesController::class.'@store');

        /*
        | 显示一个付费节点
         */

        $api->get('/purchases/{node}', API2\PurchaseController::class.'@show');

        /*
        | 为一个付费节点支付
         */

        $api->post('/purchases/{node}', API2\PurchaseController::class.'@pay');

        $api->group(['prefix' => 'report'], function (RouteContract $api) {

            // 举报一个用户
            $api->post('/users/{user}', API2\ReportController::class.'@user');

            // 举报一条评论
            $api->post('/comments/{comment}', API2\ReportController::class.'@comment');
        });

        /*
        | 环信
         */
        $api->group(['prefix' => 'easemob'], function (RouteContract $api) {

            // 注册环信用户(单个)
            $api->post('register/{user_id}', EaseMobIm\EaseMobController::class.'@createUser')->where(['user_id' => '[0-9]+']);

            //批量注册环信用户
            $api->post('/register', EaseMobIm\EaseMobController::class.'@createUsers');

            // 为未注册环信用户注册环信（兼容老用户）
            $api->post('/register-old-users', EaseMobIm\EaseMobController::class.'@registerOldUsers');

            // 重置用户环信密码
            $api->put('/password', EaseMobIm\EaseMobController::class.'@resetPassword');

            // 获取环信用户密码
            $api->get('/password', EaseMobIm\EaseMobController::class.'@getPassword');

            // 创建群组
            $api->post('/group', EaseMobIm\GroupController::class.'@store');

            // 修改群组信息
            $api->patch('/group', EaseMobIm\GroupController::class.'@update');

            // 删除群组
            $api->delete('/group', EaseMobIm\GroupController::class.'@delete');

            // 获取指定群组信息
            $api->get('/group', EaseMobIm\GroupController::class.'@getGroup');
            $api->get('/groups', EaseMobIm\GroupController::class.'@newGetGroup');

            // 获取群头像
            $api->get('/group/face', EaseMobIm\GroupController::class.'@getGroupFace');

            // 添加群成员
            $api->post('/group/member', EaseMobIm\GroupController::class.'@addGroupMembers');

            // 移除群成员
            $api->delete('/group/member', EaseMobIm\GroupController::class.'@removeGroupMembers');

            // 获取聊天记录Test
            $api->get('/group/message', EaseMobIm\EaseMobController::class.'@getMessage');
        });

        // 积分部分
        $api->group(['prefix' => 'currency'], function (RouteContract $api) {

            // 获取积分配置
            $api->get('/', API2\CurrencyConfigController::class.'@show');

            // 积分流水
            $api->get('/orders', API2\CurrencyRechargeController::class.'@index');

            // 发起充值
            $api->post('/recharge', API2\CurrencyRechargeController::class.'@store');

            // 取回凭据
            $api->get('/orders/{order}', API2\CurrencyRechargeController::class.'@retrieve');

            // 发起提现
            $api->post('/cash', API2\CurrencyCashController::class.'@store');

            // 通过积分购买付费节点
            $api->post('/purchases/{node}', API2\PurchaseController::class.'@payByCurrency');

            // 调用IAP发起充值
            $api->post('/recharge/apple-iap', API2\CurrencyApplePayController::class.'@store');

            // IAP支付完成后的验证
            $api->post('/orders/{order}/apple-iap/verify', API2\CurrencyApplePayController::class.'@retrieve');

            // IAP商品列表
            $api->get('/apple-iap/products', API2\CurrencyApplePayController::class.'@productList');

            // 积分商城（待开发）
            $api->view('/show', 'currency-developing');
        });
    });

    /*
     * 获取用户未读数信息
     */
    $api->get('/user/counts', \Zhiyi\Plus\API2\Controllers\UserCountsController::class.'@count');

    /*
     * 重置未读信息
     */
    $api->patch('/user/counts', \Zhiyi\Plus\API2\Controllers\UserCountsController::class.'@reset');
});
