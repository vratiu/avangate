<?php
namespace Vratiu\Avangate;

use Vratiu\Avangate\Notification\IPN;
use Vratiu\Avangate\Notification\LCN;

class Avangate
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function lcn()
    {
        return $this->notification()
                    ->setFields(new LCN);
    }

    public function ipn()
    {
        return $this->notification()
                    ->setFields(new IPN);
    }

    public function notification()
    {
        return new Notification($this->config);
    }
}
