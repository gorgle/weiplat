<?php

namespace app\modules\user;

use app;
use yii\base\Module;

class UserModule extends Module
{
    /**
     * Duration of login session for users in seconds.
     * By default 30 days.
     * @var int
     */
    public $loginSessionDuration = 2592000;

    /**
     * Expiration time in seconds for user password reset generated token.
     * @var int
     */
    public $passwordResetTokenExpire = 3600;


    /**
     * Layout for post-registration process with simplified template
     * Post-registration process runs when OAuth/OpenID haven't returned any of needed fields.
     * @var string
     */
//    public $postRegistrationLayout = '@app/views/layouts/minimum-layout';
    
}