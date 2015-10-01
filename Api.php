<?php

namespace nepster\perfectmoney;

use yii\web\ForbiddenHttpException;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yii\helpers\VarDumper;
use yii\helpers\Url;
use Yii;

/**
 * Class Api
 */
class Api extends \yii\base\Component
{
    /**
     * @var string
     */
    public $accountId;

    /**
     * @var string
     */
    public $accountPassword;

    /**
     * @var string
     */
    public $walletNumber;

    /**
     * @var string
     */
    public $walletCurrency = 'USD';

    /**
     * @var string
     */
    public $alternateSecret;

    /**
     * @var string
     */
    public $merchantName;

    /**
     * @var array
     */
    public $resultUrl;

    /**
     * @var array
     */
    public $successUrl;

    /**
     * @var array
     */
    public $failureUrl;

    /**
     * @var string
     */
    protected $_hash;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->resultUrl = Url::to($this->resultUrl, true);
        $this->successUrl = Url::to($this->successUrl, true);
        $this->failureUrl = Url::to($this->failureUrl, true);
        $this->_hash = strtoupper(md5($this->alternateSecret));
    }

    /**
     * Перевести средства на другой счет
     *
     * @param string $target Target wallet
     * @param float $amount
     * @param string|null $paymentId
     * @param string|null $memo
     *
     * @return array|false
     */
    public function transfer($target, $amount, $paymentId = null, $memo = null)
    {
        $params = [
            'Payer_Account' => $this->walletNumber,
            'Payee_Account' => $target,
            'Amount' => $amount,
            'PAY_IN' => 1,
        ];

        if (strlen($paymentId)) {
            $params['PAYMENT_ID'] = $paymentId;
        }

        if (strlen($memo)) {
            $params['Memo'] = $memo;
        }

        return $this->request('confirm', $params);
    }

    /**
     * Получить баланс
     *
     * @return array|bool
     */
    public function balance()
    {
        return $this->request('balance');
    }

    /**
     * Верификация платежа
     *
     * @param array $data
     * @return bool
     */
    public function checkHash($data)
    {
        if (!isset($data['PAYMENT_ID'],
            $data['PAYEE_ACCOUNT'],
            $data['PAYMENT_AMOUNT'],
            $data['PAYMENT_UNITS'],
            $data['PAYMENT_BATCH_NUM'],
            $data['PAYER_ACCOUNT'],
            $data['TIMESTAMPGMT'],
            $data['V2_HASH'])
        )
            return false;
        $params = [
            $data['PAYMENT_ID'],
            $data['PAYEE_ACCOUNT'],
            $data['PAYMENT_AMOUNT'],
            $data['PAYMENT_UNITS'],
            $data['PAYMENT_BATCH_NUM'],
            $data['PAYER_ACCOUNT'],
            $this->_hash,
            $data['TIMESTAMPGMT'],
        ];

        $hash = strtoupper(md5(implode(':', $params)));

        if ($hash == $data['V2_HASH']) {
            return true;
        }

        return false;
    }

    /**
     * Запрос к PerfectMoney api
     *
     * @param string $method
     * @param array $params
     * @return array|bool
     */
    public function request($method, $params = [])
    {
        $defaults = [
            'AccountID' => $this->accountId,
            'PassPhrase' => $this->accountPassword,
        ];

        $httpParams = http_build_query(ArrayHelper::merge($defaults, $params));
        $scriptUrl = "https://perfectmoney.is/acct/{$method}.asp?{$httpParams}";
        $queryResult = @file_get_contents($scriptUrl);

        if ($queryResult === false) {
            return false;
        }

        if (!preg_match_all("/<input name='(.*)' type='hidden' value='(.*)'>/", $queryResult, $items, PREG_SET_ORDER)) {
            return false;
        }

        $result = [];

        foreach ($items as $item) {
            $result[$item[1]] = $item[2];
        }

        return $result;
    }

    /**
     * Получить историю кошелька PerfectMoney
     *
     *   $params = [
     *       'startday' => '09',
     *       'startmonth' => '09',
     *       'startyear' => '2015',
     *       'endday' => '04',
     *       'endmonth' => '10',
     *       'endyear' => '2015',
     *   ];
     *
     *
     * @param array $params
     * @return array|string
     */
    public function history($params = [])
    {
        $defaults = [
            'AccountID' => $this->accountId,
            'PassPhrase' => $this->accountPassword,
        ];

        $httpParams = http_build_query(ArrayHelper::merge($defaults, $params));
        $scriptUrl = "https://perfectmoney.is/acct/historycsv.asp?{$httpParams}";

        $f = fopen($scriptUrl, 'rb');

        if ($f === false) {
            return 'error openning url';
        }


        $lines = array();
        while (!feof($f)) array_push($lines, trim(fgets($f)));

        fclose($f);

        $ar = array();
        if ($lines[0] != 'Time,Type,Batch,Currency,Amount,Fee,Payer Account,Payee Account,Payment ID,Memo') {

            // print error message
            return $lines[0];

        } else {

            // do parsing
            $ar = array();
            $n = count($lines);
            for ($i = 1; $i < $n; $i++) {

                $item = explode(",", $lines[$i], 9);
                if (count($item) != 9) continue; // line is invalid - pass to next one
                $item_named['Time'] = $item[0];
                $item_named['Type'] = $item[1];
                $item_named['Batch'] = $item[2];
                $item_named['Currency'] = $item[3];
                $item_named['Amount'] = $item[4];
                $item_named['Fee'] = $item[5];
                $item_named['Payer Account'] = $item[6];
                $item_named['Payee Account'] = $item[7];
                $item_named['Memo'] = $item[8];
                array_push($ar, $item_named);
            }

            return $ar;
        }
    }
}