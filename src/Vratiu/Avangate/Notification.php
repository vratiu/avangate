<?php
namespace Vratiu\Avangate;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;
use Vratiu\Avangate\Config;

class Notification extends EventDispatcher
{
    protected $dateFormat = "YmdGis";

    protected $defaultEncoding = "UTF-8";

    protected $hashMethod = "md5";
    /**
     * @var null
     */
    protected $dataProvider = null;

    /**
     * Avangate Config
     * @var null
     */
    protected $config = null;

    protected $fields = null;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Get configuration
     * @return null|Config
     */
    public function getConfig($key)
    {
        if(!empty($key) && isset($this->config[$key])){
            return $this->config[$key];
        }
        return $this->config;
    }

    public function setFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    public function verifyRequest()
    {
        $requestData = $this->getData();

        /**
        * Hash field is mandatory
        */
        if(empty($requestData[$this->fields->getHashField()])){
            return false;
        }

        $hashSource = "";

        foreach ($requestData as $fieldKey => $fieldValue) {
            if ($fieldKey == $this->fields->getHashField()) {
                continue;
            }
            if (is_array($fieldValue)) {
                foreach ($fieldValue as $arrVal) {
                    $hashSource .= $this->getFieldHashSource($arrVal);
                }

            } else {
                $hashSource .= $this->getFieldHashSource($fieldValue);
            }
        }

        $hash = hash_hmac($this->hashMethod, $hashSource, $this->getConfig('secret'));

        return (bool) ($hash === $requestData[$this->fields->getHashField()]);
    }

    public function getResponseString()
    {
        $responseDate = date($this->dateFormat);
        $hash = $this->getResponseHash($responseDate);

        return "<EPAYMENT>"
        . $responseDate
        . "|"
        . $hash
        . "</EPAYMENT>";
    }

    public function getResponseHash($date)
    {
        $requestData = $this->getData();

        $hashSource = "";
        foreach ($this->fields->responseSignature as $field) {
            if (!empty($requestData[$field])) {
                $fieldValue = $requestData[$field];
                if (is_array($fieldValue)) {
                    // ony the first item is used in calculatation
                    $hashSource .= $this->getFieldHashSource($fieldValue[0]);
                } else {
                    $hashSource .= $this->getFieldHashSource($fieldValue);
                }
            }
        }
        //add date in requested format as last parammeter
        $hashSource .= $this->getFieldHashSource($date);

        $hash = hash_hmac($this->hashMethod, $hashSource, $this->getConfig('secret'));
        return $hash;
    }

    /**
     * Creates field part hash source
     * @param $field string
     * @return string
     */
    protected function getFieldHashSource($field)
    {
        $hashSource = mb_strlen($field, $this->defaultEncoding) . $field;
        return $hashSource;
    }

    public function getData()
    {
        if (null == $this->dataProvider) {
            return $_POST;
        }

        if (is_callable($this->dataProvider)) {
            return call_user_func($this->dataProvider);
        }

        return $this->dataProvider;
    }

    public function setDataProvider($dataProvider)
    {
        $this->dataProvider = $dataProvider;
        return $this;
    }

    public function listen()
    {
        if (false == $this->verifyRequest()) {
            $this->dispatch('error',
                new GenericEvent(
                    $this->fields->getEventName(),
                    $this->getData()
                )
            );
            return false;
        }

        $this->dispatch('notification',
            new GenericEvent(
                $this->fields->getEventName(),
                $this->getData()
            )
        );

        return true;

    }

    /**
    * Overriding addListener for a more fluent interface
    * @see Symfony\Component\EventDispatcher\EventDispatcher::addListener
    * @return self
    */

    public function addListener($eventName, $listener, $priority = 0)
    {
       parent::addListener($eventName, $listener, $priority);
       return $this;
    }
}
