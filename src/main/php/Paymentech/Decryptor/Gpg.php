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

class Paymentech_Decryptor_Gpg implements Common_Decryptor
{
	private $options = array ();
	private $uri;
	
	/**
	 * @see Common_Decryptor::decrypt()
	 *
	 * @param string $encryptedData
	 * @return string
	 */
	public function decrypt ($encryptedData)
	{
		// Create a temporary file to hold the encrypted data
		if (($tempdir = $this->getOption ("tempdir")))
		{
			$encryptedTemp = tempnam ($tempdir, md5 ($encryptedData));
		}
		else
		{
			$encryptedTemp = tempnam (sys_get_temp_dir (), md5 ($encryptedData));
		}
		
		file_put_contents ($encryptedTemp, $encryptedData);
		
		// Build the command line
		$command = '"' . $this->getUri ()->getPath () . '"';
		$command .= " --decrypt --passphrase-fd 0";
		$command .= ' ' . $encryptedTemp;
		
		// Start the process
		$commandSpec = array (0 => array ("pipe", "r"), 1 => array ("pipe", "w"), 2 => array ("pipe", "w"));
		
		echo "### [MAIN] [DECRYPTOR] GPG proc_open command:\n", $command, "\n";
		
		$pipes = array ();
		$process = proc_open ($command, $commandSpec, $pipes);
		
		if (!is_resource ($process))
		{
			$error ["message"] = "Failed to open a piped process";
			$error ["command"] = $command;
			$error ["commandSpec"] = $commandSpec;
			$error ["this"] = $this;
			throw new Paymentech_Exception ($error);
		}
		
		// Write the passphrase
		fwrite ($pipes [0], $this->getUri ()->getPassword () . "\n");
		
		// Read the decrypted data
		$readStdout = stream_get_contents ($pipes [1]);
		$readStderr = stream_get_contents ($pipes [2]);
		
		// Close out pipes and clean up the temp file
		fclose ($pipes [0]);
		fclose ($pipes [1]);
		fclose ($pipes [2]);
		unlink ($encryptedTemp);
		
		// Close the process and return
		echo "### [MAIN] [DECRYPTOR] GPG pipe [stdout]:\n", $readStdout, "\n";
		echo "### [MAIN] [DECRYPTOR] GPG pipe [stderr]:\n", $readStderr, "\n";
		echo "### [MAIN] [DECRYPTOR] GPG proc_close [0 => success]: ", proc_close ($process), "\n";
		
		// Return the decrypted data
		return $readStdout;
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
