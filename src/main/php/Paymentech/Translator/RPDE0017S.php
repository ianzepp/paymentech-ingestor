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

class Paymentech_Translator_RPDE0017S implements Common_Translator
{
	/**
	 * @see Common_Translator::translate()
	 *
	 * @param array(value) $data
	 * @return SimpleXMLElement
	 * @throws Paymentech_Exception If the first column is not "RPDE0017S"
	 */
	public function translate ($data)
	{
		if ($data [0] != "RPDE0017S")
			throw new Paymentech_Exception ();
		
		$xml = simplexml_load_string ("<node />");
		$xml->{"recordType"} = "RPDE0017S";
		$xml->{"entityType"} = $data [1];
		$xml->{"entityNumber"} = $data [2];
		$xml->{"currency"} = $data [5];
		$xml->{"paymentType"} = $data [6];
		$xml->{"category"} = $data [7];
		$xml->{"financialImpact"} = $data [8];
		$xml->{"recordCount"} = $data [9];
		$xml->{"amount"} = empty ($data [10]) ? 0.00 : $data [10];
		return $xml;
	}

}

