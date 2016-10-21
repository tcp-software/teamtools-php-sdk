<?php

namespace teamtools\Exceptions;

class TTException extends \Exception
{
	protected $message;
	protected $httpCode;

	public function getHttpCode()
	{
	    return $this->httpCode;
	}
	
	public function setHttpCode($httpCode)
	{
	    $this->httpCode = $httpCode;
	    return $this;
	}

	public function __construct($message, $httpCode = null)
	{
		$this->message = json_encode($message);
		$this->httpCode = $httpCode;

		parent::__construct($this->message);
	}

	public function render()
	{
		header('Content-type: application/json');

		return json_encode([
			'httpCode' => $this->httpCode,
			'message'  => $this->message
		]);
	}
}
