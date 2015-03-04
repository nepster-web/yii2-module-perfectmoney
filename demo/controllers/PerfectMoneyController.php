<?php

namespace common\modules\perfectmoney\controllers;

use yii\web\HttpException;
use yii\base\Controller;
use Yii;

/**
 * PerfectMoney Controller
 */
class PerfectMoneyController extends Controller
{
    /**
     * @inheritdoc
     */
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['success', 'failure'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['result'],
                        'roles' => ['?']
                    ],
                ]
            ]
        ];
    }

    /**
     * Url адрес взаимодействия
     */
    public function actionResult()
    {
        echo '<pre>';
        print_r(Yii::$app->request->post());
    }

    /**
     * Успешный платеж
     */
    public function actionSuccess()
    {
        echo '<pre>';
        print_r(Yii::$app->request->post());
    }

    /**
     * Ошибка платежа
     */
    public function actionFailure()
    {
        echo '<pre>';
        print_r(Yii::$app->request->post());
    }

}