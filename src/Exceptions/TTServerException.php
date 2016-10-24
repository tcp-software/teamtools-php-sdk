<?php

namespace teamtools\Exceptions;

class TTServerException extends TTException
{
	public function __construct($message, $httpCode = 500)
	{
		parent::__construct($message, $httpCode);
	}
}