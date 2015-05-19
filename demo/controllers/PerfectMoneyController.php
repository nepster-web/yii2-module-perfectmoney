<?php

namespace app\modules\merchant\controllers;

use yii\web\HttpException;
use yii\web\Controller;
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
        if (!Yii::$app->request->post()) {
            return $this->goBack();
        }

        $post = '';
        foreach (Yii::$app->request->post() as $key => $_post) {
            $post .= $key . ': ' . $_post . PHP_EOL;
        }

        if ($this->verify(Yii::$app->request->post())) {
            $log = 'SUCCESS PAYMENT INVOICE №' . Yii::$app->request->post('PAYMENT_ID') . PHP_EOL . $post;
            Yii::info($log, 'merchant');
        } else {
            $log = 'FAIL PAYMENT INVOICE №' . Yii::$app->request->post('PAYMENT_ID') . PHP_EOL . $post;
            Yii::error($log, 'merchant');
        }
    }

    /**
     * Успешный платеж
     */
    public function actionSuccess()
    {
        if (!Yii::$app->request->post()) {
            return $this->goBack();
        }

        if ($this->verify(Yii::$app->request->post())) {
            $amount = Yii::$app->formatter->asCurrency(Yii::$app->request->post('PAYMENT_AMOUNT'));
            Yii::$app->session->setFlash('success', 'Ваш счет пополнен на ' . $amount . ' через платежную систему Perfect Money');
        } else {
            Yii::$app->session->setFlash('danger', 'Возникла критическая ошибка');
        }

        return $this->redirect(['/merchant/default/pay']);
    }

    /**
     * Ошибка платежа
     */
    public function actionFailure()
    {
        if (!Yii::$app->request->post()) {
            return $this->goBack();
        }

        Yii::$app->session->setFlash('danger', 'Оплата платежа отменена');

        return $this->redirect(['/merchant/default/pay']);
    }

    /**
     * Верификация платежа
     */
    protected function verify($data)
    {
        if (Yii::$app->pm->checkHash($data)) {

            // Начисление средств на счет пользователя

            return true;
        }
        return false;
    }

}