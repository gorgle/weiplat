<?php

namespace app\modules\user\authclients;

use Yii;
use yii\authclient\OAuth2;

class BaseWeixin extends OAuth2
{
    public $tokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token';
//    public $tokenUrl = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';

    public $apiBaseUrl = 'https://api.weixin.qq.com';

    public $refreshTokenUrl = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';

    /**
     * 第一步：请求CODE
     *
     * appid 是 应用唯一标识
     * redirect_uri 是 重定向地址，需要进行UrlEncode
     * response_type 是 填code
     * scope 是 应用授权作用域，拥有多个作用域用逗号（,）分隔，网页应用目前仅填写snsapi_login即可
     * state 否 用于保持请求和回调的状态，授权请求后原样带回给第三方。
     *     该参数可用于防止csrf攻击（跨站请求伪造攻击），建议第三方带上该参数，可设置为简单的随机数加session进行校验
     * https://open.weixin.qq.com/connect/qrconnect?appid=wxbdc5610cc59c1631&redirect_uri=https%3A%2F%2Fpassport.yhd.com%2Fwechat%2Fcallback.do&response_type=code&scope=snsapi_login&state=3d6be0a4035d839573b04816624a415e#wechat_redirect
     * Composes user authorization URL.
     * @param array $params additional auth GET params.
     * @return string authorization URL.
     */
    public function buildAuthUrl(array $params = [])
    {
        $defaultParams = [
            'appid' => $this->clientId,
            'redirect_uri' => $this->getReturnUrl(),
            'response_type' => 'code',
        ];
        if (!empty($this->scope)) {
            $defaultParams['scope'] = $this->scope;
        }

        return $this->composeUrl($this->authUrl, array_merge($defaultParams, $params));
    }

    /**
     * 第二步：通过code获取access_token
     *
     * https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code
     * Fetches access token from authorization code.
     * @param string $authCode authorization code, usually comes at $_GET['code'].
     * @param array $params additional request params.
     * @return OAuthToken access token.
     */
    public function fetchAccessToken($authCode, array $params = [])
    {
        $defaultParams = [
            'appid' => $this->clientId,
            'secret' => $this->clientSecret,
            'code' => $authCode,
            'grant_type' => 'authorization_code',
        ];
        $response = $this->sendRequest('POST', $this->tokenUrl, array_merge($defaultParams, $params));
        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);

        return $token;
    }

    /**
     * 获取用户信息
     *
     * https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID
     * @return array
     */
    protected function initUserAttributes()
    {
        return $this->api('sns/userinfo');
    }

    /**
     * @inheritdoc
     */
    protected function apiInternal($accessToken, $url, $method, array $params, array $headers)
    {
        $params['access_token'] = $accessToken->getToken();
        $params['openid'] = $this->getOpenid();

        return $this->sendRequest($method, $url, $params, $headers);
    }

    public function getOpenid()
    {
        return $this->getAccessToken()->getParam('openid');
    }
}
