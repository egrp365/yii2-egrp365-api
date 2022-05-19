egrp365-api
============
Клиент API для сайта https://egrp365.ru Документация -- https://egrp365.ru/api.php. 
API ключ выдается по запросу mail@egrp365.ru

## Установка

Via Composer

``` bash
$ composer require egrp365/yii2-egrp365-api
```

## Конфигурация

```php
$config = [
    ...
    'components' => [
        ...
        'egrp365' => [
            'class' => 'egrp365\egrp365api\Egrp365',
            'apiKey' => 'apiKey',
        ],
    ]
];
```

## Использование

### getDocs
```php
Yii::$app->egrp365->getDocs();
```

### getObjectsByKadnum
```php
Yii::$app->egrp365->getObjectsByKadnum('kadnum', 'reestr');
```

### getObjectsByAddress
```php
Yii::$app->egrp365->getObjectsByAddress([
    'region' => '',
    'street' => '',
    'house' => '',
]);
```

### getInfoByObjectId
```php
Yii::$app->egrp365->getInfoByObjectId('objectid');
```

### postOrder
```php
Yii::$app->egrp365->postOrder([
    'kadnum' => '',
    'objectid' => '',
    'email' => '',
    'phone' => '',
]);
```

### getOrderStatus
```php
Yii::$app->egrp365->getOrderStatus('orderid', 'email');
```
