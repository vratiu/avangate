<?php
namespace Vratiu\Avangate\Notification;

abstract class NotificationSpecAbstract implements NotificationInterface
{
    public function getHashField()
    {
        return $this->hashField;
    }

    public function getResponseSigFields()
    {
        return $this->responseSignature;
    }

    public function getEventName()
    {
        return $this->eventName;
    }
}
