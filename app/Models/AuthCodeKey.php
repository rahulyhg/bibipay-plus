<?php

namespace Zhiyi\Plus\Models;

use Illuminate\Database\Eloquent\Model;

class AuthCodeKey extends Model
{
    //private $authCodeKey = 'khUvFB9pijNyCYMGZdzqeKalyg7dh';
    private $authCodeKey = '2wsxxde5!@tefvf$e%e#evbgh%yt7drt@yu^iknv67u8irjmghk!io*u07&6tofl#dpdl,mjty5&f$478eio^@kdmvnb5r%wsfdvb*$fhjurj';
    function authCode($input, $key) {
        # Input must be of even length.
        if (strlen($input) % 2) {
            //$input .= '0';
        }
        # Keys longer than the input will be truncated.
        if (strlen($key) > strlen($input)) {
            $key = substr($key, 0, strlen($input));
        }
        # Keys shorter than the input will be padded.
        if (strlen($key) < strlen($input)) {
            $key = str_pad($key, strlen($input), '0', STR_PAD_RIGHT);
        }
        # Now the key and input are the same length.
        # Zero is used for any trailing padding required.

        # Simple XOR'ing, each input byte with each key byte.
        $result = '';
        for ($i = 0; $i < strlen($input); $i++) {
            $result .= $input{$i} ^ $key{$i};
        }
        return $result;
    }
    /**
     * 加密
     */
    function encrypt($sessionId) {
        $hashKey = $this->base64url_encode($this->authCode($sessionId, $this->authCodeKey));
        $hashKey = $this->base64url_encode($sessionId);
        return $hashKey;
    }
    /**
     * 解密
     */
    function decrypt($hashKey) {
        $authCodeKey = '2wsxxde5!@tefvf$e%e#evbgh%yt7drt@yu^iknv67u8irjmghk!io*u07&6tofl#dpdl,mjty5&f$478eio^@kdmvnb5r%wsfdvb*$fhjurj';
        $sessionId = $this->authCode($this->base64url_decode($hashKey), $this->authCodeKey);
        $sessionId = $this->base64url_decode($hashKey);
        return $sessionId;
    }

    // url传输需要替换部分字符
    function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    // url传输需要替换部分字符
    function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
