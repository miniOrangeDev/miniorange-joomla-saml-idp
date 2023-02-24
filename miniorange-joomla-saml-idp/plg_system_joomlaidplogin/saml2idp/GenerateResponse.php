<?php

/**
 * @package    miniOrange
 * @subpackage Plugins
 * @license    GNU/GPLv3
 * @copyright  Copyright 2015 miniOrange. All Rights Reserved.
 *
 *
 * This file is part of miniOrange Joomla SAML IDP plugin.
 *
 * miniOrange Joomla SAML IDP plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * miniOrange Joomla IDP plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with miniOrange SAML plugin.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die;
	class GenerateResponse{
		
		private $xml;
		private $acsUrl;
		private $issuer;
		private $audience;
		private $username;
		private $email;
		private $name_id_attr;
		private $name_id_attr_format;
		private $inResponseTo;
		private $subject;
		private $mo_idp_assertion_signed;
		
		function __construct($email, $username, $acs_url, $issuer, $audience, $name_id_attr=null, $name_id_attr_format = null, $mo_idp_assertion_signed=null, $inResponseTo=null){
			
			$this->xml = new DOMDocument("1.0", "utf-8");
			$this->acsUrl = $acs_url;		
			$this->issuer = $issuer;		
			$this->audience = $audience;
			$this->email = $email;
			$this->username = $username;
			$this->name_id_attr = $name_id_attr;
			$this->name_id_attr_format = $name_id_attr_format;
			$this->mo_idp_assertion_signed = $mo_idp_assertion_signed;
			$this->inResponseTo = $inResponseTo;
			
		}
		
		function createSamlResponse(){
			
			$response_params = $this->getResponseParams();

			//Create Response Element
			$resp = $this->createResponseElement($response_params);
			$this->xml->appendChild($resp);
			
			//Build Issuer
			$issuer = $this->buildIssuer();
			$resp->appendChild($issuer);
			
			//Build Status
			$status = $this->buildStatus();
			$resp->appendChild($status);
			
			//Build Status Code
			$statusCode = $this->buildStatusCode();
			$status->appendChild($statusCode);
			
			//Build Assertion
			$assertion = $this->buildAssertion($response_params);
			$resp->appendChild($assertion);
			
			//Sign Assertion
			if($this->mo_idp_assertion_signed){
				$private_key = dirname(__FILE__) .  DIRECTORY_SEPARATOR . 'cert' .  DIRECTORY_SEPARATOR . 'idp-signing.key';
				$this->signNode($private_key, $assertion, $this->subject,$response_params);
			}

			$samlResponse = $this->xml->saveXML();

			return $samlResponse;								
			
		}
		
		function getResponseParams(){
			$response_params = array();
			$time = time();
			$response_params['IssueInstant'] = str_replace('+00:00','Z',gmdate("c",$time));
			$response_params['NotOnOrAfter'] = str_replace('+00:00','Z',gmdate("c",$time+300));
			$response_params['NotBefore'] = str_replace('+00:00','Z',gmdate("c",$time-30));
			$response_params['AuthnInstant'] = str_replace('+00:00','Z',gmdate("c",$time-120));
			$response_params['SessionNotOnOrAfter'] = str_replace('+00:00','Z',gmdate("c",$time+3600*8));
			$response_params['ID'] = $this->generateUniqueID(40);
			$response_params['AssertID'] = $this->generateUniqueID(40);
			$response_params['Issuer'] = $this->issuer;
			$public_key = dirname(__FILE__) .  DIRECTORY_SEPARATOR . 'cert' .  DIRECTORY_SEPARATOR. 'idp-signing.crt';
			$objKey = new XMLSecurityKeyIDP(XMLSecurityKeyIDP::RSA_SHA256,array('type' => 'public'));
			$objKey->loadKey($public_key, TRUE,TRUE);
			$response_params['x509'] = $objKey->getX509Certificate();
			return $response_params;
		}
		
		function createResponseElement($response_params){
			$resp = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:protocol','samlp:Response');
			$resp->setAttribute('ID',$response_params['ID']);
			$resp->setAttribute('Version','2.0');
			$resp->setAttribute('IssueInstant',$response_params['IssueInstant']);
			$resp->setAttribute('Destination',$this->acsUrl);
			if(isset($this->inResponseTo) && !is_null($this->inResponseTo)){
				$resp->setAttribute('InResponseTo',$this->inResponseTo);
			}
			return $resp;
		}
		
		function buildIssuer(){
			$issuer = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:assertion','saml:Issuer',$this->issuer);
			return $issuer;
		}
		
		function buildStatus(){
			$status = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:protocol','samlp:Status');
			return $status;
		}
		
		function buildStatusCode(){
			$statusCode = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:protocol','samlp:StatusCode');
			$statusCode->setAttribute('Value', 'urn:oasis:names:tc:SAML:2.0:status:Success');
			return $statusCode;
		}
		
		function buildAssertion($response_params){
			$assertion = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:assertion','saml:Assertion');
			$assertion->setAttribute('ID',$response_params['AssertID']);
			$assertion->setAttribute('IssueInstant',$response_params['IssueInstant']);
			$assertion->setAttribute('Version','2.0');
			
			//Build Issuer
			$issuer = $this->buildIssuer($response_params);
			$assertion->appendChild($issuer);

			//Build Subject
			$subject = $this->buildSubject($response_params);
			$this->subject = $subject;
			$assertion->appendChild($subject);
			
			//Build Condition
			$condition = $this->buildCondition($response_params);
			$assertion->appendChild($condition);
			
			//Build AuthnStatement
			$authnstat = $this->buildAuthnStatement($response_params);
			$assertion->appendChild($authnstat);
	
			return $assertion;
		}
		
		function buildSubject($response_params){
			$subject = $this->xml->createElement('saml:Subject');
			$nameid = $this->buildNameIdentifier();
			$subject->appendChild($nameid);
			$confirmation = $this->buildSubjectConfirmation($response_params);
			$subject->appendChild($confirmation);
			return $subject;
		}
		
		function buildNameIdentifier(){
			if($this->name_id_attr==="emailAddress")
				$nameid = $this->xml->createElement('saml:NameID',$this->email);
			else
				$nameid = $this->xml->createElement('saml:NameID',$this->username);
			if(empty($this->name_id_attr_format)) {
				$nameid->setAttribute('Format','urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress');
			} else {
				$nameid->setAttribute('Format', $this->name_id_attr_format);
			}
			$nameid->setAttribute('SPNameQualifier',$this->audience);
			return $nameid;
		}
		
		function buildSubjectConfirmation($response_params){
			$confirmation = $this->xml->createElement('saml:SubjectConfirmation');
			$confirmation->setAttribute('Method','urn:oasis:names:tc:SAML:2.0:cm:bearer');
			$confirmationdata = $this->getSubjectConfirmationData($response_params);
			$confirmation->appendChild($confirmationdata);
			return $confirmation;
		}
		
		function getSubjectConfirmationData($response_params){
			$confirmationdata = $this->xml->createElement('saml:SubjectConfirmationData');
			$confirmationdata->setAttribute('NotOnOrAfter',$response_params['NotOnOrAfter']);
			$confirmationdata->setAttribute('Recipient',$this->acsUrl);
			if(isset($this->inResponseTo) && !is_null($this->inResponseTo)){
				$confirmationdata->setAttribute('InResponseTo',$this->inResponseTo);
			}
			return $confirmationdata;
		}
		
		function buildCondition($response_params){
			$condition = $this->xml->createElement('saml:Conditions');
			$condition->setAttribute('NotBefore',$response_params['NotBefore']);
			$condition->setAttribute('NotOnOrAfter',$response_params['NotOnOrAfter']);
			
			//Build AudienceRestriction
			$audiencer = $this->buildAudienceRestriction();
			$condition->appendChild($audiencer);
			
			return $condition;
		}
		
		function buildAudienceRestriction(){
			$audiencer = $this->xml->createElement('saml:AudienceRestriction');
			$audience = $this->xml->createElement('saml:Audience',$this->audience);
			$audiencer->appendChild($audience);
			return $audiencer;
		}
		
		function buildAuthnStatement($response_params){
			$authnstat = $this->xml->createElement('saml:AuthnStatement');
			$authnstat->setAttribute('AuthnInstant',$response_params['AuthnInstant']);
			$authnstat->setAttribute('SessionIndex','_'.$this->generateUniqueID(30));
			$authnstat->setAttribute('SessionNotOnOrAfter',$response_params['SessionNotOnOrAfter']);
			
			$authncontext = $this->xml->createElement('saml:AuthnContext');
			$authncontext_ref = $this->xml->createElement('saml:AuthnContextClassRef','urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport');
			$authncontext->appendChild($authncontext_ref);
			$authnstat->appendChild($authncontext);
			
			return $authnstat;
		}
		
		function signNode($private_key, $node, $subject,$response_params){
			//Private KEY	
			$objKey = new XMLSecurityKeyIDP(XMLSecurityKeyIDP::RSA_SHA256,array( 'type' => 'private'));
			$objKey->loadKey($private_key, TRUE);
						
			//Sign the Assertion
			$objXMLSecDSig = new XMLSecurityDSigIDP();
			$objXMLSecDSig->setCanonicalMethod(XMLSecurityDSigIDP::EXC_C14N);

			$objXMLSecDSig->addReferenceList(array($node), XMLSecurityDSigIDP::SHA256,
				array('http://www.w3.org/2000/09/xmldsig#enveloped-signature', XMLSecurityDSigIDP::EXC_C14N),array('id_name'=>'ID','overwrite'=>false));
			$objXMLSecDSig->sign($objKey);
			$objXMLSecDSig->add509Cert($response_params['x509']);
			$objXMLSecDSig->insertSignature($node,$subject);
		}
	
		function generateUniqueID($length) {
			$chars = "abcdef0123456789";
			$chars_len = strlen($chars);
			$uniqueID = "";
			for ($i = 0; $i < $length; $i++)
				$uniqueID .= substr($chars,rand(0,15),1);
			return 'a'.$uniqueID;
		}
		
	}