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
 * @package com.ianzepp.common
 */

class Common_Uri
{
	private $protocol;
	private $host;
	private $port;
	private $user;
	private $password;
	private $path;
	
	/**
	 * Enter description here...
	 *
	 * @param string $uri
	 */
	public function setCompleteUri ($uri)
	{
		$uridata = parse_url ($uri);
		$this->setProtocol (isset ($uridata ["schema"]) ? $uridata ["schema"] : "");
		$this->setHost (isset ($uridata ["host"]) ? $uridata ["host"] : "");
		$this->setPort (isset ($uridata ["port"]) ? $uridata ["port"] : "");
		$this->setUser (isset ($uridata ["user"]) ? $uridata ["user"] : "");
		$this->setPassword (isset ($uridata ["pass"]) ? $uridata ["pass"] : "");
		$this->setPath (isset ($uridata ["path"]) ? $uridata ["path"] : "");
	}
	
	/**
	 * @return string
	 */
	public function getHost ()
	{
		return $this->host;
	}
	
	/**
	 * @return string
	 */
	public function getPassword ()
	{
		return $this->password;
	}
	
	/**
	 * @return string
	 */
	public function getPath ()
	{
		return $this->path;
	}
	
	/**
	 * @return integer
	 */
	public function getPort ()
	{
		return $this->port;
	}
	
	/**
	 * @return string
	 */
	public function getProtocol ()
	{
		return $this->protocol;
	}
	
	/**
	 * @return string
	 */
	public function getUser ()
	{
		return $this->user;
	}
	
	/**
	 * @param string $host
	 */
	public function setHost ($host)
	{
		assert (is_string ($host) || empty ($host));
		$this->host = $host;
	}
	
	/**
	 * @param string $password
	 */
	public function setPassword ($password)
	{
		assert (is_string ($password) || empty ($password));
		$this->password = $password;
	}
	
	/**
	 * @param string $path
	 */
	public function setPath ($path)
	{
		assert (is_string ($path) || empty ($path));
		$this->path = $path;
	}
	
	/**
	 * @param integer $port
	 */
	public function setPort ($port)
	{
		assert (is_integer ($port) || empty ($port) || preg_match ('/^\d+$/', $port));
		$this->port = intval ($port);
	}
	
	/**
	 * @param string $protocol
	 */
	public function setProtocol ($protocol)
	{
		assert (is_string ($protocol) || empty ($protocol));
		$this->protocol = $protocol;
	}
	
	/**
	 * @param string $user
	 */
	public function setUser ($user)
	{
		assert (is_string ($user) || empty ($user));
		$this->user = $user;
	}
	
	/**
	 * @return string
	 */
	public function __toString ()
	{
		$uri = "";
		$protocol = $this->getProtocol ();
		$host = $this->getHost ();
		$port = $this->getPort ();
		$user = $this->getUser ();
		$pass = $this->getPassword ();
		$path = $this->getPath ();
		
		if ($protocol)
			$uri .= $protocol . "://";
		
		if ($user && $pass)
			$uri .= $user . ":" . $pass . "@";
		elseif ($user)
			$uri .= $user . "@";
		
		if ($host && $port)
			$uri .= $host . ":" . $port;
		elseif ($host)
			$uri .= $host;
		
		if ($path && $host)
			$uri .= "/" . $path;
		elseif ($path && $path [0] != '/' && $protocol == 'file')
			$uri = $path; // Overwrite, drop file prefix
		

		return $uri;
	}

}
