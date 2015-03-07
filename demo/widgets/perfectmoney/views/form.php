<?php
/*
 * @var \yii\web\View $this
 * @var \nepster\perfectmoney\Api $api
 * @var $invoiceId
 * @var $amount
 * @var $description
 * @var $autoRedirect
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Perfect Money</title>
    <style type="text/css">
        #perfect-money-checkout {
            width: 400px;
            height: 200px;
            margin: auto;
            text-align: center;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: <?php $autoRedirect ? 'none' : 'block' ?>;
        }
    </style>
    <noscript>
        <style type="text/css">
            #perfect-money-checkout {
                display: block;
            }
        </style>
    </noscript>
</head>
<body>
<div id="perfect-money-checkout">
    <p>
        Пополнение внутреннего счета на <?= $amount ?> <?= $api->walletCurrency ?>
    </p>
    <form id="perfect-money-checkout-form" action="https://perfectmoney.is/api/step1.asp" method="POST">
        <input type="hidden" name="PAYEE_ACCOUNT" value="<?= $api->walletNumber ?>">
        <input type="hidden" name="PAYEE_NAME" value="<?= $api->merchantName ?>">
        <input type="hidden" name="PAYMENT_UNITS" value="<?= $api->walletCurrency ?>">
        <input type="hidden" name="STATUS_URL" value="<?= $api->resultUrl ?>">
        <input type="hidden" name="PAYMENT_URL" value="<?= $api->successUrl ?>">
        <input type="hidden" name="NOPAYMENT_URL" value="<?= $api->failureUrl ?>">
        <input type="hidden" name="NOPAYMENT_URL_METHOD" value="POST">
        <input type="hidden" name="PAYMENT_URL_METHOD" value="POST">
        <input type="hidden" name="PAYMENT_ID" value="<?= $invoiceId ?>">
        <input type="hidden" name="PAYMENT_AMOUNT" value="<?= $amount ?>">
        <input type="hidden" name="SUGGESTED_MEMO" value="<?= $description ?>">
        <input type="submit" value="Перейти на сайт Perfect Money и оплатить" />
    </form>
</div>
<?php if ($autoRedirect) : ?>
    <script type="text/javascript">
        document.getElementById("perfect-money-checkout-form").submit();
    </script>
<?php endif; ?>
</body>
</html>