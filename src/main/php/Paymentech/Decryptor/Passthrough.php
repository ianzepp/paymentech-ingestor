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

class Paymentech_Decryptor_Passthrough implements Common_Decryptor
{
	/**
	 * @see Common_Decryptor::decrypt()
	 *
	 * @param string $encryptedData
	 * @return string
	 */
	public function decrypt ($encryptedData)
	{
		return $encryptedData;
	}
	
	/**
	 * @see Common_Optionable::getOption()
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getOption ($key)
	{
	}
	
	/**
	 * @see Common_Optionable::getOptions()
	 *
	 * @return array(string => mixed)
	 */
	public function getOptions ()
	{
		return array ();
	}
	
	/**
	 * @see Common_Optionable::setOption()
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function setOption ($key, $value)
	{
	}
	
	/**
	 * @see Common_Remoteable::getUri()
	 *
	 * @return Common_Uri
	 */
	public function getUri ()
	{
		return new Common_Uri ();
	}
}

