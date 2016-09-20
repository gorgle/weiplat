<?php

namespace app\backend\controllers;


use app\backend\components\BackendController;

class DashboardController extends BackendController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}