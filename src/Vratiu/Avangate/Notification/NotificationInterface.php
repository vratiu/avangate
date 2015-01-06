<?php
namespace Vratiu\Avangate\Notification;

interface NotificationInterface
{
	public function getHashField();

	public function getResponseSigFields();

	public function getEventName();
}