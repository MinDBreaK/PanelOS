<?php

namespace LoginBundle\Model;

/**
* 
*/
class CurlQuery
{
	private $headers;
	private $method;
	private $contents;

	private $targetURL;
	
	private $responseHeaders;
	private $responseContents;
	private $responseCode;


	private $rdyHeaders;
	private $send = false;
	private $rawResponse;
	private $response;

	private $asArray = false;


	function __construct()
	{
		# code...
	}


	public function setHeaders( $headers )
	{
		if (is_array( $headers ))
		{
			foreach ($headers as $key => $header) {
				$this->addHeader($key, $header);
			}
		} else {
			throw new \Exception("The header must be an array", 1);
		}
		return $this;
	}

	public function addHeader($key, $value)
	{
		if ( is_string($key) && is_string($this->headers) ) {
			$headers[$key] = $value;
		} else {
			throw new \Exception("Key and Value must be a string.", 1);
		}
		return $this;
	}

	public function removeHeader( $header )
	{
		if (array_key_exists($header, $this->headers))
		{
			unset($this->header[$key]);
		}
		return $this;
	}

	public function getHeaders()
	{
		return $this->headers();
	}

	public function setMethod( $method )
	{
		if (in_array($method, ["POST", "GET", "PUT"]))
		{
			$this->method = $method;
		} else {
			throw new \Exception("Invalid method !", 1);
		}
		return $this;
	}

	public function getMethod() 
	{
		return $this->method;
	}

	public function setContents( $contents )
	{
		if (is_array( $contents ))
		{
			foreach ($contents as $key => $header) {
				$this->addHeader($key, $header);
			}
		} else {
			throw new \Exception("The header must be an array", 1);
		}
		return $this;
	}

	public function getContent( $content)
	{
		return $this->content;
	}

	public function setTargetURL( $url )
	{
		$this->targetURL = $url;
		return $this;
	}

	public function prepareHeaders()
	{
		$rdyHeaders = array();

		foreach ($this->headers as $key => $value) {
			$this->rdyHeaders[] = $key . ": " . $value; 
		}

		return $this;
	}

	public function execute()
	{
		if ( $this->targetURL != null )
		{
			$curl = curl_init( $this->targetURL);
		} else {
			throw new \Exception("The target URL must be defined first !", 1);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		if ( $this->method == null || $this->method == "POST" )
		{
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post))
		}

		$this->rawResponse = curl_exec( $ch );


		return $this->rawResponse;

	}

	public function getRawResponse()
	{
		return $this->response;
	}

	public function getJsonDecodedReponse( $asArray = null )
	{
		if ( $asArray == null )
		{
			return json_decode( $this->rawResponse, $this->asArray );
		}

		return json_decode( $this->rawResponse, $asArray );
	}


	public function setAsArray($asArray = true)
	{
		$this->asArray = false;
		return $this;
	}

}