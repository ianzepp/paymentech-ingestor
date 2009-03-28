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

class Paymentech_Translator_RPDE0017D implements Common_Translator
{
	/**
	 * @see Common_Translator::translate()
	 *
	 * @param array(value) $data
	 * @return SimpleXMLElement
	 * @throws Paymentech_Exception If the first column is not "RPDE0017D"
	 */
	public function translate ($data)
	{
		if ($data [0] != "RPDE0017D")
			throw new Paymentech_Exception ();
		
		$xml = simplexml_load_string ("<node />");
		$xml->{"recordType"} = "RPDE0017D";
		$xml->{"entityType"} = $data [1];
		$xml->{"entityNumber"} = $data [2];
		$xml->{"issuerAmount"} = empty ($data [3]) ? 0.00 : $data [3];
		$xml->{"previousPartial"} = $data [4];
		$xml->{"currency"} = $data [5];
		$xml->{"category"} = $data [6];
		$xml->{"statusFlag"} = $data [7];
		$xml->{"sequenceNumber"} = $data [8];
		$xml->{"merchantOrderNumber"} = $data [9];
		$xml->{"cardAccountNumber"} = $data [10];
		$xml->{"reasonCode"} = $data [11];
		$xml->{"transactionDate"} = $data [12];
		$xml->{"chargebackDate"} = $data [13];
		$xml->{"activityDate"} = $data [14];
		$xml->{"actionAmount"} = empty ($data [15]) ? 0.00 : $data [15];
		$xml->{"feeAmount"} = empty ($data [16]) ? 0.00 : $data [16];
		$xml->{"usageCode"} = $data [17];
		return $xml;
	}

}


