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

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
JHtml::_('jquery.framework');

JHtml::_('stylesheet', JUri::base() .'components/com_joomlaidp/assets/css/miniorange_idp.css');
JHtml::_('stylesheet', JUri::base() .'components/com_joomlaidp/assets/css/bootstrap-tour-standalone.css');
JHtml::_('stylesheet', JUri::base() .'components/com_joomlaidp/assets/css/bootstrap-select-min.css');
JHtml::_('stylesheet', JUri::base() .'components/com_joomlaidp/assets/css/miniorange_boot.css');

JHtml::_('script', JUri::base() . 'components/com_joomlaidp/assets/js/bootstrap-tour-standalone.min.js');
JHtml::_('script', JUri::base() . 'components/com_joomlaidp/assets/js/mo-saml-idp-tour.js');
JHtml::_('script', JUri::base() . 'components/com_joomlaidp/assets/js/bootstrap-select-min.js');
JHtml::_('script', JUri::base() . 'components/com_joomlaidp/assets/js/utilityjs.js');


$tab = JFactory::getApplication()->input->get->getArray();
$idp_active_tab = isset($tab['tab-panel']) ? $tab['tab-panel'] : 'sp';
$test_config = isset($tab['test-config']) ? true : false;
if (MoSamlIdpUtility::is_curl_installed() == 0) { ?>
    <p style="color:red;">
        (Warning:
            <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP CURL extension</a>
        is not installed or disabled) Please go to Troubleshooting for steps to enable curl.
    </p>
    <?php
} ?>

<?php
    $session = JFactory::getSession();
    $current_state=$session->get('plugin_Start')==NULL?'false':'true';
    $session->set('show_test_config', false);
    $customer_details = MoSamlIdpUtility::getCustomerDetails();
    if($test_config)
    {
        $session->set('show_test_config', true);
    }
    if($customer_details['show_tc_popup'])
    {
        echo "
        <script>
            jQuery(document).ready(function(){
                show_TC_modal();
            });
        </script>
        ";
        $database_name = '#__miniorange_saml_idp_customer';
        $updatefieldsarray = array(
            'show_tc_popup' => 0,
        );
        IDP_Utilities::updateDatabaseQuery($database_name, $updatefieldsarray);
    }
    if($customer_details['show_tc_popup']==0 && $customer_details['initialise_visual_tour']==1)
    {
        echo "
        <script>
            jQuery(document).ready(function(){
                restart_tabtour();
            });
        </script>
        ";
        $database_name = '#__miniorange_saml_idp_customer';
        $updatefieldsarray = array(
            'initialise_visual_tour' => 0,
        );
        IDP_Utilities::updateDatabaseQuery($database_name, $updatefieldsarray);
    }
?>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<div class="mo_boot_container-fluid" style="margin-top:10px">
    <div class="mo_boot_row">
        <div class="mo_boot_col-lg-9 mo_boot_p-0">
            <div class="nav-tab-wrapper mo_idp_nav-tab-wrapper">
                <a id="sptab" class="mo_nav-tab <?php echo $idp_active_tab == 'sp' ? 'mo_nav_tab_active' : ''; ?>" href="#service-provider"
                onclick="add_css_tab('#sptab');"
                data-toggle="tab"><?php echo JText::_('COM_JOOMLAIDP_TAB3_SERVICE_PROVIDER'); ?>
                </a>

                <a id="idptab" class="mo_nav-tab <?php echo $idp_active_tab == 'idp' ? 'mo_nav_tab_active' : ''; ?>" href="#identity-provider"
                onclick="add_css_tab('#idptab');"
                data-toggle="tab"><?php echo JText::_('COM_JOOMLAIDP_TAB4_IDENTITY_PROVIDER'); ?>
                </a>

                <a id="advance_mapping_tab" class="mo_nav-tab <?php echo $idp_active_tab == 'advance_mapping' ? 'mo_nav_tab_active' : ''; ?>" href="#iadvance_mapping"
                onclick="add_css_tab('#advance_mapping_tab');"
                data-toggle="tab"><?php echo JText::_('COM_JOOMLAIDP_MAPPING'); ?>
                </a>

                <a id="rolerelay_restiction" class="mo_nav-tab <?php echo $idp_active_tab == 'role_relay_restriciton' ? 'mo_nav_tab_active' : ''; ?>" href="#role_relay_restriciton_id"
                onclick="add_css_tab('#rolerelay_restiction');"
                data-toggle="tab"><?php echo JText::_('COM_JOOMLAIDP_RELAY_RESTRICTION'); ?>
                </a>

                <a id="signin_settings_tab" class="mo_nav-tab <?php echo $idp_active_tab == 'signin_settings' ? 'mo_nav_tab_active' : ''; ?>" href="#signin_settings_id"
                onclick="add_css_tab('#signin_settings_tab');"
                data-toggle="tab"><?php echo JText::_('COM_JOOMLAIDP_SIGNIN_SETTINGS'); ?>
                </a>

                <a id="licensingtab" class="mo_nav-tab <?php echo $idp_active_tab == 'license' ? 'mo_nav_tab_active' : ''; ?>" href="#licensing-plans"
                onclick="add_css_tab('#licensingtab');"
                data-toggle="tab"><?php echo JText::_('COM_JOOMLAIDP_TAB6_LICENSING_PLANS'); ?>
                </a>

                <a id="accounttab" class="mo_nav-tab <?php echo $idp_active_tab == 'account' ? 'mo_nav_tab_active' : ''; ?>" href="#description"
                onclick="add_css_tab('#accounttab');"
                data-toggle="tab"><?php echo JText::_('COM_JOOMLAIDP_TAB2_ACCOUNT_SETUP'); ?>
                </a>
               
            </div>
        </div>
        <div class="mo_boot_col-lg-3">
            <input type="button" id="end_tab_tour" value="Start Plugin Tour" onclick="restart_tabtour();" style=" float: right;margin:5px" class="mo_boot_btn mo_boot_btn-saml"/>
            <button id="mo_TC"  onclick="show_TC_modal()" style=" float: right;margin:5px" class="mo_boot_btn mo_boot_btn-saml">T&C</button>
            <div id="my_TC_Modal" class="TC_modal">
                <div class="TC_modal-content">
                    <div class="mo_boot_col-12 mo_boot_text-center">
                        <span style="font-size: 28px;"><strong>Terms & Condition</strong></span>
                        <span class="TC_modal_close" onclick="MyClose()">&times;</span>
                    </div>
                    <div>
                        <hr>
                        <ul> 
                            <li>1. We'll be sending the email to your admin email to get in touch with you if you have any issues while testing our plugin.</li>
                            <li>2. Your email address will not be shared or used by any third parties. The main aim of picking an email address is to connect with you.</li>
                            <li>3. You can update your email address below if you like. So that if you require assistance, we can contact you at your correct email address.</li>
                            <li>4. If you require any assistance, you may contact us at <strong>joomlasupport@xecurify.com</strong>.</li>
                            <li>
                                <form method="post" name="f" action="<?php echo JRoute::_('index.php?option=com_joomlaidp&view=accountsetup&task=accountsetup.saveAdminMail'); ?>" > 
                                    <?php
                                        $dVar=new JConfig(); 
                                        $check_email = $dVar->mailfrom;
                                        $result = IDP_Utilities::fetchDatabaseValues('#__miniorange_saml_idp_customer', 'loadAssoc', '*');
                                
                                        if($result['email']!=NULL)
                                        {
                                            $check_email =$result['email'];
                                        }
                                    ?>
                                    <div class="mo_boot_row mo_boot_mt-3">
                                        <div class="mo_boot_col-sm-5">
                                            <input type="email" name="admin_email"  class="mo_boot_form-control" value="<?php echo $check_email;?>">
                                        </div>
                                        <div class="mo_boot_col-sm-3">
                                            <input type="submit" class="mo_boot_btn mo_boot_btn-primary">
                                        </div>
                                    </div>                            
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mo_boot_row"  style="background-color:#e0e0d8; padding:15px;">
        <div class="mo_boot_col-sm-12">
                <div class="tab-content" id="myTabContent">
                    <div id="description" class="tab-pane <?php echo $idp_active_tab == 'account' ? 'active' : ''; ?>">
                        <?php
                            $customer_details = MoSamlIdpUtility::getCustomerDetails();
                            $login_status = $customer_details['login_status'];
                            $registration_status = $customer_details['registration_status'];
                            if ($login_status) {  //Show Login Page
                                common_classes_for_UI('mo_saml_idp_login_page', 'mo_saml_idp_support');
                            } else {  // Show Registration Page
                                if ($registration_status == 'MO_IDP_OTP_DELIVERED_SUCCESS' || $registration_status == 'MO_IDP_OTP_VALIDATION_FAILURE' || $registration_status == 'MO_IDP_OTP_DELIVERED_FAILURE') {
                                    common_classes_for_UI('mo_saml_idp_show_otp_verification', 'mo_saml_idp_support');
                                } else if (!MoSamlIdpUtility::is_customer_registered()) {
                                    common_classes_for_UI('mo_saml_idp_registration_page', 'mo_saml_idp_support');
                                } else {
                                    common_classes_for_UI('mo_saml_idp_account_page', 'mo_saml_idp_support');
                                }
                            }
                        ?>
                    </div>

                    <div id="service-provider" class="tab-pane <?php echo $idp_active_tab == 'sp' ? 'active' : '';?>"> <?php
                        $class_name = "JoomlaIdpViewAccountSetup";
                        $func_name = "showServiceProviderConfigurations";
                        common_classes_for_UI_1($class_name, $func_name,'mo_saml_idp_support');?>
                    </div>

                    <div id="identity-provider" class="tab-pane <?php echo $idp_active_tab == 'idp' ? 'active' : ''; ?>"> <?php
                        $class_name = "JoomlaIdpViewAccountSetup";
                        $func_name = "showIdentityProviderConfigurations";
                        common_classes_for_UI_1($class_name, $func_name,'mo_saml_idp_support');?>
                    </div>

                    <div id="iadvance_mapping" class="tab-pane <?php echo $idp_active_tab == 'advance_mapping' ? 'active' : ''; ?>"> <?php
                        $class_name = "JoomlaIdpViewAccountSetup";
                        $func_name = "showAdvanceMapping";
                        common_classes_for_UI_1($class_name, $func_name,'mo_saml_idp_support');?>
                    </div>

                    <div id="role_relay_restriciton_id" class="tab-pane <?php echo $idp_active_tab == 'role_relay_restriciton' ? 'active' : ''; ?>"> <?php
                        $class_name = "JoomlaIdpViewAccountSetup";
                        $func_name = "showRoleRelayRestriction";
                        common_classes_for_UI_1($class_name, $func_name,'mo_advertise_2fa');?>
                    </div>

                    <div id="signin_settings_id" class="tab-pane <?php echo $idp_active_tab == 'signin_settings' ? 'active' : ''; ?>"> <?php
                        $class_name = "JoomlaIdpViewAccountSetup";
                        $func_name = "showIDPInitiatedLoginDetails";
                        common_classes_for_UI_1($class_name, $func_name,'mo_advertise_2fa');?>
                    </div>

                   <!-- <div id="custom_certificate_id" class="tab-pane <?php /*echo $idp_active_tab == 'custom_certificate' ? 'active' : ''; */?>"> <?php
/*                        $class_name = "JoomlaIdpViewAccountSetup";
                        $func_name = "custom_certificate";
                        common_classes_for_UI_1($class_name, $func_name,'mo_saml_idp_support');*/?>
                    </div>-->

                    <div id="licensing-plans" class="tab-pane <?php echo $idp_active_tab == 'license' ? 'active' : ''; ?>">
                        <div class="mo_saml_table_layout_1">
                            <div class="mo_saml_table_layout mo_saml_container_pricing">
                                    <?php
                                        $result      = IDP_Utilities::fetchDatabaseValues('#__miniorange_saml_idp_customer', 'loadAssoc','*');
                                        $email       = isset($result['email']) ? $result['email'] : '';
                                        $hostName    = MoSamlIdpUtility::getHostName();
                                        $loginUrl    = $hostName . '/moas/login';
                                        $redirectUrl = $hostName . '/moas/initializepayment';
                                        echo $this->showLicensingPlanDetails();
                                    ?>
                            </div>
                        </div>
                        <form id="idp_default_form" method="post"
                            action="<?php echo JRoute::_('index.php?option=com_joomlaidp&view=samlidpsettings'); ?>">
                        </form>
                        <form style="display:none;" id="moidp_loginform" action="<?php echo $loginUrl; ?>" target="_blank"
                            method="post">
                            <input name="username" value="<?php echo $email; ?>" type="email" style="display:none;">
                            <input name="redirectUrl" value="<?php echo $redirectUrl; ?>" type="hidden">
                            <input name="requestOrigin" id="requestOrigin" type="hidden">
                        </form>
                    </div>
                    <div id="request-demo" class="tab-pane <?php if ($tab == 'request_demo') echo 'active'; ?>">
                    <?php
                        $class_name = "JoomlaIdpViewAccountSetup";
                        $func_name = "requestfordemo";
                        common_classes_for_UI_1($class_name, $func_name,'mo_advertise_2fa');?>
                </div>
                </div>
        </div>
    </div>
    <!--
        *End Of Tabs for accountsetup view.
        *Below are the UI for various sections of Account Creation.
    -->
</div>
<?php
        function common_classes_for_UI($tab_func, $support_func)
        {
            ?>
                <div class="mo_boot_row mo_boot_ml-1">
                    <div class="mo_boot_col-lg-8 mo_boot_mt-2 " >
                        <div>
                            <?php
                                $tab_func();
                            ?>
                        </div>
                    </div>
                    <div class="mo_boot_col-lg-4 mo_boot_mt-2">
                        <div id="mo_saml_support1" style="border: 2px solid rgb(15, 127, 182);background-color:white">
                            <?php
                               $support_func();
                            ?>
                        </div>
                    </div>
                </div>
            <?php
        }

        function common_classes_for_UI_1($class_name, $method, $support_func)
        {
            ?>
                <div class="mo_boot_row  mo_boot_ml-1">
                    <div class="mo_boot_col-lg-8 mo_boot_mt-2 " >
                        <div>
                            <?php
                                $ClassType = $class_name;
                                call_user_func(array($ClassType, $method));
                            ?>
                        </div>
                    </div>
                    <div class="mo_boot_col-lg-4 mo_boot_mt-2">
                        <div id="mo_saml_support1" style="border: 2px solid rgb(15, 127, 182);background-color:white">
                            <?php
                             $support_func();
                            ?>
                        </div>
                    </div>
                </div>
            <?php
        }

        function mo_saml_idp_login_page()
        {
            $result = IDP_Utilities::fetchDatabaseValues('#__miniorange_saml_idp_customer', 'loadAssoc', '*');
            $admin_email = isset($result['email']) ? $result['email'] : '';
            ?>
            <div class="mo_boot_row mo_boot_px-4 mo_boot_py-2" style="border: 2px solid rgb(15, 127, 182); background-color:white">
                <div class="mo_boot_col-sm-12">
                    <form name="f" method="post" action="<?php echo JRoute::_('index.php?option=com_joomlaidp&view=accountsetup&task=accountsetup.verifyCustomer'); ?>">
                        <div class="mo_boot_row mo_boot_mt-3">
                            <div class="mo_boot_col-sm-12">
                                <h3>LOGIN WITH MINIORANGE</h3><hr>
                            </div>
                            <div class="mo_boot_col-sm-12">
                                <p>Please enter your miniOrange account credentials. If you forgot your password then enter your email and click on <b>Forgot your password</b> button. If you are not registered with miniOrange then click on <b>Back to registration</b> button. </p>
                            </div>
                        </div>
                        <div class="mo_boot_row mo_boot_mt-3">
                            <div class="mo_boot_col-sm-2 idp-table-td">
                                <b><span style="color:#FF0000">*</span>Email:</b>
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <input class="mo_boot_form-control idp-textfield mo_saml_idp_textfield" type="email" name="email" id="email"
                                        required placeholder="person@example.com"
                                        value="<?php echo $admin_email; ?>"/>
                            </div>
                        </div>
                        <div class="mo_boot_row mo_boot_mt-3">
                            <div class="mo_boot_col-sm-2">
                                <b><span style="color:#FF0000">*</span>Password:</b>
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <input class="mo_boot_form-control idp-textfield mo_saml_idp_textfield" required type="password"
                                        name="password" placeholder="Enter your miniOrange password"/>
                            </div>
                        </div>
                        <div class="mo_boot_row  mo_boot_mt-4 mo_boot_text-center">
                            <div class="mo_boot_col-sm-11">
                                <input type="submit" class="mo_boot_btn mo_boot_btn-saml" value="Login"/>
                                <a href="https://login.xecurify.com/moas/idp/resetpassword" target="_blank" class="mo_boot_btn mo_boot_btn-danger anchor_tag">Forgot your password?</a>
                                <input type="button" value="Back To Registration" class="mo_boot_btn mo_boot_btn-saml" onclick="mo_back_btn();"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <form id="idp_cancel_form" method="post"
                action="<?php echo JRoute::_('index.php?option=com_joomlaidp&view=accountsetup&task=accountsetup.cancelForm'); ?>">
            </form>

            <?php
        }

        /* Show OTP verification page*/
        function mo_saml_idp_show_otp_verification()
        {
            ?>
             <div class="mo_boot_row mo_boot_px-4 mo_boot_p-4" style="border: 2px solid rgb(15, 127, 182); background-color:white">
             <div class="mo_boot_col-sm-12">
            <form name="f" method="post" id="idp_form" action="<?php echo JRoute::_('index.php?option=com_joomlaidp&view=accountsetup&task=accountsetup.validateOtp'); ?>">
           
                <div class="mo_boot_row " >
                        <h3>Verify Your Email</h3>
                </div>
                <hr>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-2">
                        <b><span style="color:#FF0000">*</span>Enter OTP:</b>
                    </div>
                    <div class="mo_boot_col-sm-6">
                        <input class="mo_boot_form-control" autofocus="true" type="text" name="otp_token" required placeholder="Enter OTP"/>
                    </div>
                    <div class="mo_boot_col-sm-4">
                        <input type="button" value="Resend OTP over Email" class="mo_boot_btn mo_boot_btn-primary" onclick="resendOTPForm();"/>
                    </div>
                </div><br>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12 mo_boot_text-center">
                        <input type="submit" value="Validate OTP" class="mo_boot_btn mo_boot_btn-success"/>
                        <input type="button" value="Back" class="mo_boot_btn mo_boot_btn-danger" onclick="moCancelForm();"/>
                    </div>
                </div>
            </form>

            <form method="post" action="<?php echo JRoute::_('index.php?option=com_joomlaidp&view=accountsetup&task=accountsetup.cancelForm'); ?>"
                id="mo_saml_idp_cancel_form">
            </form>

            <form name="f" id="resend_otp_form" method="post" action="<?php echo JRoute::_('index.php?option=com_joomlaidp&view=accountsetup&task=accountsetup.resendOtp'); ?>">
            </form>
            <hr>
            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-12">
                    <h4>I did not recieve any email with OTP. What should I do?</h4>
                </div>
            </div>
            <form id="phone_verification" method="post" action="<?php echo JRoute::_('index.php?option=com_joomlaidp&view=accountsetup&task=accountsetup.phoneVerification'); ?>">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <p>
                            If you can't see the email from miniOrange in your mails, please check your <b>SPAM Folder</b>. If you don't see an email even in SPAM folder, verify your identity with our alternate method.<br><br>
                            <b>Enter your valid phone number here and verify your identity using one time passcode sent to your phone.</b>
                        </p>
                    </div>
                </div>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-6">
                        <input class="mo_boot_form-control" required="true" pattern="[\+]\d{1,3}\d{10}" autofocus="true" type="text"
                        name="phone_number" id="phone_number" placeholder="Enter Phone Number with country code eg. +1xxxxxxxxx" title="Enter phone number without any space or dashes with country code."/>
                    </div>
                    <div class="mo_boot_col-sm-3">
                        <input type="submit" value="Send OTP" class="mo_boot_btn mo_boot_btn-success"/>
                    </div>
                </div>
            </form>
        </div>
        </div>
            <?php
        }

        /* Create Customer function */
        function mo_saml_idp_registration_page()
        {
            $current_user = JFactory::getUser();
            ?>
                    <!--Register with miniOrange-->
                    <div class="mo_boot_row mo_boot_px-4 mo_boot_py-2 " style="border: 2px solid rgb(15, 127, 182); background-color:white">
                        <div class="mo_boot_col-sm-12" >
                            <form name="f" method="post" action="<?php echo JRoute::_('index.php?option=com_joomlaidp&view=accountsetup&task=accountsetup.registerCustomer'); ?>">
                                <div class="mo_boot_row mo_boot_mt-3">
                                    <div class="mo_boot_col-lg-6">
                                        <h3>REGISTER WITH MINIORANGE</h3>
                                    </div>
                                    <div class="mo_boot_col-lg-4">
                                        <input type="button" value="Already Registered with miniOrange?" style="margin-right: 22px;" id="mo_saml_login_btn" class="mo_boot_btn mo_boot_btn-saml" onclick="mo_login_page();"/>
                                    </div>
                                    <div class="mo_boot_col-lg-2">
                                        <input type="button" style="margin-left: 33%;" id="idprg_end_tour" value="Start Tour" onclick="restart_tourrg();" class="mo_boot_btn mo_boot_btn-saml"/>
                                    </div>
                                </div><hr>
                                <div class="mo_boot_row">
                                    <div class="mo_boot_col-sm-12">
                                        <p class='alert alert-info'>
                                            You should register so that in case you need help, we can help you with step by step
                                            instructions. We support all known SPs - Tableau, Inkling, Moodle, Owncloud, Zendesk etc.
                                            <b>You will also need a miniOrange account to upgrade to the premium version of the plugins</b>.
                                            We do not store any information except the email that you will use to register with us.
                                        </p>
                                    </div>
                                </div>
                                <div class="mo_boot_row">
                                    <div class="mo_boot_col-sm-12">
                                        <p style="color: green">
                                            If you face any issues during registraion then you can
                                            <a href="https://www.miniorange.com/businessfreetrial" target="_blank"><b>click here</b></a>
                                            to quick register your account with miniOrange
                                            and use the same credentials to login into the plugin.
                                        </p>
                                    </div>
                                </div>
                                <div class="mo_boot_row">
                                    <div class="mo_boot_col-sm-12 mo_boot_table-responsive">
                                        <table id="idpregistration" class="idp-table">
                                            <tr>
                                                <td class="idp-table-td"><b><span style="color:#FF0000">*</span>Email:</b></td>
                                                <td>
                                                    <input class="mo_boot_form-control idp-textfield mo_saml_idp_textfield" type="email" name="email" required placeholder="person@example.com"
                                                        value="<?php echo $current_user->email; ?>"/></td>
                                            </tr>
                                            <tr>
                                                <td><b>Phone number:</b></td>
                                                <td><br/><input class="mo_boot_form-control idp-textfield mo_saml_idp_textfield" type="tel" id="phone"
                                                                pattern="[\+]\d{11,14}|[\+]\d{1,4}([\s]{0,1})(\d{0}|\d{9,10})" name="phone"
                                                                title="Phone with country code eg. +1xxxxxxxxxx"
                                                                placeholder="Phone with country code eg. +1xxxxxxxxxx"/><br/>
                                                    <i>We will call only if you call for support</i></td>
                                            </tr>
                                            <tr>
                                                <td><br/><b><span style="color:#FF0000">*</span>Password:</td>
                                                <td><br/><input class="mo_boot_form-control idp-textfield mo_saml_idp_textfield" required type="password"
                                                                name="password" placeholder="Choose your password (Min. length 6)"/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><br/><b><span style="color:#FF0000">*</span>Confirm Password:</b></td>
                                                <td><br/><input class="mo_boot_form-control idp-textfield mo_saml_idp_textfield" required type="password"
                                                                name="confirmPassword" placeholder="Confirm your password"/>
                                                </td>
                                            </tr>
                                            <tr class="mo_boot_text-center">
                                                <td colspan="2"><input type="submit" value="Register" class="mo_boot_btn mo_boot_btn-success mo_boot_mt-4"/></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </form>
                            <form name="f" id="cus_login_form" method="post" action="<?php echo JRoute::_('index.php?option=com_joomlaidp&view=accountsetup&task=accountsetup.customerLoginForm'); ?> "></form>
                        </div>
                    </div>

                <?php
        }

        function mo_saml_idp_account_page()
        {

            $result         = IDP_Utilities::fetchDatabaseValues('#__miniorange_saml_idp_customer', 'loadAssoc', '*');
            $email          = isset($result['email']) ? $result['email'] : '';
            $customer_key   = isset($result['customer_key']) ? $result['customer_key'] : '';
            $api_key        = isset($result['api_key']) ? $result['api_key'] : '';
            $customer_token = isset($result['customer_token']) ? $result['customer_token'] : '';
            $jVersion   = new JVersion;
            $jCmsVersion = $jVersion->getShortVersion();
            $jCmsVersion = substr($jCmsVersion,0,3);
            $joomla_version= IDP_Utilities::getJoomlaCmsVersion();
            $phpVersion = phpversion();
            $PluginVersion =  IDP_Utilities::GetPluginVersion();
            ?>

        <div class="mo_boot_row mo_boot_px-4 mo_boot_py-2"style="border: 2px solid rgb(15, 127, 182); background-color:white">
            <div class="mo_boot_col-sm-12">
                <p style="display: block;margin-top: 10px;text-align: center;font-size: 15px;color: rgba(0,128,0,0.80);background-color: rgba(0,255,0,0.15);padding: 5px;border-radius: 10px;">
                    <b>Thank You for registering with miniOrange.</b><p><br>
                    <h3>Your Profile</h3>
                <div class="mo_boot_row mo_boot_mt-3 mo_boot_p-3 mo_boot_responsive">
                        <table class="table table-striped table-hover table-bordered idp-table">
                            <tr>
                                <td><b>Username/Email</b></td>
                                <td><?php echo $email ?></td>
                            </tr>
                            <tr>
                                <td><b>Customer ID</b></td>
                                <td><?php echo $customer_key ?></td>
                            </tr>
                            <tr>
                                <td><b>Joomla Version</b></td>
                                <td><?php echo  $joomla_version ?></td>
                            </tr>
                            <tr>
                                <td><b>PHP Version</b></td>
                                <td><?php echo  $phpVersion ?></td>
                            </tr>
                            <tr>
                                <td><b>Plugin Version</b></td>
                                <td><?php echo $PluginVersion ?></td>
                            </tr>
                        </table>
                </div>
                <form method="post" id="saml-sp-key-form" action="<?php echo JRoute::_('index.php?option=com_joomlaidp&view=accountsetup&task=accountsetup.removeAccount'); ?>">
                    <h3>Remove Your Account</h3><hr>
                    <p><b>Note: By clicking the button, you can remove your account and all the configurations saved for this site won't be deleted.</b></p><br>
                    <input type="button" value="Remove Account" class="mo_boot_btn mo_boot_btn-danger"  onclick="removeUserAccount();"><br><br><br>
                </form>

            </div>
        </div>
            <?php
        }
        function mo_saml_idp_support()
        {
            $strJsonFileContents = file_get_contents(__DIR__ . '/../../../assets/json/timezones.json'); 
            
            $timezoneJsonArray = json_decode($strJsonFileContents, true);
            $current_user = JFactory::getUser();
            $result       = IDP_Utilities::fetchDatabaseValues('#__miniorange_saml_idp_customer', 'loadAssoc', '*');
            $admin_email  = isset($result['email']) ? $result['email'] : '';
            $admin_phone  = isset($result['admin_phone']) ? $result['admin_phone'] : '';

            if ($admin_email == '' || empty($admin_email))
                $admin_email = $current_user->email;
            ?>
                <div class="mo_boot_row mo_boot_p-4 mo_saml_support" id="mo_saml_support" >
                    <div class="mo_boot_col-sm-12">
                        <div class="mo_boot_row">
                            <h3>Feature Request/ Contact Us<br> (24*7 Support)</h3>
                        </div>
                        <hr>
                    </div>
                   
                    <div class="mo_boot_col-sm-12">	
                        <div class="mo_boot_row ">
						    <img src="<?php echo JUri::base();?>/components/com_joomlaidp/assets/images/phone.svg" width="27" height="27"  alt="Phone Image">
                            <p><strong>&emsp;Need any help? <br>&emsp;Just give us a call at <span style="color:red">+1 978 658 9387</span></strong></p><br>
                            <p style="padding: 0.5rem !important;"> We can help you with configuring your Service Provider. Just send us a query and we will get back to you soon.</p>
						</div>
					</div>
                    <div class="mo_boot_col-sm-12" id="idp_support">
                        <form name="f" method="post" action="<?php echo JRoute::_('index.php?option=com_joomlaidp&view=accountsetup&task=accountsetup.contactUs'); ?>">
                            <input type="email" class="mo_boot_form-control" name="mo_saml_query_email" value="<?php echo $admin_email; ?>" placeholder="Enter your email" required/><br>
                            <input type="text" pattern="[\+][0-9]{7,15}" class="mo_boot_form-control" name="mo_saml_query_phone" value="<?php echo $admin_phone; ?>" placeholder="Enter your phone with country code"/><br>
                            <textarea name="mo_saml_query"  style="border-radius:4px;resize: vertical;width:100%; border: 1px solid #868383!important;" cols="52" rows="5" placeholder="Write your query here" required></textarea>
                            <div class="mo_boot_row mo_boot_text-center">
                                <div class="mo_boot_col-sm-12">
                                    <input type="hidden" name="option1" value="mo_saml_login_send_query"/><br>
                                    <input type="submit" name="send_query" value="Submit Query" class="mo_boot_btn mo_boot_btn-success"  style="margin-top:5px"/>
                                    <input type="button" onclick="window.open('https://faq.miniorange.com/kb/joomla-saml/')" target="_blank" value="FAQ's" class="mo_boot_btn mo_boot_btn-saml" style="margin-top:5px">
                                    <input type="button" onclick="window.open('https://forum.miniorange.com/')" target="_blank" value="Ask Questions on Forum"   class="mo_boot_btn mo_boot_btn-saml" style="margin-top:5px">
                                </div>
					        </div><hr>
                        </form>
                    </div>
                    <div class="mo_boot_col-sm-12">
                        <p><br>
                            If you want custom features in the plugin, just drop an email to
                            <a href="mailto:joomlasupport@xecurify.com"><i style="word-wrap:break-word">joomlasupport@xecurify.com</i></a>
                        </p>
                    </div>
                </div>
             
            <?php
        }


        function mo_advertise_2fa()
            {
                ?>


                <form name="f2" class="mo_boot_p-3">
                    <h5 style="text-align: center;">Looking for a Joomla Two-Factor Authentication (2FA)?</h5>
                    <div class="mo_boot_row ">
                        <div class="mo_boot_col-sm-12">
                            <div class="mo_boot_text-center">
                                    <img src="<?php echo JURI::root(); ?>administrator/components/com_joomlaidp/assets/images/2fa.png" alt="miniOrange icon" height=80% width=80%>
                            </div>   
                            <div class="mo_boot_row">
                                <div class="mo_boot_col-sm-2">
                                    <img src="<?php echo JURI::root(); ?>administrator/components/com_joomlaidp/assets/images/miniorange.png" alt="miniOrange icon" height=25px width=25px>
                                </div>  
                                <div class="mo_boot_col-sm-10">    
                                    <h4>Two-Factor Authentication (2FA)</h4>
                                </div>
                            </div> 
                               <br>
                               <p style="text-align: center;font-size:14px">
                                    Two Factor Authentication (2FA) plugin adds a second layer of authentication at the time of login to
                                    secure your Joomla accounts. 
                                </p>
                        </div>
                    </div><br>
                    <div class="mo_boot_row mo_boot_text-center">
                        <div class="mo_boot_col-sm-12">
                            <a href="https://prod-marketing-site.s3.amazonaws.com/plugins/joomla/joomla-2fa-plugin.zip" class="mo_boot_btn mo_boot_btn-saml" style="margin-top:5px"> Download Plugin</a>
                            <a href="https://plugins.miniorange.com/joomla-two-factor-authentication-2fa"  class="mo_boot_btn mo_boot_btn-success"  style="margin-top:5px" target="_blank">Know More</a>
                        </div>
                    </div>
                </form>

                <?php
            }
            
    ?>
