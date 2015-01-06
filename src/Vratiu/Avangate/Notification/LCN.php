<?php
namespace Vratiu\Avangate\Notification;

class LCN extends NotificationSpecAbstract
{
    public $hashField = 'HASH';

    public $responseSignature = array(
        'LICENSE_CODE',
        'EXPIRATION_DATE',
    );

    public $eventName = 'lcn';
}
