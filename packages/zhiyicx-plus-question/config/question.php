<?php

/*
 * +----------------------------------------------------------------------+
 * |                          ThinkSNS Plus                               |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2017 Chengdu ZhiYiChuangXiang Technology Co., Ltd.     |
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

return [

    /*
     * 应用开关
     */
    'app' => [
        'switch' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | 申请精选问题需要支付的金额
    |--------------------------------------------------------------------------
    | 金额单位为「分」
    | 以保证整数计算。默认为 200。
    |
    */

    'apply_amount' => 200,

    /*
    |--------------------------------------------------------------------------
    | 围观答案需要支付的金额
    |--------------------------------------------------------------------------
    | 金额单位为「分」
    | 以保证整数计算。默认为 100。
    |
    */

    'onlookers_amount' => 100,

    /*
     * 匿名规则
     */
    'anonymity_rule' => '匿名规则',
];
