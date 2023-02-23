<?php
defined('_JEXEC') or die;
/** miniOrange enables user to log in using saml credentials.
 * Copyright (C) 2015  miniOrange
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 * @package        miniOrange OAuth
 * @license        http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

/**
 * This library is miniOrange Authentication Service.
 * Contains Request Calls to Customer service.
 **/
require_once 'MoConstants.php';

class Mo_saml_Local_Customer
{

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


    private $defaultCustomerKey = "16555";
    private $defaultApiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";

    function create_customer()
    {
        if (!Mo_saml_Local_Util::is_curl_installed()) {
            return json_encode(array("statusCode" => 'ERROR', 'statusMessage' => 'Error while creating user.' . '. Please check your configuration. Also check troubleshooting under saml configuration.'));
        }
        $hostname = Mo_saml_Local_Util::getHostname();

        $url = $hostname . '/moas/rest/customer/add';
        $ch = curl_init($url);
        $current_user = JFactory::getUser();
        $customer_details = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_customer_details');

        $this->email = $customer_details['email'];
        $this->phone = $customer_details['admin_phone'];
        $password = $customer_details['password'];
        if (!empty($password))
            $password = base64_decode($password);

        $fields = array(
            'companyName' => $_SERVER['SERVER_NAME'],
            'areaOfInterest' => 'Joomla SAML 2.0 SP SSO Plugin',
            'firstname' => $current_user->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $password
        );
        $field_string = json_encode($fields);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls

        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'charset: UTF - 8',
            'Authorization: Basic'
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__miniorange_saml_proxy_setup'));
        $query->where($db->quoteName('id') . " = 1");

        $db->setQuery($query);
        $proxy = $db->loadAssoc();

        $proxy_host_name = isset($proxy['proxy_host_name']) ? $proxy['proxy_host_name'] : '';
        $port_number = isset($proxy['port_number']) ? $proxy['port_number'] : '';
        $username = isset($proxy['username']) ? $proxy['username'] : '';
        $password = isset($proxy['password']) ? base64_decode($proxy['password']) : '';

        if (!empty($proxy_host_name)) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy_host_name);
            curl_setopt($ch, CURLOPT_PROXYPORT, $port_number);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $username . ':' . $password);
        }

        $content = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Request Error:' . curl_error($ch);
            exit();
        }


        curl_close($ch);
        return $content;
    }

    function get_customer_key($email, $password)
    {
        if (!Mo_saml_Local_Util::is_curl_installed()) {
            return json_encode(array("apiKey" => 'CURL_ERROR', 'token' => '<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
        }

        $hostname = Mo_saml_Local_Util::getHostname();

        $url = $hostname . "/moas/rest/customer/key";
        $ch = curl_init($url);

        $fields = array(
            'email' => $email,
            'password' => $password
        );

        $field_string = json_encode($fields);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls

        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'charset: UTF - 8',
            'Authorization: Basic'
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        $content = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Request Error:' . curl_error($ch);
            exit();
        }
        curl_close($ch);

        return $content;
    }


    public static function submit_feedback_form($email, $phone, $query,$cause)
    {

        $url = 'https://login.xecurify.com/moas/api/notify/send';
        $ch = curl_init($url);

        $customerKey = "16555";
        $apiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";

        $currentTimeInMillis = round(microtime(true) * 1000);
        $stringToHash = $customerKey . number_format($currentTimeInMillis, 0, '', '') . $apiKey;
        $hashValue = hash("sha512", $stringToHash);
        $customerKeyHeader = "Customer-Key: " . $customerKey;
        $timestampHeader = "Timestamp: " . number_format($currentTimeInMillis, 0, '', '');
        $authorizationHeader = "Authorization: " . $hashValue;
        $fromEmail = $email;
        

        //Get PHP Version
        $phpVersion = phpversion();
        //Get Joomla Core Version
        $jCmsVersion = SAML_Utilities::getJoomlaCmsVersion();

        //Get Installed Miniorange SAML IDP plugin version
        $moPluginVersion = SAML_Utilities::GetPluginVersion();
        $os_version    = SAML_Utilities::_get_os_info();
        $pluginName    = (new Mo_saml_Local_Customer)->pluginName($jCmsVersion);
        $result        = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_config');
        $details       = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_customer_details');
        $ad_email       = isset($details ['email']) ? $details ['email'] : '';
        $nousercreated=  base64_decode($result['userslim']);
        $noauthentication=  base64_decode($result['sso_test']);
        $SSO_URL = $result['single_signon_service_url'];
        $testConfiguration = ($result['test_configuration']==true)? 'Successful': 'Unsuccessful'; 
        $SSO_Status = ($result['sso_status']==true)? 'Successful': 'Unsuccessful'; 
        $query1 = '['.$pluginName.' | '.$moPluginVersion.' | PHP ' . $phpVersion.' | OS ' . $os_version.'] ';
        if($query=='Test Configuration')
        {
            $ccEmail='arati.chaudhari@xecurify.com'; 
            $bccEmail='somshekhar@xecurify.com';
            $content = '<div >Hello, <br><br><strong>Company :<a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" ></strong>' . $_SERVER['SERVER_NAME'] . '</a><br><br><strong>Phone Number :<strong>' . $phone . '<br><br><strong>Admin Email :<a href="mailto:' . $fromEmail . '" target="_blank">' . $fromEmail . '</a></strong><br><br><strong>Email :<a href="mailto:' . $ad_email . '" target="_blank">' . $ad_email . '</a></strong><br><br><strong>SSO URL: </strong>'.$SSO_URL.' <br><br><strong>Auto created Users:</strong>'.$nousercreated.' <br><br> <strong> No. of Authentication:</strong>'.$noauthentication.' <br><br>  <strong>Test Configuration:</strong> '.$testConfiguration .'<br><br><strong>Possible Cause:</strong> '.$cause .'<br><br><strong> System Information: </strong>' . $query1 . '</div>';
            $subject = "MiniOrange Joomla SAML SP [Free] for Efficiency";
        }
        else if($query=='SSO Status')
        {
            $ccEmail='arati.chaudhari@xecurify.com'; 
            $bccEmail='somshekhar@xecurify.com';
            $content = '<div >Hello, <br><br><strong>Company :<a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" ></strong>' . $_SERVER['SERVER_NAME'] . '</a><br><br><strong>Phone Number :</strong>' . $phone . '<br><br><strong>Admin Email :<a href="mailto:' . $fromEmail . '" target="_blank">' . $fromEmail . '</a></strong><br><br><strong>Email :<a href="mailto:' . $ad_email . '" target="_blank">' . $ad_email . '</a></strong><br><br><strong>SSO URL: </strong>'.$SSO_URL.' <br><br><strong>Auto created Users:</strong>'.$nousercreated.' <br><br> <strong> No. of Authentication:</strong>'.$noauthentication.' <br><br> <strong>SSO Status:</strong> '.$SSO_Status .'<br><br><strong>Possible Cause:</strong> '.$cause .'<br><br><strong> System Information:</strong> ' . $query1 . '</div>';
            $subject = "MiniOrange Joomla SAML SP [Free] for Efficiency";
           
        }
        else
        {
            $ccEmail='joomlasupport@xecurify.com';
            $bccEmail='joomlasupport@xecurify.com';
            $content = '<div >Hello, <br><br><strong>Company :<a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" ></strong>' . $_SERVER['SERVER_NAME'] . '</a><br><br><strong>Phone Number :</strong>' . $phone . '<br><br><strong>Admin Email :<a href="mailto:' . $fromEmail . '" target="_blank"></strong>' . $fromEmail . '</a><br><br><strong>Email :<a href="mailto:' . $ad_email . '" target="_blank"></strong>' . $ad_email . '</a><br><br><strong>Plugin Deactivated: </strong>' . $query1 . '<br><br><strong>Reason: </strong>' . $query . '<br><br><strong>Auto created Users:</strong>'.$nousercreated.' <br><br> <strong> No. of Authentication:</strong>'.$noauthentication.' <br><br> <strong>Test Configuration:</strong> '.$testConfiguration .'</div>';   
            $subject = "MiniOrange Joomla SAML SP Feedback";
        }
       
      
       
        

        $fields = array(
            'customerKey' => $customerKey,
            'sendEmail' => true,
            'email' => array(
                'customerKey' 	=> $customerKey,
                'fromEmail' 	=> $fromEmail,
                'bccEmail' 		=> $bccEmail,
                'fromName' 		=> 'miniOrange',
                'toEmail' 		=> $ccEmail,
                'toName' 		=> $bccEmail,
                'subject' 		=> $subject,
                'content' 		=> $content
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

    function submit_contact_us($q_email, $q_phone, $query)
    {
        if (!Mo_saml_Local_Util::is_curl_installed()) {
            return json_encode(array("status" => 'CURL_ERROR', 'statusMessage' => '<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
        }
       
        $hostname = Mo_saml_Local_Util::getHostname();
        $url = $hostname . "/moas/rest/customer/contact-us";
        $ch = curl_init($url);
        $current_user = JFactory::getUser();
        $phpVersion = phpversion();
        $jVersion = new JVersion;
        $jCmsVersion = $jVersion->getShortVersion();
        $moPluginVersion = Mo_saml_Local_Util::GetPluginVersion();
        $pluginName = (new Mo_saml_Local_Customer)->pluginName($jCmsVersion);
        $result = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_config');
        $nousercreated= base64_decode($result['userslim']);
        $testConfiguration = ($result['test_configuration']==true)? 'Successful': 'Unsuccessful';
        $os_version    = SAML_Utilities::_get_os_info();
        $query = '['.$pluginName.' | ' . $moPluginVersion . ' ] PHP ' . $phpVersion.' | OS '.$os_version.' | Auto created Users: ' . $nousercreated. ' | Test Configuration:'.$testConfiguration .' || Query :' . $query ;
        

        $fields = array(
            'firstName' => $current_user->username,
            'company' => $_SERVER['SERVER_NAME'],
            'email' => $q_email,
            'ccEmail' => 'joomlasupport@xecurify.com',
            'phone' => $q_phone,
            'query' => $query
        );
        $field_string = json_encode($fields);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls

        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'charset: UTF-8', 'Authorization: Basic'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__miniorange_saml_proxy_setup'));
        $query->where($db->quoteName('id') . " = 1");

        $db->setQuery($query);
        $proxy = $db->loadAssoc();

        $proxy_host_name = isset($proxy['proxy_host_name']) ? $proxy['proxy_host_name'] : '';
        $port_number = isset($proxy['port_number']) ? $proxy['port_number'] : '';
        $username = isset($proxy['username']) ? $proxy['username'] : '';
        $password = isset($proxy['password']) ? base64_decode($proxy['password']) : '';

        if (!empty($proxy_host_name)) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy_host_name);
            curl_setopt($ch, CURLOPT_PROXYPORT, $port_number);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $username . ':' . $password);
        }

        $content = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Request Error:' . curl_error($ch);
            return false;
        }
        curl_close($ch);
        return true;
    }

    function send_otp_token($auth_type, $phone)
    {

        if (!Mo_saml_Local_Util::is_curl_installed()) {
            return json_encode(array("status" => 'CURL_ERROR', 'statusMessage' => '<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
        }

        $hostname = Mo_saml_Local_Util::getHostname();
        $url = $hostname . '/moas/api/auth/challenge';
        $ch = curl_init($url);
        $customerKey = $this->defaultCustomerKey;
        $apiKey = $this->defaultApiKey;

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('email');
        $query->from($db->quoteName('#__miniorange_saml_customer_details'));
        $query->where($db->quoteName('id') . " = 1");

        $db->setQuery($query);
        $username = $db->loadResult();


        /* Current time in milliseconds since midnight, January 1, 1970 UTC. */
        $currentTimeInMillis = round(microtime(true) * 1000);

        /* Creating the Hash using SHA-512 algorithm */
        $stringToHash = $customerKey . number_format($currentTimeInMillis, 0, '', '') . $apiKey;
        $hashValue = hash("sha512", $stringToHash);

        $customerKeyHeader = "Customer-Key: " . $customerKey;
        $timestampHeader = "Timestamp: " . number_format($currentTimeInMillis, 0, '', '');
        $authorizationHeader = "Authorization: " . $hashValue;
        if ($auth_type == "EMAIL") {
            $fields = array(
                'customerKey' => $this->defaultCustomerKey,
                'email' => $username,
                'authType' => $auth_type,
                'transactionName' => 'Joomla SAML 2.0 SP SSO Plugin'
            );
        } else {
            $fields = array(
                'customerKey' => $this->defaultCustomerKey,
                'phone' => $phone,
                'authType' => $auth_type,
                'transactionName' => 'Joomla SAML 2.0 SP SSO Plugin'
            );
        }
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
            echo 'Request Error:' . curl_error($ch);
            exit();
        }
        curl_close($ch);
        return $content;
    }

    function validate_otp_token($transactionId, $otpToken)
    {
        if (!Mo_saml_Local_Util::is_curl_installed()) {
            return json_encode(array("status" => 'CURL_ERROR', 'statusMessage' => '<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
        }
        $hostname = Mo_saml_Local_Util::getHostname();
        $url = $hostname . '/moas/api/auth/validate';
        $ch = curl_init($url);

        $customerKey = $this->defaultCustomerKey;
        $apiKey = $this->defaultApiKey;

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('email');
        $query->from($db->quoteName('#__miniorange_saml_customer_details'));
        $query->where($db->quoteName('id') . " = 1");

        $db->setQuery($query);
        $username = $db->loadResult();
    

        /* Current time in milliseconds since midnight, January 1, 1970 UTC. */
        $currentTimeInMillis = round(microtime(true) * 1000);

        /* Creating the Hash using SHA-512 algorithm */
        $stringToHash = $customerKey . number_format($currentTimeInMillis, 0, '', '') . $apiKey;
        $hashValue = hash("sha512", $stringToHash);

        $customerKeyHeader = "Customer-Key: " . $customerKey;
        $timestampHeader = "Timestamp: " . number_format($currentTimeInMillis, 0, '', '');
        $authorizationHeader = "Authorization: " . $hashValue;

        $fields = '';

        //*check for otp over sms/email
        $fields = array(
            'txId' => $transactionId,
            'token' => $otpToken,
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
            echo 'Request Error:' . curl_error($ch);
            exit();
        }
        curl_close($ch);
   
        return $content;
    }

    function check_customer($email)
    {
        if (!Mo_saml_Local_Util::is_curl_installed()) {
            return json_encode(array("status" => 'CURL_ERROR', 'statusMessage' => '<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
        }
        $hostname = Mo_saml_Local_Util::getHostname();
        $url = $hostname . "/moas/rest/customer/check-if-exists";
        $ch = curl_init($url);

        $fields = array(
            'email' => $email,
        );
        $field_string = json_encode($fields);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls

        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'charset: UTF - 8', 'Authorization: Basic'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        $content = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Request Error:' . curl_error($ch);
            exit();
        }
        curl_close($ch);

        return $content;
    }

    function request_for_trial($email, $plan,$demo,$description = '')
    {
        $url = 'https://login.xecurify.com/moas/api/notify/send';
        $ch = curl_init($url);

        $customerKey = "16555";
        $apiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";

        $currentTimeInMillis = round(microtime(true) * 1000);
        $stringToHash = $customerKey . number_format($currentTimeInMillis, 0, '', '') . $apiKey;
        $hashValue = hash("sha512", $stringToHash);
        $customerKeyHeader = "Customer-Key: " . $customerKey;
        $timestampHeader = "Timestamp: " . number_format($currentTimeInMillis, 0, '', '');
        $authorizationHeader = "Authorization: " . $hashValue;
        $fromEmail = $email;
        $subject = 'MiniOrange Joomla SAML SP Request for '.$demo;

        //Get PHP Version
        $phpVersion = phpversion();
        //Get Joomla Core Version
        $jCmsVersion = SAML_Utilities::getJoomlaCmsVersion();

        //Get Installed Miniorange SAML IDP plugin version
        $moPluginVersion = SAML_Utilities::GetPluginVersion();
        $pluginName = (new Mo_saml_Local_Customer)->pluginName($jCmsVersion);

        $pluginInfo = '['.$pluginName.' | '.$moPluginVersion.' | PHP ' . $phpVersion.'] : ';

        $content = '<div >Hello, <br>
                        <br><strong>Company :</strong><a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" >' . $_SERVER['SERVER_NAME'] . '</a><br><br>
                        <strong>Email :</strong><a href="mailto:' . $fromEmail . '" target="_blank">' . $fromEmail . '</a><br><br>
                        <strong>Plugin Info: </strong>'.$pluginInfo.'<br><br>
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

    function request_for_setupCall($email, $query, $description, $callDate, $timeZone)
    {
        $url = 'https://login.xecurify.com/moas/api/notify/send';
        $ch = curl_init($url);

        $customerKey = "16555";
        $apiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";

        $currentTimeInMillis = round(microtime(true) * 1000);
        $stringToHash = $customerKey . number_format($currentTimeInMillis, 0, '', '') . $apiKey;
        $hashValue = hash("sha512", $stringToHash);
        $customerKeyHeader = "Customer-Key: " . $customerKey;
        $timestampHeader = "Timestamp: " . number_format($currentTimeInMillis, 0, '', '');
        $authorizationHeader = "Authorization: " . $hashValue;
        $fromEmail = $email;

        $subject = "MiniOrange Joomla SAML SP Free - Screen Share/Call Request";

        //Get PHP Version
        $phpVersion = phpversion();
        //Get Joomla Core Version
        $jCmsVersion = SAML_Utilities::getJoomlaCmsVersion();

        //Get Installed Miniorange SAML IDP plugin version
        $moPluginVersion = SAML_Utilities::GetPluginVersion();
        $pluginName = (new Mo_saml_Local_Customer)->pluginName($jCmsVersion);

        $pluginInfo = '['.$pluginName.' | '.$moPluginVersion.' | PHP ' . $phpVersion.'] : ';

        $content = '<div>Hello, <br><br>
                        <strong>Company :</strong><a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" >' . $_SERVER['SERVER_NAME'] . '</a><br><br>
                        <strong>Plugin Info: </strong>'.$pluginInfo.'<br><br>
                        <strong>Email :</strong><a href="mailto:' . $fromEmail . '" target="_blank">' . $fromEmail . '</a><br><br>
                        <strong>Time Zone:</strong> ' . $timeZone . '<br><br><strong>Date to set up call: </strong>' . $callDate . '<br><br>
                        <strong>Issue : </strong>' . $query . '<br><br>
                        <strong>Description:</strong> ' . $description . '</div>';

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

    function pluginName($jCmsVersion)
    {
        if(MoConstants::MO_SAML_SP == "ALL")
        {
            $pluginName = 'Joomla '.$jCmsVersion.' SAML 2.0 SP Free Plugin';
        }
        elseif(MoConstants::MO_SAML_SP == "ADFS")
        {
            $pluginName = 'Joomla '.$jCmsVersion.' SAML SP - Login with ADFS';
        }
        elseif (MoConstants::MO_SAML_SP == "GOOGLEAPPS")
        {
            $pluginName = 'Joomla '.$jCmsVersion.' SAML SP - Login with Google Apps';
        }
        return $pluginName;
    }
}
