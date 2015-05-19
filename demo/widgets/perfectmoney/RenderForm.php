<?php

namespace app\modules\merchant\widgets\perfectmoney;

use yii\base\Widget;
use yii\web\View;
use Yii;

class RenderForm extends Widget
{
    /**
     * @var \nepster\perfectmoney\Api
     */
    public $api;

    /**
     * @var int
     */
    public $invoiceId;

    /**
     * @var decimal
     */
    public $amount;

    /**
     * @var string
     */
    public $description = '';

    /**
     * @var bool
     */
    public $autoRedirect = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        assert(isset($this->api));
        assert(isset($this->autoRedirect));
        assert(isset($this->invoiceId));
        assert(isset($this->amount));
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('form', [
            'api' => $this->api,
            'autoRedirect' => $this->autoRedirect,
            'invoiceId' => $this->invoiceId,
            'amount' => number_format($this->amount, 2, '.', ''),
            'description' => $this->description,
        ]);
    }
}