<?php

namespace teamtools\Exceptions;

class TTConnectionException extends TTException
{
	public function __construct($message, $httpCode = 444)
	{
		parent::__construct($message, $httpCode);
	}
}