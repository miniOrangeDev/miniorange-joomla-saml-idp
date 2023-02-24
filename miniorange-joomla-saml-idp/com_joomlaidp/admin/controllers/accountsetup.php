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

/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlaidp
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * AccountSetup Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_joomlaidp
 * @since       0.0.9
 */
class JoomlaIdpControllerAccountSetup extends JControllerForm
{
	function __construct()
	{
		$this->view_list = 'accountsetup';
		parent::__construct();
	}
	
	function customerLoginForm() {
	    $nameOfDatabase = '#__miniorange_saml_idp_customer';
        $updateFieldsArray = array(
            'login_status'  => 1,
            'password'      => '',
            'email_count'   => 0,
            'sms_count'     => 0,
        );

        IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
		$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account');
	}

	 function verifyCustomer()
	{
		$post = JFactory::getApplication()->input->post->getArray();
		if(empty($post)){
		    return;
        }
		
		$email = '';
		$password = '';
		
		if( MoSamlIdpUtility::checkEmptyOrNull( $post['email'] ) ||MoSamlIdpUtility::checkEmptyOrNull( $post['password'] ) ) {
			JFactory::getApplication()->enqueueMessage('All the fields are required. Please enter valid entries.', 'error');
			return;
		} else{
			$email =$post['email'];
			$password =  $post['password'] ;
		}
		
		$customer = new MoSamlIdpCustomer();
		$content = $customer->get_customer_key($email,$password);
		
		$customerKey = json_decode( $content, true );
		if( strcasecmp( $customerKey['apiKey'], 'CURL_ERROR') == 0) {
			$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup',$customerKey['token'],'error');
		} else if( json_last_error() == JSON_ERROR_NONE ) {
			if(isset($customerKey['id']) && isset($customerKey['apiKey']) && !empty($customerKey['id']) && !empty($customerKey['apiKey'])){
				$this->saveCustomerConfigurations($email,$customerKey['id'], $customerKey['apiKey'], $customerKey['token'],$customerKey['phone']);
				$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=license','Your account has been retrieved successfully. Now you can click on <strong>Upgrade</strong> button to upgrade to the premium plan.');
			}else{
				$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account','There was an error in fetching your details. Please try again.','error');
			}
		} else {
			$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account','Invalid username or password. Please try again.','error');
		}		
	}

	function saveServiceProvider(){

		$post = JFactory::getApplication()->input->post->getArray();
        if(!isset($post['sp_name']) && !isset($post['sp_entityid']) && !isset($post['acs_url'])){
            $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp');
            return;
        }
		$isDelete = isset($post['mo_saml_delete']) ? $post['mo_saml_delete'] : '';

		if ($isDelete == "Delete SP Configuration")
        {
            $data = new stdClass();
            $data->id = 1;
            $data->sp_name = '';
            $data->sp_entityid = '';
            $data->acs_url = '';
            $data->nameid_format = '';
            $data->nameid_attribute = '';
            $data->default_relay_state = '';
            $data->assertion_signed = 0;
            $data->enabled = 0;

            $db = JFactory::getDBO();
            $db->updateObject( '#__miniorangesamlidp', $data, 'id', true);

            $message = 'Service Provider Configurations has been deleted successfully.';
            $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp', $message );
        }
		else{
            $spName = isset($post['sp_name']) ? $post['sp_name'] : '';
            $issuer = isset($post['sp_entityid']) ? $post['sp_entityid'] : '';
            $acsUrl = isset($post['acs_url']) ? $post['acs_url'] : '';
            $nameIdFormat = isset($post['nameid_format']) ? $post['nameid_format'] : '';
            $defaultRelayState = isset($post['default_relay_state']) ? $post['default_relay_state'] : '';
            $assertionSigned = isset($post['assertion_signed']) ? isset($post['assertion_signed']) : 0;
            if(empty($spName) || empty($issuer) || empty($acsUrl) || empty($nameIdFormat)){
                $message = 'Please enter the values in all required fields.<br />Required Fields: Service Provider Name, SP Entity ID or Issuer, ACS URL, NameID Format.';
                $this->setRedirect('index.php?option=com_joomlaidp&view=samlidpsettings',  $message,'error');
                return FALSE;
            }

            $spName = strtolower(trim($spName));
            $issuer = trim($issuer);
            $acsUrl = trim($acsUrl);

            $data = new stdClass();
            $data->id = 1;
            $data->sp_name = $spName;
            $data->sp_entityid = $issuer;
            $data->acs_url = $acsUrl;
            $data->nameid_format = $nameIdFormat;
            $data->nameid_attribute = empty($post['nameid_attribute']) ? 'emailAddress' : $post['nameid_attribute'];
            $data->default_relay_state = $defaultRelayState;
            $data->assertion_signed = $assertionSigned;
            $data->enabled = TRUE;

            $db = JFactory::getDBO();
            $db->updateObject( '#__miniorangesamlidp', $data, 'id', true);

            IDP_Utilities::isValidCheck($spName, $acsUrl,'Save Details','');
            $message = 'Service Provider (' . $spName . ') Configurations are saved successfully. Please click on <a href="index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp&test-config=true" class="mo_boot_btn mo_boot_btn-saml">Test Configuration</a> button to proceed futher.';
            $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp', $message );
        }
	}

	

	function handleUploadMetadata(){

		require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'MetadataReader.php';
		$post = JFactory::getApplication()->input->post->getArray();
        if(count($post) == 0){
            $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp');
            return;
        }
        $file = JFactory::getApplication()->input->files->getArray();

        if ( !isset($post['sp_upload_name']) || empty($post['sp_upload_name'])) {
        	$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp',  'No Service Provider Name Provided.','error');
        	return;
        }

        $sp_name = $post['sp_upload_name'];

        if (isset($file['metadata_file']) || isset($post['metadata_url'])) {
            if(!empty($file['metadata_file']['tmp_name'])) {
                $file = @file_get_contents( $file['metadata_file']['tmp_name']);
            }
            else {
                $url = filter_var($post['metadata_url'],FILTER_SANITIZE_URL);
                $arrContextOptions=array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                );
                if(empty($url)) {
                    $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp',  'Invalid metadata File/URL Provided.','error');
                    return;
                }
                else {
                    $file = file_get_contents($url, false, stream_context_create($arrContextOptions));
                }
            }
            $this->uploadMetadata($file, $sp_name);
        }
	}

	function uploadMetadata($file, $sp_name){

		$post = JFactory::getApplication()->input->post->getArray();
        if(count($post) == 0){
            $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp');
            return;
        }
		$document = new DOMDocument();
        $document->loadXML( $file );
        restore_error_handler();
        $first_child = $document->firstChild;

        if( !empty( $first_child ) ) {
            $metadata = new MetadataReader($document);
            $service_providers = $metadata->getServiceProviders();
            if( empty( $service_providers ) ) {
                $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp',  'Please provide valid metadata.','error');
                return;
            }
            foreach( $service_providers as $key => $sp ) {
                $issuer = $sp->getEntityID();
                $acs_url = $sp->getAcsURL();
                $is_assertion_signed = $sp->getAssertionsSigned() == 'true' ? TRUE : FALSE;
            }
            $data = new stdClass();
			$data->id = 1;
			$data->sp_name = $sp_name;
			$data->sp_entityid = $issuer;
			$data->acs_url = $acs_url;
			$data->nameid_format = empty($post['nameid_format']) ? 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified' : $post['nameid_format'];
        
			$data->nameid_attribute = empty($post['nameid_attribute']) ? 'emailAddress' : $post['nameid_attribute'];
			$data->assertion_signed = $is_assertion_signed;
			$data->enabled = TRUE;
		
		    $db = JFactory::getDBO();
		    $db->updateObject( '#__miniorangesamlidp', $data, 'id', true);
            IDP_Utilities::isValidCheck($sp_name, $acs_url,'Save Details','');
		    $message = 'Service Provider (' . $sp_name . ') Configurations are saved successfully. Please click on <a href="index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp&test-config=true" class="mo_boot_btn mo_boot_btn-saml">Test Configuration</a> button to proceed futher.';
		    $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp', $message );
            return;
        }
        else {
        	$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp',  '<b>Please provide a valid metadata URL/file.</b>','error');
        	return;
        }
	}

	function updateIdpEntityId()
	{
		$post = JFactory::getApplication()->input->post->getArray();
        if(count($post) == 0){
            $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=idp');
            return;
        }
		$newIdp = $post['mo_saml_idp_entity_id'];

        $nameOfDatabase = '#__miniorange_saml_idp_customer';
        $updateFieldsArray = array(
            'idp_entity_id'  => $newIdp,
        );

        IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
		$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=idp','IDP EntityID / Issuer Updated Successfully.');
	}
	
    function requestForDemoPlan()
    {
        $post = JFactory::getApplication()->input->post->getArray();
        if ((!isset($post['email'])) || (!isset($post['plan'])) || (!isset($post['description']))) {
            $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp');
            return;
        }
        $email = $post['email'];
        $plan = $post['plan'];
        $description = trim($post['description']);
        $demo = $post['demo'];

        if (!isset($plan) || empty($description)) {
            $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp', 'All the fields are required. Please enter valid entries.', 'error');
            return;
        }

        $customer = new MoSamlIdpCustomer();
        $response = json_decode($customer->request_for_trial($email, $plan, $demo, $description));

        if ($response->status != 'ERROR')
            $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp', 'We have recieved your demo request. Someone from our team will contact you shortly regarding the next steps.');
        else {
            $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp', 'Server is busy. Please try again later.', 'error');
            return;
        }

    }
	
	function saveCustomerConfigurations($email, $id, $apiKey, $token, $phone) {
        $databaseName = '#__miniorange_saml_idp_customer';
        $updateFieldsArray = array(
            'email'               => $email,
            'customer_key'        => $id,
            'api_key'             => $apiKey,
            'customer_token'      => $token,
            'admin_phone'         => $phone,
            'login_status'        => 0,
            'registration_status' => 'SUCCESS',
            'password'            => '',
            'email_count'         => 0,
            'sms_count'           => 0,
        );
        IDP_Utilities::updateDatabaseQuery($databaseName, $updateFieldsArray);
	}

    function saveAdminMail()
    {
        $post=	JFactory::getApplication()->input->post->getArray();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('email') . ' = '.$db->quote($post['admin_email']),

        );

        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__miniorange_saml_idp_customer'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $result = $db->execute();
        $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp','Email successfully changed');
        return;
    }
	
	function registerCustomer(){
		//validate and sanitize
		$email = '';
		$phone = '';
		$password = '';
		$confirmPassword = '';
		$post = JFactory::getApplication()->input->post->getArray();
		if( MoSamlIdpUtility::checkEmptyOrNull( $post['email'] ) || MoSamlIdpUtility::checkEmptyOrNull( $post['password'] ) || MoSamlIdpUtility::checkEmptyOrNull( $post['confirmPassword'] ) ) {
			$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account',  'All the fields are required. Please enter valid entries.','error');
			return;
		} else if( strlen($post['password'] ) < 6 || strlen( $post['confirmPassword'] ) < 6){	//check password is of minimum length 6
			$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account',  'Choose a password with minimum length 6.','error');
			return;
		} else{			
			$email = $post['email'];
			$email = strtolower($email);
			$phone = $post['phone'];
			$password =$post['password'];
			$confirmPassword = $post['confirmPassword'];
		}	

		if( strcmp( $password, $confirmPassword) == 0 ) {
            $nameOfDatabase = '#__miniorange_saml_idp_customer';
            $updateFieldsArray = array(
                'email'       => $email,
                'admin_phone' => $phone,
                'password'    => $password,
            );
            IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);

			$customer = new MoSamlIdpCustomer();
			$content = json_decode($customer->check_customer($email), true);
			if( strcasecmp( $content['status'], 'CUSTOMER_NOT_FOUND') == 0 ){
				$auth_type = 'EMAIL';

				$content = json_decode($customer->send_otp_token($auth_type, $email), true);
				if(strcasecmp($content['status'], 'SUCCESS') == 0) {
                    $nameOfDatabase = '#__miniorange_saml_idp_customer';
                    $updateFieldsArray = array(
                        'email_count'       => 1,
                        'transaction_id' => $content['txId'],
                        'login_status'    => 0,
                        'registration_status'    => 'MO_IDP_OTP_DELIVERED_SUCCESS',
                    );
                    IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
					$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account', 'A One Time Passcode has been sent to <b>' . $email . '</b>. Please enter the OTP below to verify your email. ');
				} else {
                    $nameOfDatabase = '#__miniorange_saml_idp_customer';
                    $updateFieldsArray = array(
                        'login_status'    => 0,
                        'registration_status'    => 'MO_IDP_OTP_DELIVERED_FAILURE',
                    );
                    IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
					$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account', 'There was an error in sending email. Please click on Resend OTP to try again. ','error');
				}
			} else if( strcasecmp( $content['status'], 'CURL_ERROR') == 0 ){
                $nameOfDatabase = '#__miniorange_saml_idp_customer';
                $updateFieldsArray = array(
                    'login_status'    => 0,
                    'registration_status'    => 'MO_IDP_OTP_DELIVERED_FAILURE',
                );
                IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
                $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account', $content['statusMessage'],'error');
				
			} else{
				$content = $customer->get_customer_key($email,$password);
				$customerKey = json_decode($content, true);
				if(json_last_error() == JSON_ERROR_NONE) {
					$this->saveCustomerConfigurations($email,$customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['phone']);
					$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=license', 'Your account has been retrieved successfully. Now you can click on <strong>Upgrade</strong> button to upgrade to the premium plan.');
				}elseif (strcasecmp( $content['status'], 'TRANSACTION_LIMIT_EXCEEDED') != 0) {
					$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account', 'An error occured. Please try after sometime or contact us at <a href="mailto:joomlasupport@xecurify.com">joomlasupport@xecurify.com</a>','error');
				}
				else {
				    $nameOfDatabase = '#__miniorange_saml_idp_customer';
				    $updateFieldsArray = array(
				        'login_status'        => 0,
                        'registration_status' => '',
                    );
				    IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
				    $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account', 'You already have an account with miniOrange. Please enter a valid password. ','error');
				}
			}
		} else {
            $nameOfDatabase = '#__miniorange_saml_idp_customer';
            $updateFieldsArray = array(
                'login_status'        => 0,
            );
            IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
            $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account', 'Password and Confirm password do not match.','error');
		}
	}
	
	function validateOtp(){
	    //validation and sanitization
		$post = JFactory::getApplication()->input->post->getArray();
        if(count($post) == 0){
            $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup');
            return;
        }
		$otp_token = '';
		if( MoSamlIdpUtility::checkEmptyOrNull( $post['otp_token'] ) ) {
			$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup', 'Please enter a valid OTP.','error');
			return;
		} else{
			$otp_token =  trim($post['otp_token']) ;
		}

        $transaction_id = IDP_Utilities::fetchDatabaseValues('#__miniorange_saml_idp_customer', 'loadResult', 'transaction_id');
		$customer = new MoSamlIdpCustomer();
		$content = json_decode($customer->validate_otp_token($transaction_id, $otp_token ),true);
		if(strcasecmp($content['status'], 'SUCCESS') == 0) {
			$customerKey = json_decode($customer->create_customer(), true);

            $nameOfDatabase = '#__miniorange_saml_idp_customer';
            $updateFieldsArray = array(
                'email_count'        => 0,
                'sms_count'        => 0,
            );
            IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);

            $customerResult = IDP_Utilities::fetchDatabaseValues('#__miniorange_saml_idp_customer', 'loadAssoc', array('*'));
            $userEmail = isset($customerResult['email']) ? $customerResult['email'] : '';
            $userPassword = isset($customerResult['password']) ? $customerResult['password'] : '';

            if(strcasecmp($customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0) {	//admin already exists in miniOrange
				$content = $customer->get_customer_key($userEmail, $userPassword);
				$customerKey = json_decode($content, true);
				if(json_last_error() == JSON_ERROR_NONE) {
					$this->saveCustomerConfigurations($customerKey['email'], $customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['phone']);
					$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=license','Your account has been retrieved successfully. Now you can click on <strong>Upgrade</strong> button to upgrade to the premium plan.');
				} else {
                    $nameOfDatabase = '#__miniorange_saml_idp_customer';
                    $updateFieldsArray = array(
                        'login_status'        => 1,
                        'password'        => '',
                    );
                    IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
					$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup', 'You already have an account with miniOrange. Please enter a valid password.','error');
				}
			} else if(strcasecmp($customerKey['status'], 'SUCCESS') == 0) {
				$phone = isset($customerKey['phone'])?$customerKey['phone']:'';
				//registration successful
				$this->saveCustomerConfigurations($customerKey['email'], $customerKey['id'], $customerKey['apiKey'], $customerKey['token'],$phone);
				$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=license','Your account has been successfully created. To upgrade your plugin, click the upgrade button.');
			}else if(strcasecmp($customerKey['status'], 'INVALID_EMAIL_QUICK_EMAIL') == 0){
                $nameOfDatabase = '#__miniorange_saml_idp_customer';
                $updateFieldsArray = array(
                    'registration_status'        => '',
                    'email'        => '',
                    'password'        => '',
                    'transaction_id'        => '',
                );
                IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
                $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account',
                    'There was an error creating an account for you. You may have entered an invalid Email-Id.(We discourage the use of disposable emails) Please try again with a valid email.', 'error');
			}
			
		} else if( strcasecmp( $content['status'], 'CURL_ERROR') == 0) {
            $nameOfDatabase = '#__miniorange_saml_idp_customer';
            $updateFieldsArray = array(
                'registration_status'        => 'MO_IDP_OTP_VALIDATION_FAILURE',
            );
            IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
			$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account', $content['statusMessage'],'error');
		} else {
            $nameOfDatabase = '#__miniorange_saml_idp_customer';
            $updateFieldsArray = array(
                'registration_status'        => 'MO_IDP_OTP_VALIDATION_FAILURE',
            );
            IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
			$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account','Invalid one time passcode. Please enter a valid OTP.','error');
		}
	} 
	
	function resendOtp(){
		$customer = new MoSamlIdpCustomer();
		$auth_type = 'EMAIL';
		$email = IDP_Utilities::fetchDatabaseValues('#__miniorange_saml_idp_customer', 'loadResult', 'email');

		$content = json_decode($customer->send_otp_token($auth_type, $email), true);
		if(strcasecmp($content['status'], 'SUCCESS') == 0) {
		    $customer_details = MoSamlIdpUtility::getCustomerDetails();
		    $email_count = $customer_details['email_count'];
		    $admin_email = $customer_details['email'];

		    if($email_count != '' && $email_count >= 1){
		        $email_count = $email_count + 1;
		        $nameOfDatabase = '#__miniorange_saml_idp_customer';
		        $updateFieldsArray = array(
		            'email_count'         => $email_count,
                    'transaction_id'      => $content['txId'],
                    'registration_status' => 'MO_IDP_OTP_DELIVERED_SUCCESS',
                );
		        IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
		        $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account', 'Another One Time Passcode has been sent <b>( ' .$email_count .' )</b> to <b>' . ( $admin_email) . '</b>. Please enter the OTP below to verify your email.');
		    }else{
		        $nameOfDatabase = '#__miniorange_saml_idp_customer';
		        $updateFieldsArray = array(
		            'email_count'         => 1,
                    'transaction_id'      => $content['txId'],
                    'registration_status' => 'MO_IDP_OTP_DELIVERED_SUCCESS',
                );
		        IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
		        $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account',  'An OTP has been sent to <b>' . ($admin_email) . '</b>. Please enter the OTP below to verify your email.');
		    }
		} else if( strcasecmp( $content['status'], 'CURL_ERROR') == 0) {
            $nameOfDatabase = '#__miniorange_saml_idp_customer';
            $updateFieldsArray = array(
                'registration_status' => 'MO_IDP_OTP_DELIVERED_FAILURE',
            );
            IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
			$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account',  $content['statusMessage'],'error');
			
		} else{
            $nameOfDatabase = '#__miniorange_saml_idp_customer';
            $updateFieldsArray = array(
                'registration_status' => 'MO_IDP_OTP_DELIVERED_FAILURE',
            );
            IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);
			$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account',  'There was an error in sending email. Please click on Resend OTP to try again.','error');
		}
	}

	function cancelForm(){
	    $databaseName = '#__miniorange_saml_idp_customer';
        $updateFieldsArray = array(
            'email'               => '',
            'password'            => '',
            'customer_key'        => '',
            'api_key'             => '',
            'customer_token'      => '',
            'admin_phone'         => '',
            'login_status'        => 0,
            'registration_status' => '',
            'transaction_id'      => '',
            'email_count'         => 0,
            'sms_count'           => 0,
        );
        IDP_Utilities::updateDatabaseQuery($databaseName, $updateFieldsArray);
		$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account');
	}
	
	function phoneVerification(){

		$post = JFactory::getApplication()->input->post->getArray();

        if(count($post) == 0){
            $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account');
            return;
        }
		$phone = $post['phone_number'];
		$phone = str_replace(' ', '', $phone);
		
		$pattern = "/[\+][0-9]{1,3}[0-9]{10}/";					
		
		if(preg_match($pattern, $phone, $matches, PREG_OFFSET_CAPTURE)){
			$auth_type = 'SMS';
			$customer = new MoSamlIdpCustomer();
			$send_otp_response = json_decode($customer->send_otp_token($auth_type, $phone));
			if($send_otp_response->status == 'SUCCESS'){

			    $sms_count = IDP_Utilities::fetchDatabaseValues('#__miniorange_saml_idp_customer', 'loadResult', 'sms_count');

                if($sms_count != '' && $sms_count >= 1){
					$sms_count = $sms_count + 1;

					$nameOfDatabase = '#__miniorange_saml_idp_customer';
                    $updateFieldsArray = array(
                        'sms_count'      => $sms_count,
                        'transaction_id' => $send_otp_response->txId,
                    );
                    IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);

					$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account', 'Another One Time Passcode has been sent <b>(' . $sms_count . ')</b> for verification to ' . $phone);
				} else{
				    $nameOfDatabase = '#__miniorange_saml_idp_customer';
                    $updateFieldsArray = array(
                        'sms_count'      => 1,
                        'transaction_id' => $send_otp_response->txId,
                    );
                    IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);

					$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account', 'A One Time Passcode has been sent for verification to ' . $phone);
				}
			} else{
				$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account', 'An error occurred while sending OTP to phone. Please try again.');
			}
		}else{
			$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account', 'Please enter the phone number in the following format: <b>+##country code## ##phone number##','error');
		}
	}
	
	 function forgotPassword(){
		$post = JFactory::getApplication()->input->post->getArray();
         if(count($post) == 0){
             $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account');
             return;
         }
		$admin_email = $post['current_admin_email'];
		
		if(MoSamlIdpUtility::checkEmptyOrNull( $admin_email )){
			$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account','Please enter your registered email id and then click on Forgot Password button.','error');
			return;
		}
	
		$customer = new MoSamlIdpCustomer();
		$forgot_password_response = json_decode($customer->mo_saml_idp_forgot_password($admin_email));
		if($forgot_password_response->status == 'SUCCESS'){
			$message = 'You password has been reset successfully. A new password has been sent to your registered mail.';
			$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account', $message);
			
		} else {
			$this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account', $forgot_password_response->message, 'error');
		}
	}


	function contactUs(){
        $post = JFactory::getApplication()->input->post->getArray();
        if( MoSamlIdpUtility::checkEmptyOrNull( $post['mo_saml_query_email'] ) || MoSamlIdpUtility::checkEmptyOrNull( trim($post['mo_saml_query_email'])) ) {
            $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup', 'Please submit your query with email.', 'error');
            return;
        } else{
            $query = $post['mo_saml_query'];
            $email = $post['mo_saml_query_email'];
            $phone = $post['mo_saml_query_phone'];

            if(isset($post['mo_saml_select_plan']) && !empty($post['mo_saml_select_plan'] && $post['mo_saml_select_plan'] != 'none')
                || isset($post['number_of_users']) && !empty($post['number_of_users']))
            {
                $number_users = isset($post['number_of_users']) ? $post['number_of_users'] : '';
                if(empty($number_users)){
                    $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup', 'Please enter the number of users.', 'error');
                    return;
                }
                $plan_name = $post['mo_saml_select_plan'];
                $query = "Plan Name : ".$plan_name.", Users : ".$number_users.' '.$query;
            }

            $contact_us = new MoSamlIdpCustomer();
            $submited = json_decode($contact_us->submit_contact_us($email, $phone, $query),true);
            if(json_last_error() == JSON_ERROR_NONE) {
                if(is_array($submited) && array_key_exists('status', $submited) && $submited['status'] == 'ERROR'){
                    $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup', $submited['message'],'error');
                }else{
                    if ( $submited == false ) {
                        $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp', 'Your query could not be submitted. Please try again.','error');
                    } else {
                        $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp', 'Thanks for getting in touch! We will get back to you shortly.');
                    }
                }
            }
        }
    }

    function removeAccount()
    {
        $nameOfDatabase = '#__miniorange_saml_idp_customer';
        $updateFieldsArray = array(
            'email'               => '',
            'password'            => '',
            'customer_key'        => '',
            'api_key'             => '',
            'customer_token'      => '',
            'admin_phone'         => '',
            'login_status'        => 0,
            'registration_status' => 'SUCCESS',
            'email_count'         => 0,
            'sms_count'           => 0,
        );
        IDP_Utilities::updateDatabaseQuery($nameOfDatabase, $updateFieldsArray);

        $this->setRedirect('index.php?option=com_joomlaidp&view=accountsetup&tab-panel=account', 'Your account has been removed successfully.');
    }
}