# paymentech-ingestor

Note: Project was originally written in 2008/2009, saved in Google Code until the service was shut down, and then archived to Github.

## Description

This project is an interesting combination of Zend PHP + Maven. It uses the publicly available Chase Paymentech APIs for receiving credit card chargeback and void CSV files and converts them to a usable XML structure.

In the process, it pulls in external files through Curl, Sftp, or a standard Stream Context, run a Gpg decryption against them, extracts the chargeback information from the CSV file, converts the data to an XML structure, validates the XML against a predefined XML Schema Definition, and them forwards the validated XML to a remote HTTP controller action.

This project is designed to run as a cronjob periodically, and uses Maven's support for profiles to automatically build and package a deployable bundle to different environments.

http://code.google.com/p/ianzepp/source/browse/trunk/paymentech-ingestor SVN Source Tree
