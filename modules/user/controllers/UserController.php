<?php

namespace app\modules\user\controllers;


use yii\web\Controller;
use yii\filters\AccessControl;

class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['sign-in','auth','sign-up'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],


            ]
        ];
    }

    public function actions()
    {
        return [
            'auth' => [
                'class' => 'app\modules\user\actions\AuthAction',
                'successCallback' => [$this,'callback'],
            ]
        ];
    }

    public function callback($client)
    {
        
    }

    public function actionSignIn()
    {
        
    }

    public function actionSignOut()
    {
        
    }

    public function actionSignUp()
    {
        
    }
}