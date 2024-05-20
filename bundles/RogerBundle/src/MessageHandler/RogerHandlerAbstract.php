<?php

namespace Beerfranz\RogerBundle\MessageHandler;

use Beerfranz\RogerBundle\MessageHandler\RogerHandlerTrait;

abstract class RogerHandlerAbstract {

	use RogerHandlerTrait;

	protected $handlerName;
	protected $messageClass;

}
