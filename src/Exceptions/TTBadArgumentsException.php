<?php

namespace teamtools\Exceptions;

class TTBadArgumentsException extends TTException
{
	public function __construct($message, $httpCode = 400)
	{
		parent::__construct($message, $httpCode);
	}
}