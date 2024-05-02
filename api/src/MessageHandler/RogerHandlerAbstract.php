<?php

namespace App\MessageHandler;

use App\MessageHandler\RogerHandlerTrait;

abstract class RogerHandlerAbstract {

	use RogerHandlerTrait;

	protected $handlerName;
	protected $messageClass;

}
