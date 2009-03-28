<?php

/**
 * The MIT License
 * 
 * Copyright (c) 2009 Ian Zepp
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * @author Ian Zepp
 * @package com.ianzepp.paymentech
 */

class Paymentech_Ingestor_Curl implements Common_Ingestor
{
	private $curlopt = array ();
	private $options = array ();
	private $uri;
	
	/**
	 * @see Common_Ingestor::ingest()
	 *
	 * @return string
	 * @throws Paymentech_Exception If CURL is unable to fetch data. 
	 */
	public function ingest ()
	{
		// Set mandatory options
		$this->setOption ("CURLOPT_RETURNTRANSFER", "true");
		$this->setOption ("CURLOPT_FORBID_REUSE", "true");
		$this->setOption ("CURLOPT_FRESH_CONNECT", "true");
		
		$curl = curl_init ((string) $this->getUri ());
		curl_setopt_array ($curl, $this->getCurlOptions ());
		$result = curl_exec ($curl);
		curl_close ($curl);
		
		if ($result === false)
		{
			$error ["message"] = "CURL was unable to fetch data (exec returned false)";
			$error ["curl"] = $curl;
			$error ["this"] = $this;
			throw new Paymentech_Exception ($error);
		}
		else
		{
			echo "### [MAIN] [INGESTOR] Curl fetched ", strlen ($result), " bytes\n";
		}
		
		return $result;
	}
	
	/**
	 * @see Common_Optionable::getOptions()
	 *
	 * @return array(string => mixed)
	 */
	public function getCurlOptions ()
	{
		return $this->curlopt;
	}
	
	/**
	 * @see Common_Optionable::getOption()
	 *
	 * @param string $key
	 */
	public function getOption ($key)
	{
		return array_key_exists ($key, $this->options) ? $this->options [$key] : null;
	}
	
	/**
	 * @see Common_Optionable::getOptions()
	 *
	 * @return array(string => mixed)
	 */
	public function getOptions ()
	{
		return $this->options;
	}
	
	/**
	 * @see Common_Optionable::setOption()
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function setOption ($key, $value)
	{
		if (preg_match ('/^CURLOPT_.+$/', strtoupper ($key)))
		{
			$this->curlopt [constant (strtoupper ($key))] = $value;
		}
		else
		{
			$this->options [$key] = $value;
		}
	}
	
	/**
	 * @see Common_Remoteable::getUri()
	 *
	 * @return Common_Uri
	 */
	public function getUri ()
	{
		if (is_null ($this->uri))
			$this->uri = new Common_Uri ();
		return $this->uri;
	}
}
