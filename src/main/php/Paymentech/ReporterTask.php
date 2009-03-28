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

class Paymentech_ReporterTask implements Common_Task
{
	const XML_NS = "http://chasepaymentech.net/netconnect";
	const XML_ROOT = "<chargebackReport xmlns='http://chasepaymentech.net/netconnect' />";
	
	private $decryptor;
	private $ingestor;
	private $validator;
	private $overrides = array ();
	
	/**
	 * @see Common_Task::execute()
	 *
	 * @return mixed
	 */
	public function execute ()
	{
		// Process ingestion
		echo "### [REPORTER] Starting ingestion process...\n";
		$data = $this->getIngestor ()->ingest ();
		
		// Process decryption
		echo "### [REPORTER] Starting decryption process...\n";
		$data = $this->getDecryptor ()->decrypt ($data);
		
		echo "### [REPORTER] Decrypted data:\n\n";
		echo $data, "\n";
		
		// Where do we put the temp data?
		$tempdir = $this->getDecryptor ()->getOption ("tempdir");
		
		if ($tempdir)
		{
			$tempfile = tempnam ($tempdir, md5 ($data));
		}
		else
		{
			$tempfile = tempnam (sys_get_temp_dir (), md5 ($data));
		}
		
		// Put the decrypted data into a temp file, open a resource
		file_put_contents ($tempfile, $data);
		$resource = fopen ($tempfile, "r");
		
		// Create the master XML node
		$root = simplexml_load_string (self::XML_ROOT);
		
		// Ignore the first header line
		fgetcsv ($resource);
		
		// Process the relevant data
		$this->executeHPDE0017 ($root, $resource);
		$this->executeRPDE0017S ($root, $resource);
		$this->executeRPDE0017D ($root, $resource);
		$this->applyOverrides ($root);
		
		// Cleanup & export the final XML as a string
		echo "### [REPORTER] Cleaning up temporary files...\n";
		fclose ($resource);
		unlink ($tempfile);
		$data = $root->asXML ();
		
		// Process validation
		echo "### [REPORTER] Validating XML response...\n";
		$this->getValidator ()->validate ($data);
		
		// Finished!
		return $data;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param SimpleXMLElement $root
	 * @param resource $resource
	 */
	private function executeHPDE0017 (SimpleXMLElement $root, $resource)
	{
		echo "### [REPORTER] Translating HPDE0017 data...\n";
		$translator = new Paymentech_Translator_HPDE0017 ();
		$this->executeTranslator ($root, fgetcsv ($resource), $translator);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param SimpleXMLElement $root
	 * @param resource $resource
	 */
	private function executeRPDE0017S (SimpleXMLElement $root, $resource)
	{
		echo "### [REPORTER] Translating RPDE0017S data...\n";
		$translator = new Paymentech_Translator_RPDE0017S ();
		$node = $root->addChild ("summary");
		$this->executeTranslator ($node, fgetcsv ($resource), $translator);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param SimpleXMLElement $root
	 * @param resource $resource
	 */
	private function executeRPDE0017D (SimpleXMLElement $root, $resource)
	{
		echo "### [REPORTER] Translating RPDE0017D data...\n";
		$translator = new Paymentech_Translator_RPDE0017D ();
		
		while (($delimitedData = fgetcsv ($resource)))
		{
			$node = $root->addChild ("record");
			$this->executeTranslator ($node, $delimitedData, $translator);
		}
	}
	
	/**
	 * Enter description here...
	 *
	 * @param SimpleXMLElement $root
	 */
	private function applyOverrides (SimpleXMLElement $root)
	{
		echo "### [REPORTER] Overrides are not currently enabled...\n";
		return;
		
		echo "### [REPORTER] Evaluating overrides...\n";
		$root->registerXPathNamespace ("xmlns", self::XML_NS);
		
		foreach ($this->getOverrides () as $xpath => $replacement )
		{
			if (empty ($xpath))
				continue;
			
			echo "### [REPORTER] Applying override: '{$xpath}' => '{$replacement}'\n";
			
			foreach ($root->xpath ($xpath) as $node )
			{
				$node = $replacement; // TODO will this even do anything? [No, it doesn't]
			}
		}
	}
	
	/**
	 * Enter description here...
	 *
	 * @param SimpleXMLElement $node
	 * @param array $delimitedData
	 * @param Common_Translator $translator
	 */
	private function executeTranslator (SimpleXMLElement $node, array $delimitedData, Common_Translator $translator)
	{
		foreach ($translator->translate ($delimitedData)->children () as $name => $value )
		{
			$node->addChild ($name, (string) $value);
		}
	}
	
	/**
	 * @return Common_Decryptor
	 */
	public function getDecryptor ()
	{
		return $this->decryptor;
	}
	
	/**
	 * @return Common_Ingestor
	 */
	public function getIngestor ()
	{
		return $this->ingestor;
	}
	
	/**
	 * @return Common_Validator
	 */
	public function getValidator ()
	{
		return $this->validator;
	}
	
	/**
	 * @param Common_Decryptor $decryptor
	 */
	public function setDecryptor (Common_Decryptor $decryptor)
	{
		$this->decryptor = $decryptor;
	}
	
	/**
	 * @param Common_Ingestor $ingestor
	 */
	public function setIngestor (Common_Ingestor $ingestor)
	{
		$this->ingestor = $ingestor;
	}
	
	/**
	 * @param Common_Validator $validator
	 */
	public function setValidator (Common_Validator $validator)
	{
		$this->validator = $validator;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $xpath
	 * @param string $replacement
	 */
	public function addOverride ($xpath, $replacement)
	{
		$this->overrides [$xpath] = $replacement;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return array
	 */
	public function getOverrides ()
	{
		return $this->overrides;
	}
}
