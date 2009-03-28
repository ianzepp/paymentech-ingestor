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

require_once "Zend/Loader.php"

/** Register the autoloader */
Zend_Loader::registerAutoload ();

/** Command line arguments */
global $argv;

/** Define option injection method */
function inject_options ($instance, $config, $prefix)
{
	$matches = array ();
	
	foreach ($config as $name => $data )
	{
		// Cast values
		$data = (string) $data;
		
		if (preg_match ('/^\d+$/', $data))
		{
			$data = intval ($data);
		}
		else if (strtolower ($data) == 'true')
		{
			$data = true;
		}
		else if (strtolower ($data) == 'false')
		{
			$data = false;
		}
		
		// Route values
		$name = (string) $name;
		$matches = array ();
		
		if (preg_match ("/^{$prefix}\\.uri\\.(.+)$/", $name, $matches))
		{
			$method = 'set' . ucfirst ($matches [1]);
			
			if ($instance instanceof Common_Uri)
			{
				echo "### [MAIN] Common_Uri->{$method}('{$data}')\n";
				$instance->$method ($data);
			}
			else if (method_exists ($instance, "getUri"))
			{
				echo "### [MAIN] ", get_class ($instance), "->getUri()->{$method}('{$data}')\n";
				$instance->getUri ()->$method ($data);
			}
		}
		else if (preg_match ("/^{$prefix}\\.(.+)$/", $name, $matches))
		{
			if (method_exists ($instance, "setOption"))
			{
				echo "### [MAIN] ", get_class ($instance), "->setOption('{$matches [1]}', '{$data}')\n";
				$instance->setOption ($matches [1], $data);
			}
		}
	}
}

/** Start output buffering */
if (!in_array ("--debug", $argv))
{
	ob_start ();
}

echo "### [MAIN] Started\n";

/** Process command line arguments */
if (in_array ("--help", $argv))
{
	echo "### [HELP] Displaying available command-line options:\n";
	echo "\n";
	echo "General options:\n";
	echo "\n";
	echo "\t--help		Prints this help message.\n";
	echo "\t--debug		Disables output buffering so trace messages are visible.\n";
	echo "\n";
	echo "Options for receiving incoming Paymentech Void reports:\n";
	echo "\n";
	echo "\t--ingestor.class <classname>\n";
	echo "\t\t Common_Order_Paymentech_Ingestor_Stream (i.e.: file_get_contents)\n";
	echo "\t\t Common_Order_Paymentech_Ingestor_Curl\n";
	echo "\t\t Common_Order_Paymentech_Ingestor_Sftp\n";
	echo "\t--ingestor.uri.protocol <file|ftp|ftps|http|https|sftp>\n";
	echo "\t--ingestor.uri.host <hostname>\n";
	echo "\t--ingestor.uri.port <port>\n";
	echo "\t--ingestor.uri.user <user>\n";
	echo "\t--ingestor.uri.password <password>\n";
	echo "\t--ingestor.uri.path <path>\n";
	echo "\t--ingestor.verbose <true|false>\n";
	echo "\n";
	echo "\tCURL specific options:\n";
	echo "\n";
	echo "\t--ingestor.CURLOPT_CONNECTTIMEOUT <seconds>\n";
	echo "\t--ingestor.CURLOPT_SSL_VERIFYHOST <true|false>\n";
	echo "\t--ingestor.CURLOPT_SSL_VERIFYPEER <true|false>\n";
	echo "\t--ingestor.CURLOPT_VERBOSE <true|false>\n";
	echo "\n";
	echo "\tSFTP specific options:\n";
	echo "\n";
	echo "\t--ingestor.key.public.path <path>\n";
	echo "\t--ingestor.key.public.fingerprint <hash>\n";
	echo "\t--ingestor.key.private.path <path>\n";
	echo "\t--ingestor.key.private.password <password>\n";
	echo "\n";
	echo "Options for decrypting received reports:\n";
	echo "\n";
	echo "\t--decryptor.class <classname>\n";
	echo "\t\t Common_Order_Paymentech_Decryptor_Gpg\n";
	echo "\t\t Common_Order_Paymentech_Decryptor_Passthrough\n";
	echo "\t--decryptor.uri.path <path> Such as /usr/bin/gpg or gpg.exe\n";
	echo "\t--decryptor.uri.password <password>\n";
	echo "\n";
	echo "Options for overriding chargeback report data with preset values:\n";
	echo "\n";
	echo "\t--override <xpath=value>\t(NOT CURRENTLY ENABLED)\n";
	echo "\n";
	echo "Options for publishing the post-processed chargeback report XML:\n";
	echo "\n";
	echo "\t--publisher.uri.host <host>\n";
	echo "\t--publisher.uri.path <path>\n";
	echo "\t--publisher.debug <level 0-3>\n";
	echo "\n";
	echo "Options for logging the execution output:\n";
	echo "\n";
	echo "\t--logging.uri.protocol <file|http>\n";
	echo "\t--logging.uri.host <host>\n";
	echo "\t--logging.uri.path <path>\n";
	echo "\t--logging.rotated <true|false>\n";
	echo "\t--logging.alerts.enabled <true|false>\n";
	echo "\t--logging.alerts.recipient <user@host.com>\n";
	echo "\n";
	echo "Additional configuration options:\n";
	echo "\n";
	echo "\t--config <path> Load additional configuration from this file.\n";
	echo "\n";
	echo "### [HELP] [IMPORTANT] All of these options can be permanently defined in the 'profiles.xml' file prior to ";
	echo "building a distribution package, overridden during the build process from the command line using ";
	echo "the mavan -D flag (for example: mvn package -Dingestor.uri.user=SuperCoolUser), or updated after ";
	echo "the build process by modifing the XML configuration values in 'Paymentech/Profile/Config.xml'.\n";
	echo "\n";
	exit ("### [MAIN] [FINISHED] Displayed help.\n");
}

/** Pull in the ingestor config */
$configPath = "Paymentech/Profile/Config.xml";
$configSource = file_get_contents ($configPath, FILE_USE_INCLUDE_PATH);
$config = simplexml_load_string ($configSource);

echo "### [CONFIG] Loading configuration XML from:\n\n";
echo $configPath, " (with FILE_USE_INCLUDE_PATH)\n\n";

/** Process command-line arguments */
array_shift ($argv);

while (count ($argv))
{
	$property = strtolower (array_shift ($argv));
	$property = preg_replace ("/^--/", "", $property);
	
	if ($property == 'debug')
		continue;
	
	$value = array_shift ($argv);
	
	if (preg_match ("/^--/", $value))
	{
		echo "### [CONFIG] [FATAL] Mismatched command line arguments: ";
		echo "Expected property value for property '{$property}', but received ";
		echo "the start of an unexpected property '{$value}' instead. Quitting.\n";
		exit ("### [MAIN] [FINISHED] With fatal configuration error.\n");
	}
	else if ($property == "override")
	{
		echo "### [CONFIG] Appending a new XPath override using '{$value}'\n";
		$config->{"overrides"}->addChild ("override", $value);
	}
	else if ($property == "config")
	{
		echo "### [CONFIG] Updating base config using '{$value}'...\n";
		
		try
		{
			$overrideSource = file_get_contents ($value, FILE_USE_INCLUDE_PATH);
			$overrideXml = simplexml_load_string ($overrideSource);
		}
		catch (Exception $e)
		{
			echo "### [CONFIG] Unable to load file: " . $e->getMessage ();
			continue;
		}
		
		foreach ($overrideXml as $overrideName => $overrideData )
		{
			echo "### [CONFIG] Replacing property '{$property}' value of ";
			echo "'" . $config->{$property} . "' with new value '{$overrideData}'\n";
			$config->{$property} = $overrideData;
		}
	
	}
	else
	{
		echo "### [CONFIG] Replacing property '{$property}' value of ";
		echo "'" . $config->{$property} . "' with new value '{$value}'\n";
		$config->{$property} = $value;
	}
}

echo "### [CONFIG] Load complete, using final configuration:\n";
echo $config->asXML (), "\n";

/** Set the assertation options */
assert_options (ASSERT_ACTIVE, true);
assert_options (ASSERT_BAIL, false);
assert_options (ASSERT_WARNING, true);

try
{
	echo "### [MAIN] [EXECUTING]\n";
	
	/** Create the ingestor */
	echo "### [MAIN] Creating Ingestor\n";
	$ingestorClass = (string) $config->{"ingestor.class"};
	$ingestor = new $ingestorClass ();
	inject_options ($ingestor, $config, "ingestor");
	
	/** Create the decryptor */
	echo "### [MAIN] Creating Decryptor\n";
	$decryptorClass = (string) $config->{"decryptor.class"};
	$decryptor = new $decryptorClass ();
	inject_options ($decryptor, $config, "decryptor");
	
	/** Create the validator */
	echo "### [MAIN] Creating Validator\n";
	$validatorClass = (string) $config->{"validator.class"};
	$validator = new $validatorClass ();
	inject_options ($validator, $config, "validator");
	
	/** Create the chargeback reporter task */
	echo "### [MAIN] Creating Chargeback Reporter\n";
	$reporter = new Paymentech_ReporterTask ();
	$reporter->setDecryptor ($decryptor);
	$reporter->setValidator ($validator);
	$reporter->setIngestor ($ingestor);
	
	/** 
	 * Add in the overrides:
	 * 
	 * Note: namespace definitions in the config file must be prefixed with a 'xmlns' qualifier,
	 * for example as in the following definition:
	 * 
	 * <overrides>
	 * 		<override>/xmlns:record/currency=USD</override>
	 * </overrides/
	 * 
	 * Note #2: This doesn't currently work, due to shortcomings with SimpleXmlElement
	 */
	foreach ($config->{"overrides"}->{"override"} as $override => $value )
	{
		$value = (string) $value;
		echo "### [MAIN] Adding override: '{$value}'... ";
		list ($xpath, $replacement) = explode ("=", $value);
		$reporter->addOverride ($xpath, $replacement);
		echo "ok\n";
	}
	
	/** Execute */
	echo "### [MAIN] [EXECUTE] Object creation and initialization complete.\n";
	echo "### [MAIN] [EXECUTE] Running ...\n";
	
	$chargebackReport = $reporter->execute ();
	
	echo "\n";
	echo "### [MAIN] [EXECUTE] Created chargeback report XML:\n\n";
	echo $chargebackReport, "\n\n";
	
	/** Extract outbound publishing uri */
	echo "### [MAIN] Creating Publisher\n";
	$publisherUri = new Common_Uri ();
	inject_options ($publisherUri, $config, "publisher");
	$publisherProtocol = $publisherUri->getProtocol ();
	
	/** Create the publisher */
	echo "### [MAIN] [PUBLISHING] Sending chargeback report ...\n";
	echo "### [MAIN] [PUBLISHING] Publishing to: ", $publisherUri, "\n";
	
	if ($publisherProtocol == 'http' || $publisherProtocol == 'https')
	{
		$publisher = new Zend_Http_Client ((string) $publisherUri);
		$publisher->setParameterPost ("_debug", (string) $config ["publisher.debug"]);
		$publisher->setParameterPost ("voidRequest", $chargebackReport);
		
		$publisherResponse = $publisher->request (Zend_Http_Client::POST);
		
		echo "### [MAIN] [PUBLISHING] Complete (using {$publisherProtocol} + post):\n";
		echo $publisherResponse->getHeadersAsString (), "\n\n";
		echo $publisherResponse->getBody (), "\n";
	}
	elseif ($publisherProtocol == 'file' || empty ($publisherProtocol))
	{
		file_put_contents ((string) $publisherUri, $chargebackReport);
		echo "### [MAIN] [PUBLISHING] Complete (using file)\n";
	}

}
catch (Exception $exception)
{
	echo "### [MAIN] [EXCEPTION] Caught exception: ";
	echo print_r ($exception, true);
	echo "\n";
	echo "With configuration: ";
	echo print_r ($config, true);
	echo "\n";
	echo "With reporter: ";
	echo isset ($reporter) ? print_r ($reporter, true) : "null\n";
	echo "\n";
	echo "With publisher: ";
	echo isset ($publisher) ? print_r ($publisher, true) : "null\n";
	echo "\n";
	echo "### [MAIN] [END OF EXCEPTION REPORT]\n";
	
	// If alerts are enabled, email a copy of the exception report
	if ((string) $config->{"logging.alerts.enabled"} == 'true')
	{
		$recipient = (string) $config->{"logging.alerts.recipient"};
		mail ($recipient, "Exception Report: " . __FILE__, ob_get_contents ());
	}
}

/** Done: publish log file */
inject_options (($loggingUri = new Common_Uri ()), $config, "logging");

if ((string) $config->{"logging.rotated"} == 'true')
{
	$loggingUri->setPath ($loggingUri->getPath () . "." . time ());
}

file_put_contents ((string) $loggingUri, ob_get_clean ());

/** Really Done! */
