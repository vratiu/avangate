<?php
namespace Vratiu\Avangate\Notification;

class IPN extends NotificationSpecAbstract
{

    /**
     * Fields from the IPN Notification that is used when generating response signature
     * Order of fields is important
     * @var array
     */
    public $responseSignature = array(
        "IPN_PID",
        "IPN_PNAME",
        "IPN_DATE",
    );

    /**
     * Hash field name
     * @var string
     */
    public $hashField = "HASH";

    public $eventName = "ipn";
}
