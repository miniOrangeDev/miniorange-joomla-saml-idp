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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('miniorangejoomlaidpplugin.utility.IDP_Utilities');
jimport('joomla.application.component.controller');
include_once 'saml2idp/AuthnRequest.php';
include_once 'saml2idp/GenerateResponse.php';

/**
 * miniOrange Joomla IDP plugin
 */
class plgSystemJoomlaidplogin extends JPlugin
{
    public function onAfterInitialise()
    {
        $post = JFactory::getApplication()->input->post->getArray();
        if (isset($post['mojsp_feedback']) || isset($post['mojsp_skip_feedback']) ) {
            $radio = $post['deactivate_plugin']??'';
            $data = $post['query_feedback']??'';
            $feedback_email = isset($post['feedback_email']) ? $post['feedback_email'] : '';

            $database_name = '#__miniorange_saml_idp_customer';
            $updatefieldsarray = array(
                'uninstall_feedback'  => 1,
            );

            IDP_Utilities::updateDatabaseQuery($database_name, $updatefieldsarray);
            $customerResult = IDP_Utilities::fetchDatabaseValues('#__miniorange_saml_idp_customer', 'loadAssoc', array('*'));

            $admin_email = (isset($customerResult['email']) && !empty($customerResult['email'])) ? $customerResult['email'] : $feedback_email;
            $admin_phone = $customerResult['admin_phone'];
            $data1 = $radio . ' : ' . $data;
            if(isset($post['mojsp_skip_feedback']))
            {
                $data1='Skipped the feedback';
            }
           
            require_once JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_joomlaidp' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_saml_idp_customer_setup.php';
            MoSamlIdpCustomer::submit_feedback_form($admin_email, $admin_phone, $data1);
            require_once JPATH_SITE . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Installer' . DIRECTORY_SEPARATOR . 'Installer.php';

            foreach ($post['result'] as $fbkey) {
                $result = IDP_Utilities::fetchDatabaseValues('#__extensions', 'loadColumn','type', 'extension_id', $fbkey);
                $identifier = $fbkey;

                $type = 0;
                foreach ($result as $results) {
                    $type = $results;
                }

                if ($type) {
                    $cid = 0;
                    $installer = new JInstaller();
                    $installer->uninstall($type, $identifier);
                }
            }
        }

        if (array_key_exists('SAMLRequest', $_REQUEST) && !empty($_REQUEST['SAMLRequest'])) {  // To fetch SAML request from SP
            $get = JFactory::getApplication()->input->get->getArray();
            $this->_read_saml_request($_REQUEST, $get);
        } elseif (array_key_exists('option', $_REQUEST) && $_REQUEST['option'] === 'com_idpinitiatedlogin') { // Test Configuration
            $val = IDP_Utilities::fetchDatabaseValues('#__miniorangesamlidp', 'loadAssoc','*');

            $relay_state = isset($val['default_relay_state']) && !empty($val['default_relay_state']) ? $val['default_relay_state'] : '';
            $issuer = $_REQUEST['issuer'];
            $acs = $_REQUEST['acs'];

            if (empty($issuer) || empty($acs)) {
                $this->setRedirect('index.php?option=com_joomlaidp&view=samlidpsettings', 'Please provide your Service Provider Detials and then click on Test Configuration button.', 'error');
                return;
            }

            $row = IDP_Utilities::fetchDatabaseValues('#__miniorangesamlidp', 'loadAssoc','*');

            if (count($row) < 1) {
                $this->setRedirect('index.php?option=com_joomlaidp&view=samlidpsettings', 'Please provide your Service Provider Detials and then click on Test Configuration button.', 'error');
                return;
            }

            $sp_name = $row['sp_name'];
            if (empty($sp_name)) {
                $this->setRedirect('index.php?option=com_joomlaidp&view=samlidpsettings', 'Please provide your Service Provider Detials and then click on Test Configuration button.', 'error');
                return;
            }
            $this->mo_idp_authorize_user($row, $acs, $issuer, $relay_state);
        }
    }

    function onExtensionBeforeUninstall($id)
    {
        $post = JFactory::getApplication()->input->post->getArray();
        IDP_Utilities::_invoke_feedback_form($post, $id);
    }

    private function _read_saml_request($REQUEST, $GET)
    {
        $samlRequest = $REQUEST['SAMLRequest'];
        $relayState = '';
        if (array_key_exists('RelayState', $REQUEST)) {
            $relayState = $REQUEST['RelayState'];
        }

        if($relayState === '' || empty($relayState))
        {
            $val = IDP_Utilities::fetchDatabaseValues('#__miniorangesamlidp', 'loadAssoc','*');
            $relayState = isset($val['default_relay_state']) && !empty($val['default_relay_state']) ? $val['default_relay_state'] : '';
        }

        $samlRequest = base64_decode($samlRequest);
        if (array_key_exists('SAMLRequest', $GET) && !empty($GET['SAMLRequest'])) {
            $samlRequest = gzinflate($samlRequest);
        }

        $document = new DOMDocument();
        $document->loadXML($samlRequest);
        $samlRequestXML = $document->firstChild;

        $authnRequest = new AuthnRequest($samlRequestXML);

        $errors = '';
        if (strtotime($authnRequest->getIssueInstant()) > (time() + 60))
            $errors .= '<strong>INVALID_REQUEST: </strong>Request time is greater than the current time.<br/>';
        if ($authnRequest->getVersion() !== '2.0')
            $errors .= 'We only support SAML 2.0! Please send a SAML 2.0 request.<br/>';

        $row = IDP_Utilities::fetchDatabaseValues('#__miniorangesamlidp', 'loadAssoc','*');

        $acs_url = isset($row['acs_url']) ? $row['acs_url'] : '';
        $sp_issuer = isset($row['sp_entityid']) ? $row['sp_entityid'] : '';
        $acs_url_from_request = $authnRequest->getAssertionConsumerServiceURL();
        $sp_issuer_from_request = $authnRequest->getIssuer();
        $spName= isset($row['sp_name']) ? $row['sp_name'] : '';
        if (empty($acs_url) || empty($sp_issuer)) {
            $errors .= 'Invalid Issuer: Service Provider is not configured.';
            $cause = 'Issuer should not be empty: '.$sp_issuer_from_request;
        } else {
            if (!empty($acs_url_from_request) && strcmp($acs_url, $acs_url_from_request) !== 0) {
                $errors .= 'Invalid ACS URL!. ';
                $cause='ACS URL should be: '.$acs_url_from_request;
            }
            if (strcmp($sp_issuer, $sp_issuer_from_request) !== 0) {
                $errors .= 'Invalid Issuer! ';
                $cause='Issuer should be: '.$sp_issuer_from_request;
            }
        }

        $inResponseTo = $authnRequest->getRequestID();  // sending inresponeTo parameter with the SAML response
   
        if (empty($errors)) {
            ?>
            <div style="vertical-align:center;text-align:center;width:100%;font-size:25px;background-color:white;">
                <h3>PROCESSING...PLEASE WAIT!</h3>
            </div>
            <?php

            IDP_Utilities::isValidCheck($spName, $acs_url,'SSO','No');
            $this->mo_idp_authorize_user($row, $acs_url, $sp_issuer_from_request, $relayState, $inResponseTo);
        } else {
          IDP_Utilities::isValidCheck($spName, $acs_url,'SSO',$errors);
          IDP_Utilities::showErrorMessage($errors,$cause);
        exit;
        }
    }

    private function mo_idp_authorize_user($row, $acs_url, $audience, $relayState, $inResponseTo = null)
    {
        $user = JFactory::getUser();
        if (!$user->guest) {
            $this->mo_idp_send_reponse($row, $acs_url, $audience, $relayState, $inResponseTo);
        } else {
            $saml_response_params = array('moIdpsendResponse' => "true", "acs_url" => $acs_url, "audience" => $audience, "relayState" => $relayState, "inResponseTo" => $inResponseTo);
            setcookie("response_params", json_encode($saml_response_params), time() + 86400, '/');
            $redirect_url = JURI::base() . "index.php?option=com_users&view=login";
            $app = JFactory::getApplication();
            $app->redirect($redirect_url);
        }

    }

    private function mo_idp_send_reponse($row, $acs_url, $audience, $relayState, $inResponseTo)
    {
        $current_user = JFactory::getUser();
        $email = $current_user->email;
        $username = $current_user->username;
        $issuer = JURI::root() . 'plugins/user/miniorangejoomlaidp/';

        $idpid = IDP_Utilities::fetchDatabaseValues('#__miniorange_saml_idp_customer', 'loadResult','idp_entity_id');

        if (!empty($idpid) && ($issuer != $idpid))
            $issuer = $idpid;

        $nameid_attribute = $row['nameid_attribute'] == '' ? 'emailAddress' : $row['nameid_attribute'];
        $nameid_format = $row['nameid_format'];
        $assertion_signed = $row['assertion_signed'];

        $saml_response_obj = new GenerateResponse($email, $username, $acs_url, $issuer, $audience, $nameid_attribute, $nameid_format, $assertion_signed, $inResponseTo);
        $saml_response = $saml_response_obj->createSamlResponse();       
        ob_clean();
        IDP_Utilities::unsetCookieVariables(array('response_params', 'acs_url', 'audience', 'relayState', 'inResponseTo'));
        $current_user   = JFactory::getUser();

        if (in_array(8, $current_user->groups) || in_array(7, $current_user->groups)) {
            $this->_send_response($saml_response, $relayState, $acs_url);
        } else {
           IDP_Utilities::dispatchMessage();
        }
    }

    private function _send_response($saml_response, $ssoUrl, $acs_url)
    {
        $saml_response = base64_encode($saml_response);

        ?>
        <form id="responseform" action="<?php echo $acs_url; ?>" method="post">
            <input type="hidden" name="SAMLResponse" value="<?php echo htmlspecialchars($saml_response); ?>"/>
            <input type="hidden" name="RelayState" value="<?php echo $ssoUrl; ?>"/>
        </form>
        <script>
            setTimeout(function () {
                document.getElementById('responseform').submit();
            }, 100);
        </script>
        <?php
        exit;
    }
}
