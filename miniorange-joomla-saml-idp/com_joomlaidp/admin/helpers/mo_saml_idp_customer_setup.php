<?php
/*/**
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

/** miniOrange enables user to log in using saml credentials.
    Copyright (C) 2015  miniOrange

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>
* @package 		miniOrange OAuth
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/
/**
This library is miniOrange Authentication Service.
Contains Request Calls to Customer service.

**/
defined('_JEXEC') or die;
require_once 'MoIDPConstants.php';
class MoSamlIdpCustomer{

	public $email;
	public $phone;
	public $customerKey;
	public $transactionId;

	/*
	** Initial values are hardcoded to support the miniOrange framework to generate OTP for email.
	** We need the default value for creating the OTP the first time,
	** As we don't have the Default keys available before registering the user to our server.
	** This default values are only required for sending an One Time Passcode at the user provided email address.
	*/

	//auth
	private $defaultCustomerKey = "16555";
	private $defaultApiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";

	function create_customer(){

		if(!MoSamlIdpUtility::is_curl_installed()) {
			return json_encode(array("statusCode"=>'ERROR','statusMessage'=>'Error while creating user.' . '. Please check your configuration. Also check troubleshooting under saml configuration.'));
		}

		$url = MoIDPConstants::MO_HOSTNAME . '/moas/rest/customer/add';
		$ch = curl_init($url);
		$current_user =  JFactory::getUser();
		$customer_details = MoSamlIdpUtility::getCustomerDetails();

		$this->email = $customer_details['email'];
		$this->phone = $customer_details['admin_phone'];
		$password = $customer_details['password'];

		$fields = array(
			'companyName' => $_SERVER['SERVER_NAME'],
			'areaOfInterest' => 'JOOMLA IDP Plugin',
			'firstname' => $current_user->name,
			'lastname' => '',
			'email' => $this->email,
			'phone' => $this->phone,
			'password' => $password
		);
		$field_string = json_encode($fields);

		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'charset: UTF - 8',
			'Authorization: Basic'
			));
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
		$content = curl_exec($ch);

		if(curl_errno($ch)){
			echo 'Request Error:' . curl_error($ch);
		   exit();
		}

		curl_close($ch);
		return $content;
	}

	function get_customer_key($email, $password) {

		if(!MoSamlIdpUtility::is_curl_installed()) {
			return json_encode(array("apiKey"=>'CURL_ERROR','token'=>'<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
		}

		$url = MoIDPConstants::MO_HOSTNAME. "/moas/rest/customer/key";
		$ch = curl_init($url);

		$fields = array(
			'email' => $email,
			'password' => $password
		);
		$field_string = json_encode($fields);

		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'charset: UTF - 8',
			'Authorization: Basic'
			));
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
		$content = curl_exec($ch);
		if(curl_errno($ch)){
			echo 'Request Error:' . curl_error($ch);
		   exit();
		}
		curl_close($ch);

		return $content;
	}

	public static function submit_feedback_form($email,$phone,$query)
	{

        $url =  MoIDPConstants::MO_HOSTNAME . '/moas/api/notify/send';
        $customerKey = MoIDPConstants::MO_CUSTOMER_KEY;
        $apiKey = MoIDPConstants::MO_APIKEY;
        $ch = curl_init($url);

		$jConfig = new JConfig();
        $adEmail = $jConfig->mailfrom;

        $currentTimeInMillis = round(microtime(true) * 1000);
        $stringToHash 		 = $customerKey .  number_format($currentTimeInMillis, 0, '', '') . $apiKey;
        $hashValue 			 = hash("sha512", $stringToHash);
        $customerKeyHeader 	 = "Customer-Key: " . $customerKey;
        $timestampHeader 	 = "Timestamp: " .  number_format($currentTimeInMillis, 0, '', '');
        $authorizationHeader = "Authorization: " . $hashValue;
        $fromEmail 			 = !empty($email)?$email:$adEmail;
        $subject             = "MiniOrange Joomla Feedback for SAML IDP ";

        //Get PHP Version
        $phpVersion = phpversion();

        //Get Joomla Core Version
        $jVersion   = new JVersion;
        $jCmsVersion = $jVersion->getShortVersion();

        //Get Installed Miniorange SAML IDP plugin version
        $moPluginVersion = IDP_Utilities::GetPluginVersion();
		//get OS informaion
		$OS = IDP_Utilities:: getOSInfo();

        $query1 = '[Joomla '.$jCmsVersion.' SAML IDP Free Plugin | '.$moPluginVersion.' | PHP ' . $phpVersion.' | OS : '.$OS.']';

        $content = '<div >Hello, <br><br>
                        <b>Company :</b><a href="'.$_SERVER['SERVER_NAME'].'" target="_blank" >'.$_SERVER['SERVER_NAME'].'</a><br><br>
                        <b>Phone Number :</b>'.$phone.'<br><br>
                        <b>Email: </b><a href="mailto:'.$fromEmail.'" target="_blank">'.$fromEmail.'</a><br><br>
                        <b>Plugin Deactivated: </b>'.$query1. '<br><br>
                        <b>Reason: </b>' .$query. '</div>';

        $fields = array(
            'customerKey'	=> $customerKey,
            'sendEmail' 	=> true,
            'email' 		=> array(
                'customerKey' 	=> $customerKey,
                'fromEmail' 	=> $fromEmail,
                'fromName' 		=> 'miniOrange',
                'toEmail' 		=> 'joomlasupport@xecurify.com',
                'toName' 		=> 'joomlasupport@xecurify.com',
                'subject' 		=> $subject,
                'content' 		=> $content
            ),
        );
        $field_string = json_encode($fields);


        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader, $timestampHeader, $authorizationHeader));
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
        $content = curl_exec($ch);

        if(curl_errno($ch)){
            return json_encode(array("status"=>'ERROR','statusMessage'=>curl_error($ch)));
        }
        curl_close($ch);

        return ($content);

	}

    function request_for_demo($email, $plan, $description,$callDate,$timeZone)
	{
		$url =  MoIDPConstants::MO_HOSTNAME . '/moas/api/notify/send';
        $ch = curl_init($url);

        $customerKey = MoIDPConstants::MO_CUSTOMER_KEY;
        $apiKey = MoIDPConstants::MO_APIKEY;

        $currentTimeInMillis= round(microtime(true) * 1000);
        $stringToHash 		= $customerKey .  number_format($currentTimeInMillis, 0, '', '') . $apiKey;
        $hashValue 			= hash("sha512", $stringToHash);
        $customerKeyHeader 	= "Customer-Key: " . $customerKey;
        $timestampHeader 	= "Timestamp: " .  number_format($currentTimeInMillis, 0, '', '');
        $authorizationHeader= "Authorization: " . $hashValue;
        $fromEmail 			= $email;

        //Get PHP Version
        $phpVersion = phpversion();

        //Get Joomla Core Version
        $jVersion   = new JVersion;
        $jCmsVersion = $jVersion->getShortVersion();

        //Get Installed Miniorange SAML IDP plugin version
        $moPluginVersion = IDP_Utilities::GetPluginVersion();

        $subject = '[Joomla '.$jCmsVersion.' SAML IDP Free Plugin - Screen Share/Call Request| '.$moPluginVersion.' | PHP ' . $phpVersion.'] : ';

        $content='<div>Hello, <br><br>
                        <b>Company : </b><a href="'.$_SERVER['SERVER_NAME'].'" target="_blank" >'.$_SERVER['SERVER_NAME'].'</a><br><br>
                        <b>Email : </b><a href="mailto:'.$fromEmail.'" target="_blank">'.$fromEmail.'</a><br><br>
                        <b>Time Zone: </b>'.$timeZone. '<br><br>
                        <b>Date to set up call : </b>' .$callDate. '<br><br>
                        <b>Issue : </b>' .$plan. '<br><br>
                        <b>Description: </b>'.$description. '</div>';

        $fields = array(
            'customerKey'	=> $customerKey,
            'sendEmail' 	=> true,
            'email' 		=> array(
                'customerKey' 	=> $customerKey,
                'fromEmail' 	=> $fromEmail,                
                'fromName' 		=> 'miniOrange',
                'toEmail' 		=> 'joomlasupport@xecurify.com',
                'toName' 		=> 'joomlasupport@xecurify.com',
                'subject' 		=> $subject,
                'content' 		=> $content
            ),
		);
        $field_string = json_encode($fields);


        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader,
            $timestampHeader, $authorizationHeader));
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
        $content = curl_exec($ch);

        if(curl_errno($ch)){
            return json_encode(array("status"=>'ERROR','statusMessage'=>curl_error($ch)));
        }
        curl_close($ch);

        return ($content);
	}


	function submit_contact_us( $q_email, $q_phone, $query ) {

		if(!MoSamlIdpUtility::is_curl_installed()) {
			return json_encode(array("status"=>'CURL_ERROR','statusMessage'=>'<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
		}
		$url =  MoIDPConstants::MO_HOSTNAME . "/moas/rest/customer/contact-us";
		$ch = curl_init($url);
		$current_user =  JFactory::getUser();


        //Get PHP Version
        $phpVersion = phpversion();

        //Get Joomla Core Version
        $jVersion   = new JVersion;
        $jCmsVersion = $jVersion->getShortVersion();

        //Get Installed Miniorange SAML IDP plugin version
        $moPluginVersion = IDP_Utilities::GetPluginVersion();
		$os_version    =  IDP_Utilities::_get_os_info();
        $query = '[Joomla '.$jCmsVersion.' SAML IDP Free Plugin | '.$moPluginVersion.' ] | PHP ' . $phpVersion.' | OS '.$os_version.' | Query '. $query;

		$fields = array(
			'firstName'			=> $current_user->username,
			'lastName'	 		=> '',
			'company' 			=> $_SERVER['SERVER_NAME'],
			'ccEmail' 			=> 'joomlasupport@xecurify.com',
			'email' 			=> $q_email,
			'phone'				=> $q_phone,
			'query'				=> $query,
			'subject' 			=> "JOOMLA IDP Free Plugin Query",
		);
		$field_string = json_encode( $fields );

		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'charset: UTF-8', 'Authorization: Basic' ) );
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
		$content = curl_exec( $ch );

		if(curl_errno($ch)){
			echo 'Request Error:' . curl_error($ch);
		   return false;
		}
		curl_close($ch);

		return true;
	}

	function send_otp_token($auth_type, $emailOrPhone){

		if(!MoSamlIdpUtility::is_curl_installed()) {
			return json_encode(array("status"=>'CURL_ERROR','statusMessage'=>'<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
		}

		$url =  MoIDPConstants::MO_HOSTNAME . '/moas/api/auth/challenge';
		$ch = curl_init($url);
		$customerKey =  $this->defaultCustomerKey;
		$apiKey =  $this->defaultApiKey;

		/* Current time in milliseconds since midnight, January 1, 1970 UTC. */
		$currentTimeInMillis = round(microtime(true) * 1000);

		/* Creating the Hash using SHA-512 algorithm */
		$stringToHash = $customerKey .  number_format($currentTimeInMillis, 0, '', '') . $apiKey;
		$hashValue = hash("sha512", $stringToHash);

		$customerKeyHeader = "Customer-Key: " . $customerKey;
		$timestampHeader = "Timestamp: " .  number_format($currentTimeInMillis, 0, '', '');
		$authorizationHeader = "Authorization: " . $hashValue;
		if($auth_type=="EMAIL")
		{
			$fields = array(
				'customerKey' => $this->defaultCustomerKey,
				'email' => $emailOrPhone,
				'authType' => $auth_type,
				'transactionName' => 'JOOMLA IDP Plugin'
			);
		}
		else{
			$fields = array(
				'customerKey' => $this->defaultCustomerKey,
				'phone' => $emailOrPhone,
				'authType' => $auth_type,
				'transactionName' => 'JOOMLA IDP Plugin'
			);
		}

		$field_string = json_encode($fields);

		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader,
											$timestampHeader, $authorizationHeader));
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
		$content = curl_exec($ch);

		if(curl_errno($ch)){
			echo 'Request Error:' . curl_error($ch);
		   exit();
		}
		curl_close($ch);
		return $content;
	}

	function validate_otp_token($transactionId,$otpToken){
		if(!MoSamlIdpUtility::is_curl_installed()) {
			return json_encode(array("status"=>'CURL_ERROR','statusMessage'=>'<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
		}
		$url = MoIDPConstants::MO_HOSTNAME . '/moas/api/auth/validate';
		$ch = curl_init($url);

		$customerKey =  $this->defaultCustomerKey;
		$apiKey =  $this->defaultApiKey;

		/* Current time in milliseconds since midnight, January 1, 1970 UTC. */
		$currentTimeInMillis = round(microtime(true) * 1000);

		/* Creating the Hash using SHA-512 algorithm */
		$stringToHash = $customerKey .  number_format($currentTimeInMillis, 0, '', '') . $apiKey;
		$hashValue = hash("sha512", $stringToHash);

		$customerKeyHeader = "Customer-Key: " . $customerKey;
		$timestampHeader = "Timestamp: " .  number_format($currentTimeInMillis, 0, '', '');
		$authorizationHeader = "Authorization: " . $hashValue;

		//*check for otp over sms/email
        $fields = array(
            'txId' => $transactionId,
            'token' => $otpToken,
        );

		$field_string = json_encode($fields);

		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader,
											$timestampHeader, $authorizationHeader));
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
		$content = curl_exec($ch);

		if(curl_errno($ch)){
			echo 'Request Error:' . curl_error($ch);
		   exit();
		}
		curl_close($ch);
		return $content;
	}

	function check_customer($email) {

	    if(!MoSamlIdpUtility::is_curl_installed()) {
			return json_encode(array("status"=>'CURL_ERROR','statusMessage'=>'<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
		}
		$url = MoIDPConstants::MO_HOSTNAME . "/moas/rest/customer/check-if-exists";
		$ch 	= curl_init( $url );

		$fields = array(
			'email' 	=> $email,
		);
		$field_string = json_encode( $fields );

		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'charset: UTF - 8', 'Authorization: Basic' ) );
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
		$content = curl_exec( $ch );
		if( curl_errno( $ch ) ){
			echo 'Request Error:' . curl_error( $ch );
			exit();
		}
		curl_close( $ch );

		return $content;
	}

	function mo_saml_idp_forgot_password($email)
    {

		$url = MoIDPConstants::MO_HOSTNAME . '/moas/rest/customer/password-reset';
		$ch = curl_init($url);

		$fields = '';

		//*check for otp over sms/email
		$fields = array(
			'email' => $email
		);

		$field_string = json_encode($fields);

		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'charset: UTF - 8', 'Authorization: Basic' ) );
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt( $ch, CURLOPT_TIMEOUT, 20);
		$content = curl_exec($ch);

		if( curl_errno( $ch ) ){
			echo 'Request Error:' . curl_error( $ch );
			exit();
		}

		curl_close( $ch );
		return $content;
	}

    public static function isVal($email, $spName, $acsUrl, $baseURL, $crntTime,$task,$error)
    {
        $url = MoIDPConstants::MO_HOSTNAME . '/moas/api/notify/send';
        $ch = curl_init($url);

        $customerKey = MoIDPConstants::MO_CUSTOMER_KEY;
        $apiKey = MoIDPConstants::MO_APIKEY;

        $currentTimeInMillis= round(microtime(true) * 1000);
        $stringToHash 		= $customerKey .  number_format($currentTimeInMillis, 0, '', '') . $apiKey;
        $hashValue 			= hash("sha512", $stringToHash);
        $customerKeyHeader 	= "Customer-Key: " . $customerKey;
        $timestampHeader 	= "Timestamp: " .  number_format($currentTimeInMillis, 0, '', '');
        $authorizationHeader= "Authorization: " . $hashValue;
        $fromEmail 			= $email;
        $subject            = "Joomla SAML IDP Free plugin check";

        //Get PHP Version
        $phpVersion = phpversion();

        //Get Joomla Core Version
        $jVersion   = new JVersion;
        $jCmsVersion = $jVersion->getShortVersion();

        //Get Installed Miniorange SAML IDP plugin version
        $moPluginVersion = IDP_Utilities::GetPluginVersion();

		//get OS informaion
		$OS = IDP_Utilities:: getOSInfo();

        $query = '[Joomla '.$jCmsVersion.' SAML IDP Free Plugin | '.$moPluginVersion.' | PHP ' . $phpVersion.' | OS : '.$OS.']';

        $content='Hello, <br><br>
                    <strong>Plugin: </strong>'.$query. '<br><br>
                    <strong>Company: </strong><a href="'.$_SERVER['SERVER_NAME'].'" target="_blank" >'.$_SERVER['SERVER_NAME'].'</a><br><br>
                    <strong>SP Name: </strong>'.$spName.'<br><br>
                    <strong>ACS URL: </strong>'.$acsUrl.'<br><br>
                    <strong>Email: </strong><a href="mailto:'.$fromEmail.'" target="_blank">'.$fromEmail.'</a><br><br>
                    <strong>Website: </strong>' .$baseURL. '<br><br>
                    <strong>Date: </strong>'.$crntTime.'<br><br>
					<strong>Task: </strong>'.$task.'<br><br>';

		if($task=='SSO')
		{
			$content.=' <strong>Error: </strong>'.$error. '<br><br>';
		}

        $fields = array(
            'customerKey'	=> $customerKey,
            'sendEmail' 	=> true,
            'email' 		=> array(
                'customerKey' 	=> $customerKey,
                'fromEmail' 	=> $fromEmail,
                'fromName' 		=> 'miniOrange',
                'toEmail' 		=> 'somshekhar@xecurify.com',
                'toName' 		=> 'somshekhar@xecurify.com',
				'bccEmail'		=> 'arati.chaudhari@xecurify.com',
                'subject' 		=> $subject,
                'content' 		=> $content
            ),
        );
        $field_string = json_encode($fields);

        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader,
            $timestampHeader, $authorizationHeader));
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
        curl_exec($ch);

        if(curl_errno($ch)){

            return;
        }
        curl_close($ch);
        return;
    }

	function request_for_trial($email, $plan,$demo,$description = '')
    {
		$url = MoIDPConstants::MO_HOSTNAME . '/moas/api/notify/send';
        $ch = curl_init($url);

        $customerKey = MoIDPConstants::MO_CUSTOMER_KEY;
        $apiKey = MoIDPConstants::MO_APIKEY;

        $currentTimeInMillis= round(microtime(true) * 1000);
        $stringToHash 		= $customerKey .  number_format($currentTimeInMillis, 0, '', '') . $apiKey;
        $hashValue 			= hash("sha512", $stringToHash);
        $customerKeyHeader 	= "Customer-Key: " . $customerKey;
        $timestampHeader 	= "Timestamp: " .  number_format($currentTimeInMillis, 0, '', '');
        $authorizationHeader= "Authorization: " . $hashValue;
        $fromEmail 			= $email;
        $subject            = "Joomla SAML IDP Demo/Trial Request";

        //Get PHP Version
        $phpVersion = phpversion();

        //Get Joomla Core Version
        $jVersion   = new JVersion;
        $jCmsVersion = $jVersion->getShortVersion();

        //Get Installed Miniorange SAML IDP plugin version
        $moPluginVersion = IDP_Utilities::GetPluginVersion();

		//get OS informaion
		$OS = IDP_Utilities:: getOSInfo();

        $pluginInfo = '[Joomla '.$jCmsVersion.' SAML IDP Free Plugin | '.$moPluginVersion.' | PHP ' . $phpVersion.' | OS : '.$OS.']';

        $content = '<div >Hello, <br>
                        <br><strong>Company :</strong><a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" >' . $_SERVER['SERVER_NAME'] . '</a><br><br>
                        <strong>Email :</strong><a href="mailto:' . $fromEmail . '" target="_blank">' . $fromEmail . '</a><br><br>
                        <strong>Plugin Info: </strong>'.$pluginInfo.'<br><br>
                        <strong>'.$demo. ':</strong> ' . $plan . '<br><br>
                        <strong>Description: </strong>' . $description . '</div>';

        $fields = array(
            'customerKey' => $customerKey,
            'sendEmail' => true,
            'email' => array(
                'customerKey' => $customerKey,
                'fromEmail' => $fromEmail,
                'fromName' => 'miniOrange',
                'toEmail' => 'joomlasupport@xecurify.com',
                'toName' => 'joomlasupport@xecurify.com',
                'subject' => $subject,
                'content' => $content
            ),
        );
        $field_string = json_encode($fields);


        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls

        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader,
            $timestampHeader, $authorizationHeader));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        $content = curl_exec($ch);
        if (curl_errno($ch)) {
            return json_encode(array("status" => 'ERROR', 'statusMessage' => curl_error($ch)));
        }
        curl_close($ch);

        return ($content);
    }
}