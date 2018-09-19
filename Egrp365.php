<?php

namespace egrp365\egrp365api;

use Exception;
use yii\base\Component;
use yii\httpclient\Client;

/**
 * Class Egrp365
 * @package egrp365\egrp365api
 */
class Egrp365 extends Component
{
    /** @var string */
    public $apiKey = '';

    /** @var string */
    public $userAgent = 'yii-egrp365-client';

    /** @var string */
    protected $baseURL = 'https://egrp365.ru/api/v2/';

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function init()
    {
        if (!$this->apiKey) {
            throw new Exception('Не установлен индивидуальный ключ пользователя');
        }
        parent::init();
    }

    /**
     * @param $method string
     * @param $apiMethod string
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected function _send($method, $apiMethod, array $data = [])
    {
        $response = (new Client())->createRequest()
            ->setMethod($method)
            ->setData(array_merge(['apiKey' => $this->apiKey], $data))
            ->setHeaders([
                'Accept' => 'application/json, text/javascript',
                'User-Agent' => $this->userAgent
            ])
            ->setUrl($this->baseURL . $apiMethod)
            ->send();

        if (!$response->getIsOk()) {
            throw new Exception('Ошибка получения данных');
        }

        $data = $response->getData();

        if (isset($data['error']) && isset($data['error'][1])) {
            throw new Exception($data['error'][1]);
        }

        return $response->getData();
    }

    /**
     * @param array $required
     * @param array $params
     * @throws Exception
     */
    protected function _checkRequired(array $required, array $params)
    {
        $errorFields = array_diff($required, array_keys($params));
        if (!empty($errorFields)) {
            throw new Exception('Отсутствуют обязательные поля: ' . implode(', ', $errorFields));
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getDocs()
    {
        return $this->_send('get', 'getDocs');
    }

    /**
     * @param string $kadnum
     * @param null|string $reestr
     * @return array
     * @throws Exception
     */
    public function getObjectsByKadnum(string $kadnum, $reestr = null)
    {
        $params = [
            'kadnum' => $kadnum,
            'reestr' => $reestr,
        ];
        return $this->_send('GET', 'getObjectsByKadnum', $params);
    }

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function getObjectsByAddress(array $params)
    {
        $required = ['region', 'city', 'street', 'house'];
        $this->_checkRequired($required, $params);
        return $this->_send('GET', 'getObjectsByAddress', $params);
    }

    /**
     * @param string $objectid
     * @return array
     * @throws Exception
     */
    public function getInfoByObjectId(string $objectid)
    {
        if (!preg_match('#^([0-9\-\:\/\ \_\*]+)$#', $objectid)) {
            throw new Exception('Неверный формат id объекта');
        }
        return $this->_send('GET', 'getInfoByObjectId', ['objectid' => $objectid]);
    }

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function postOrder(array $params)
    {
        $required = ['kadnum', 'objectid', 'email', 'phone'];
        $this->_checkRequired($required, $params);

        if (!preg_match('#^([0-9\-\:\/\ \_\*]+)$#', $params['objectid'])) {
            throw new Exception('Неверный формат id объекта');
        }

        if (!preg_match('#^([0-9\-\:\/\ \_\*]+)$#', $params['kadnum'])) {
            throw new Exception('Неверный формат кадастрового номера');
        }

        return $this->_send('POST', 'postOrder', $params);
    }

    /**
     * @param int $orderid
     * @param string $email
     * @return array
     * @throws Exception
     */
    public function getOrderStatus(int $orderid, string $email)
    {
        $params = [
            'orderid' => $orderid,
            'email' => $email,
        ];
        return $this->_send('GET', 'getOrderStatus', $params);
    }
}
