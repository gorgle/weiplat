<?php

namespace app\modules\user\authclients;

use Yii;
use yii\authclient\OAuth2;

/**
 * 微信公众号内获取用户信息
 *
 * Class WeixinPub
 * @package mobile\modules\user\authclients
 */
class MpWeixin extends BaseWeixin
{
    public $authUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize';

    public $scope = 'snsapi_userinfo';

    const USER_URL = 'https://api.weixin.qq.com/cgi-bin/user/get';

    const QR_CODE_SCENE = 1;

    const QR_CODE_LIMIT = 2;

    const QR_CODE_STRLIMIT = 3;

    const SIGN_SCENE = 'QR_SCENE';

    const SIGN_LIMIT = 'QR_LIMIT_SCENE';

    const SIGN_LIMIT_STR = 'QR_LIMIT_STR_SCENE';

    /**
     * 返回用户的基本信息
     *
     * @return array
     * @throws HttpException
     */
    /*public function getUserInfo()
    {
        $userinfo = $this->getUserAttributes();

        $avatar_url = \yii\helpers\StringHelper::byteSubstr(
            $userinfo['headimgurl'],
            0,
            strrpos($userinfo['headimgurl'], '/') + 1
        );
        $userinfo['avatar_large'] = str_replace('\\', '', $avatar_url . '132');

        return $userinfo;
    }*/

    protected function defaultName()
    {
        return 'weixin pub';
    }

    protected function defaultTitle()
    {
        return 'Weixin Pub';
    }

    /**
     * 获取用户列表
     * @throws \yii\base\Exception
     */
    public function getUserList()
    {
        return $this->api(self::USER_URL,'get',[
            'access_token' => $this->accessToken,
            'next_openid' => '',
        ]);
    }

    public function getAccessTokenWinXinPub()
    {
        if(null !== ($re = $this->api('https://api.weixin.qq.com/cgi-bin/token','get',[
            'grant_type' => 'client_credential',
            'appid' => 'wx3dcce80e587c827b',
            'secret' => '5f0923eeb07e414be0a2a72fe08809ad',
        ]))){
            return $re['access_token'];
        }
    }

    public function getUIO()
    {
        $access_token = $this->getAccessTokenWinXinPub();
        return $this->sendRequest('GET','https://api.weixin.qq.com/cgi-bin/user/info',[
            'access_token' => $access_token,
            'openid' => $this->getOpenid()
        ],[] );
        /*return $this->api('https://api.weixin.qq.com/cgi-bin/user/info','get',[
            'access_token' => $access_token,
            'openid' => $this->getOpenid()
        ]);*/
    }

    public function generateParaQrCode($type = 2)
    {
        $accessToken = $this->getAccessTokenWinXinPub();
        $actionName = $this->getQrCodeType($type);
        return $this->sendRequest('POST', 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$accessToken,[
            'action_name' => $actionName,
            'action_info' => [
                'scene' => ["scene_id" => 123],
            ]
        ],[
            'Content-Type: application/json; charset=utf-8'
        ]);
        
    }

    public function getQrCodeType($type)
    {
        switch ($type){
            case self::QR_CODE_SCENE:
                return self::SIGN_SCENE;
            case self::QR_CODE_LIMIT:
                return self::SIGN_LIMIT;
            case self::QR_CODE_STRLIMIT:
                return self::SIGN_LIMIT_STR;
        }
    }

    public function getUL()
    {
        $access_token = $this->getAccessTokenWinXinPub();
        return $this->api('https://api.weixin.qq.com/cgi-bin/user/get','get',[
            'access_token' => $access_token,
        ]);
    }
}
