<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
/*
* @package    miniOrange
* @subpackage Plugins
* @license    GNU/GPLv3
* @copyright  Copyright 2015 miniOrange. All Rights Reserved.
*/
jimport('joomla.plugin.plugin');
jimport('miniorangesamlplugin.utility.SAML_Utilities');
include_once JPATH_SITE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_miniorange_saml' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo-saml-utility.php';
include_once JPATH_SITE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_miniorange_saml' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'saml_handler.php';
/**
 * miniOrange SAML System plugin
 */
class plgSystemSamlredirect extends JPlugin
{

    public function onAfterRender()
    {
        $app = JFactory::getApplication();
        $body = $app->getBody();
        $url = JURI::root();
        $tab = 0;
        $tables = JFactory::getDbo()->getTableList();
        foreach ($tables as $table) {
            if (strpos($table, "miniorange_saml_config") !== FALSE)
                $tab = $table;
        }
        if ($tab === 0)
            return;

        
        if($tab )
        {
            $button_style='border: 1px solid rgba(0, 0, 0, 0.2);color: #fff;background-color: #226a8b !important; padding: 4px 12px; border-radius: 3px;';
            $attribute = new Mo_saml_Local_Util();
            $linkCheck = $attribute->_load_db_values('#__miniorange_saml_config');
            $linkChecked = isset($linkCheck['login_link_check']) && $linkCheck['login_link_check'] == 1;
            $dynamicLink = empty($linkCheck['dynamic_link']) || !isset($linkCheck['dynamic_link']) ? "Login with IDP" : $linkCheck['dynamic_link'];
            if ($linkChecked == 1 && $app->isClient('site')) {
                
    
                if (stristr($body, "Submit")) {
                    if (stristr($body, "user.login")) {
                        $linkPosition = "Log in</button><br><br><a style='.$button_style.' href = " . $url . "?morequest=sso>" . $dynamicLink . " ";
                        $body = str_replace('Log in</button>', $linkPosition . '</a>', $body);
                        $app->setBody($body);
    
                    }
                }
            }
        }
       


    }
 
    public function onAfterInitialise()
    {

        $get = JFactory::getApplication()->input->get->getArray();
        $post = JFactory::getApplication()->input->post->getArray();
        $tab = 0;
        $tables = JFactory::getDbo()->getTableList();
    

        foreach ($tables as $table) {
            if ((strpos($table, "miniorange_saml_config") !== FALSE) ||(strpos($table, "miniorange_saml_customer_details") !== FALSE)  )
                $tab = $table;
        }
        if ($tab === 0)
            return;


        if (isset($post['mojsp_feedback']) || isset($post['mojspfree_skip_feedback'])) {
        
            if($tab)
            {
                $radio = $post['deactivate_plugin']??'';
                $data = $post['query_feedback']??'';
                $feedback_email = $post['feedback_email']??'';
    
                $database_name = '#__miniorange_saml_config';
                $updatefieldsarray = array(
                    'uninstall_feedback' => 1,
                );
                $result = new Mo_saml_Local_Util();
                $result->generic_update_query($database_name, $updatefieldsarray);
                $current_user = JFactory::getUser();
    
                 $customerResult = new Mo_saml_Local_Util();
                 $customerResult = $customerResult->_load_db_values('#__miniorange_saml_customer_details');
    
                $dVar=new JConfig();
                $check_email = $dVar->mailfrom;
                $admin_email = !empty($details ['admin_email']) ? $details ['admin_email'] :$check_email;
                $admin_email = !empty($admin_email)?$admin_email:self::getSuperUser();
                $admin_phone = $customerResult['admin_phone'];
                $data1 = $radio . ' : ' . $data . '  <br><br><strong>Feedback Email:</strong>  ' . $feedback_email;
    
                if(isset($post['mojspfree_skip_feedback']))
                {
                    $data1='Skipped the feedback';
                }
    
                if(file_exists(JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_miniorange_saml' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo-saml-customer-setup.php'))
                {
                    require_once JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_miniorange_saml' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo-saml-customer-setup.php';
    
                    Mo_saml_Local_Customer::submit_feedback_form($admin_email, $admin_phone, $data1,'');
                }
              
                require_once JPATH_SITE . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Installer' . DIRECTORY_SEPARATOR . 'Installer.php';
    
                foreach ($post['result'] as $fbkey) {
    
                     $result = Mo_saml_Local_Util::loadDBValues('#__extensions', 'loadColumn','type',  'extension_id', $fbkey);
                    $identifier = $fbkey;
                    $type = 0;
                    foreach ($result as $results) {
                        $type = $results;
                    }
    
                    if ($type) {
                        $cid = 0;
                        $installer = new JInstaller();
                        $installer->uninstall($type, $identifier, $cid);
                    }
                }
            }
    
        }

      if($tab)
    {
            $obj = new Mo_saml_Local_Util();
            if (isset($get['morequest']) && $get['morequest'] == 'sso') {
                $pluginconfig = $obj->_load_db_values('#__miniorange_saml_config');
                $this->sendSamlRequest($pluginconfig);
            } else if (isset($get['morequest']) && $get['morequest'] == 'metadata') {
                $pluginconfig = $obj->_load_db_values('#__miniorange_saml_config');
                $this->generateMetadata($pluginconfig);
            } else if (isset($get['morequest']) && $get['morequest'] == 'download_metadata') {
                $pluginconfig = $obj->_load_db_values('#__miniorange_saml_config');
                $download = true;
                $this->generateMetadata($pluginconfig, $download);
            } else if (isset($get['morequest']) && $get['morequest'] == 'acs') {
                $pluginconfig = $obj->_load_db_values('#__miniorange_saml_config');
                $this->getSamlResponse($pluginconfig);
            }
    }

       
    }

    function onExtensionBeforeUninstall($id)
    {
        $post = JFactory::getApplication()->input->post->getArray();
        $tables = JFactory::getDbo()->getTableList();
        $result = SAML_Utilities::_load_db_values('#__extensions', 'loadColumn', 'extension_id', 'element', 'com_miniorange_saml');
        $tables = JFactory::getDbo()->getTableList();
        $tab = 0;
        $tables = JFactory::getDbo()->getTableList();
        foreach ($tables as $table) {
            if (strpos($table, "miniorange_saml_config") !== FALSE)
                $tab = $table;
        }
        if ($tab === 0)
            return;
        if ($tab) {
            $fid = new Mo_saml_Local_Util();
            $fid = $fid->_load_db_values('#__miniorange_saml_config');
            $fid = $fid['uninstall_feedback'];
            $tpostData = $post;

            if (1) {
                if ($fid == 0) {
                    foreach ($result as $results) {
                        if ($results == $id) {?>
                          <link rel="stylesheet" type="text/css" href="<?php echo JURI::base();?>/components/com_miniorange_saml/assets/css/mo_saml_style.css" />
                            <div class="form-style-6 " style="width:35% !important; margin-left:33%; margin-top: 4%;">
                                <h1> Feedback form for Joomla SAML SP</h1>
                                <form name="f" method="post" action="" id="mojsp_feedback" style="background: #f3f1f1; padding: 10px;">
                                    <h3>What Happened? </h3>
                                    <input type="hidden" name="mojsp_feedback" value="mojsp_feedback"/>
                                    <div>
                                        <p style="margin-left:2%">
                                            <?php
                                            $deactivate_reasons = array(
                                                "Facing issues During Registration",
                                                "Does not have the features I'm looking for",
                                                "Not able to Configure",
                                                "I found a better plugin",
                                                "It's a temporary deactivation",
                                                "The plugin didn't working",
                                                "Other Reasons:"
                                            );
                                            foreach ($deactivate_reasons as $deactivate_reasons) { ?>
                                        <div class="radio" style="padding:1px;margin-left:2%">
                                            <label style="font-weight:normal;font-size:14.6px;font-family: cursive;" for="<?php echo $deactivate_reasons; ?>">
                                                <input type="radio" name="deactivate_plugin" value="<?php echo $deactivate_reasons; ?>" required>
                                                <?php echo $deactivate_reasons; ?></label>
                                        </div>

                                        <?php } ?>
                                        <br>

                                        <textarea id="query_feedback" name="query_feedback" rows="4" style="margin-left:3%;width: 91%" cols="50" placeholder="Write your query here"></textarea><br><br><br>
                                        <tr>
                                            <td width="20%"><strong>Email<span style="color: #ff0000;">*</span>:</strong></td>
                                            <td><input type="email" name="feedback_email" required placeholder="Enter email to contact." style="width:55%"/></td>
                                        </tr>

                                        <?php
                                        foreach ($tpostData['cid'] as $key) { ?>
                                            <input type="hidden" name="result[]" value=<?php echo $key ?>>
                                        <?php } ?>
                                        <br><br>
                                        <div class="mojsp_modal-footer">
                                            <input style="cursor: pointer;font-size: large;" type="submit" name="miniorange_feedback_submit" class="button button-primary button-large" value="Submit"/>
                                        </div>
                                    </div>
                                </form>
                                <form name="f" method="post" action="" id="mojspfree_feedback_form_close">
                                    <input type="hidden" name="mojspfree_skip_feedback" value="mojspfree_skip_feedback"/>
                                    <div style="text-align:center">
                                        <button class="button button-primary button-large" onClick="skipSAMLSPForm()">Skip Feedback</button>
                                    </div>
                                    <?php
                                        foreach ($tpostData['cid'] as $key) { ?>
                                            <input type="hidden" name="result[]" value=<?php echo $key ?>>
                                        <?php }
                                    ?>
                                </form>
                            </div>
                            <script src="https://code.jquery.com/jquery-3.6.3.js"></script>
                            <script>
                                jQuery('input:radio[name="deactivate_plugin"]').click(function () {
                                    var reason = jQuery(this).val();
                                    jQuery('#query_feedback').removeAttr('required')

                                    if (reason === 'Facing issues During Registration') {
                                        jQuery('#query_feedback').attr("placeholder", "Can you please describe the issue in detail?");
                                    } else if (reason === "Does not have the features I'm looking for") {
                                        jQuery('#query_feedback').attr("placeholder", "Let us know what feature are you looking for");
                                    } else if (reason === "I found a better plugin"){
                                        jQuery('#query_feedback').attr("placeholder", "Can you please name that plugin which one you feel better.");
                                    }else if (reason === "The plugin didn't working"){
                                        jQuery('#query_feedback').attr("placeholder", "Can you please let us know which plugin part you find not working.");
                                    } else if (reason === "Other Reasons:" || reason === "It's a temporary deactivation" ) {
                                        jQuery('#query_feedback').attr("placeholder", "Can you let us know the reason for deactivation");
                                        jQuery('#query_feedback').prop('required', true);
                                    } else if (reason === "Not able to Configure") {
                                        jQuery('#query_feedback').attr("placeholder", "Not able to Configure? let us know so that we can improve the interface");
                                    }
                                });

                                function skipSAMLSPForm(){
                                    jQuery('#mojspfree_feedback_form_close').submit();
                                }
                            </script>
                            <?php
                            exit;
                        }
                    }
                }
            }
        }
       
    }

    function sendSamlRequest($pluginconfig)
    {
        $get = JFactory::getApplication()->input->get->getArray();

        $siteUrl = JURI::root();
        $sp_base_url = $siteUrl;

        $result = new Mo_saml_Local_Util();
        $result = $result->_load_db_values('#__miniorange_saml_config');

        $sp_entity_id = isset($result['sp_entity_id']) ? $result['sp_entity_id'] : "";
        if ($sp_entity_id == '') {
            $sp_entity_id = $siteUrl . 'plugins/authentication/miniorangesaml';
        }

        if (!defined('_JDEFINES')) {
            require_once JPATH_BASE . '/includes/defines.php';
        }
        require_once JPATH_BASE . '/includes/framework.php';

        // Instantiate the application.
        $app = JFactory::getApplication('site');

        $jCmsVersion = SAML_Utilities::getJoomlaCmsVersion();
        $jCmsVersion = substr($jCmsVersion, 0, 3);

        if ($jCmsVersion < 4.0) {
            $app->initialise();
        }
        $login_url = $sp_base_url;

        $user = JFactory::getUser(); #Get current user info

        $acsUrl = $sp_base_url . '?morequest=acs';
        $ssoUrl = $pluginconfig['single_signon_service_url'];
        $sso_binding_type = $pluginconfig['binding'];
        $name_id_format = $pluginconfig['name_id_format'];

        $sendRelayState = $this->getRelayState($sp_base_url, $_REQUEST);

        $samlRequest = SAML_Utilities::createAuthnRequest($acsUrl, $sp_entity_id, $ssoUrl, $name_id_format, 'false', $sso_binding_type);

        if (isset($get['q'])) {
            if ($get['q'] == "sso") {
                $this->mo_saml_show_SAML_log($samlRequest, "displaySAMLRequest");
            }
        }

        $samlRequest = SAML_Utilities::samlRequestBind($samlRequest, $sso_binding_type);
        $this->sendSamlRequestByBindingType($samlRequest, $sso_binding_type, $sendRelayState, $ssoUrl);
    }

    function mo_saml_show_SAML_log($samlRequestResponceXML, $type)
    {
        header("Content-Type: text/html");
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->loadXML($samlRequestResponceXML);

        if ($type == 'displaySAMLRequest')
            $show_value = 'SAML Request';
        else
            $show_value = 'SAML Response';
        $out = $doc->saveXML();

        $out1 = htmlentities($out);
        $out1 = rtrim($out1);
        $xml = simplexml_load_string($out);
        $json = json_encode($xml);
        $array = json_decode($json);

        echo '<link rel="stylesheet" type="text/css" href="' . JURI::root() . 'media/com_miniorange_saml/css/style_settings.css"/>
            <div class="mo-display-logs" >
                <p type="text"   id="SAML_type">' . $show_value . '</p>
            </div>
            <div type="text" id="SAML_display" class="mo-display-block">
                <pre class=\'brush: xml;\'>' . $out1 . '</pre>
            </div><br>
            <div style="margin:3%;display:block;text-align:center;">
                <div style="margin:3%;display:block;text-align:center;" ></div>
                <button id="copy" onclick="copyDivToClipboard()" class="mo_saml_logs_css">Copy</button>&nbsp;
                <input id="dwn-btn" class="mo_saml_download_css "type="button" value="Download">
            </div>
            </div>';

        ob_end_flush(); ?>

        <script>

            function copyDivToClipboard() {
                var aux = document.createElement("input");
                aux.setAttribute("value", document.getElementById("SAML_display").textContent);
                document.body.appendChild(aux);
                aux.select();
                document.execCommand("copy");
                document.body.removeChild(aux);
                document.getElementById('copy').textContent = "Copied";
                document.getElementById('copy').style.background = "grey";
                window.getSelection().selectAllChildren(document.getElementById("SAML_display"));
            }

            function download(filename, text) {
                var element = document.createElement('a');
                element.setAttribute('href', 'data:Application/octet-stream;charset=utf-8,' + encodeURIComponent(text));
                element.setAttribute('download', filename);

                element.style.display = 'none';
                document.body.appendChild(element);

                element.click();

                document.body.removeChild(element);
            }

            document.getElementById("dwn-btn").addEventListener("click", function () {

                var filename = document.getElementById("SAML_type").textContent + ".xml";
                var node = document.getElementById("SAML_display");
                htmlContent = node.innerHTML;
                text = node.textContent;
                console.log(text);
                download(filename, text);
            }, false);

        </script>
        <?php
        exit;
    }

    function sendSamlRequestByBindingType($samlRequest, $sso_binding_type, $sendRelayState, $ssoUrl)
    {

        if (empty($sso_binding_type) || $sso_binding_type == 'HttpRedirect') {

            $samlRequest = "SAMLRequest=" . $samlRequest . "&RelayState=" . $sendRelayState;

            $param = array('type' => 'private');

            $redirect = $ssoUrl;
            if (strpos($ssoUrl, '?') !== false) {
                $redirect .= '&';
            } else {
                $redirect .= '?';
            }
            $redirect .= $samlRequest;

            header('Location: ' . $redirect);
            exit();
        }
    }

    function getRelayState($sp_base_url, $request)
    {

        $sendRelayState = $sp_base_url;

        if (isset($request['q'])) {
            if ($request['q'] == 'test_config') {
                $sendRelayState = 'testValidate';
            }
        } else if (isset($request['RelayState']) && $request['RelayState'] != '/' && $request['RelayState'] != '') {
            $sendRelayState = $request['RelayState'];
        } else if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '') {
            $sendRelayState = $_SERVER['HTTP_REFERER'];
        }


        return $sendRelayState;
    }

    function getSamlResponse($pluginconfig)
    {
        $post = JFactory::getApplication()->input->post->getArray();
        $obj = new Mo_saml_Local_Util();
        if (!defined('_JDEFINES')) {
            require_once JPATH_BASE . '/includes/defines.php';
        }
        require_once JPATH_BASE . '/includes/framework.php';

        if(isset($post['fix_issuer_issue']) && $post['fix_issuer_issue']=='true')
        {
            $database_name = '#__miniorange_saml_config';
            $updatefieldsarray = array(
                'idp_entity_id' =>($post['issuer']),
            );
            $obj->generic_update_query($database_name,$updatefieldsarray);
        }
        if(isset($post['quick_fix_cert']) && ($post['quick_fix_cert']=='true'))
        {
            $cert=isset($post['expected_cert'])?$post['expected_cert']:'';
            $database_name = '#__miniorange_saml_config';
            $updatefieldsarray = array(
                'certificate' => $cert,
            );
            $obj->generic_update_query($database_name, $updatefieldsarray);
        }
        $authBase = JPATH_BASE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'authentication' . DIRECTORY_SEPARATOR . 'miniorangesaml';
        include_once $authBase . DIRECTORY_SEPARATOR . 'saml2' . DIRECTORY_SEPARATOR . 'Response.php';
        jimport('miniorangesamlplugin.utility.encryption');
        jimport('joomla.application.application');
        jimport('joomla.html.parameter');
        $pluginconfig = $obj->_load_db_values('#__miniorange_saml_config');
        $sp_base_url = "";
        $sp_entity_id = "";
        if (isset($pluginconfig['sp_base_url'])) {
            $sp_base_url = $pluginconfig['sp_base_url'];
            $sp_entity_id = $pluginconfig['sp_entity_id'];
        }

        if (isset($pluginconfig['sp_entity_id'])) {

            $sp_entity_id = $pluginconfig['sp_entity_id'];

        }

        $siteUrl = JURI::root();

        if (empty($sp_base_url))
            $sp_base_url = $siteUrl;

        if (empty($sp_entity_id))
            $sp_entity_id = $siteUrl . 'plugins/authentication/miniorangesaml';

        $app = JFactory::getApplication('site');

        $jCmsVersion = SAML_Utilities::getJoomlaCmsVersion();
        $jCmsVersion = substr($jCmsVersion, 0, 3);

        if ($jCmsVersion < 4.0) {
            $app->initialise();
        }
        $get = JFactory::getApplication()->input->get->getArray();

        if (array_key_exists('SAMLResponse', $post)) {
            (new mo_saml_hander)->getResource();
            $this->validateSamlResponse($post, $sp_base_url, $sp_entity_id, $pluginconfig, $app);
        } else {
            throw new Exception ('Missing SAMLRequest or SAMLResponse parameter.');
        }
    }

    function validateSamlResponse($post, $sp_base_url, $sp_entity_id, $attribute, $app)
    {
        $samlResponse = $post ['SAMLResponse'];
        if (array_key_exists('RelayState', $_REQUEST) && ($_REQUEST['RelayState'] != '') && ($_REQUEST['RelayState'] != '/')) {
            $relayState = $_REQUEST ['RelayState'];
        } else {
            $relayState = $sp_base_url;
        }

     
        $samlResponse = base64_decode($samlResponse);

        $document = new DOMDocument ();
        $document->loadXML($samlResponse);
        $samlResponseXml = $document->firstChild;
        $doc = $document->documentElement;

        $xpath = new DOMXpath($document);
        $xpath->registerNamespace('samlp', 'urn:oasis:names:tc:SAML:2.0:protocol');
        $xpath->registerNamespace('saml', 'urn:oasis:names:tc:SAML:2.0:assertion');

        $status = $xpath->query('/samlp:Response/samlp:Status/samlp:StatusCode', $doc);
        $statusString = $status->item(0)->getAttribute('Value');
        $statusChildString = '';
        if ($status->item(0)->firstChild !== null) {
            $statusChildString = $status->item(0)->firstChild->getAttribute('Value');
        }

        $stat = explode(":", $statusString);
        $status = $stat[7];

        if ($relayState == "response") {

            $this->mo_saml_show_SAML_log($samlResponse, "displaySAMLResponse");
        }

        if ($status != "Success") {
            if (!empty($statusChildString)) {
                $stat = explode(":", $statusChildString);
                $status = $stat[7];
            }
            $this->show_error_message($status, $relayState);
        }


        $acsUrl = $sp_base_url . '?morequest=acs';

        $certFromPlugin = $attribute['certificate'];
        if (!empty($certFromPlugin)) {
            $certFromPlugin = SAML_Utilities::sanitize_certificate($certFromPlugin);
        }
        $certfpFromPlugin = XMLSecurityKey::getRawThumbprint($certFromPlugin);
        $samlResponse = new SAML2_Response ($samlResponseXml);
        $responseSignatureData = $samlResponse->getSignatureData();


        $assertionSignatureData = current($samlResponse->getAssertions())->getSignatureData();
        /* convert to UTF-8 character encoding */
        $certfpFromPlugin = iconv("UTF-8", "CP1252//IGNORE", $certfpFromPlugin);

        /* remove whitespaces */
        $certfpFromPlugin = preg_replace('/\s+/', '', $certfpFromPlugin);

        // /* Validate signature */
        if (!empty($certfpFromPlugin)) {
            if (!empty($responseSignatureData)) {
                $validSignature = SAML_Utilities::processResponse($acsUrl, $certfpFromPlugin, $responseSignatureData, $samlResponse, $certFromPlugin, $relayState,$post);
                if ($validSignature === FALSE) {
                    echo "Invalid signature in the SAML Response.<br><br>";
                    exit;
                }
            }

            if (!empty($assertionSignatureData)) {
                $validSignature = SAML_Utilities::processResponse($acsUrl, $certfpFromPlugin, $assertionSignatureData, $samlResponse, $certFromPlugin, $relayState,$post);
                if ($validSignature === FALSE) {
                    echo "Invalid signature in the SAML Assertion.<br><br>";
                    exit;
                }
            }
        }

        $db = JFactory::getDbo();
        $appdata = new Mo_saml_Local_Util();
        $appdata = $appdata->_load_db_values('#__miniorange_saml_config');
        $uid=SAML_Utilities::getSuperUser();
        $AdminUser =SAML_Utilities::_load_db_values('#__users','loadAssoc','*', 'id', $uid);
        
    

       
        // verify the issuer and audience from saml response
        $issuer = $appdata['idp_entity_id'];
     
        SAML_Utilities::validateIssuerAndAudience($samlResponse, $sp_entity_id, $issuer, $relayState,$post);

        $username = current(current($samlResponse->getAssertions())->getNameId());
        $attrs = current($samlResponse->getAssertions())->getAttributes();
        $attrs ['NameID'] = current(current($samlResponse->getAssertions())->getNameId());

        if ($relayState == 'testValidate') {
            SAML_Utilities::mo_saml_show_test_result($username, $attrs, $sp_base_url);
        }

        $sessionIndex = current($samlResponse->getAssertions())->getSessionIndex();
        $attrs ['ASSERTION_SESSION_INDEX'] = $sessionIndex;


        $posts=JFactory::getApplication()->input->post->getArray();
        if(isset($posts['quick_fix_attributes']) && ($posts['quick_fix_attributes']=='true'))
        {
            foreach($attrs as $attr)
            {
                if(filter_var($attr[0], FILTER_VALIDATE_EMAIL))
                {
                    $username=$attr[0];
                }
            }
            $session = JFactory::getSession();
            $session->set('quick_fix_attributes', "true");
            $session->set('attribute_email', $username);
        }
       
        $email = $username;
        $name = '';
        $saml_groups = '';

        $NameMapping = (string)$attribute['name'];
        $usernameMapping = $attribute['username'];
        $mailMapping = $attribute['email'];

        if (!empty($usernameMapping) && isset($attrs[$usernameMapping]) && !empty($attrs[$usernameMapping])) {
            $username = $attrs[$usernameMapping];
            if (is_array($username))
                $username = $username[0];
        }
        if (!empty($mailMapping) && isset($attrs[$mailMapping]) && !empty($attrs[$mailMapping])) {
            $email = $attrs[$mailMapping];
            if (is_array($email))
                $email = $email[0];
        }

        if (!empty($NameMapping) && isset($attrs[$NameMapping]) && !empty($attrs[$NameMapping])) {
            $name = $attrs[$NameMapping];

        }
        if (is_array($name)) {
            $name = $name[0];
        }

        if (!empty($groupsMapping) && isset($attrs[$groupsMapping]) && !empty($attrs[$groupsMapping])) {
            $saml_groups = $attrs[$groupsMapping];
        } else {
            $saml_groups = array();
        }
        $matcher = 'email';


        $result = SAML_Utilities::get_user_from_joomla($matcher, $username, $email);
        $login_url = isset($relayState) ? $relayState : $sp_base_url;
     

        if ($result) {
            $this->loginCurrentUser($result, $attrs, $login_url, $name, $username, $email, $matcher, $app);
        } else if (isset($AdminUser['authCount']) && $AdminUser['authCount']) {
            SAML_Utilities::saveTestConfig('#__miniorange_saml_config','sso_status',0);
            SAML_Utilities::keepRecords('SSO Status','Registration Flow');
            SAML_Utilities::rmex(); 
        } else {
            $this->RegisterCurrentUser($attrs, $login_url, $name, $username, $email, $saml_groups, $matcher, $app,$post);
        }
    }

    function loginCurrentUser($result, $attrs, $login_url, $name, $username, $email, $matcher, $app)
    {
        $user = JUser::getInstance($result->id);
        SAML_Utilities::updateCurrentUserName($user->id,$name,'name');

        if($user->block==1)
        {
            self::saveTestConfig('#__miniorange_saml_config','sso_status',0);
            self::keepRecords('SSO Status','User is blocked by Administrator');
            $app=JFactory::getApplication();
            $app->enqueueMessage('You are not allowed to login into the site. Please contact your Administrator.', 'error');
            $app->redirect(JURI::root());
        }
        $role_mapping = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_role_mapping');
        $uid=SAML_Utilities::getSuperUser();
        $AdminUser =SAML_Utilities::_load_db_values('#__users','loadAssoc','*', 'id', $uid);
        $result = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_config');
        $sso_count= base64_decode($result['sso_test']);

        if (isset($role_mapping['enable_saml_role_mapping'])) {
            if ($role_mapping['enable_saml_role_mapping'] == 1)
                $enable_rolemapping = 1;
            else
                $enable_rolemapping = 0;

        } else {
            $enable_rolemapping = 0;
        }

        jimport('joomla.user.helper');
        if ($enable_rolemapping) {
            if (isset($role_mapping['mapping_value_default']))
                $default_group = $role_mapping['mapping_value_default'];
            JUserHelper::addUserToGroup($user->id, $default_group);

            foreach ($user->groups as $existinggroup) {
                if ($existinggroup != $default_group && $existinggroup != 7 && $existinggroup != 8)
                    JUserHelper::removeUserFromGroup($user->id, $existinggroup);
            }
        }
        if(isset($AdminUser['authCount']) && $AdminUser['authCount'])
        {
            SAML_Utilities::saveTestConfig('#__miniorange_saml_config','sso_status',0);
            SAML_Utilities::keepRecords('SSO Status','Login Flow');
            SAML_Utilities::rmex();
        }

        $session = JFactory::getSession(); #Get current session vars
        // Register the needed session variables
        $session->set('user', $user);
        $session->set('MO_SAML_NAMEID', isset($attrs['NAME_ID']) ? $attrs['NAME_ID'] : '');
        $session->set('MO_SAML_SESSION_INDEX', isset($attrs['ASSERTION_SESSION_INDEX']) ? $attrs['ASSERTION_SESSION_INDEX'] : '');

        $app->checkSession();
        $sessionId = $session->getId();
        SAML_Utilities::updateUsernameToSessionId($user->id, $user->username, $sessionId);
       
        
        
       $sso_count    = base64_encode($sso_count + 1);

       $database_name = '#__miniorange_saml_config';
       $updatefieldsarray = array(
           'sso_test' => $sso_count,
       );
       $update_db = new Mo_saml_Local_Util();
       $update_db->generic_update_query($database_name, $updatefieldsarray);
       $OQEvKKrLDi=base64_decode($result['userslim']);
      
        if( $OQEvKKrLDi <=1 || $OQEvKKrLDi % 5 ==0)
        {
           SAML_Utilities::saveTestConfig('#__miniorange_saml_config','sso_status',true);
           SAML_Utilities::keepRecords('SSO Status','Login Flow');   
        }
        
       $user->setLastVisit();
       $app->redirect(urldecode($login_url));

       
    }

    function show_error_message($statusCode, $relayState)
    {
        if ($relayState == 'testValidate') {

            echo '<div style="font-family:Calibri;padding:0 3%;">';
            echo '<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;"> ERROR</div>
            <div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>Error: </strong> Invalid SAML Response Status.</p>
            <p><strong>Causes</strong>: Identity Provider has sent \'' . $statusCode . '\' status code in SAML Response. </p>
                            <p><strong>Reason</strong>: ' . $this->get_status_message($statusCode) . '</p><br>
            </div>

            <div style="margin:3%;display:block;text-align:center;">
            <div style="margin:3%;display:block;text-align:center;"><input style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"type="button" value="Done" onClick="self.close();"></div>';
            exit;
        } else {
            if ($statusCode == 'RequestDenied') {
                echo 'You are not allowed to login into the site. Please contact your Administrator.';
                exit;
            } else {
                echo 'We could not sign you in. Please contact your Administrator.';
                exit;
            }

        }
    }

    function get_status_message($statusCode)
    {
        switch ($statusCode) {
            case 'RequestDenied':
                return 'You are not allowed to login into the site. Please contact your Administrator.';
                break;
            case 'Requester':
                return 'The request could not be performed due to an error on the part of the requester.';
                break;
            case 'Responder':
                return 'The request could not be performed due to an error on the part of the SAML responder or SAML authority.';
                break;
            case 'VersionMismatch':
                return 'The SAML responder could not process the request because the version of the request message was incorrect.';
                break;
            default:
                return 'Unknown';
        }
    }

    function generateMetadata($attribute, $download = false)
    {
        $sp_base_url = "";
        $sp_entity_id = "";
        $name_id_format = "";


        if (isset($attribute['sp_base_url'])) {
            $sp_base_url = $attribute['sp_base_url'];
            $sp_entity_id = $attribute['sp_entity_id'];
            $name_id_format = $attribute['name_id_format'];
        }

        if (isset($attribute['sp_entity_id']))
            $sp_entity_id = $attribute['sp_entity_id'];

        $siteUrl = JURI::root();

        if (empty($sp_base_url))
            $sp_base_url = $siteUrl;

        if (empty($sp_entity_id))
            $sp_entity_id = $siteUrl . 'plugins/authentication/miniorangesaml';

        $acs_url = $sp_base_url . '?morequest=acs';
        $logout_url = $sp_base_url . 'index.php?option=com_users&amp;task=logout';

        $certificate = JPATH_BASE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'authentication' . DIRECTORY_SEPARATOR . 'miniorangesaml' . DIRECTORY_SEPARATOR . 'saml2' . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR . 'sp-certificate.crt';
        $certificate = file_get_contents($certificate);
        $certificate = SAML_Utilities::desanitize_certificate($certificate);
        if ($download) {
            header('Content-Disposition: attachment; filename="Metadata.xml"');
        } else {
            header('Content-Type: text/xml');
        }
        echo '<?xml version="1.0"?>
        <md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" validUntil="2025-08-04T23:59:59Z" cacheDuration="PT1446808792S" entityID="' . $sp_entity_id . '">
          <md:SPSSODescriptor AuthnRequestsSigned="false" WantAssertionsSigned="true" protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
            <md:NameIDFormat>urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified</md:NameIDFormat>
            <md:AssertionConsumerService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="' . $acs_url . '" index="1"/>
          </md:SPSSODescriptor>
          <md:Organization>
            <md:OrganizationName xml:lang="en-US">miniOrange</md:OrganizationName>
            <md:OrganizationDisplayName xml:lang="en-US">miniOrange</md:OrganizationDisplayName>
            <md:OrganizationURL xml:lang="en-US">http://miniorange.com</md:OrganizationURL>
          </md:Organization>
          <md:ContactPerson contactType="technical">
            <md:GivenName>miniOrange</md:GivenName>
            <md:EmailAddress>info@xecurify.com</md:EmailAddress>
          </md:ContactPerson>
          <md:ContactPerson contactType="support">
            <md:GivenName>miniOrange</md:GivenName>
            <md:EmailAddress>info@xecurify.com</md:EmailAddress>
          </md:ContactPerson>
        </md:EntityDescriptor>';
        exit();
    }

    function RegisterCurrentUser($attrs, $login_url, $name, $username, $email, $saml_groups, $matcher, $app,$post)
    {
        $role_mapping = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_role_mapping');
        $enable_saml_role_mapping = 0;
        if (isset($role_mapping['enable_saml_role_mapping']))
            $enable_saml_role_mapping = json_decode($role_mapping['enable_saml_role_mapping']);

        // user data
        $data['name'] = (isset($name) && !empty($name)) ? $name : $username;
        $data['username'] = $username;
        $data['email'] = $data['email1'] = $data['email2'] = JStringPunycode::emailToPunycode($email);
        $data['password'] = $data['password1'] = $data['password2'] = JUserHelper::genRandomPassword();
        $data['activation'] = '0';
        $data['block'] = '0';

        if ($enable_saml_role_mapping == 1)
        {
            $data['groups'][] = isset($role_mapping['mapping_value_default']) ? $role_mapping['mapping_value_default'] : 2;
        }
        else
        {
            $data['groups'][] = 2;
        }

        $result = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_config');

        // Get the model and validate the data.
        jimport('joomla.application.component.model');

        if (!defined('JPATH_COMPONENT')) {
            define('JPATH_COMPONENT', JPATH_BASE . '/components/');
        }

        $user = new JUser;
        //Write to database
        if (!$user->bind($data)) {
            SAML_Utilities::saveTestConfig('#__miniorange_saml_config','sso_status',0);
            SAML_Utilities::keepRecords('SSO Status','Could not bind data');
            throw new Exception("Could not bind data. Error: " . $user->getError());
        }

        $result = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_config');
        
        $uid=SAML_Utilities::getSuperUser();
        $AdminUser =SAML_Utilities::_load_db_values('#__users','loadAssoc','*', 'id', $uid);
       

        $session = JFactory::getSession();
        if (!$user->save()) {
            SAML_Utilities::saveTestConfig('#__miniorange_saml_config','sso_status',0);
            SAML_Utilities::keepRecords('SSO Status','Could not save user due to invalid email address (Attribute Mapping Issue)');
            if($session->get('quick_fix_attributes') && ($session->get('quick_fix_attributes')=='true') && $session->get('attribute_email') && (strcmp($session->get('attribute_email'),$user->email==0)))
            {
                self::mo_user_not_registered_nameid_issue($user);
            }
            else
            {
               self::mo_user_not_registered_issue($user, $post);
            }
            

        }
        $usrlim = base64_decode($result['userslim']);
        $usrlim = base64_encode($usrlim  + 1);
        $sso    = base64_decode($result['sso_test']);
        $sso    = base64_encode($sso+ 1);


        $database_name = '#__miniorange_saml_config';
        $updatefieldsarray = array(
            'userslim' => $usrlim,
            'sso_test' => $sso,
        );
        $result = new Mo_saml_Local_Util();
        $result->generic_update_query($database_name, $updatefieldsarray);
        if (isset($AdminUser['authCount']) && $AdminUser['authCount']) {
            SAML_Utilities::saveTestConfig('#__miniorange_saml_config','sso_status',0);
            SAML_Utilities::keepRecords('SSO Status','User creation limit Exceeded');
            SAML_Utilities::rmex();
        }
      
        $result = SAML_Utilities::get_user_from_joomla($matcher, $username, $email);

        if ($result) {
            $user = JUser::getInstance($result->id);

             #Get current session vars
            // Register the needed session variables
            $session->set('user', $user);
            if (isset($attrs['NAME_ID']))
                $session->set('MO_SAML_NAMEID', $attrs['NAME_ID']);
            $session->set('MO_SAML_SESSION_INDEX', $attrs['ASSERTION_SESSION_INDEX']);

            $app->checkSession();
            $sessionId = $session->getId();
            SAML_Utilities::updateUsernameToSessionId($user->id, $user->username, $sessionId);
         
            SAML_Utilities::saveTestConfig('#__miniorange_saml_config','sso_status',true);
            SAML_Utilities::keepRecords('SSO Status','Registration Flow');
            /* Update Last Visit Date */
            $user->setLastVisit();
            $app->redirect(urldecode($login_url));
        }

    }

    function mo_get_version_informations()
    {
        $array_version = array();
        $array_version["PHP_version"] = phpversion();
        $array_version["OPEN_SSL"] = $this->mo_saml_is_openssl_installed();
        $array_version["CURL"] = $this->mo_saml_is_curl_installed();
        $array_version["ICONV"] = $this->mo_saml_is_iconv_installed();
        $array_version["DOM"] = $this->mo_saml_is_dom_installed();
        return $array_version;
    }

    function mo_user_not_registered_nameid_issue($user)
    {
        $siteUrl = JURI::root();
        ob_end_clean();
        $siteUrl = $siteUrl . 'plugins/authentication/miniorangesaml/';
        echo '<div style="font-family:Calibri;padding:0 3%;">';
        echo '<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;">
        <img style="width:15px;"src="' . $siteUrl . 'images/wrong.png"> ERROR</div>
        <div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>Error: </strong>Could not save user. ' . $user->getError() . '</p>
        <p>It seems like you are not receiving email id in any attributes from your IDP. Now you need to make changes to the attribute mapping on your IDP side if the email address is not receiving in any attributes.</p>
       </div>
            
        <div style="text-align:center;">
            <div style="display:inline-block">
                <a href="index.php" type="button" style="padding: 10px 20px;background: #226a8b;cursor: pointer;font-size:15px;border-radius: 3px;border-color:black;border-width: 1px;border-style: solid;color: #FFF;">Back to Home</a>
            </div>
            <div style="display:inline-block">
                <a href="https://plugins.miniorange.com/joomla-single-sign-on-sso#pricing" type="button" style="padding: 10px 20px;background: #226a8b;cursor: pointer;font-size:15px;border-radius: 3px;border-color:black;border-width: 1px;border-style: solid;color: #FFF;" target="_blank">Upgrade</a>
            </div>  
        </div>';
        exit;
    }


    function mo_user_not_registered_issue($user,$post)
    {
        $siteUrl = JURI::root();
        ob_end_clean();
        $siteUrl = $siteUrl . 'plugins/authentication/miniorangesaml/';
        echo '<div style="font-family:Calibri;padding:0 3%;">';
        echo '<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;">
        <img style="width:15px;"src="' . $siteUrl . 'images/wrong.png"> ERROR</div>
        <div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>Error: </strong>Could not save user. ' . $user->getError() . '</p>
        <p>You are receiving this error because you are receiving <strong>'.$user->email.'</strong> in NameID attribute from your IDP which is bind to <strong>Email</strong> attribute in Joomla</p>
        <p>
            <strong>Solutions:</strong>
            <ul>
                <li>ATTRIBUTE NAME for Email should be NameID only. Make changes to the attribute mapping on your IDP side if the email address is not receiving in NameID.</li>
                <li>You can Upgrade to <strong>Premium</strong> version if you wish to do custom attribute mapping.</li>
                <li>You can click on <strong>Quick Fix</strong> button to test SSO in our Free plugin</li>
            </ul>
        </p>
       </div>
            
        <div style="text-align:center;">
            <div style="display:inline-block">
                <a href="index.php" type="button" style="padding: 10px 20px;background: #226a8b;cursor: pointer;font-size:15px;border-radius: 3px;border-color:black;border-width: 1px;border-style: solid;color: #FFF;">Back to Home</a>
            </div>
            <div style="display:inline-block">
                <form  method="post" action="'.JURI::root().'?morequest=acs" >
                     <input style="padding: 10px 20px;background: green;cursor: pointer;font-size:15px;border-radius: 3px;border-color:black;border-width: 1px;border-style: solid;color: #FFF;" type="submit" value="Quick Fix">
                     <input type="hidden" name="quick_fix_attributes" value="true" >
                     <input type="hidden" name="SAMLResponse" value="'.$post["SAMLResponse"].'">
                     <input type="hidden" name="RelayState" value="'.$post["RelayState"].'">
                 </form>
             </div>
          
            <div style="display:inline-block">
                <a href="https://plugins.miniorange.com/joomla-single-sign-on-sso#pricing" type="button" style="padding: 10px 20px;background: #226a8b;cursor: pointer;font-size:15px;border-radius: 3px;border-color:black;border-width: 1px;border-style: solid;color: #FFF;" target="_blank">Upgrade</a>
            </div>
          
        </div>';
        exit;
    }

    function mo_saml_is_openssl_installed()
    {
        if (in_array('openssl', get_loaded_extensions())) {
            return 1;
        } else {
            return 0;
        }
    }

    function mo_saml_is_curl_installed()
    {
        if (in_array('curl', get_loaded_extensions())) {
            return 1;
        } else {
            return 0;
        }
    }

    function mo_saml_is_iconv_installed()
    {
        if (in_array('iconv', get_loaded_extensions())) {
            return 1;
        } else {
            return 0;
        }
    }

    function mo_saml_is_dom_installed()
    {
        if (in_array('dom', get_loaded_extensions())) {
            return 1;
        } else {
            return 0;
        }
    }

}