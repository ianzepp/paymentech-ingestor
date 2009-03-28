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

class Paymentech_Ingestor_Sftp implements Common_Ingestor
{
	private $options = array ();
	private $uri;
	
	/**
	 * @see Common_Ingestor::ingest()
	 *
	 * @return string
	 */
	public function ingest ()
	{
		$uri = $this->getUri ();
		$resource = ssh2_connect ($uri->getHost (), $uri->getPort ());
		
		if (false === $resource)
		{
			$error ["message"] = "Failed to connect to remote SSH host";
			$error ["this"] = $this;
			throw new Paymentech_Exception ($error);
		}
		
		// Check the expected fingerprint
		$fingerprint = ssh2_fingerprint ($resource);
		
		if ($this->getOption ("key.public.fingerprint") !== $fingerprint)
		{
			$error ["message"] = "Fingerprint validation failed: possible main-in-the-middle attack.";
			$error ["expected"] = $this->getOption ("key.public.fingerprint");
			$error ["received"] = $fingerprint;
			$error ["this"] = $this;
			throw new Paymentech_Exception ($error);
		}
		
		// Authenticate via pubkey
		$pubpath = $this->getOption ("key.public.path");
		$pripath = $this->getOption ("key.private.path");
		$pripass = $this->getOption ("key.private.password");
		
		if (!ssh2_auth_pubkey_file ($resource, $uri->getUser (), $pubpath, $pripath, $pripass))
		{
			$error ["message"] = "Failed to authenticate using public / private keys";
			$error ["this"] = $this;
			throw new Paymentech_Exception ($error);
		}
		
		// Create a temporary file location
		$temporaryPath = tempnam (sys_get_temp_dir (), "ssh");
		
		if (!ssh2_scp_recv ($resource, $uri->getPath (), $temporaryPath))
		{
			$error ["message"] = "Failed to download remote file to local temp file";
			$error ["temporaryPath"] = $temporaryPath;
			$error ["remotePath"] = $uri->getPath ();
			$error ["this"] = $this;
			throw new Paymentech_Exception ($error);
		}
		
		// Read the data
		$data = file_get_contents ($temporaryPath);
		
		if (empty ($data))
		{
			$error ["message"] = "Downloaded empty file from remote host";
			$error ["temporaryPath"] = $temporaryPath;
			$error ["this"] = $this;
			throw new Paymentech_Exception ($error);
		}
		
		// Close the SSH connection, cleanup temp file and return
		fclose ($resource);
		unlink ($temporaryPath);
		return $data;
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
		$this->options [$key] = $value;
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
