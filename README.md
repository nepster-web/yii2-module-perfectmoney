# Модуль платежной системы Perfect Money для Yii2

Обеспечивает простой и расширяемый функционал для работы с платежной системой [Perfect Money](http://perfectmoney.is).


## Установка

Предпочтительный способ установки через [composer](http://getcomposer.org/download/).

Запустите в консоле

```
php composer.phar require --prefer-dist nepster-web/yii2-module-perfectmoney "*"
```

или добавьте

```
"nepster-web/yii2-module-perfectmoney": "*"
```

в файл `composer.json` в секцию require.


## Документация

**После того как модуль успешно установлен:**

Необходимо добавить настройки в конфигурационный файл Вашего приложения:

```
'components' => [
    ...
    'pm' => [
        'class' => '\nepster\perfectmoney\Api',
        'accountId' => '0000000',
        'accountPassword' => '*******',
        'walletNumber' => 'U0000000',
        'merchantName' => 'Название компании',
        'alternateSecret' => 'Секретная фраза',
        'resultUrl' => ['/merchant/perfect-money/result'],
        'successUrl' => ['/merchant/perfect-money/success'],
        'failureUrl' => ['/merchant/perfect-money/failure'],
    ];
]
```

После успешной установки Вам будет доступен виджет с платежной формой, задача которого сделать автоматический редирект на сайт платежной системы:

```
return \common\modules\merchant\widgets\PmForm\RenderForm::widget([
   'api' => Yii::$app->pm,
   'invoiceId' => $invoiceId,
   'amount' => $amount,
   'description' => 'Пополнение внутреннего счета',
   'autoRedirect' => true,
]);
```

**Обратите внимание**

Редирект реализован с помощью JavaScript, поэтому если он будет выключен, то пользователю необходимо подтвердить переход нажав на кнопку.

Виджет можно реализовать подходящим для Вас способом.


## Зависимости

Текущий модуль, устанавливает следующие пакеты:

 * [yii2-extensions-installer](https://github.com/nepster-web/yii2-extensions-installer)
 

## Лицензия

Данный модуль выпущен под лицензией MIT. Подробную информацию читайте в файле [LICENSE.md](LICENSE.md).