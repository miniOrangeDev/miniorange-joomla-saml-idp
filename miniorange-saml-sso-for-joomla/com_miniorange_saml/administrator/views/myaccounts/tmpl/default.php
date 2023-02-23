<?php
defined('_JEXEC') or die;
/*
 * @package    miniOrange
 * @subpackage Plugins
 * @license    GNU/GPLv3
 * @copyright  Copyright 2015 miniOrange. All Rights Reserved.
*/

JHtml::_('jquery.framework');
JHtml::_('stylesheet', JUri::base() .'components/com_miniorange_saml/assets/css/mo_saml_style.css');
JHtml::_('stylesheet', JUri::base() . 'components/com_miniorange_saml/assets/css/bootstrap-select-min.css');
JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
JHtml::_('stylesheet', JUri::base() . 'components/com_miniorange_saml/assets/css/miniorange_boot.css');
JHtml::_('script', JUri::base() . 'components/com_miniorange_saml/assets/js/samlUtility.js');
JHtml::_('script', JUri::base() . 'components/com_miniorange_saml/assets/js/bootstrap-select-min.js');

if (!Mo_Saml_Local_Util::is_curl_installed())
{
?>
    <div id="help_curl_warning_title" class="mo_saml_title_panel">
        <p><a target="_blank" style="cursor: pointer;"><span style="color:#FF0000">Warning: PHP cURL extension is not installed or disabled.</span> <span style="color:blue">Click here</span> for instructions to enable it.</font></a></p>
    </div>
    <div hidden="" id="help_curl_warning_desc" class="mo_saml_help_desc">
        <ul>
            <li>Step 1:&nbsp;&nbsp;&nbsp;&nbsp;Open php.ini file located under php installation folder.</li>
            <li>Step 2:&nbsp;&nbsp;&nbsp;&nbsp;Search for <strong>extension=php_curl.dll</strong> </li>
            <li>Step 3:&nbsp;&nbsp;&nbsp;&nbsp;Uncomment it by removing the semi-colon(<strong>;</strong>) in front of it.</li>
            <li>Step 4:&nbsp;&nbsp;&nbsp;&nbsp;Restart the Apache Server.</li>
        </ul>
        For any further queries, please <a href="mailto:joomlasupport@xecurify.com">contact us</a>.
    </div>
    <?php
}

$tab = "idp";
$get = JFactory::getApplication()->input->get->getArray();
$test_config = isset($get['test-config']) ? true: false;
if (isset($get['tab']) && !empty($get['tab']))
{
    $tab = $get['tab'];
}
?>
<?php
    $saml_configuration=SAML_Utilities::_get_values_from_table('#__miniorange_saml_config');
    $session = JFactory::getSession();
    $session->set('show_test_config', false);
    if($test_config)
    {
        $session->set('show_test_config', true);
    }
    if($saml_configuration['show_tc_popup']==false)
    {
        echo "
        <script>
            jQuery(document).ready(function(){
                show_TC_modal();
            });
        </script>
        ";
        $database_name = '#__miniorange_saml_config';
        $updatefieldsarray = array(
            'show_tc_popup' => true,
        );
        Mo_saml_Local_Util::generic_update_query($database_name, $updatefieldsarray);
    }

    echo "
    <style>
    .skip-to{
        position: absolute;
        top: -30em;
        left: 0;
    }
    </style>
    ";
    

?>

    <div class="mo_boot_row" style="width:100%!important">
        <div class="mo_boot_col-lg-10 ">
            <div class="nav-tab-wrapper mo_idp_nav-tab-wrapper ">
                <a id="idptab"  class="mo_nav-tab <?php echo $tab == 'idp' ? 'mo_nav_tab_active' : ''; ?>" href="#identity-provider"
                onclick="add_css_tab('#idptab');" 
                data-toggle="tab" >Service Provider Setup
                </a>

                <a id="descriptiontab" class="mo_nav-tab <?php echo $tab == 'description' ? 'mo_nav_tab_active' : ''; ?>" href="#description"
                onclick="add_css_tab('#descriptiontab');"
                data-toggle="tab" >Service Provider Metadata
                </a>

                <a id="sso_login" class="mo_nav-tab <?php echo $tab == 'sso_settings' ? 'mo_nav_tab_active' : ''; ?>" href="#sso_settings"
                onclick="add_css_tab('#sso_login');"
                data-toggle="tab">Login Settings
                </a>

                <a id="attributemappingtab" class="mo_nav-tab <?php echo $tab == 'attribute_mapping' ? 'mo_nav_tab_active' : ''; ?>" href="#attribute-mapping"
                onclick="add_css_tab('#attributemappingtab');"
                data-toggle="tab">Attribute Mapping
                </a>

                <a id="groupmappingtab" class="mo_nav-tab <?php echo $tab == 'group_mapping' ? 'mo_nav_tab_active' : ''; ?>" href="#group-mapping"
                onclick="add_css_tab('#groupmappingtab');"
                data-toggle="tab">Group Mapping
                </a>

                <a id="custcert" class="mo_nav-tab <?php echo $tab == 'ccert' ? 'mo_nav_tab_active' : ''; ?>" href="#ccert"
                onclick="add_css_tab('#custcert');"
                data-toggle="tab">Custom Certificate
                </a>

                <a id="licensingtab" class="mo_nav-tab <?php echo $tab == 'licensing' ? 'mo_nav_tab_active' : ''; ?>" href="#licensing-plans"
                onclick="add_css_tab('#licensingtab');"
                data-toggle="tab" style="background-color: orange !important;">Upgrade
                </a>

                <a id="supporttab" class="mo_nav-tab <?php echo $tab == 'request-demo' ? 'mo_nav_tab_active' : ''; ?>" href="#request-demo"
                onclick="add_css_tab('#supporttab');"
                data-toggle="tab" style="background-color: orange !important;">Trial
                </a>

                <a id="registrationtab" class="mo_nav-tab <?php echo $tab == 'account' ? 'mo_nav_tab_active' : ''; ?>" href="#account"
                onclick="add_css_tab('#registrationtab');"
                data-toggle="tab">My Account
                </a>

                

            </div>
        </div>
        <div class="mo_boot_col-lg-2">
        <button id="mo_TC"  onclick="show_TC_modal()" style=" float: right; margin-right:10px;padding: 7px !important" class="mo_boot_btn mo_boot_btn-saml">T&C</button>
        <div id="my_TC_Modal" class="TC_modal">
            <div class="TC_modal-content">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-12 mo_boot_text-center">
                        <span style="font-size: 28px;"><strong>Terms & Condition</strong></span>
                        <span class="TC_modal_close" onclick="close_TC_modal()">&times;</span>
                    </div>
                    
                </div>
                    <hr>
                    <ul> 
                        <li>1. We'll be sending the email to your admin email to get in touch with you if you have any issues while testing our plugin.</li>
                        <li>2. Your email address will not be shared or used by any third parties. The main aim of picking an email address is to connect with you.</li>
                        <li>3. You can update your email address below if you like. So that if you require assistance, we can contact you at your correct email address.</li>
                        <li>4. If you require any assistance, you may contact us at <strong>joomlasupport@xecurify.com</strong>.</li>
                        <li>
                            <form method="post" name="f" action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.saveAdminMail'); ?>" > 
                                <?php
                                    $dVar=new JConfig(); 
                                    $check_email = $dVar->mailfrom;
                                    $result       = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_customer_details');
                                    
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
    
    <script>
        function close_TC_modal(){
            jQuery("#my_TC_Modal").css("display","none");
            location.reload();
        }
        function show_TC_modal(){
            jQuery("#my_TC_Modal").css("display","block");
        }
        function add_css_tab(element) 
        {
            jQuery(".mo_nav_tab_active ").removeClass("mo_nav_tab_active").removeClass("active");
            jQuery(element).addClass("mo_nav_tab_active");
        }
  
    </script>
    <div class="mo_boot_row" style="background-color:#e0e0d8;">
        <div class="mo_boot_col-sm-12">
            <div class="tab-content" id="myTabContent">
                <div id="account" class="tab-pane <?php if ($tab == 'account') echo 'active'; ?> ">
                    <?php common_classes('account_tab','mo_saml_local_support');?>
                </div>

                <div id="description" class="tab-pane <?php if ($tab == 'description') echo 'active'; ?> ">
                    <?php common_classes_for_UI('description', 'mo_saml_local_support','mo_saml_adv_pagerestriction');?>
                </div>

                <div id="sso_settings" class="tab-pane <?php if ($tab == 'sso_settings') echo 'active'; ?>">
                    <?php common_classes_for_UI('mo_sso_login','mo_saml_local_support','mo_saml_adv_net');?>
                </div>

                <div id="identity-provider" class="tab-pane <?php if ($tab == 'idp') echo 'active'; ?>">
                    <?php common_classes_for_UI('select_identity_provider', 'mo_saml_local_support','mo_saml_advertise');?>
                </div>

                <div id="attribute-mapping" class="tab-pane <?php if ($tab == 'attribute_mapping') echo 'active'; ?>">
                    <?php common_classes_for_UI('attribute_mapping','mo_saml_local_support','mo_saml_adv_idp');?>
                </div>

                <div id="group-mapping" class="tab-pane <?php if ($tab == 'group_mapping') echo 'active'; ?>">
                    <?php common_classes_for_UI('group_mapping','mo_saml_local_support','mo_saml_adv_loginaudit');?>
                </div>

                <div id="proxy-setup" class="tab-pane <?php if ($tab == 'proxy_setup') echo 'active'; ?>">
                    <?php common_classes_for_UI('proxy_setup', 'mo_saml_local_support','mo_saml_advertise');?>
                </div>

                <div id="request-demo" class="tab-pane <?php if ($tab == 'request_demo') echo 'active'; ?>">
                    <?php common_classes_for_UI('requestfordemo', 'mo_saml_advertise','mo_saml_adv_idp');?>
                </div>
                    
                <div id="licensing-plans" class="tab-pane <?php if ($tab == 'licensing') echo 'active'; ?>">
                    <div class="row-fluid">
                        <table style="width:100%;">
                            <caption></caption>
                            <tr>
                                <th id="s"></th>
                            </tr>
                            <tr>
                                <td style="width:65%;vertical-align:top;" class="configurationForm">
                                    <?php Licensing_page(); ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div id="ccert" class="tab-pane <?php if ($tab == 'ccert') echo 'active'; ?>">
                    <?php common_classes('customcertificate', 'mo_saml_local_support');?>
                </div>

                <div id="advance" class="tab-pane <?php if ($tab == 'advancesettab') echo 'active'; ?>">
                    <div class="mo_boot_row mo_saml_table_layout_1">
                        <div class="mo_boot_col-sm-12 ">
                            <?php advancesetting(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
<?php function common_classes_for_UI($tab_func, $support_func, $add_func)
{
    ?>
    <div class="mo_boot_row mo_boot_px-4 mo_boot_py-3">
        <div class="mo_boot_col-sm-8">
            <div>
                <?php
                    $tab_func();
                ?>
            </div>
        </div>
        <div class="mo_boot_col-sm-4">
            <div id="mo_saml_support1" >
                <?php
                $support_func();
                ?>
            </div>
        <div id="mo_saml_support2" class="mo_boot_py-3">
                <?php
                    $add_func();
                ?>
            </div>
        </div>
    </div>
    <?php
}

function common_classes($tab_func, $support_func)
{
    ?>
    <div class="mo_boot_row mo_boot_px-4 ">
        <div class="mo_boot_col-sm-8 mo_boot_py-3 ">
            <div>
                <?php
                    $tab_func();
                ?>
            </div>
        </div>
        <div class="mo_boot_col-sm-4 " >
            <div id="mo_saml_support1" class="mo_boot_py-3">
                <?php
                    $support_func();
                    ?>
                </div>
            </div>
        </div>
    <?php
}
?>

<?php

function account_tab()
{
    ?>
   <div class="mo_boot_row mo_boot_m-0 mo_boot_p-0" id="registrationForm">
    <?php
            $customer_details = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_customer_details');
            $login_status = $customer_details['login_status'];
            $registration_status = $customer_details['registration_status'];
            if ($login_status)
            {
                mo_saml_local_login_page();
            }
            else if ($registration_status == 'MO_OTP_DELIVERED_SUCCESS' || $registration_status == 'MO_OTP_VALIDATION_FAILURE' || $registration_status == 'MO_OTP_DELIVERED_FAILURE')
            {
                mo_saml_local_show_otp_verification();
            }
            else if (!Mo_Saml_Local_Util::is_customer_registered())
            {
                mo_saml_local_registration_page();
            }
            else
            {
                mo_saml_local_account_page();
            }
        ?>
    </div>
    <?php
}

function mo_saml_local_login_page()
{
    ?>
    <div class="mo_boot_row  mo_boot_mr-1 mo_boot_p-3" style="border: 2px solid rgb(15, 127, 182); background-color:white">
        <div class="mo_boot_col-sm-12">
            <form name="f" method="post" action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.verifyCustomer'); ?>">
                <div class="mo_boot_row mo_boot_mt-2">
                    <div class="mo_boot_col-sm-12">
                        <input type="hidden" name="option1" value="mo_saml_local_verify_customer" />
                        <h3>Login with miniOrange</h3><hr>
                        <p>
                            Please enter your miniOrange account credentials. If you forgot your password then enter your email and click
                            on <strong>Forgot your password</strong> button. If you are not registered with miniOrange then click on <strong>Back to registration</strong> button.
                        </p>
                    </div>
                </div>
                <div id="panel1" style="align:center!important;">
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-3 mo_boot_offset-sm-1">
                            <strong><em style="color:#FF0000">*</em> Email:</strong>
                        </div>
                        <div class="mo_boot_col-sm-6">
                            <input class="mo_saml_table_textbox mo_boot_form-control" type="email" name="email" style="border: 1px solid #868383 !important;" required placeholder="person@example.com" value="" />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-3 mo_boot_offset-sm-1">
                            <strong><em style="color:#FF0000">*</em> Password:</strong>
                        </div>
                        <div class="mo_boot_col-sm-6">
                            <input class="mo_saml_table_textbox mo_boot_form-control" required type="password" name="password" style="border: 1px solid #868383 !important;" placeholder="Enter your miniOrange password" />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-12 mo_boot_text-center">
                            <input type="submit" class="mo_boot_btn mo_boot_btn-saml mo_boot_mt-1" value="Login"/>
                            <input type="button" value="Back to Registration" onclick="moSAMLCancelForm();" class="mo_boot_btn mo_boot_btn-danger mo_boot_mt-1" />
                            <a href="https://login.xecurify.com/moas/idp/resetpassword" target="_blank"  class="mo_boot_btn mo_boot_btn-saml mo_boot_mt-1 anchor_tag">Forgot your password?</a>
                        </div>
                    </div>
                </div>
            </form>
            <form id="cancel_form" method="post" action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.cancelform'); ?>">
                <input type="hidden" name="option1" value="mo_saml_local_cancel" />
            </form>
        </div>
    </div>
    <?php
}

    function mo_saml_local_account_page()
    {
        $result = new Mo_saml_Local_Util();
        $result = $result->_load_db_values('#__miniorange_saml_customer_details');
        $email = $result['email'];
        $customer_key = $result['customer_key'];
        $api_key = $result['api_key'];
        $customer_token = $result['customer_token'];
        $hostname = Mo_Saml_Local_Util::getHostname();
        $joomla_version=SAML_Utilities::getJoomlaCmsVersion();
        $phpVersion = phpversion();
        $PluginVersion = SAML_Utilities::GetPluginVersion();
        ?>
        <div class="mo_boot_row  mo_boot_mr-1 mo_boot_p-3" id="cum_pro" style="background-color:#FFFFFF;border:2px solid rgb(15, 127, 182);">
            <div class="mo_boot_col-sm-12 mo_saml_welcome_message">
                <h4>Thank You for registering with miniOrange.</h4>
            </div>
            <div class="mo_boot_col-sm-12 table-responsive mo_boot_mt-3">
                <table class="table table-striped table-hover table-bordered ">
                <tr>
                    <td class="mo_profile_td_h">Username/Email</td>
                    <td class="mo_profile_td"><?php echo $email ?></td>
                </tr>
                <tr>
                    <td class="mo_profile_td_h">Customer ID</td>
                    <td class="mo_profile_td"><?php echo $customer_key ?></td>
                </tr>
                <tr>
                    <td class="mo_profile_td_h">Joomla Version</td>
                    <td class="mo_profile_td"><?php echo  $joomla_version ?></td>
                </tr>
                <tr>
                    <td class="mo_profile_td_h">PHP Version</td>
                    <td class="mo_profile_td"><?php echo  $phpVersion ?></td>
                </tr>
                <tr>
                    <td class="mo_profile_td_h">Plugin Version</td>
                    <td class="mo_profile_td"><?php echo $PluginVersion ?></td>
                </tr>
            </table>
            </div>
            <div class="mo_boot_col-sm-12 mo_boot_text-center" id="sp_proxy_setup">
                <input id="sp_proxy" type="button" class='mo_boot_btn mo_boot_btn-saml mo_boot_d-inline-block' onclick='show_proxy_form()' value="Configure Proxy"/>
                <form class="mo_boot_d-inline-block" action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.ResetAccount'); ?>" name="reset_useraccount" method="post">
                    <input type="button"  value="Remove Account" onclick='submit();' class="mo_boot_btn mo_boot_btn-danger"  /> <br/>
                </form>
            </div>
        </div>
        <div class="mo_boot_row" id="submit_proxy" style="background-color:#FFFFFF;border:2px solid rgb(15, 127, 182); display:none ;" >
            <?php proxy_setup() ?>
        </div>
        <?php
    }

/* Show OTP verification page*/
function mo_saml_local_show_otp_verification()
{
    ?>
    <div id="panel2" class="mo_boot_p-4" style="background-color:#FFFFFF; border:2px solid rgb(15, 127, 182);">
        <form name="f" method="post" id="idp_form" action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.validateOtp'); ?>">
            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-12">
                    <input type="hidden" name="option1" value="mo_saml_local_validate_otp" />
                    <h3>Verify Your Email</h3>
                    <hr>
                </div>
            </div>
            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-2">
                    <strong><span style="color:#FF0000">*</span>Enter OTP:</strong>
                </div>
                <div class="mo_boot_col-sm-6">
                    <input class="mo_boot_form-control" autofocus="true" type="text" name="otp_token" required placeholder="Enter OTP"/>
                </div>
                <div class="mo_boot_col-sm-4">
                    <a style="cursor:pointer;" class="mo_boot_btn mo_boot_btn-primary" onclick="document.getElementById('resend_otp_form').submit();">Resend OTP over Email</a>
                </div>
            </div>
            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-12 mo_boot_text-center"><br>
                    <input type="submit" value="Validate OTP" class="mo_boot_btn mo_boot_btn-success"/>
                    <input type="button" value="Back" class="mo_boot_btn mo_boot_btn-danger" onclick="moSAMLBack();"/>
                </div>
            </div>
        </form>

        <form method="post" action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.cancelform'); ?>" id="mo_saml_cancel_form">
            <input type="hidden" name="option1" value="mo_saml_local_cancel" />
        </form>

        <form name="f" id="resend_otp_form" method="post" action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.resendOtp'); ?>">
            <input type="hidden" name="option1" value="mo_saml_local_resend_otp"/>
        </form>
        <hr>
        <div class="mo_boot_row">
            <div class="mo_boot_col-sm-12">
                <h3>I did not receive any email with OTP. What should I do?</h3>
            </div>
        </div>
        <form id="phone_verification" method="post" action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.phoneVerification'); ?>">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <input type="hidden" name="option1" value="mo_saml_local_phone_verification" />
                        <p>
                            If you can't see the email from miniOrange in your mails, please check your <strong>SPAM Folder</strong>. If you don't see an email even in SPAM folder, verify your identity with our alternate method.<br><br>
                            <strong>Enter your valid phone number here and verify your identity using one time passcode sent to your phone.</strong>
                        </p>
                    </div>
                </div>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-6">
                        <input class="mo_boot_form-control" required="true" pattern="[\+]\d{1,3}\d{10}" autofocus="true" type="text"
                        name="phone_number" id="phone" placeholder="Enter Phone Number with country code eg. +1xxxxxxxxx" title="Enter phone number without any space or dashes with country code. (Please include country code ex:+91xxxxxxxxxx)"/>
                    </div>
                    <div class="mo_boot_col-sm-3">
                        <input type="submit" value="Send OTP on Phone" class="mo_boot_btn mo_boot_btn-primary"/>
                    </div>
                </div>
        </form>
    </div>

    <?php
}
/* End Show OTP verification page*/
/* Create Customer function */
function mo_saml_local_registration_page()
{
    $database_name = '#__miniorange_saml_customer_details';
    $updatefieldsarray = array(
        'new_registration' => 1,
    );
    $result = new Mo_saml_Local_Util();
    $result->generic_update_query($database_name, $updatefieldsarray);
    ?>

    <!--Register with miniOrange-->
    <div class="mo_boot_row  mo_boot_mr-1 mo_boot_p-3" id="submit_proxy" style="background-color:#FFFFFF; border:2px solid rgb(15, 127, 182);display:none;">
        <div class="mo_boot_col-sm-12">
            <?php 
                proxy_setup() 
            ?>
        </div>
    </div>
    <div class="mo_boot_row  mo_boot_mr-1 mo_boot_p-3" id="panel1" style="border: 2px solid rgb(15, 127, 182); background-color:white">
        <div class="mo_boot_col-sm-12">
            <form name="f" method="post" action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.registerCustomer'); ?>">
                <div class="mo_boot_row mo_boot_mt-3">
                    <div class="mo_boot_col-lg-5  mo_boot_mt-1">
                        <input type="hidden" name="option1" value="mo_saml_local_register_customer" />
                        <h3>Register with miniOrange</h3>
                    </div>
                    <div class="mo_boot_col-lg-7  mo_boot_mt-1">
                        <input type="button" value="Already registered with miniOrange?" class="mo_boot_btn mo_boot_btn-saml" style="margin-right:2%" onclick="mo_login_page();"/>
                    </div>
                </div>
                <hr/>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <h4>Why should I register?</h4>
                        <p class='alert alert-info'>
                            You should register so that in case you need help, we can help you with step by step instructions. We support all known IdPs - ADFS, Okta, Salesforce,
                            Shibboleth, SimpleSAMLphp, OpenAM, Centrify, Ping, RSA, IBM, Oracle, OneLogin, Bitium, WSO2 etc. <strong>You will also need a miniOrange account to upgrade
                            to the license version of the plugins</strong>. We do not store any information except the email that you will use to register with us.
                        </p><br>
                        <p style="color: #fa2727">
                            If you face any issues during registraion then you can 
                            <a href="https://www.miniorange.com/businessfreetrial" target="_blank"><strong>click here</strong></a>
                            to quick register your account with miniOrange and use the same credentials to login into the plugin.
                        </p>
                    </div>
                </div><br>
                <div id="spregister" class="mo_saml_settings_table">
                    <div class="mo_boot_row" id="spemail">
                        <div class="mo_boot_col-sm-3">
                            <strong>Email<em style="color:#FF0000">*</em>:</strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <?php 
                                $current_user = JFactory::getUser();
                                $result = new Mo_saml_Local_Util();
                                $result = $result->_load_db_values('#__miniorange_saml_customer_details');
                                $admin_email = $result['email'];
                                $admin_phone = $result['admin_phone'];
                                if ($admin_email == '')
                                {
                                    $admin_email = $current_user->email;
                                }
                            ?>
                            <input class="mo_saml_table_textbox mo_boot_form-control" type="email" name="email" style="border: 1px solid #868383 !important;" placeholder="person@example.com" required value="<?php echo $admin_email; ?>" />
                        </div>
                    </div><br>
                    <div class="mo_boot_row" id="sprg_phone">
                        <div class="mo_boot_col-sm-3">
                            <strong>Phone number:</strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input class="mo_saml_table_textbox mo_boot_form-control" type="tel" id="phone" style="border: 1px solid #868383 !important;" pattern="[\+]\d{11,14}|[\+]\d{1,4}([\s]{0,1})(\d{0}|\d{9,10})" name="phone" title="Phone with country code eg. +1xxxxxxxxxx"  placeholder="Phone with country code eg. +1xxxxxxxxxx" value="<?php echo $admin_phone; ?>" />
                            <p><em><strong>NOTE:</strong>We will call only if you call for support</em></p>
                        </div>
                    </div>
                    <div class="mo_boot_row" id="sprg_passwd">
                        <div class="mo_boot_col-sm-3">
                            <strong>Password<em style="color:#FF0000">*</em>:</strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input class="mo_saml_table_textbox mo_boot_form-control"  required  type="password" style="border: 1px solid #868383 !important;" name="password" placeholder="Choose your password (Min. length 6)" />
                        </div>
                    </div><br>
                    <div class="mo_boot_row" id="rg_repasswd">
                        <div class="mo_boot_col-sm-3">
                            <strong>Confirm Password<em style="color:#FF0000">*</em>:</strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input class="mo_saml_table_textbox mo_boot_form-control"  required type="password" style="border: 1px solid #868383 !important;" name="confirmPassword" placeholder="Confirm your password" />
                        </div>
                    </div>
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-12 mo_boot_text-center">
                            <input type="submit" value="Register" class="mo_boot_btn mo_boot_btn-saml" />
                            <div class="mo_boot_d-inline-block" id="sp_proxy_setup"><br>
                                <input id="sp_proxy" type="button" class='mo_boot_btn mo_boot_btn-saml' onclick='show_proxy_form_one()' value="Configure Proxy"/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <form name="f" id="customer_login_form" method="post" action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.customerLoginForm'); ?> ">
            </form>
        </div>
    </div>
    <?php
}

function description()
{
    $siteUrl = JURI::root();
    $sp_base_url = '';

    $result = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_config');
    $sp_entity_id = isset($result['sp_entity_id']) ? $result['sp_entity_id'] : '';

    if($sp_entity_id == ''){
        $sp_entity_id = $siteUrl . 'plugins/authentication/miniorangesaml';
    }

    if(isset($result['sp_base_url'])){
        $sp_base_url = $result['sp_base_url'];
    }

    if (empty($sp_base_url))
        $sp_base_url = $siteUrl;

    $org_name=$result['organization_name'];
    $org_dis_name=$result['organization_display_name'];
    $org_url=$result['organization_url'];
    $tech_name=$result['tech_per_name'];
    $tech_email=$result['tech_email_add'];
    $support_name=$result['support_per_name'];
    $support_email=$result['support_email_add'];

    ?>
        <div class="mo_boot_row  mo_boot_mr-1  mo_boot_p-3"  style="background-color:#FFFFFF; border:2px solid rgb(15, 127, 182);">
            <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-lg-10 mo_boot_mt-1">
                        <h3>Service Provider Metadata <sup><a href="https://developers.miniorange.com/docs/joomla/saml-sso/saml-service-provider-metadata" target="_blank" style="font-size:20px;color:black" title="Know More"><div class="fa fa-question-circle-o"></div></a></sup></h3>
                    </div>
                </div><hr>
            </div>
            <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                <h3 style="color: #d9534f;">Update SP Entity ID or Base URL</h3><br>
                <form action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.updateSPIssuerOrBaseUrl'); ?>" method="post" name="updateissueer" id="identity_provider_update_form">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-3">
                            <strong><span style="color: red;">*</span>SP EntityID / Issuer <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext">If you have already shared the URLs or Metadata with your IdP, do not change SP EntityID. It might break your existing login flow.</span></div></strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input class="mo_saml_table_textbox mo_boot_form-control" type="text" style="border: 1px solid #868383 !important;" name="sp_entity_id" value="<?php echo $sp_entity_id; ?>" required />
                            <br>
                        </div>

                        <div class="mo_boot_col-sm-3">
                            <strong><span style="color: red;">*</span>SP Base URL</strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input class="mo_saml_table_textbox mo_boot_form-control" type="text" style="border: 1px solid #868383 !important;" name="sp_base_url" value="<?php echo $sp_base_url; ?>" required />
                        </div>
                    </div>
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-12 mo_boot_text-center"><br>
                            <input type="submit" class="mo_boot_btn mo_boot_btn-success" value="Update"/>
                        </div>
                    </div>
                </form><hr>
            </div>
            
        
            <div  id="metadata" class="mo_boot_col-sm-12  mo_boot_mt-2">
                <p style="color: #d9534f;">
                    <strong>Provide this plugin information to your Identity Provider team. 
                        You can choose any one of the below options:</strong>
                </p>
                <p  class="mo_boot_mt-3">
                    <strong>a) Provide this metadata URL to your Identity Provider OR download the .xml file to upload it in your IDP:</strong>
                </p>
        
                <div class="mo_boot_col-sm-12  mo_boot_mt-2 mo_boot_table-responsive">
                    <div class="mo_saml_highlight_background_url_note">
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-10">
                                <strong>Metadata URL:
                                    <span id="idp_metadata_url" >
                                        <a  href='<?php echo $sp_base_url . '?morequest=metadata'; ?>' id='metadata-linkss' target='_blank'><?php echo '<strong>' . $sp_base_url . '?morequest=metadata </strong>'; ?></a>
                                    </span>  
                                </strong>
                            </div>
                            <div class="mo_boot_col-2">
                                <em class="fa fa-lg fa-copy mo_copy_sso_url mo_copytooltip" onclick="copyToClipboard('#idp_metadata_url');" ><span class="mo_copytooltiptext copied_text">Copy</span></em>
                            </div>
                        </div>
                    </div>
             
                </div>
            
                <div class="mo_boot_col-sm-12 mo_boot_mt-2">
                    <p id="mo_download_metadata" class="mo_boot_mt-3">
                        <strong>Download metadata XML file:</strong>
                        <a href="<?php echo $sp_base_url . '?morequest=download_metadata'; ?>" class="mo_boot_btn mo_boot_btn-saml anchor_tag">
                            Download XML Metadata
                        </a>
                    </p>
                    <h2 style="text-align: center">OR</h2>
                    <p  class="mo_boot_mt-3">
                        <strong>b) You will need the following information to configure your Identity Provider. Copy it and keep it handy:</strong>
                    </p>
                </div>
            
                <div id="mo_other_idp" style="overflow-x: scroll; ">
                    <table class='customtemp'>
                        <tr>
                            <td style="font-weight:bold;padding: 15px;">SP-EntityID / Issuer</td>
                            <td><span id="entidy_id"><?php echo $sp_entity_id; ?></span>
                                <em class="fa fa-pull-right  fa-lg fa-copy mo_copy mo_copytooltip"  style="color:black"
                                    onclick="copyToClipboard('#entidy_id');"><span class="mo_copytooltiptext copied_text">Copy</span></em>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold;padding: 15px;">ACS URL /<br>Single Sign-On URL (SSO)</td>
                            <td>
                                <span id="acs_url"><?php echo $sp_base_url . '?morequest=acs'; ?></span>
                                <em class="fa fa-pull-right  fa-lg fa-copy mo_copy mo_copytooltip" onclick="copyToClipboard('#acs_url');"  style="color:black"><span class="mo_copytooltiptext copied_text">Copy</span> </em>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold;padding: 15px;">Audience URI</td>
                            <td>
                                <span id="audience_url"><?php echo $sp_entity_id; ?></span>
                                <em class="fa fa-pull-right  fa-lg fa-copy mo_copy mo_copytooltip"  style="color:black"
                                    onclick="copyToClipboard('#audience_url');" ><span class="mo_copytooltiptext copied_text">Copy</span></em>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold;padding: 15px;">NameID Format</td>
                            <td>
                                <span id="name_id_format">urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified</span>
                                <em class="fa fa-pull-right  fa-lg fa-copy mo_copy mo_copytooltip" style="color:black"
                                    onclick="copyToClipboard('#name_id_format');"><span class="mo_copytooltiptext copied_text">Copy</span> <em>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold;padding: 15px;">Single Logout URL (SLO)</td>
                            <td>
                            
                                Available in the <strong><a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Premium</strong></a>, <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Enterprise</strong></a></strong> versions
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold;padding: 15px;">Default Relay State (Optional)</td>
                            <td>
                            Available in the <strong><a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Standard</strong></a>, <strong><a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Premium</strong></a>, <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Enterprise</strong></a></strong> versions
                            </td>
                        </tr>

                    
                        <tr>
                            <td style="font-weight:bold;padding: 15px;"><b>Certificate (Optional)</b></td>
                            <td>
                            Available in the <strong><a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Standard</strong></a>, <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Premium</strong></a>, <a href='#' class='premium'><strong>Enterprise</strong></a></strong> versions
                            </td>
                        </tr>
                    </table>
                </div>
                <hr>
            </div>
         
        <div class=" mo_boot_col-sm-12 mo_boot_mt-3">
            <div class="mo_boot_row">
                <div><h3 style="color: #d9534f;">Customize metadata organizational details</h3></div><br><br><br>
                <div><strong style="margin-left:10px;"> 
                <a href='#' class='premium' onclick="moSAMLUpgrade();">[Standard,</a>
                    <a href='#' class='premium' onclick="moSAMLUpgrade();">Premium,</a>
                    <a href='#' class='premium' onclick="moSAMLUpgrade();">Enterprise]</a>
                </strong></div><br>
                </div>
                <form action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.updateOrganizationDetails'); ?>" method="post" name="updateorg" id="custmize_organization">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-12 ">
                            <details !important open>
                                <summary class="mo_saml_summary"!important>
                                    <strong>1. Oraganization</strong>
                                </summary><hr>
                                <div class="mo_boot_row">
                                    <div class="mo_boot_col-sm-3" style="margin-left:30px">
                                        <strong >Name :<span style="color: red;">*</span></strong>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <input class="mo_saml_table_textbox mo_boot_form-control" type="text" style="border: 1px solid #868383 !important;" name="organization_name" value="<?php echo $org_name; ?>" required disabled/>
                                    </div><br><br>
                                    <div class="mo_boot_col-sm-3" style="margin-left:30px">
                                        <strong>Display Name :<span style="color: red;">*</span></strong>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <input class="mo_saml_table_textbox mo_boot_form-control" type="text" style="border: 1px solid #868383 !important;" name="organization_display_name" value="<?php echo $org_dis_name; ?>" required  disabled/>
                                    </div><br><br>
                                    <div class="mo_boot_col-sm-3" style="margin-left:30px">
                                        <strong>URL :<span style="color: red;">*</span></strong>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <input class="mo_saml_table_textbox mo_boot_form-control" type="text" style="border: 1px solid #868383 !important;" name="organization_url" value="<?php echo $org_url; ?>" required  disabled/>
                                    </div>
                                </div><br>
                            </details>
                        </div>
                        <div class="mo_boot_col-sm-12 ">
                            <details !important>
                                <summary class="mo_saml_summary">
                                    <strong>2. Technical contact</strong>
                                </summary><hr>
                                <div class="mo_boot_row">
                                    <div class="mo_boot_col-sm-3" style="margin-left:30px">
                                        <strong>Person name:<span style="color: red;">*</span></strong>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <input class="mo_saml_table_textbox mo_boot_form-control" type="text" style="border: 1px solid #868383 !important;" name="tech_per_name" value="<?php echo $tech_name; ?>" required   disabled/>
                                    </div><br><br>
                                    <div class="mo_boot_col-sm-3" style="margin-left:30px">
                                        <strong>Email address:<span style="color: red;">*</span></strong>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <input class="mo_saml_table_textbox mo_boot_form-control" type="text" style="border: 1px solid #868383 !important;"  name="tech_email_add" value="<?php echo $tech_email; ?>" required  disabled/>
                                    </div>
                                </div><br>
                            </details>
                        </div>
                                
                        <div class="mo_boot_col-sm-12 ">
                            <details !important>
                                <summary class="mo_saml_summary" !important>
                                    <strong>3. Support contact</strong>
                                </summary><hr>
                                <div class="mo_boot_row">
                                    <div class="mo_boot_col-sm-3" style="margin-left:30px">
                                        <strong>Person name:<span style="color: red;">*</span></strong>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <input class="mo_saml_table_textbox mo_boot_form-control" type="text" style="border: 1px solid #868383 !important;" name="support_per_name"  value="<?php echo $support_name; ?>" required  disabled />
                                    </div><br><br>
                                    <div class="mo_boot_col-sm-3" style="margin-left:30px">
                                        <strong>Email address:<span style="color: red;">*</span></strong>
                                    </div><br><br>
                                    <div class="mo_boot_col-sm-8">
                                        <input class="mo_saml_table_textbox mo_boot_form-control" type="text" style="border: 1px solid #868383 !important;" name="support_email_add" value="<?php echo $support_email; ?>" required  disabled/>
                                    </div>
                                </div>
                            </details>
                        </div>         
                            
                        <div class="mo_boot_col-sm-12 mo_boot_text-center"><br>
                            <input type="submit" class="mo_boot_btn mo_boot_btn-success" value="Update" disabled/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
    
        <style>
                .selected-text, .selected-text>*{
                background: #2196f3;
                color: #ffffff;
                }
        </style>
    <?php
}
function mo_saml_get_saml_request_url()
{
    $url = '?morequest=sso&q=sso';
    return $url;
}
function mo_saml_get_saml_response_url()
{
    $url = '?morequest=sso&RelayState=response';
    return $url;
}
function Licensing_page()
{
	$useremail = new Mo_saml_Local_Util();
	$useremail = $useremail->_load_db_values('#__miniorange_saml_customer_details');
    if (isset($useremail)) $user_email = $useremail['email'];
    else $user_email = "xyz";
    ?>

    <div id="myModal" class="TC_modal">
        <div class="TC_modal-content" style="width: 40%!important;">
            <span class="TC_modal_close" onclick="hidemodal()" >&times;</span><br><br>
            <div class=" mo_boot_text-center">
            <p>
                You Need to Login / Register in <strong>My Account</strong> tab to Upgrade your License 
            </p><br><br>
            
            <a href="<?php echo JURI::base()?>index.php?option=com_miniorange_saml&tab=account" class="mo_boot_btn mo_boot_btn-primary">LOGIN / REGISTER</a>
            </div>
        </div>
    </div>
    <div class="mo_boot_row mo_boot_ml-4 mo_boot_mr-4 mo_boot_mb-4">
        <div class="mo_boot_col-sm-12 mo_boot_p-2 mo_boot_text-center">     
            <style>
                .switch
                {
                    position: relative;
                    display: inline-block;
                    width: 210px;
                    height: 34px;
                
                }

                .switch input
                {
                    opacity: 0;
                    width: 0;
                    height: 0;
                }

                .slider
                {
                    position: absolute;
                    cursor: pointer;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #001b4c;
                    -webkit-transition: .4s;
                    transition: .4s;

                }

                .slider:before
                {
                    position: absolute;
                    content: "Plans";
                    font-weight:400;
                    height: 26px;
                    width: 100px;
                    left: 4px;
                    bottom: 4px;
                    background-color: white;
                    -webkit-transition: .4s;
                    transition: .4s;
                }

                input:checked + .slider
                {
                    background-color: #001b4c;
                }

                input:focus + .slider
                {
                    box-shadow: 0 0 1px #2196F3;
                }

                input:checked + .slider:before
                {
                    -webkit-transform: translateX(100px);
                    -ms-transform: translateX(100px);
                    transform: translateX(100px);
                    content:"Bundle Plan";
                }

                /* Rounded sliders */
                .slider.round
                {
                    border-radius: 34px;
                }

                .slider.round:before
                {
                    border-radius: 34px;
                }
                .navbar-links{
                    float: left;
                    display: block;
                    color: white;
                    text-align: center;
                
                    text-decoration: none;
                    font-size: 17px;
                    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
                    border-radius: 15px;
                    background: #226a8b;
                
                
                }

                .navbar-links:hover{
                    color: white;
                    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.8);
                }
                .works-step{
                    margin-bottom: 50px;
                }


                .works-step div{
                    color:#0a3273;
                    border: 2px solid #0a3273;
                    border-radius: 50%;
                    width: 50px;
                    height: 50px;
                    text-align: center;
                    padding: 8px;
                    float: left;
                    font-size: 20px;
                    margin-right: 25px;
                    margin-bottom: 27px;
                }

                .works-step p{
                    font-size:15px;
                    text-align: left;
                }


                .plan-boxes{
                        width:85%;
                    }

                    .plan-box{
                        border: 1px solid #6e727a;
                        min-height:250px;
                        background-color: #80808026;
                        border-radius: 10px;
                        margin: 0 10px;
                        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
                        transition: 0.3s;
                    }

                    .plan-box:hover{
                        color:black;
                        border-color: #093553;
                        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
                    }

                    .plan-box div{
                        padding:20px;
                        /* height: 100px; */
                        font-size: 14px;
                        line-height: normal;
                        font-weight:400;    
                    }
                    .plan-box div:first-child{
                        height: 90px;
                        text-align: center;
                        border-bottom: 4px solid #b9babd;
                    }

                    .payment-images{
                        width:140px;
                    }

                    .plan-box div:last-child{
                        font-size: 15px;
                        line-height: 23px;
                    }
                    
                .payment-methods{
                    background-color: #f9f9f9;
                    padding-left: 10%;
                    padding-right: 10%;
                    margin-top: 0px;
                    margin-bottom: 0%
                }
            </style>
           
            <br>
        </div>

        <div class="tab-content" >
        
    
            <div class="mo_boot_col-sm-12" style="border:2px solid blue;background:white;box-sizing:border-box;">
            
            <div id="navbar" style="">
                <div class="mo_boot_row " style="background-color: #e4e6e8  !important; height:90px; text-align:center;position:stikcy" >
                <div class="mo_boot_col-sm-3 mo_boot_mt-3">  <strong><a href="#licensing_plans" id="plans-section" class="navbar-links" style=" width:70%;padding:10px 9px 9px 15px">Plans</a></strong></div>
                <div class="mo_boot_col-sm-3 mo_boot_mt-3">  <strong><a href="#addonContent" id="addon-section" class="navbar-links" style="  width:70%;padding:10px 9px 9px 15px ">Add Ons</a></strong></div>
                <div class="mo_boot_col-sm-3 mo_boot_mt-3">  <strong><a href="#upgrade-steps" id="upgrade-section" class="navbar-links" style=" width:70%;padding:10px 9px 9px 15px ">Upgrade Steps</a></strong></div>
                <div class="mo_boot_col-sm-3 mo_boot_mt-3">  <strong><a href="#payment-method" id="payment-section" class="navbar-links" style=" width:70%;padding:10px 9px 9px 15px ">Payment Methods</a></strong></div>
                    
                </div><br>
            </div>
          
            <div class="mo_boot_m-2 mo_boot_text-center">
                <h2>Choose From The Below Plans To Upgrade</h2>
                <label class="switch "  style=" text-align: center !important;">
                    <input type="checkbox" id="bundle_checked" onclick="show_bundle()" />
                    <span class="slider round"></span>
                </label>
            </div>
            <div class="mo_boot_m-5 mo_boot_text-center" style="display:none;background-color:#ecf2f2;"  id="bundle_content">
                <div class="mo_boot_col-sm-12">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-4 mo_boot_p-4">
                            <div class="mo_boot_row mo_boot_p-2" style="box-shadow: 0 4px 8px 0 rgb(0 0 0 / 20%);background-color:white;">
                                <div class="mo_boot_col-sm-8 mo_boot_p-5 mo_boot_bg-light">
                                    <h3>
                                        <strong>Joomla SAML SP Standard</strong><br>
                                        <strong>+</strong><br>
                                        <strong>Joomla SCIM Premium</strong>
                                    </h3>
                                </div>
                                <div class="mo_boot_col-sm-4 mo_boot_p-3" style="background:#226a8b">
                                    <h3 style="color:white">
                                        <strong>$199</strong><br>
                                        <strong>+</strong><br>
                                        <strong>$249</strong><br><br>
                                        <strong><del style="color:orange">$448</del><br>$399</strong>
                                    </h3>
                                    <a href="https://www.miniorange.com/contact" target="_blank" class="mo_boot_mt-3 mo_boot_btn mo_boot_btn-success mo_verticle_center">Contact Us</a>
                                </div>
                            </div>
                        </div>
                        <div class="mo_boot_col-sm-4 mo_boot_p-4">
                            <div class="mo_boot_row mo_boot_p-2" style="box-shadow: 0 4px 8px 0 rgb(0 0 0 / 20%);background-color:white;">
                                <div class="mo_boot_col-sm-8 mo_boot_p-5 mo_boot_bg-light">
                                    <h3>
                                        <strong>Joomla SAML SP Premium</strong><br>
                                        <strong>+</strong><br>
                                        <strong>Joomla SCIM Premium</strong>
                                    </h3>
                                </div>
                                <div class="mo_boot_col-sm-4 mo_boot_p-3" style="background:#226a8b">
                                    <h3 style="color:white">
                                        <strong>$349</strong><br>
                                        <strong>+</strong><br>
                                        <strong>$249</strong><br><br>
                                        <strong><del style="color:orange">$598</del><br>$549</strong>
                                    </h3>
                                    <a href="https://www.miniorange.com/contact" target="_blank" class="mo_boot_mt-3 mo_boot_btn mo_boot_btn-success mo_verticle_center">Contact Us</a>
                                </div>
                            </div>
                        </div>
                        <div class="mo_boot_col-sm-4 mo_boot_p-4">
                            <div class="mo_boot_row mo_boot_p-2" style="box-shadow: 0 4px 8px 0 rgb(0 0 0 / 20%);background-color:white;">
                                <div class="mo_boot_col-sm-8 mo_boot_p-5 mo_boot_bg-light">
                                    <h3>
                                        <strong>Joomla SAML SP Enterprise</strong><br>
                                        <strong>+</strong><br>
                                        <strong>Joomla SCIM Premium</strong>
                                    </h3>
                                </div>
                                <div class="mo_boot_col-sm-4 mo_boot_p-3" style="background:#226a8b">
                                    <h3 style="color:white">
                                        <strong>$399</strong><br>
                                        <strong>+</strong><br>
                                        <strong>$249</strong><br><br>
                                        <strong><del style="color:orange">$648</del><br>$599</strong>
                                    </h3>
                                    <a href="https://www.miniorange.com/contact" target="_blank" class="mo_boot_mt-3 mo_boot_btn mo_boot_btn-success mo_verticle_center">Contact Us</a>
                                </div>
                            </div>
                        </div>
                        <div class="mo_boot_col-sm-4 mo_boot_p-4"  >
                            <div class="mo_boot_row mo_boot_p-2" style="box-shadow: 0 4px 8px 0 rgb(0 0 0 / 20%);background-color:white;">
                                <div class="mo_boot_col-sm-8 mo_boot_p-5 mo_boot_bg-light">
                                    <h3>
                                        <strong>Joomla SAML SP Standard</strong><br>
                                        <strong>+</strong><br>
                                        <strong>Joomla Page and Article Restriction Premium</strong>
                                    </h3>
                                </div>
                                <div class="mo_boot_col-sm-4 mo_boot_p-3" style="background:#226a8b">
                                    <h3 style="color:white">
                                        <strong>$199</strong><br>
                                        <strong>+</strong><br>
                                        <strong>$199</strong><br><br>
                                        <strong><del style="color:orange">$398</del><br>$349</strong>
                                    </h3>
                                    <a href="https://www.miniorange.com/contact" target="_blank" class="mo_boot_mt-3 mo_boot_btn mo_boot_btn-success  mo_verticle_center">Contact Us</a>
                                </div>
                            </div>
                        </div>
                        <div class="mo_boot_col-sm-4 mo_boot_p-4">
                            <div class="mo_boot_row mo_boot_p-2" style="box-shadow: 0 4px 8px 0 rgb(0 0 0 / 20%);background-color:white;">
                                <div class="mo_boot_col-sm-8 mo_boot_p-5 mo_boot_bg-light">
                                    <h3>
                                        <strong>Joomla SAML SP Premium</strong><br>
                                        <strong>+</strong><br>
                                        <strong>Joomla Page and Article Restriction Premium</strong>
                                    </h3>
                                </div>
                                <div class="mo_boot_col-sm-4 mo_boot_p-3" style="background:#226a8b">
                                    <h3 style="color:white">
                                        <strong>$349</strong><br>
                                        <strong>+</strong><br>
                                        <strong>$199</strong><br><br>
                                        <strong><del style="color:orange">$548</del><br>$499</strong>
                                    </h3>
                                    <a href="https://www.miniorange.com/contact" target="_blank" class="mo_boot_mt-3 mo_boot_btn mo_boot_btn-success mo_verticle_center">Contact Us</a>
                                </div>
                            </div>
                        </div>
                        <div class="mo_boot_col-sm-4 mo_boot_p-4">
                            <div class="mo_boot_row mo_boot_p-2" style="box-shadow: 0 4px 8px 0 rgb(0 0 0 / 20%);background-color:white;">
                                <div class="mo_boot_col-sm-8 mo_boot_p-5 mo_boot_bg-light">
                                    <h3>
                                        <strong>Joomla SAML SP Enterprise</strong><br>
                                        <strong>+</strong><br>
                                        <strong>Joomla Page and Article Restriction Premium</strong>
                                    </h3>
                                </div>
                                <div class="mo_boot_col-sm-4 mo_boot_p-3" style="background:#226a8b">
                                    <h3 style="color:white">
                                        <strong>$399</strong><br>
                                        <strong>+</strong><br>
                                        <strong>$199</strong><br><br>
                                        <strong><del style="color:orange">$598</del><br>$549</strong>
                                    </h3>
                                    <a href="https://www.miniorange.com/contact" target="_blank" class="mo_boot_mt-3 mo_boot_btn mo_boot_btn-success mo_verticle_center">Contact Us</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="licensing_plans">
            <div class="tab-pane active " style="  background-color:#ecf2f2;" id="license_content"  >
            <div class="cd-pricing-container cd-has-margins"><br>
                <ul class="cd-pricing-list cd-bounce-invert" >
                    <li class="cd-black" style="box-shadow: 0 4px 8px 0 rgb(0 0 0 / 40%);">
                        <ul class="cd-pricing-wrapper" >
                            <li id="singlesite_tab" data-type="singlesite" class="mosslp is-visible cd-singlesite" style="width: 100%">
                                <header class="cd-pricing-header" style="height: 170px">
                                    <h2 style="margin-bottom: 10px" >Basic</h2><span class="mo_saml_plan_description"><strong>(Unlimited Authentication)</strong></span>
                                
                                </header> <!-- .cd-pricing-header -->
                                <div style="text-align:center">
                                        <span id="plus_total_price" style="font-weight: bolder;font-size: xx-large;color:black">$99*</span><br><span class="mo_saml_note"><strong>[One Time Payment]</strong></span>
                                </div>
                            
                                <footer class="cd-pricing-footer">
                                <?php
                                      if (!Mo_Saml_Local_Util::is_customer_registered())
                                      {
                                          echo '<button class="cd-select" style="font-size: 85.5%; cursor: pointer;width:100%;border:none;height:70px" onclick="showmodal()">UPGRADE NOW</button>';
                                      }
                                      else
                                      {
                                          $redirect1= "https://login.xecurify.com/moas/login?username=" . $user_email . "&redirectUrl=https://login.xecurify.com/moas/initializepayment&requestOrigin=joomla_saml_sso_basic_plan";
                                          echo '<a target="_blank" class="cd-select" style="font-size: 85.5%; cursor: pointer;width:100%;border:none;height:70px;padding:25px"  href="'.$redirect1.'" >UPGRADE NOW</a>';
                                      }
                                ?>
                                </footer><br>
                                <!--                                <strong style="color: coral;">See the Standard Plugin features list below</strong>-->
                                <div class="cd-pricing-body" >

                                    <ul class="cd-pricing-features" >
                                        <li class="mo_pricing_list">&#9989;&emsp;Unlimited auto creation of users</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Unlimited authentication</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Configure SP Using Metadata XML File/URL</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Export Configuration</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Basic Role Mapping</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Basic Attribute Mapping(User Name, Email)</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Select SAML Request binding type</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Auto-Redirect to IdP</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Default redirect URL after Login</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Default redirect URL after Logout</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Signed Request for SSO</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Custom Role Mapping</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Custom Attribute Mapping</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Single Logout</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Backend Login for Super Users/Administrator/Manager</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Backdoor URL</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Backend Login for Super Users/Administrator/Manager child groups</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Domain Restriction</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Domain Mapping</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Generate Custom SP Certificate</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Auto-sync IdP Configuration from metadata</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Store Multiple IDP certificates</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Multiple IdP Support*</li>
                                        <li class="mo_pricing_list" style="text-align:center"><strong>End to End Identity Provider Integration</strong></li>
                                    </ul>
                                </div>
                            </li>
                        </ul> <!-- .cd-pricing-wrapper -->
                    </li>
                    <li class="cd-black" style="box-shadow: 0 4px 8px 0 rgb(0 0 0 / 40%);">
                        <ul class="cd-pricing-wrapper"  >
                            <li id="singlesite_tab" data-type="singlesite" class="mosslp is-visible cd-singlesite" style="width: 100%">
                                <header class="cd-pricing-header" style="height: 170px">
                                    <h2 style="margin-bottom: 10px" >Standard<br/></h2><span class="mo_saml_plan_description"><strong>(AUTO REDIRECT TO IDP)</strong></span><br>
                                
                                
                                </header> <!-- .cd-pricing-header -->
                                <div style="text-align:center" >
                                        <span id="plus_total_price" style="font-weight: bolder;font-size: xx-large;color:black">$199*</span><br><span class="mo_saml_note"><strong>[One Time Payment]</strong></span> <br/>
                                    </div>
                                <footer class="cd-pricing-footer" >
                                <?php
                                    if (!Mo_Saml_Local_Util::is_customer_registered())
                                    {
                                        echo '<button class="cd-select" style="font-size: 85.5%; cursor: pointer;width:100%;border:none;height:70px" onclick="showmodal()">UPGRADE NOW</button>';
                                    }
                                    else
                                    {
                                        $redirect1= "https://login.xecurify.com/moas/login?username=" . $user_email . "&redirectUrl=https://login.xecurify.com/moas/initializepayment&requestOrigin=joomla_saml_sso_standard_plan";
                                        echo '<a target="_blank" class="cd-select" style="font-size: 85.5%; cursor: pointer;width:100%;border:none;height:70px;padding:25px"  href="'.$redirect1.'" >UPGRADE NOW</a>';
                                    }
                                ?>
                                </footer><br>
                                <!--                                <strong style="color: coral;">See the Standard Plugin features list below</strong>-->
                                <div class="cd-pricing-body">
                                    <ul class="cd-pricing-features">
                                        <li class="mo_pricing_list">&#9989;&emsp;Unlimited auto creation of users</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Unlimited authentication</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Configure SP Using Metadata XML File/URL</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Impot/Export Configuration</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Basic Role Mapping</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Basic Attribute Mapping(User Name, Email)</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Select SAML Request binding type</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Auto-Redirect to IdP</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Default redirect URL after Login</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Default redirect URL after Logout</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Signed Request for SSO</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Custom Role Mapping</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Custom Attribute Mapping</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Single Logout</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Backend Login for Super Users/Administrator/Manager</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Backdoor URL</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Backend Login for Super Users/Administrator/Manager child groups</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Domain Restriction</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Domain Mapping</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Generate Custom SP Certificate</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Auto-sync IdP Configuration from metadata</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Store Multiple IDP certificates</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Multiple IdP Support*</li>
                                        <li class="mo_pricing_list" style="text-align:center"><strong>End to End Identity Provider Integration</strong></li>
                                        
                                    </ul>
                                </div>
                            </li>
                        </ul> <!-- .cd-pricing-wrapper -->
                    </li>
                    <li class="cd-black" style="box-shadow: 0 4px 8px 0 rgb(0 0 0 / 40%);">
                        <ul class="cd-pricing-wrapper">
                            <li id="singlesite_tab" data-type="singlesite" class="mosslp is-visible" style="height=600px; width: 100%; left: 30%; ">
                                <header class="cd-pricing-header" style="height: 170px">
                                    <h2 style="margin-bottom: 10px">Premium<br/></h2><span class="mo_saml_plan_description"><strong>(ATTRIBUTE & ROLE MANAGEMENT)</strong></span><br/>
                                    
                                </header> <!-- .cd-pricing-header -->
                                <div style="text-align:center">
                                        <span id="plus_total_price" style="font-weight: bolder;font-size: xx-large;color:black">$349*</span><br><span class="mo_saml_note"><strong>[One Time Payment]</strong></span> <br/> 
                                </div>
                                <footer class="cd-pricing-footer">
                                    <?php
                                        if (!Mo_Saml_Local_Util::is_customer_registered())
                                        {
                                            echo '<button class="cd-select" style="font-size: 85.5%; cursor: pointer;width:100%;border:none;height:70px" onclick="showmodal()">UPGRADE NOW</button>';
                                        }
                                        else
                                        {
                                            $redirect1= "https://login.xecurify.com/moas/login?username=" . $user_email . "&redirectUrl=https://login.xecurify.com/moas/initializepayment&requestOrigin=joomla_saml_sso_premium_plan";
                                            echo '<a target="_blank" class="cd-select" style="font-size: 85.5%; cursor: pointer;width:100%;border:none;height:70px;padding:25px"  href="'.$redirect1.'" >UPGRADE NOW</a>';
                                        }
                                    ?>
                                </footer><br>
                                <div class="cd-pricing-body">
                                    <ul class="cd-pricing-features">
                                        <li class="mo_pricing_list">&#9989;&emsp;Unlimited auto creation of users</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Unlimited authentication</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Configure SP Using Metadata XML File/URL</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Import/Export Configuration</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Basic Role Mapping</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Basic Attribute Mapping(User Name, Email)</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Select SAML Request binding type</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Auto-Redirect to IdP</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Default redirect URL after Login</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Default redirect URL after Logout</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Signed Request for SSO</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Custom Role Mapping</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Custom Attribute Mapping</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Single Logout</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Backend Login for Super Users/administrator/Manager</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Backdoor URL</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Backend Login for Super Users/administrator/Manager child groups</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Domain Restriction</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Domain Mapping</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Generate Custom SP Certificate</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Auto-sync IdP Configuration from metadata</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Store Multiple IDP certificates</li>
                                        <li class="mo_pricing_list">&#10060;&emsp;Multiple IdP Support*</li>
                                        <li class="mo_pricing_list" style="text-align:center"><strong>End to End Identity Provider Integration</strong></li>
                                    </ul>
                                </div> <!-- .cd-pricing-body -->
                            </li>
                        </ul> <!-- .cd-pricing-wrapper -->
                    </li>
                    <li class="cd-black" style="box-shadow: 0 4px 8px 0 rgb(0 0 0 / 40%);">
                        <ul class="cd-pricing-wrapper">
                            <li id="singlesite_tab" data-type="singlesite" class="mosslp is-visible" style="width: 100%; left: 60%;">
                                <header class="cd-pricing-header" style="height: 170px">
                                    <h2 style="margin-bottom:10px;">Enterprise<br/></h2><span class="mo_saml_plan_description"><strong>(AUTO-SYNC IDP METADATA & MULTIPLE CERTIFICATE)</strong></span><br/>
                                    
                                </header> <!-- .cd-pricing-header -->
                                <div style="text-align:center" >
                                        <span id="plus_total_price" style="font-weight: bolder;font-size: xx-large;color:black">$399*</span><br><span class="mo_saml_note"><strong>[One Time Payment]</strong></span> <br/>
                                </div>
                                <footer class="cd-pricing-footer">
                                <?php
                                    if (!Mo_Saml_Local_Util::is_customer_registered())
                                    {
                                        echo '<button class="cd-select" style="font-size: 85.5%; cursor: pointer;width:100%;border:none;height:70px;padding:25px" onclick="showmodal()">UPGRADE NOW</button>';
                                    }
                                    else
                                    {
                                        $redirect1= "https://login.xecurify.com/moas/login?username=" . $user_email . "&redirectUrl=https://login.xecurify.com/moas/initializepayment&requestOrigin=joomla_saml_sso_enterprise_plan";
                                        echo '<a target="_blank" class="cd-select" style="font-size: 85.5%; cursor: pointer;width:100%;border:none;height:70px;padding:25px"  href="'.$redirect1.'" >UPGRADE NOW</a>';
                                    }
                                ?>
                                </footer><br>
                                <!--                                <strong style="color: coral;">See the Enterprise Plugin features list below</strong>-->
                                <div class="cd-pricing-body">
                                    <ul class="cd-pricing-features">
                                        <li class="mo_pricing_list">&#9989;&emsp;Unlimited auto creation of users</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Unlimited authentication</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Configure SP Using Metadata XML File/URL</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Import/Export Configuration</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Basic Role Mapping</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Basic Attribute Mapping(User Name, Email)</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Select SAML Request binding type</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Auto-Redirect to IdP</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Default redirect URL after Login</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Default redirect URL after Logout</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Signed Request for SSO</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Custom Role Mapping</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Custom Attribute Mapping</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Single Logout</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Backend Login for Super Users/Administrator/Manager</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Backdoor URL</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Backend Login for Super Users/Administrator/Manager child groups</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Domain Restriction</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Domain Mapping</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Generate Custom SP Certificate</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Auto-sync IdP Configuration from metadata</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Store Multiple IDP certificates</li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Multiple IdP Support*</li>
                                        <li class="mo_pricing_list" style="text-align:center"><strong>End to End Identity Provider Integration</strong></li>
                                    </ul>
                                </div> <!-- .cd-pricing-body -->
                                <!-- .cd-pricing-body -->
                            </li>
                        </ul> <!-- .cd-pricing-wrapper -->
                    </li>
                    <li class="cd-black" style="box-shadow: 0 4px 8px 0 rgb(0 0 0 / 40%);">
                        <ul class="cd-pricing-wrapper" >
                            <li id="singlesite_tab" data-type="singlesite" class="mosslp is-visible cd-singlesite" style="width: 100%">
                                <header class="cd-pricing-header" style="height: 170px">
                                    <h2 style="margin-bottom: 10px" >All Inclusive</h2><span class="mo_saml_plan_description"><strong>(ALL FEATURES ALONG WITH ADD-ONS)</strong></span><br>
                                    <select name="user-slab" class="mo_sp_inclusive_plans slab_dropdown">
                                        <option value="basic" style="text-align:center" selected>Basic</option>
                                        <option value="pro" style="text-align:center" >Pro</option>    
                                    </select>
                                </header> <!-- .cd-pricing-header -->
                                <div style="text-align:center" >
                                    <span id="plus_total_price_basic" style="font-weight: bolder;font-size: xx-large;color:black;">$499*</span>
                                    <span id="plus_total_price_pro" style="font-weight: bolder;font-size: xx-large;color:black;display:none">$599*</span>
                                </div>
                                <div style="text-align:center" >
                                    <span class="mo_saml_note"><strong>[One Time Payment]</strong></span>
                                </div>
                                <footer class="cd-pricing-footer">
                                <?php
                                    echo '<a target="_blank" class="cd-select" style="font-size: 85.5%; cursor: pointer;width:100%;border:none;height:70px;padding:25px" href="https://www.miniorange.com/contact" >CONTACT US</a>';
                                ?>
                                </footer><br>
                                <!--                                <strong style="color: coral;">See the Standard Plugin features list below</strong>-->
                                <div class="cd-pricing-body">

                                    <ul class="cd-pricing-features" style="">
                                        <li class="mo_pricing_list">&#9989;&emsp;All Enterprise Plan Features<br><span style="font-size:10px!important"></span></li>
                                        <li class="mo_pricing_list">&#9989;&emsp;Support following Add-Ons:</li>
                                        <li type="square" class="mo_pricing_list">1. SSO Login Audit</li>
                                        <li type="square" class="mo_pricing_list">2. Role/Group Based Redirection</li>
                                        <li type="square" class="mo_pricing_list">3. Integrate with Community Builder</li>
                                        <li type="square" id="mo_pricing_list4" class="mo_pricing_list" style="display:none">4. Page and Article Restriction</li>
                                        <li class="mo_pricing_list" id="mo_pricing_list3" style="display:none"></li>
                                        <li class="mo_pricing_list" id="mo_pricing_list2"></li>
                                        <li class="mo_pricing_list" id="mo_pricing_list1"></li>
                                        <li class="mo_pricing_list"></li>
                                        <li class="mo_pricing_list"></li>
                                        <li class="mo_pricing_list"></li>
                                        <li class="mo_pricing_list"></li>
                                        <li class="mo_pricing_list"></li>
                                        <li class="mo_pricing_list"></li>
                                        <li class="mo_pricing_list"></li>
                                        <li class="mo_pricing_list"></li>
                                        <li class="mo_pricing_list"></li>
                                        <li class="mo_pricing_list"></li>
                                        <li class="mo_pricing_list"></li>
                                        <li class="mo_pricing_list"></li>
                                        <li class="mo_pricing_list"></li>
                                        <li class="mo_pricing_list"></li>
                                        <li class="mo_pricing_list"></li>
                                        <li class="mo_pricing_list"></li>
                                        <li class="mo_pricing_list" style="text-align:center"><strong>End to End Identity Provider Integration</strong></li>
                                    </ul>
                                </div>
                            </li>
                        </ul> <!-- .cd-pricing-wrapper -->
                    </li>
                </ul> <!-- .cd-pricing-list -->
            </div> <!-- .cd-pricing-container -->
        </div><br>
        </div>  
                                </div>  
            <!-- Modal -->
            <br/>
        
            <?php echo showAddonsContent();?>
        <div style="border:2px solid blue;background:white;box-sizing:border-box; padding-top: 10px;" id="upgrade-steps">
            <div  style="padding-top: 1px;">
                        <h2 style="text-align:center">HOW TO UPGRADE TO PREMIUM</h2>
                        <!-- <hr style="background-color:#17a2b8; width: 20%;height: 3px;border-width: 3px;"> -->
            </div> <hr>
                <section   id="section-steps" >
                    <div class="mo_boot_col-sm-12 mo_boot_row ">
                        <div class=" mo_boot_col-sm-6 works-step">
                                    <div><strong>1</strong></div>
                                    <p>
                                        Click on <strong><em>Upgrade Now</em></strong> button for required license version and you will be redirected to register with miniorange page. After registration click on  <strong><em>Upgrade Now</em></strong> and you will be redirected to<span stye="margin-left:10px"></span> miniOrange login console.
                                    </p>
                        </div>
                        <div class="mo_boot_col-sm-6 works-step">
                                    <div><strong>4</strong></div>
                                    <p>
                                        You can download the license version plugin from the <strong><em>View License > Releases and Downloads</em></strong> section on the miniOrange console.
                                    </p>
                        </div>
                            
                    </div>
                    <div class="mo_boot_col-sm-12 mo_boot_row ">
                        <div class=" mo_boot_col-sm-6 works-step">
                                    <div><strong>2</strong></div>
                                    <p>
                                        Enter your miniOrange account credentials. You can create one for free <em><strong><a href="<?php echo JURI::base()?>index.php?option=com_miniorange_saml&tab=account">here</a></strong></em> if you don't have. Once you have successfuly logged in, you will be redirected towards the payment page.
                                    </p>
                        </div>
                        <div class="mo_boot_col-sm-6 works-step">
                                    <div><strong>5</strong></div>
                                    <p>
                                        Uninstall the free plugin that is currently installed from the JOOMLA admin dashboard.
                                    </p>
                        </div>
                            
                    </div>

                        <div class="mo_boot_col-sm-12 mo_boot_row ">
                        <div class="mo_boot_col-sm-6 works-step">
                                    <div><strong>3</strong></div>
                                    <p>
                                        Enter your card details and proceed for payment. On successful payment completion, the license version plugin will be available to download. 
                                    </p>
                            </div>
                            <div class=" mo_boot_col-sm-6 works-step">
                                <div><strong>6</strong></div>
                                
                                    <p >
                                        Now install the downloaded license version plugin and login using the account which you have used for the purchase of license version plugin.<br> <br>
                                    </p>
                            </div>
                        
                        </div> 
                    </section>
            
                    
        </div>
        <div  id="payment-method"  style="border:2px solid blue;background:white;box-sizing:border-box; padding-top: 10px;margin-top:20px;" >
            <h2 style="text-align:center">ACCEPTED PAYMENT METHODS</h2><hr>
                <section style="height: 350px;" >
                <br>
                    <div class="mo_boot_col-sm-12 mo_boot_row">  
                    
                            <div class="mo_boot_col-sm-4">
                                <div class="plan-box">
                                    <div style="background-color:white; border-radius:10px; ">
                                        <em style="font-size:30px;" class="fa fa-cc-amex" aria-hidden="true"></em>
                                        <em style="font-size:30px;" class="fa fa-cc-visa" aria-hidden="true"></em>
                                        <em style="font-size:30px;" class="fa fa-cc-mastercard" aria-hidden="true"></em>
                                    </div>
                                    <div >
                                        If the payment is made through Credit Card/International Debit Card, the license will be created automatically once the payment is completed.
                                    </div>
                                </div>
                            </div>
                            <div class="mo_boot_col-sm-4">
                                <div class="plan-box">
                                    <div style="background-color:white; border-radius:10px; ">
                                        <img class="payment-images"  src="<?php echo JUri::base();?>/components/com_miniorange_saml/assets/images/paypal.png" alt=""  width="140px;">
                                    </div>
                                    <div>
                                        Use the following PayPal ID <em><strong>info@xecurify.com</strong></em> for making the payment via PayPal.<br><br>
                                    </div>
                                </div>
                            </div>
                            <div class="mo_boot_col-sm-4">
                                <div class="plan-box">
                                    <div style="background-color:white; border-radius:10px; ">
                                        <img class="payment-images card-image" src="" alt=""> 
                                        <em style="font-size:30px;" class="fa fa-university" aria-hidden="true"><span style="font-size: 20px;font-weight:500;">&nbsp;&nbsp;Bank Transfer</span></em>
                                        
                                    </div>
                                    <div> 
                                        If you want to use bank transfer for the payment then contact us at <strong><em><span>info@xecurify.com</span></em></strong>  so that we can provide you the bank details.
                                    </div>
                                </div>
                            </div>
                        
                    </div>
                    <div class="row">
                            <p style="margin-top:20px;font-size:16px;text-align:center">
                                <span style="font-weight:500;"> Note :</span> Once you have paid through PayPal/Net Banking, please inform us so that we can confirm and update your license.
                            </p>
                    </div>
                        </section>
            </div>
            <!--Don't delete below function call-->
            <div style="border:2px solid blue;background:white;box-sizing:border-box; padding-top: 10px;margin-top:20px;" >
            <h2 style="text-align:center">LICENSING POLICY</h2><hr>
                <div style="margin-left: 33px;"><br>
                    <h3>This is the price for 1 instance. Check our <a style="color:blue;" href="https://login.xecurify.com/moas/login?username=&redirectUrl=https://login.xecurify.com/moas/initializepayment&requestOrigin=joomla_saml_sso_enterprise_plan" target='_blank'>payment page</a> for full details.</h3>
                    <br>
                    <p><h3>* Multiple IdPs Supported</h3></p>
                    <p>If you want users from different Identity Providers to SSO into your site then you can configure the plugin with multiple IDPs. Additional charges will be applicable based on the number of Identity Providers you wish to configure.</p><br>
                    <p><h3>The plugin licenses are perpetual, which include 12 months of plugin maintenance (version upgrades). The Support Plan provides annual support. After a year, you can renew your license at 50% of the current license cost at the time of renewal if you want regular updates and security patches.</h3></p><br>
                    
                    <h3>End to End Identity Provider Integration - </h3>
                    <p>We will setup a Conference Call / Gotomeeting and do end to end configuration for your IDP as well as plugin. We provide services to do the configuration on your behalf.</p>
                    <p>If you have any doubts regarding the licensing plans, you can email us at <a href="mailto:joomlasupport@xecurify.com">joomlasupport@xecurify.com</a>.</p>
                    <h3>Return Policy -</h3>
                    <p>If the licensed plugin you purchased is not working as advertised and you’ve attempted to resolve any feature issues with our support team, which couldn't get resolved,
                        we will refund the whole amount. If the request for issue resolution is made within 10 days of the purchase, the issue will be resolved within the period stated by the team.

                        <br><br><strong>Note that this policy does not cover the following cases:</strong>
                        <li>1. Change in mind or change in requirements after purchase.</li>

                        <li>2. Infrastructure issues do not allow the functionality to work.</li>
                    </p><br>
                </div>
            </div>
        </div>
    </div>
    <script>
                jQuery('#plans-section').click(function(){
                    jQuery("#bundle_checked").prop("checked", false);
                    jQuery("#bundle_content").css("display","none");
                    jQuery("#license_content").css("display","block");
                    

                });
        function show_bundle()
        {
            if(jQuery("#bundle_checked").is(":checked"))
            {
                jQuery("#bundle_content").css("display","block");
                jQuery("#license_content").css("display","none");
            }
            else
            {
                jQuery("#bundle_content").css("display","none");
                jQuery("#license_content").css("display","block");
            }
        }
    </script>
    <style>
    .cd-black :hover #singlesite_tab.is-visible{
        margin-right : 4px;
        transition : 0.4s;
        -moz-transition : 0.4s;
        -webkit-transition : 0.4s;
        border-radius: 8px;
        transform: scale(1.03);
        -ms-transform: scale(1.03); /* IE 9 */
        -webkit-transform: scale(1.03); /* Safari */

        box-shadow: 0 0 4px 1px rgba(255,165, 0, 0.8);
    }
    h1 {
            margin: .67em 0;
            font-size: 2em;
        }

        ul {
            list-style: none; /* Remove HTML bullets */
            padding: 0;
            margin: 0;
        }
        
        li {
            list-style: none; /* Remove HTML bullets */
            padding: 0;
            margin: 0;
        }
    </style>
    <style>
    .popover-title{
            background: #ffff99;
        }
        .popover-content{ background: #F2F8FA; }
    </style>
    
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <script>
        jQuery(document).ready(function(){
            jQuery('[data-toggle="popover"]').popover();   
        });
    </script>
    <?php
}

function showAddonsContent(){

    define("MO_ADDONS_CONTENT",serialize( array(

        "JOOMLA_ICB" =>      [
            'id' => 'mo_joomla_icb',
            'addonName'  => 'Integrate with Community Builder',
            'addonDescription'  => 'By using the Community Builder Add-on you would be mapping the user details into the CB\'s comprofilers fields table which containing the values from the table comprofiler',
            'addonLink' => 'https://www.miniorange.com/contact',
        ],
        "JOOMLA_IP_RESTRICT" =>      [
            'id' => 'mo_joomla_ip_rest',
            'addonName'  => 'IP based restriction for auto redirect',
            'addonDescription'  => 'Restrict specific IP addresses from auto-redirect to IDP.',
            'addonLink' => 'https://plugins.miniorange.com/page-and-article-restriction-for-joomla',
        ],
        "JOOMLA_USER_SYNC_OKTA" =>      [
            'id' => 'mo_joomla_okta_sync',
            'addonName'  => 'Sync users from your IdP in Joomla (SCIM Plugin)',
            'addonDescription'  => 'This plugin sync users from your IdP to Joomla database.When an Identity Provider creates, updates, or deletes a user, that user will also be added, updated, or deleted from the Joomla site.',
            'addonLink' => 'https://plugins.miniorange.com/joomla-scim-user-provisioning',
        ],
        "JOOMLA_PAGE_RESTRICTION" =>      [
            'id' => 'mo_joomla_page_rest',
            'addonName'  => 'Page Restriction',
            'addonDescription'  => 'This plugin is basically used to protect the pages/posts of your site with IDP login page and also, restrict the access to pages/posts of the site based on the user roles.',
            'addonLink' => 'https://plugins.miniorange.com/page-and-article-restriction-for-joomla',
        ],
        "JOOMLA_SSO_AUDIT" =>      [
            'id' => 'mo_joomla_audit',
            'addonName'  => 'SSO Login Audit',
            'addonDescription'  => 'SSO Login Audit captures all the SSO users and will generate the reports.',
            'addonLink' => 'https://plugins.miniorange.com/joomla-login-audit-login-activity-report',
        ],
        "JOOMLA_RBA" =>      [
            'id' => 'mo_joomla_rba',
            'addonName'  => 'Role/Group Based Redirection',
            'addonDescription'  => 'This plugin helps you to redirect your users to different pages after they log into your site, based on the role sent by your Identity Provider.',
            'addonLink' => 'https://plugins.miniorange.com/role-based-redirection-for-joomla',
        ],
    )));

    $displayMessage = "";
    $messages = unserialize(MO_ADDONS_CONTENT);


    echo '<div style="border:2px solid blue;background:white;box-sizing:border-box;padding: 55px;" id="addonContent"><h2 style="text-align:center">SAML 2.0 Plugin Add-ons</h2><hr><div class="mo_otp_wrapper">';
    foreach ($messages as $messageKey)
    {
        $message_keys = isset($messageKey['addonName']) ? $messageKey['addonName'] : '';
        $message_description = isset($messageKey["addonDescription"]) ? $messageKey["addonDescription"] : 'Hi! I am interested in the addon, could you please tell me more about this addon?';
        echo'<div id="'.$messageKey["id"].'">
                <h3 style="color:white;text-align:center">'.$message_keys.'<br /><br /></h3>                              
                <footer style="text-align:center">
                    <a type="button" class="mo_btn btn-primary" style="background-color: #007bff" href="'.$messageKey['addonLink'].'" target="_blank">Interested</a>  
                </footer>
                <span class="cd-pricing-body">
                    <ul class="cd-pricing-features">
                        <li style="color:white;text-align: center;">'.$message_description.'</li>
                    </ul>
                </span>
            </div>';
    }
    echo '</div></div><br>';
    return $displayMessage;
}

function group_mapping()
{
    $role_mapping = new Mo_saml_Local_Util();
    $role_mapping = $role_mapping->_load_db_values('#__miniorange_saml_role_mapping');
    $role_mapping_key_value = array();
    $attribute = new Mo_saml_Local_Util();
    $attribute = $attribute->_load_db_values('#__miniorange_saml_config');

    if ($attribute) {
        $group_attr = $attribute['grp'];
    } else {
        $group_attr = '';
    }
    if (isset($role_mapping['mapping_value_default'])) $mapping_value_default = $role_mapping['mapping_value_default'];
    else $mapping_value_default = "";
    $enable_role_mapping = 0;
    if (isset($role_mapping['enable_saml_role_mapping'])) $enable_role_mapping = $role_mapping['enable_saml_role_mapping'];
    ?>
    <div class="mo_boot_row  mo_boot_mr-1 mo_boot_p-3" style="background-color: #FFFF;border: 2px solid rgb(15, 127, 182) !important;">
        <div class="mo_boot_col-sm-6  mo_boot_mt-3">
            <h3>Group Mapping <sup><a href="https://developers.miniorange.com/docs/joomla/saml-sso/saml-group-mapping" target="_blank" style="font-size:20px;color:black" title="Know More"><div class="fa fa-question-circle-o"></div></a></sup></h3>
        </div>
        <div class="mo_boot_col-sm-12  mo_boot_mt-1">
        <hr>
            <form action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.saveRolemapping'); ?>" method="post" name="adminForm" id="group_mapping_form">
                <div class="mo_boot_row mo_boot_mt-1">
                    <div class="mo_boot_col-sm-12">
                        <p class='alert alert-info' style="color: #151515;">NOTE: In Free Version we are assigning <strong>'Register'</strong> group to user after SSO. If you want to assign other groups then upgrade to our <strong><a href='#' class='premium' onclick="moSAMLUpgrade();">Standard</a><a href='#' class='premium' onclick="moSAMLUpgrade();">, Premium</a><a href='#' class='premium' onclick="moSAMLUpgrade();">, Enterprise</a></strong> versions.</p>
                    </div>
                    <div class="mo_boot_col-sm-12">
                        <input id="mo_sp_grp_enable" class="mo_saml_custom_checkbox" type="checkbox" name="enable_role_mapping" value="1"  <?php if ($enable_role_mapping == 1) echo "checked"; ?> disabled >&emsp;<strong>Check this option if you want to enable Group Mapping</strong> <sup><strong><a href='#' class='premium' onclick="moSAMLUpgrade();">[Standard</a> <a href='#' class='premium' onclick="moSAMLUpgrade();">, Premium</a>, <a href='#' class='premium' onclick="moSAMLUpgrade();">Enterprise]</a></strong></sup><br>
                        <p class="mo_saml_custom_checkbox"><strong style="color: chocolate">&emsp;&emsp;Note:</strong> Enable this checkbox first before using any of the feature below.</p>
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-3" id="mo_sp_grp_defaultgrp">
                    
                    <div class="mo_boot_col-sm-4">
                        <p><strong>Select default group for both new user and logged in users.</strong></p>
                    </div>
                    <div class="mo_boot_col-sm-4">
                        <select class="mo_boot_form-control" name="mapping_value_default" style="width:100%" id="default_group_mapping" >
                            <?php $noofroles = 0;

                                $db = JFactory::getDbo();
                                $db->setQuery($db->getQuery(true)
                                    ->select('*')
                                    ->from("#__usergroups"));
                                $groups = $db->loadRowList();
                                foreach ($groups as $group) {
                                    if ($group[4] != 'Super Users') {
                                        if ($mapping_value_default == $group[0]) echo '<option selected="selected" value = "' . $group[0] . '">' . $group[4] . '</option>';
                                        else echo '<option  value = "' . $group[0] . '">' . $group[4] . '</option>';
                                    }
                                }
                            ?>
                        </select><br><br>
                    </div>
                    <div class="mo_boot_col-sm-4">
                        <select style="display:none" id="wp_roles_list">
                            <?php
                                $db = JFactory::getDbo();
                                $db->setQuery('SELECT `title`' . ' FROM `#__usergroups`');
                                $groupNames = $db->loadColumn();
                                $noofroles = count($groupNames);
                                for ($i = 0; $i < $noofroles; $i++) {
                                    echo '<option  value = "' . $groupNames[$i] . '">' . $groupNames[$i] . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-1" style="background-color: #d2d1d1;padding:10px;">
                    <div class="mo_boot_col-sm-12">
                        <input type="checkbox" class="mo_saml_custom_checkbox" name="disable_update_existing_users_role" value="1" disabled>&emsp;Do not update existing user&#39;s roles, if roles are not mapped. <strong> <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>[Premium</strong></a>, <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Enterprise]</strong></a></strong><br>
                        <input type="checkbox" class="mo_saml_custom_checkbox" name="disable_update_existing_users_role" value="1"  disabled>&emsp;Do not update existing user&#39;s roles and add newly mapped roles.<strong> <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>[Premium</strong></a>, <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Enterprise]</strong></a></strong><br>
                        <input type="checkbox" class="mo_saml_custom_checkbox" name="disable_create_users" value="1"  disabled>&emsp;Do not auto create users if roles are not mapped. <strong> <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>[Premium</strong></a>, <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Enterprise]</strong></a></strong><br>
                    </div>
                </div>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <p class='alert alert-info' style="color: #151515;">NOTE: Customized group mapping options shown below are configurable in the <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Premium</strong></a>, <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Enterprise</strong></a> versions of the plugin.</p>
                    </div>
                </div>
            </form>
        </div>
        <div class="mo_boot_col-sm-12 mo_boot_table-responsive">
            <table class="mo_saml_settings_table" id="saml_role_mapping_table">
            <caption></caption>
                <tr>
                    <th id="tt"></th>
                </tr>
                <tr>
                    <td><h4>Group</h4></td>
                    <td><input disabled class="mo_saml_table_textbox mo_boot_form-control" type="text" name="grp" value="<?php echo $group_attr; ?>" placeholder="Enter Attribute Name for Group"/></td>
                </tr>
                <tr>
                    <td><br></td>
                    <td><br></td>
                </tr>
                <tr>
                    <td style="width:20%"><h3><strong>Group Name in Joomla</strong></h3></td>
                    <td style="width:50%" class="mo_boot_text-center"><h3><strong>Group Name from IDP</strong></h3></td>
                </tr>
                <?php
                    $user_role = array();
                    $db = JFactory::getDbo();
                    $db->setQuery($db->getQuery(true)
                        ->select('*')
                        ->from("#__usergroups"));
                    $groups = $db->loadRowList();
                    if (empty($role_mapping_key_value)) {
                        foreach ($groups as $group) {
                            if ($group[4] != 'Super Users') {
                                echo '<tr><td><h5>' . $group[4] . '</h5></td><td><input type="text" name="saml_am_group_attr_values_' . $group[0] . '" value= "" placeholder="Semi-colon(;) separated Group/Role value for ' . $group[4] . '"  disabled class="mo_boot_form-control"' . ' /></td></tr>';
                            }
                        }
                ?>
                <?php
                    } else {
                        $j = 1;
                        foreach ($role_mapping_key_value as $mapping_key => $mapping_value) {
                ?>
                <tr>
                    <td>
                        <input class="mo_saml_table_textbox mo_boot_form-control" type="text" name="mapping_key_<?php echo $j; ?>" value="<?php echo $mapping_key; ?>" placeholder="cn=group,dc=domain,dc=com"/>
                    </td>
                    <td>
                        <select name="mapping_value_<?php echo $j; ?>" id="role" class="mo_boot_form-control">
                            <?php
                                $db = JFactory::getDbo();
                                $db->setQuery('SELECT `title`' . ' FROM `#__usergroups`');
                                $groupNames = $db->loadColumn();
                                $noofroles = count($groupNames);
                                for ($i = 0; $i < $noofroles; $i++) {
                                if ($mapping_value == $groupNames[$i]) echo '<option selected="selected" value = "' . $groupNames[$i] . '">' . $groupNames[$i] . '</option>';
                                else echo '<option value = "' . $groupNames[$i] . '">' . $groupNames[$i] . '</option>';
                                }
                            ?>
                    </select>
                    </td>
                </tr>
                <?php $j++;
                        }
                    }
                ?>
            </table>
            <div class="mo_boot_col-sm-12 mo_boot_text-center">
                <input id="mo_sp_grp_save" type="submit" class="mo_boot_btn  mo_boot_btn-success" value="Save" disabled/>
            </div>
        </div>   
    </div>
    <?php

}

//Don't delete the below function

/*function mo_sliding_support()
{
    $result = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_customer_details');
    $admin_email = isset($result['email']) ? $result['email'] : '';
    $admin_phone = isset($result['admin_phone']) ? $result['admin_phone'] : '';
    */?><!--
    <div id="mosaml_support_button_sliding" class="mo_saml_sliding_support_btn">
        <input type="button" class="mo_boot_btn mo_boot_btn-primary" id="mo_support_btn" value="Feature Request" onclick="support_form_open();"/>
        <div id="Support_Section" class="mo_saml_table_layout_support_3">
            <form name="f" method="post" action="<?php /*echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.contactUs'); */?>">
                <h3>Feature/Add-On Request</h3>
                <div>Need any help?<br /><br /></div>
                <div>
                    <table class="mo_saml_settings_table">
                        <tr>
                            <td>
                                <input style="width: 100%; border: 1px solid #868383 !important;" type="email" class="mo_saml_table_textbox" name="query_email" value="<?php /*echo $admin_email; */?>" placeholder="Enter your email" required /><br><br>
                            </td>
                        </tr>
                        <tr><td>
                                <input style="width: 100%; border: 1px solid #868383 !important;" type="tel" class="mo_saml_table_textbox" name="query_phone" value="<?php /*echo $admin_phone; */?>" placeholder="Enter your phone"/><br><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <textarea id="mo_saml_query" name="mo_saml_query" class="mo_saml_settings_textarea" style="border-radius:4px;resize: vertical;width:100%; border: 1px solid #868383 !important;" cols="52" rows="7" onkeyup="mo_saml_valid(this)" onblur="mo_saml_valid(this)" onkeypress="mo_saml_valid(this)" required placeholder="Write your query here"></textarea>
                            </td>
                        </tr>
                    </table>
                </div>
                <input type="hidden" name="option1" value="mo_saml_login_send_query"/><br>
                <input type="submit" name="send_query" style ="margin-left: 23%" value="Submit Query" class="btn btn-medium btn-success" />
                <input type="button" onclick="window.open('https://faq.miniorange.com/kb/joomla-saml/')" target="_blank" value="FAQ's"  style= "margin-right: 25px; margin-left: 25px;" class="btn btn-medium btn-success" />
                <p><br>If you want custom features in the plugin, just drop an email to <a href="mailto:joomlasupport@xecurify.com"up> joomlasupport@xecurify.com</a> </p>
            </form>
        </div>
    </div>
    <div hidden id="mosaml-feedback-overlay"></div>
    <script>
        function support_form_open(event) {
            var qmessage = "Hi! I am interested in the \""+event+"\" addon, could you please tell me more about this addon?";

            var n = jQuery("#mosaml_support_button_sliding").css("right");

            if (n != "929") {
                jQuery("#mosaml-feedback-overlay-1").show();
                jQuery("#mosaml_support_button_sliding").animate({
                    "right": "929"
                });
            } else {
                jQuery("#mosaml-feedback-overlay-1").hide();
                jQuery("#mosaml_support_button_sliding").animate({
                    right: "-259"
                });
            }
        }
    </script>
    --><?php
/*}*/

function mo_sso_login()
{

    $siteUrl = JURI::root();
    $sp_base_url = $siteUrl;
    $button_style="{
        border: 1px solid rgba(0, 0, 0, 0.2);
        color: #fff;
        background-color: #226a8b !important;
        padding: 4px 12px;
        border-radius: 3px;
    }";
    ?>
    <div class="mo_boot_row  mo_boot_mx-1 mo_boot_p-3" style="background-color: #FFFF;border: 2px solid rgb(15, 127, 182) !important;">
        <div class="mo_boot_col-sm-12 mo_boot_mt-3">
            <div class="mo_boot_row">
                <div class="mo_boot_col-lg-9 mo_boot_mt-1">
                    <h3>Login Settings <sup><a href='https://developers.miniorange.com/docs/joomla/saml-sso/saml-redirection-and-sso-links' target='_blank' style="font-size:20px;color:black" title="Know More"><div class="fa fa-question-circle-o"></div></a></sup></h3>
                </div>
            </div>
        </div>
        <div class="mo_boot_col-sm-12 mo_boot_mt-3 ">
            <div class="mo_saml_sso_url_style" >
                <div class="mo_boot_row ">
                    <div class="mo_boot_col-lg-2 ">
                        <strong>SSO URL <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext" >This link is used for Single Sign-On by end users.</span></div> : </strong>
                    </div>
                    <div class="mo_boot_col-lg-8 ">
                        <span id="sso_url" style="color:#2a69b8" >
                            <strong><?php echo  $sp_base_url . '?morequest=sso'; ?></strong>
                        </span>
                    </div>
                    <div class="mo_boot_col-lg-2 ">
                        <em class="fa fa-lg fa-copy mo_copy mo_copytooltip" onclick="copyToClipboard('#sso_url');" style="color:black;position: absolute;top: 50%;transform: translateY(-50%);"><span class="mo_copytooltiptext copied_text">Copy</span>  </em>
                    </div>
                </div>
            </div>
        </div>
        <div class="mo_boot_col-sm-12 mo_boot_mt-4">
                <details>
                    <summary class="mo_saml_summary">Add a link or button on your site login page.</summary><hr>
                    <ul><li><b>Step 1.</b> &nbsp;&nbsp;In the left menu, click over <b>Menus</b>, hover over the menu you would like to add the link for login to your SP using IDP, and then click <b>New</b>.<br /><br /></li>
                    <li><b>Step 2.</b> &nbsp;&nbsp;In the <b>Menu Title</b>, Enter the text you want to show to users. (for e.g. Login using &lt;IDP&gt;).<br /><br /></li>
                    <li><b>Step 3.</b> &nbsp;&nbsp;Click on the <b>Select</b> button next to Menu Item Type. A popup will appear.<br /><br /></li>
                    <li><b>Step 4.</b> &nbsp;&nbsp;Click on <b>System Link</b> and select <b>URL</b>.<br /><br /></li>
                    <li><b>Step 5.</b> &nbsp;&nbsp;Enter the <b>SSO URL</b> in <b>Link</b> textbox.<br /><br /></li>
                    <li><b>Step 6.</b> &nbsp;&nbsp;Select one of the option from <b>Target Window</b> .<br /><br /></li>
                    <li><b>Step 7.</b> &nbsp;&nbsp;In the top left,click <b>Save.</b><br /></li>
                </ul>
                </details>
        </div>

        <div class="mo_boot_col-sm-12">
                <details>
                    <summary class="mo_saml_summary">How to add custom CSS for Login Button?<sup>[<strong><a href='#' class='premium' onclick="moSAMLUpgrade();">Standard</a>, <a href='#' class='premium' onclick="moSAMLUpgrade();">Premium</a>, <a href='#' class='premium' onclick="moSAMLUpgrade();">Enterprise</a></strong>]</sup></summary><hr>
                    <textarea disabled="disabled" rows="5" cols="100" class="mo_saml_table_textbox mo_boot_w-100" placeholder="<?php echo $button_style ?>"></textarea>
                </ul>
                </details>
                <hr/>
        </div>
        <div class="mo_boot_col-sm-12 mo_boot_mt-4" style="text-align: center;padding: 25px;background: #bababa;font-weight: bold;color: white;">
            <h2>License Version Features</h2>
        </div>
        <div class="mo_boot_col-sm-12 mo_saml_sso_link_style">
            <p><h4><input type="checkbox" style="margin-right:11px;margin-top:-1px;border: 1px solid #868383 !important;" disabled>
                Check this option if you want to disable auto creation of users if user does not exist.<sup>[<strong><a href='#' class='premium' onclick="moSAMLUpgrade();">Standard</a>, <a href='#' class='premium' onclick="moSAMLUpgrade();">Premium</a>, <a href='#' class='premium' onclick="moSAMLUpgrade();">Enterprise</a></strong>]</sup>
                </h4>    
            </p>
            <p class='alert alert-info' style="color: #151515;">NOTE: If you enable this feature new user's wont be created, only existing users can perform SSO.</p><br>

            <p>
                <h4><input type="checkbox" style="margin-right:11px;margin-top:-1px;border: 1px solid #868383 !important;" disabled>Protect complete site from anonymous access/ Auto Redirect to IdP.<sup>[<strong><a href='#' class='premium' onclick="moSAMLUpgrade();">Standard</a>, <a href='#' class='premium' onclick="moSAMLUpgrade();">Premium</a>, <a href='#' class='premium' onclick="moSAMLUpgrade();">Enterprise</a></strong>]</sup></h4>     
            </p>
            <p class='alert alert-info' style="color: #151515;">NOTE: If you enable this feature new user's wont be created, only existing users can perform SSO.</p><br>

            <p><h4><input type="checkbox" style="margin-right:11px;margin-top:-1px;border: 1px solid #868383 !important;" disabled>Enable auto redirect to IDP for "http://localhost/joomla_4.2/administrator" URL
                    <sup>[<strong><a href='#' class='premium' onclick="moSAMLUpgrade();">Premium</a>, <a href='#' class='premium' onclick="moSAMLUpgrade();">Enterprise</a></strong>]</sup><br><br>
                    <input type="checkbox" style="margin-right:11px;margin-top:-1px;border: 1px solid #868383 !important;" disabled>Enable backdoor URL
                    <sup>[<strong><a href='#' class='premium' onclick="moSAMLUpgrade();">Premium</a>, <a href='#' class='premium' onclick="moSAMLUpgrade();">Enterprise</a></strong>]</sup>
                </h4>
            </p>
            <p class='alert alert-info' style="color: #151515;">NOTE:If you want users to access the admin console /backend after SSO then enable this feature. Also it will create a backdoor login to your website using Joomla credentials in case you get locked out of your IdP.</p><br>

            <p><h4><input type="checkbox" style="margin-right:11px;margin-top:-1px;border: 1px solid #868383 !important;" disabled>Domain Restriction
                <sup>[<strong><a href='#' class='premium' onclick="moSAMLUpgrade();">Enterprise </a></strong>]</sup>
                </h4>
            </p>
            <p class='alert alert-info' style="color: #151515;">
                NOTE: Domain Restriction provides the functionality to allow or restrict the users of a particular domain to login or register. It allows users either to login with specified domains or deny users to login with specified domains.
            </p><br>

            <p><h4><input type="checkbox" style="margin-right:11px;margin-top:-1px;border: 1px solid #868383 !important;" disabled>Domain Mapping
                <sup>[<strong><a href='#' class='premium' onclick="moSAMLUpgrade();">Enterprise </a></strong>]</sup>
                </h4>
            </p>
            <p class='alert alert-info' style="color: #151515;">
                NOTE: Map the domain in order to auto-redirect to a particular IDP when the user tries to login with domain email.
            </p><br>

            <p><h4><input type="checkbox" style="margin-right:11px;margin-top:-1px;border: 1px solid #868383 !important;" disabled>Ignore special characters from the Email for Registration/Login
                <sup>[<strong><a href='#' class='premium' onclick="moSAMLUpgrade();">Enterprise </a></strong>]</sup>
                </h4>
            </p>
            <p class='alert alert-info' style="color: #151515;">
                NOTE: It will allow users with special characters in their email id to register/ login to Joomla 
            </p><br>

            <p><h4><input type="checkbox" style="margin-right:11px;margin-top:-1px;border: 1px solid #868383 !important;" disabled>Error Handling
                <sup>[<strong><a href='#' class='premium' onclick="moSAMLUpgrade();">Standard</a>, <a href='#' class='premium' onclick="moSAMLUpgrade();">Premium</a>, <a href='#' class='premium' onclick="moSAMLUpgrade();">Enterprise</a></strong>]</sup>
                </h4>
            </p>
            <p class='alert alert-info' style="color: #151515;">
                NOTE: Error Handling provides the functionality to allow custom error messages for duplicate users.
            </p>
            <hr>
        </div>
    </div>
    <?php
}

function attribute_mapping()
{
    ?>
    <div class="mo_boot_row  mo_boot_mx-1 mo_boot_p-3" style="background-color: #FFFF;border: 2px solid rgb(15, 127, 182) !important;">
        <div class="mo_boot_col-sm-12 mo_boot_mt-3">
            <div class="mo_boot_row">
                <div class="mo_boot_col-lg-9 mo_boot_mt-1">
                    <h3>Attribute Mapping <sup><a href='https://developers.miniorange.com/docs/joomla/saml-sso/saml-attribute-mapping' target='_blank' style="font-size:20px;color:black" title="Know More"><div class="fa fa-question-circle-o"></div></a></sup></h3>
                </div>
            </div>
            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-12">
                    <hr>
                </div>
            </div> 
        </div>
        <div class="mo_boot_col-sm-12 mo_boot_mt-2">
            <form action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.saveConfig'); ?>" method="post" name="adminForm" id="attribute_mapping_form">
                <div class="mo_boot_row mo_boot_mt-1">
                    
                    <div class="mo_boot_col-sm-12">
                        <input type="checkbox" value="1" disabled class="mo_saml_custom_checkbox">&emsp;<strong>Do not update existing user&#39;s attributes.</strong> <sup><strong> <a href='#' class='premium' onclick="moSAMLUpgrade();">[Premium</a>, <a href='#' class='premium' onclick="moSAMLUpgrade();">Enterprise]</a></strong></sup>
                        <p class='alert alert-info mo_boot_mt-3' style="color:#151515;">NOTE: You need to add addtribute name which you recived after clicking on Test Configuration. For user creation, Joomla requires an email address. If you're not receiving email addresses in NameID format, you'll need to modify your attribute mapping.</p>
                    </div>   
                </div>
                <div class="mo_boot_col-sm-12">
                    <strong>Basic Attribute Mapping.</strong> <sup><strong><a href='#' class='premium' onclick="moSAMLUpgrade();">[Standard</a> <a href='#' class='premium' onclick="moSAMLUpgrade();">, Premium</a>, <a href='#' class='premium' onclick="moSAMLUpgrade();">Enterprise]</a></strong></sup>
                    <div class="mo_boot_row mo_boot_mt-3"  id="mo_saml_uname" >
                    <div class="mo_boot_col-sm-3">
                        <strong>Username</strong>
                    </div>
                    <div class="mo_boot_col-sm-8">
                        <input disabled class="mo_saml_table_textbox mo_boot_form-control" type="text" name="username"required placeholder="NameID" value="NameID" />
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-1"  id="mo_saml_email">
                    <div class="mo_boot_col-sm-3">
                        <strong>Email</strong>
                    </div>
                    <div class="mo_boot_col-sm-8">
                        <input disabled class="mo_saml_table_textbox mo_boot_form-control" type="text" name="email"required placeholder="NameID" value="NameID" />
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-1" id="mo_sp_attr_name">
                    <div class="mo_boot_col-sm-3">
                        <strong>Name</strong>
                    </div>
                    <div class="mo_boot_col-sm-8">
                        <input  disabled class="mo_saml_table_textbox mo_boot_form-control" type="text" name="name"  placeholder="Enter Attribute Name for Name" />
                    </div>
                </div>
                <div class="mo_boot_row  mo_boot_mt-4 mo_boot_text-center" id="mo_sp_attr_save_attr">
                    <div class="mo_boot_col-sm-12">
                        <input disabled type="submit" class="mo_boot_btn mo_boot_btn-success" value="Save Attribute Mapping"/>
                    </div>
                </div>
                </div>
            
            </form>
        </div>
        <div class="mo_boot_col-sm-12 mo_boot_mt-5">
            <strong> 
                Map Joomla's User Profile Attributes
                <sup>
                <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>[Premium</strong></a>, <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Enterprise]</strong></a>
                </sup>
                <input type="button" class="mo_boot_btn mo_boot_btn-primary" disabled value="+" />
                <input type="button" class="mo_boot_btn mo_boot_btn-danger" disabled value="-" />
            </strong>
            <hr>
            <a id="attribute_profile_mapping_info" href="#info1" >Click here to know more?</a>
            <div id='profile_mapping_info' style="display:none">
                <p class="alert alert-info">NOTE: During the user's registration or login, the User Profile Attributes field in the User profile table of Joomla will be updated with the value corresponding to User Profile Attributes Mapping Value from IDP.</p>
            </div>
            <script>
                jQuery('#attribute_profile_mapping_info').click(function(){
                    jQuery('#profile_mapping_info').slideToggle('fast');
                });
            </script>
            
            <p class="alert alert-info ">NOTE: Add the Joomla's profile attributes in the User Profile Attribute textfield and add the IdP attributes that you need to map with the Joomla's User Profile attributes in the IdP Attribute textfield.
                <br> <b>User Profile Attribute :</b> It is the user profile attribute whose value you want to set in joomla site.
                <br> <b>IDP Attribute :</b> It is the name attribute which you get after test configurtion from IDP. It should be unique.<br>
            </p>
        </div>
        <div class="mo_boot_col-sm-6 mo_boot_mt-1">
            <div class="mo_boot_row mo_boot_mt-1">
                <div class="mo_boot_col-sm-12">
                    <strong>Joomla's User Profile Attribute</strong>
                </div>
                <div class="mo_boot_col-sm-12">
                    <input type="text" class="mo_boot_form-control" disabled="disabled"/>
                </div>
            </div>
        </div>
        <div class="mo_boot_col-sm-6 mo_boot_mt-1">
            <div class="mo_boot_row mo_boot_mt-1">
                <div class="mo_boot_col-sm-12">
                    <strong>IDP Attribute</strong>
                </div>
                <div class="mo_boot_col-sm-12">
                    <input type="text" class="mo_boot_form-control" disabled="disabled"/>
                </div>
            </div>
        </div>
        <div class="mo_boot_col-sm-12 mo_boot_mt-5">
            <strong> 
                Map Joomla's Field Attributes
                <sup>
                <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>[Enterprise]</strong></a>
                </sup>
                <input type="button" class="mo_boot_btn mo_boot_btn-primary" disabled value="+" />
                <input type="button" class="mo_boot_btn mo_boot_btn-danger" disabled value="-" />
            </strong>
            <hr>
            <a id="attribute_field_mapping_info" href="#info1" >Click here to know more?</a>
            <div id='field_mapping_info' style="display:none">
                <p class="alert alert-info">NOTE: During the user's registration or login, the User Field Attributes field in the User field table of Joomla will be updated with the value corresponding to User Profile Attributes Mapping Value from IDP.</p>
            </div>
            <script>
                jQuery('#attribute_field_mapping_info').click(function(){
                    jQuery('#field_mapping_info').slideToggle('fast');
                });
            </script>
            <p class="alert alert-info">NOTE: Add the Joomla's field attributes in the User Field Attribute textfield and add the IdP attributes that you need to map with the Joomla's User Field attributes in the IdP Attribute textfield.
                <br> <b>User Field Attribute :</b> It is the user field attribute whose value you want to set in joomla site.
                <br> <b>IDP Attribute :</b> It is the name attribute which you get after test configurtion from IDP. It should be unique.</p>
        </div>
        <div class="mo_boot_col-sm-6 mo_boot_mt-1">
            <div class="mo_boot_row mo_boot_mt-1">
                <div class="mo_boot_col-sm-12">
                    <strong>Joomla's User Field Attribute</strong>
                </div>
                <div class="mo_boot_col-sm-12">
                    <input type="text" class="mo_boot_form-control" disabled="disabled"/>
                </div>
            </div>
        </div>
        <div class="mo_boot_col-sm-6 mo_boot_mt-1">
            <div class="mo_boot_row mo_boot_mt-1">
                <div class="mo_boot_col-sm-12">
                    <strong>IDP Attribute</strong>
                </div>
                <div class="mo_boot_col-sm-12">
                    <input type="text" class="mo_boot_form-control" disabled="disabled"/>
                </div>
            </div>
        </div>
        <div class="mo_boot_col-sm-12 mo_boot_mt-5">
            <strong> 
                Map Joomla's Contact Attributes
                <sup>
                <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>[Enterprise]</strong></a>
                </sup>
                <input type="button" class="mo_boot_btn mo_boot_btn-primary" disabled value="+" />
                <input type="button" class="mo_boot_btn mo_boot_btn-danger" disabled value="-" />
            </strong>
            <hr>
            <a id="attribute_contact_mapping_info" href="#info1" >Click here to know more?</a>
            <div id='contact_map_info' style="display:none">
                <p class="alert alert-info">NOTE: During the user's registration or login, the User Contact Attributes field in the User contact details table of Joomla will be updated with the value corresponding to User Contact Attributes Mapping Value from IDP.</p>
            </div>
            <script>
                jQuery('#attribute_contact_mapping_info').click(function(){
                    jQuery('#contact_map_info').slideToggle('fast');
                });
            </script>
            <p class="alert alert-info">NOTE: Add the Joomla's Contact attributes in the User Contact Attribute textfield and add the IdP attributes that you need to map with the Joomla's User Contact attributes in the IdP Attribute textfield.
                <br> <b>User Contact Attribute :</b> It is the user contact attribute whose value you want to set in joomla site.
                <br> <b>IDP Attribute :</b> It is the name attribute which you get after test configurtion from IDP. It should be unique.</p>
        </div>
        <div class="mo_boot_col-sm-6 mo_boot_mt-1">
            <div class="mo_boot_row mo_boot_mt-1">
                <div class="mo_boot_col-sm-12">
                    <strong>Joomla's User Contact Attribute</strong>
                </div>
                <div class="mo_boot_col-sm-12">
                    <input type="text" class="mo_boot_form-control" disabled="disabled"/>
                </div>
            </div>
        </div>
        <div class="mo_boot_col-sm-6 mo_boot_mt-1">
            <div class="mo_boot_row mo_boot_mt-1">
                <div class="mo_boot_col-sm-12">
                    <strong>IDP Attribute</strong>
                </div>
                <div class="mo_boot_col-sm-12">
                    <input type="text" class="mo_boot_form-control" disabled="disabled"/>
                </div>
            </div>
        </div>
        <div class="mo_boot_col-sm-12 mo_boot_text-center  mo_boot_mt-4">
            <input type="submit" class="mo_boot_btn mo_boot_btn-success" value="Save Attribute Mapping" disabled/>
        </div>
    </div>
    <style>
        .att li{
            list-style-type: disc ;
        }
    </style>
    <?php
}

function proxy_setup()
{
    $proxy = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_proxy_setup');
    $proxy_host_name = isset($proxy['proxy_host_name']) ? $proxy['proxy_host_name'] : '';
    $port_number = isset($proxy['port_number']) ? $proxy['port_number'] : '';
    $username = isset($proxy['username']) ? $proxy['username'] : '';
    $password = isset($proxy['password']) ? base64_decode($proxy['password']) : '';
    ?>
    <div class="mo_boot_row mo_boot_p-3"> 
        <div class="mo_boot_col-sm-12"  id="mo_sp_proxy_config">
            <div class="mo_boot_row mo_boot_mt-2">
                <div class="mo_boot_col-sm-9">
                    <input type="hidden" name="option1" value="mo_saml_save_proxy_setting" />
                    <h3>Configure Proxy Server</h3>
                </div>
                <div class="mo_boot_col-sm-3">
                    <input type="button" class="mo_boot_float-right mo_boot_btn mo_boot_btn-danger" value="Cancel" onclick = "hide_proxy_form();"/>
                </div>
            </div>
            <hr>
            <form  action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.proxyConfig'); ?>" name="proxy_form" method="post">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">  
                        <p><strong>If your organization dont allow you to connect to internet directly and if you need to login to your proxy server please configure following details.</strong></p>
                        <p>Enter the information to setup the proxy server.</p>
                    </div>
                </div>
                <div class="mo_boot_row" id="mo_sp_proxy_host_name">
                    <div class="mo_boot_col-sm-3">
                        <strong>Proxy Host Name:<span style="color: #FF0000">*</span></strong>
                    </div>
                    <div class="mo_boot_col-sm-8">
                        <input type="text" name="mo_proxy_host" placeholder="Enter the host name" class="mo_saml_proxy_setup mo_boot_form-control" value="<?php echo $proxy_host_name ?>" required/>
                    </div>
                </div>
                <div class="mo_boot_row" id="mo_sp_port_number">
                    <div class="mo_boot_col-sm-3"><br>
                        <strong>Port Number:<span style="color: #FF0000">*</span></strong>
                    </div>
                    <div class="mo_boot_col-sm-8"><br>
                        <input type="number" name="mo_proxy_port" placeholder="Enter the port number of the proxy" class="mo_boot_form-control mo_saml_proxy_setup" value="<?php echo $port_number ?>" required/>
                    </div>
                </div>
                <div class="mo_boot_row" id="mo_sp_proxy_username">
                    <div class="mo_boot_col-sm-3"><br>
                        <strong>Username:</strong>
                    </div>
                    <div class="mo_boot_col-sm-8"><br>
                        <input type="text" name="mo_proxy_username" placeholder="Enter the username of proxy server" class="mo_boot_form-control mo_saml_proxy_setup" value="<?php echo $username ?>" />
                    </div>
                </div>
                <div class="mo_boot_row" id="mo_sp_proxy_password">
                    <div class="mo_boot_col-sm-3"><br>
                        <strong>Password:</strong>
                    </div>
                    <div class="mo_boot_col-sm-8"><br>
                        <input type="password" name="mo_proxy_password" placeholder="Enter the password of proxy server" class="mo_boot_form-control mo_saml_proxy_setup" value="<?php echo $password ?>">
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_text-center mo_boot_mt-3">
                    <div class="mo_boot_col-sm-12">
                        <input type="submit" style="width:100px;" value="Save" class="mo_boot_btn mo_boot_btn-success" />
                    </div>
                </div>
            </form>
            <div class="mo_boot_col-sm-12  mo_boot_text-center  mo_boot_mt-3">
                <form style="background-color:#FFFFFF; " action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.proxyConfigReset'); ?>" name="proxy_form1" method="post">
                    <input type="button" value="Reset" onclick='submit();' class="mo_boot_btn mo_boot_btn-success" />
                </form>
            </div>
        </div>
    </div>
    <?php
}

/* show custom certificare */
function customcertificate(){
    ?>
    <form action="" name="customCertificateForm" id="custom_certificate_form">
        
        <div class="mo_boot_row  mo_boot_mx-1 mo_boot_p-3" id="generate_certificate_form"   style="background-color: #FFFF;border: 2px solid rgb(15, 127, 182) !important;display:none">
                <div class="mo_boot_col-sm-12">
                    <div class="mo_boot_row  mo_boot_mt-2">
                        <div class="mo_boot_col-sm-10">
                            <h3>
                                Generate Custom Certificate
                            </h3>
                        </div>
                        <div class="mo_boot_col-sm-2">
                            <input type="button" class="mo_boot_btn mo_boot_btn-success" value="Back" onclick = "hide_gen_cert_form()"/>
                        </div>
                    </div>
                    <hr> 
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-3">
                            <strong><span style="color:red;">*</span>Country code :</strong>
                        </div>
                        <div class="mo_boot_col-sm-7">
                            <input class="mo_saml_table_textbox  mo_boot_form-control" type="text"  placeholder="Enter your country code" disabled>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-3">
                            <strong><span style="color:red;">*</span>State</strong>
                        </div>
                        <div class="mo_boot_col-sm-7">
                            <input class="mo_saml_table_textbox mo_boot_form-control" type="text"  placeholder="Enter State Name" disabled />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-3">
                            <strong><span style="color:red;">*</span>Company</strong>
                        </div>
                        <div class="mo_boot_col-sm-7">
                            <input  class="mo_saml_table_textbox mo_boot_form-control" type="text"  placeholder="Enter your Company Name" disabled />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-3">
                            <strong><span style="color:red;">*</span>Unit</strong>
                        </div>
                        <div class="mo_boot_col-sm-7">
                            <input  class="mo_saml_table_textbox mo_boot_form-control" type="text" placeholder="Unit Name(eg. section) : Information Technology" disabled />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-3">
                            <strong><span style="color:red;">*</span>Common</strong>
                        </div>
                        <div class="mo_boot_col-sm-7">
                            <input  class="mo_saml_table_textbox mo_boot_form-control" type="text" placeholder="Common Name(eg. your name or your servers hostname)" disabled />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-3">
                            <strong><span style="color:red;">*</span>Digest Algorithm</strong>
                        </div>
                        <div class="mo_boot_col-sm-7">
                            <select class="mo_saml_table_textbox mo_boot_form-control">                              
                                <option>SHA512</option>
                                <option>SHA384</option>
                                <option>SHA256</option>
                                <option>SHA1</option>                            
                            </select>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-3">
                            <strong><span style="color:red;">*</span>Bits to generate the private key:</strong>
                        </div>
                        <div class="mo_boot_col-sm-7">
                            <select class="mo_saml_table_textbox mo_boot_form-control">                              
                                <option>2048 bits</option>
                                <option>1024 bits</option>                                                               
                            </select>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-3">
                            <strong><span style="color:red;">*</span>Valid days:</strong>
                        </div>
                        <div class="mo_boot_col-sm-7">
                            <select class="mo_saml_table_textbox mo_boot_form-control">                              
                                <option>365 days</option>                                                                                               
                                <option>180 days</option>                                                                                               
                                <option>90 days</option>                                                                                               
                                <option>45 days</option>                                                                                               
                                <option>30 days</option>                                                                                               
                                <option>15 days</option>                                                                                               
                                <option>7 days</option>                                                                                               
                            </select>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_text-center mo_boot_mt-2">
                        <div class="mo_boot_col-sm-12">
                        <input type="submit" value="Generate Self-Signed Certs" disabled class="mo_boot_btn mo_boot_btn-success"; />
                        </div>
                    </div>
                </div>
        </div>
        <div class="mo_boot_row  mo_boot_mx-1 mo_boot_p-3" id="mo_gen_cert"  style="background-color: #FFFF;border: 2px solid rgb(15, 127, 182) !important;">
                <div class="mo_boot_col-sm-12">
                    <input id="miniorange_saml_custom_certificate" type="hidden" name="cust_certificate_option" value="miniorange_saml_save_custom_certificate"/>
                    <h3>Generate Custom Certificate <sup><a href='https://developers.miniorange.com/docs/joomla/saml-sso/saml-custom-certificate' target='_blank' style="font-size:20px;color:black" title="Know More"><div class="fa fa-question-circle-o"></div></a></sup></h3>
                    <hr>
                </div>
                <div class="mo_boot_col-sm-12" style="background-color: #e2e6ea;border-radius:5px">
                    <p style="margin-top:10px;">
                        You can <strong>Upload</strong> your own custom certificate or generate your own key pair of certificates by clicking on the <strong>Generate</strong> button.
                    </p>
                </div>
                <div class="mo_boot_col-sm-12 mo_boot_mt-3" id="customCertificateData"><br>
                    <div class="mo_boot_row custom_certificate_table"  >
                        <div class="mo_boot_col-sm-3">
                            <strong>
                                X.509 Public Certificate
                                <span style="color: #FF0000; font-size: large;">*</span>
                                <a href='#' class='premium' onclick="moSAMLUpgrade();">[Enterprise]</a>
                            </strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <textarea disabled="disabled" rows="5" cols="100" class="mo_saml_table_textbox mo_boot_w-100"></textarea>
                        </div>
                    </div>
                    <div class="mo_boot_row custom_certificate_table"  >
                        <div class="mo_boot_col-sm-3">
                            <strong>
                                X.509 Private Certificate
                                <span style="color: #FF0000; font-size: large;">*</span>
                                <a href='#' class='premium' onclick="moSAMLUpgrade();">[Enterprise]</a>
                            </strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <textarea disabled="disabled" rows="5" cols="100" class="mo_saml_table_textbox mo_boot_w-100"></textarea>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-3 custom_certificate_table"  id="save_config_element">
                        <div class="mo_boot_col-sm-12 mo_boot_text-center">
                            <input disabled="disabled" type="submit" name="submit" value="Upload" class="mo_boot_btn mo_boot_btn-success"/> &nbsp;&nbsp;
                            <input type="button" name="submit" value="Generate" class="mo_boot_btn  mo_boot_btn-success" onclick="show_gen_cert_form()"/>&nbsp;&nbsp;
                            <input disabled type="submit" name="submit" value="Remove" class="mo_boot_btn  mo_boot_btn-saml"/>
                        </div>
                    </div>
                </div>
        </div>
    </form>
    <?php
}


function requestfordemo()
{
    $current_user = JFactory::getUser();
    $result = new Mo_saml_Local_Util();
    $result = $result->_load_db_values('#__miniorange_saml_customer_details');
    $admin_email = isset($result['email']) ? $result['email'] : '';
    if ($admin_email == '') $admin_email = $current_user->email;
    ?>
    <div class="mo_boot_px-3">
    <div class="mo_boot_row mo_boot_p-3" style="border: 2px solid rgb(15, 127, 182);background-color:white">
        <div class="mo_boot_col-sm-12 mo_boot_text-center">
            <h3>Request for Trial</h3>
            <hr>
        </div>
        <div class="mo_boot_col-sm-12" style="background-color: #e2e6ea;border-radius:5px">
            <p>
                If you want to try the license version of the plugin then you can request for trial for plugin.<br>
                &nbsp;1) We can give you requested plugin for 7 days trial.<br> 
                &nbsp;2) You can configure it with your Identity Provider, test the SSO and play around with the premium features. <br>
            </p>
            <strong>Note:</strong> Please describe your use-case in the <strong>Description</strong> below.
        </div>
        <div class="mo_boot_col-sm-12 mo_boot_mt-3">
            <form  name="demo_request" method="post" action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.requestForTrialPlan');?>">
                <div class="mo_boot_row mo_boot_mt-1">
                    <div class="mo_boot_col-sm-4">
                        <p><span style="color: red;">*</span><strong>Email: </strong></p>
                    </div>
                    <div class="mo_boot_col-sm-8">
                        <input style="border: 1px solid #868383 !important;" type="email" class="mo_saml_table_textbox mo_boot_form-control" name="email" value="<?php echo $admin_email; ?>" placeholder="person@example.com" required />
                    </div>
                    <div class="mo_boot_col-sm-12 mo_boot_mt-1">
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-4">
                                <p><span style="color: red;">*</span><strong>Request a trial for:</strong></p>
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <select required class="mo_boot_form-control" style="border: 1px solid #868383 !important;" name="plan">
                                    <option disabled selected style="text-align: center">----------------------- Select -----------------------</option>
                                    <option value="Joomla SAML Standard Plugin">Joomla SAML SP Standard Plugin</option>
                                    <option value="Joomla SAML Premium Plugin">Joomla SAML SP Premium Plugin</option>
                                    <option value="Joomla SAML Enterprise Plugin">Joomla SAML SP Enterprise Plugin</option>
                                    <option value="Not Sure">Not Sure</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mo_boot_col-sm-12 mo_boot_mt-1">
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-4">
                                <p><span style="color: red;">*</span><strong>Description:</strong></p>
                            </div>
                            <div class="mo_boot_col-sm-8">
                                <textarea  name="description" class="mo_boot_form-text-control" style="border-radius:4px;resize: vertical;width:100%; border: 1px solid #868383 !important;" cols="52" rows="7" onkeyup="mo_saml_valid(this)"
                                    onblur="mo_saml_valid(this)" onkeypress="mo_saml_valid(this)" required placeholder="Need assistance? Write us about your requirement and we will suggest the relevant plan for you."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_text-center">
                    <div class="mo_boot_col-sm-12">
                        <input type="hidden" name="option1" value="mo_saml_login_send_query"/><br>
                        <input  type="submit" name="submit" value="Submit" class="mo_boot_btn mo_boot_btn-primary"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
    <?php
}
/* End of Create Customer function */

function select_identity_provider()
{
    $attribute = new Mo_saml_Local_Util();
    $attribute = $attribute->_load_db_values('#__miniorange_saml_config');
    $idp_entity_id = "";
    $single_signon_service_url = "";
    $name_id_format = "";
    $certificate = "";
    $dynamicLink="Login with IDP";
    $siteUrl = JURI::root();
    $sp_base_url = $siteUrl;

    $session = JFactory::getSession();
    $current_state=$session->get('show_test_config');
    if($current_state)
    {
        ?>
        <script>
            jQuery(document).ready(function () {
                var elem = document.getElementById("test-config");
                elem.scrollIntoView();
            });
        </script>
        <?php
        $session->set('show_test_config', false);
        }
    if (isset($attribute['idp_entity_id']))
    {
        $idp_entity_id = $attribute['idp_entity_id'];
        $single_signon_service_url = $attribute['single_signon_service_url'];
        $name_id_format = $attribute['name_id_format'];
        $certificate = $attribute['certificate'];
    }
    $isAuthEnabled = JPluginHelper::isEnabled('authentication', 'miniorangesaml');
    $isSystemEnabled = JPluginHelper::isEnabled('system', 'samlredirect');
    if (!$isSystemEnabled || !$isAuthEnabled)
    {
        ?>
        <div id="system-message-container">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <div class="alert alert-error">
                <h4 class="alert-heading">Warning!</h4>
                <div class="alert-message">
                    <h4>This component requires Authentication and System Plugin to be activated. Please activate the following 2 plugins to proceed further.</h4>
                    <ul>
                        <li>Authentication - miniOrange</li>
                        <li>System - Miniorange Saml Single Sign-On</li>
                    </ul>
                    <h4>Steps to activate the plugins.</h4>
                    <ul><li>In the top menu, click on Extensions and select Plugins.</li>
                        <li>Search for miniOrange in the search box and press 'Search' to display the plugins.</li>
                        <li>Now enable both Authentication and System plugin.</li></ul>
                </div>
            </div>
        </div>
        <?php
    } ?>
    <style>
        table.ex1 {
            border-collapse: separate;
            border-spacing: 15px;
        }
    </style>
    <div class="mo_boot_row mo_boot_mr-1 mo_boot_mt-3 mo_boot_py-3 mo_boot_px-2" id="upload_metadata_form" style="background-color:#FFFFFF;border:2px solid rgb(15, 127, 182); display:none ;">
        <div class="mo_boot_col-sm-12 mo_boot_mt-3">
            <h3>
                Upload IDP Metadata
                <span style="float:right;">
                    <input type="button" class="mo_boot_btn mo_boot_btn-danger" value="Cancel" onclick = "hide_metadata_form()"/>
                </span><hr>
            </h3>
        </div>
        <div class="mo_boot_col-sm-12 mo_boot_mt-1">
            <form action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.handle_upload_metadata'); ?>" name="metadataForm" method="post" id="IDP_meatadata_form" enctype="multipart/form-data">
                <div class="mo_boot_row mo_boot_mt-2">
                    <div class="mo_boot_col-sm-3">
                        <input id="mo_saml_upload_metadata_form_action" type="hidden" name="option1" value="upload_metadata" />
                        <strong>Upload Metadata  :</strong>
                    </div>
                    <div class="mo_boot_col-sm-7">
                        <input type="hidden" name="action"  value="upload_metadata" />
                        <input type="file"  id="metadata_uploaded_file" class="mo_boot_form-control-file"  name="metadata_file" />
                    </div>
                    <div class="mo_boot_col-sm-2">
                        <input type="button" class="mo_boot_btn mo_boot_btn-saml" id="upload_metadata_file"  name="option1" method="post" style="float:right!important" value="Upload"/>
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-2">
                    <div class="mo_boot_col-sm-12">
                        <p style="font-size:13pt;text-align:center;"><strong>OR</strong></p>
                    </div>
                    <div class="mo_boot_col-lg-3">
                        <input type="hidden" name="action" value="fetch_metadata" />
                        <strong>Enter Metadata URL:</strong>
                    </div>
                    <div class="mo_boot_col-lg-6">
                        <input type="url" id="metadata_url" name="metadata_url" placeholder="Enter metadata URL of your IdP." class="mo_boot_form-control"/>
                    </div>
                    <div class="mo_boot_col-lg-3 mo_boot_text-center">
                        <input type="button" class=" mo_boot_float-lg-right mo_boot_btn mo_boot_btn-saml" name="option1" method="post" id="fetch_metadata"  value="Fetch Metadata"/>
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-3">
                    <div class="mo_boot_col-sm-8 mo_boot_offset-lg-3">
                        <input type="checkbox" disabled>
                        <strong>Update IdP settings by pinging metadata URL? <a href='#' class='premium' onclick="moSAMLUpgrade();">Enterprise feature</a></strong>
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-3" id="select_time_sync_metadata">
                    <div class="mo_boot_col-sm-5 mo_boot_offset-lg-3">
                        <span>Select how often you want to ping the IdP : </span>
                    </div>
                    <div class="mo_boot_col-sm-2">
                        <select name = "sync_interval" class="mo_boot_form-control" disabled>
                            <option value = "hourly">hourly</option>
                            <option value = "daily">daily</option>
                            <option value = "weekly">weekly</option>
                            <option value = "monthly">monthly</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        jQuery('#upload_metadata_file').click(function(){
            var file = document.getElementById("metadata_uploaded_file");
            if(file.files.length != 0 ){
                jQuery('#IDP_meatadata_form').submit();
            } else {
                alert("Please uplod the metadata file");
                jQuery('#metadata_uploaded_file').attr('required','true');
                jQuery('#metadata_url').attr('required','false');
            }
        
        });

        jQuery('#fetch_metadata').click(function(){
            var url = jQuery("#metadata_url").val();
            if(url!='')
            {
                jQuery('#IDP_meatadata_form').submit(); 
            }
            else{
                alert("Please enter the metadata URL");
                jQuery('#metadata_url').attr('required','true');
                jQuery('#metadata_uploaded_file').attr('required','false');
            }
            
        });
    </script>
    <div class="mo_boot_row  mo_boot_mr-1  mo_boot_py-3 mo_boot_px-2" id="import_export_form" style="background-color:#FFFFFF; border:2px solid rgb(15, 127, 182);display:none ;">
        <div class="mo_boot_col-sm-12">
            <h3>
                Import /Export Configuration <sup><a href="https://plugins.miniorange.com/documentation/6-import-export-configuration" target="_blank" style="font-size:20px;color:black" title="Know More"><div class="fa fa-question-circle-o"></div></a></sup>
                <span style="float:right;margin-right:25px;">
                    <input type="button" class="mo_boot_btn mo_boot_btn-danger" value="Cancel" onclick="hide_import_export_form()"/>
                </span><hr>
            </h3>
            
        </div>
        <!-- <div class="mo_boot_col-sm-12">
            <p>This tab will help you to transfer your plugin configurations when you change your Joomla instance</p>
            <p>Example: When you switch from test environment to production. Follow these 3 simple steps to do that:</p>
            <ol class ="att">
                <li>Download plugin configuration file by clicking on the button given below.</li>
                <li>Install the plugin on new Joomla instance.</li>
                <li>Upload the configuration file in Import Plugin Configurations section.</li>
            </ol>
        </div> -->
        <div class="mo_boot_col-sm-12">
            <h3>Download configuration file</h3>
        </div>
        <div class="mo_boot_col-sm-12 mo_boot_pl-sm-4">
            <form name="f" method="post" action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.importexport'); ?>">
                <input id="mo_sp_exp_exportconfig" type="button" class="mo_boot_btn mo_boot_btn-saml" onclick="submit();" value= "Export Configuration" />
            </form>
        </div>


        <div class="mo_boot_col-sm-12"><br>
            <h3>Import Configurations</h3><hr>
            <p> This feature is available in the <a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Standard</strong></a>,<a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Premium</strong></a>,<a href='#' class='premium' onclick="moSAMLUpgrade();"><strong>Enterprise</strong></a> version of plugin.</p>
        </div>

        <div class="mo_boot_col-sm-4"><br>
            <input type="file" class="form-control-file mo_boot_d-inline" name="configuration_file" disabled="disabled">
        </div>
        <div class="mo_boot_col-sm-4 mo_boot_pl-sm-4"><br>
            <input id="mo_sp_exp_importconfig" type="submit" disabled="disabled" name="submit" class="mo_boot_btn mo_boot_btn-saml" value="Import"/>
        </div>
    </div>
    <div class="mo_boot_row mo_boot_mr-1  mo_boot_p-3 mo_boot_px-2" id="tabhead"  style="background-color:#FFFFFF; border:2px solid rgb(15, 127, 182);">
        <div class="mo_boot_col-sm-12">
            <form action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.saveConfig'); ?>" method="post" name="adminForm" id="identity_provider_settings_form" enctype="multipart/form-data">
            <div class="mo_boot_row mo_boot_mt-3" >
                <div class="mo_boot_col-lg-5">
                        <h3>Service Provider Setup <sup><a href="https://developers.miniorange.com/docs/joomla/saml-sso/saml-service-provider-setup" target="_blank" style="font-size:20px;color:black" title="Know More"><div class="fa fa-question-circle-o"></div></a></sup></h3>
                </div>

                    <div class="mo_boot_col-lg-7 " >
                        <a href="https://plugins.miniorange.com/step-by-step-guide-for-joomla-single-sign-on-sso" target="_blank" style="float:right;margin-right: 2%;  ">
                            <span class="fa fa-file  mo_boot_btn mo_boot_btn-secondary"  > Guides</span>
                        </a>
                        <span style="margin-right:4%; float:right;">
                            <a href="https://www.youtube.com/playlist?list=PL2vweZ-PcNpdkpUxUzUCo66tZsEHJJDRl"  target="_blank">
                                <span class="fa fa-youtube mo_boot_text-light mo_boot_btn mo_boot_btn-danger" > Videos</span>
                            </a> 
                        </span> 
                        <input id="mo_saml_local_configuration_form_action" type="hidden" name="option1" value="mo_saml_save_config" />
                    </div>
                    <div class="mo_boot_col-sm-12">
                        <hr>
                        <div class="mo_boot_row">
                                <div class="mo_boot_col-lg-6">
                                    <strong>Enter the information gathered from your Identity Provider </strong>
                                </div>
                                <div class="mo_boot_col-lg-3">
                                    <strong> OR </strong>
                                </div>
                                <div class="mo_boot_col-lg-3 mo_boot_mt-1" id="tour_upload_metadata">
                                    <input id="sp_upload_metadata" type="button" class='mo_boot_btn mo_boot_btn-saml' onclick='show_metadata_form();' value="Upload IDP Metadata"/>
                                </div>
                            </div>
                        </div>
                    </div> 
                <div id="idpdata" class="ex1">
                    <div class="mo_boot_row mo_boot_mt-3" id="sp_select_idp">
                        <div class="mo_boot_col-sm-4">
                            <strong>Select your Identity Provider for Guide <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext" >You can select any IDP from dropdown to refer its guide for configuration.</span></div></strong>
                        </div>
                        <div class="mo_boot_col-sm-8 start_dropdown">
                            <div class="mo_boot_form-control" style="width:100%;cursor:pointer" id="select_idp" >Select Identity Provider<span style="float:right!important" ><i class='fa fa-angle-down'></i></span></div>
 
                                <div id="myDropdown" class="myDropdown mo_dropdown-content">
                                            
                                    <div id="dropdown_options" class="dropdown_options" >
                                        <input type="text"  class="mo_boot_form-control" style="display:none" placeholder="Search/Select Identity Provider to open setup guide" id="myInput" onkeyup="filterFunction()">
                                        <div class="mo_dropdown_options" id="dropdown-test">
                                        <a href="https://plugins.miniorange.com/guide-joomla-single-sign-sso-using-adfs-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="ADFS">ADFS</a>
                                        <a href="https://plugins.miniorange.com/joomla-single-sign-sso-using-azure-ad-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Azure AD">Azure AD</a>
                                        <a href="https://plugins.miniorange.com/joomla-single-sign-sso-using-google-apps-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Google Apps">Google Apps</a>
                                        <a href="https://plugins.miniorange.com/joomla-single-sign-sso-using-okta-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Okta">Okta</a>
                                        <a href="https://plugins.miniorange.com/saml-single-sign-on-sso-for-joomla-using-office-365-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Office 365">Office 365</a>
                                        <a href="https://plugins.miniorange.com/joomla-single-sign-sso-using-salesforce-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="SalesForce">SalesForce</a>
                                        <a href="https://plugins.miniorange.com/joomla-single-sign-sso-using-onelogin-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="OneLogin">OneLogin</a>
                                        <a href="https://plugins.miniorange.com/saml-single-sign-on-sso-for-joomla-using-simplesaml-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="SimpleSAML">SimpleSAML</a>
                                        <a href="https://plugins.miniorange.com/joomla-single-sign-sso-using-miniorange-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Miniorange">Miniorange</a>
                                        <a href="https://plugins.miniorange.com/joomla-single-sign-on-sso-using-centrify-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Centrify">Centrify</a>
                                        <a href="https://plugins.miniorange.com/joomla-single-sign-sso-using-bitium-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Bitium">Bitium</a>
                                        <a href="https://plugins.miniorange.com/guide-to-configure-lastpass-as-an-idp-saml-sp" target="_blank" class="dropdown_option mo_dropdown_option" id="LastPass">LastPass</a>
                                        <a href="https://plugins.miniorange.com/guide-for-pingfederate-as-idp-with-joomla" target="_blank" class="dropdown_option mo_dropdown_option" id="Ping Federate">Ping Federate</a>
                                        <a href="https://plugins.miniorange.com/guide-for-joomla-single-sign-on-sso-using-rsa-securid-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="RSA SecureID">RSA SecureID</a>
                                        <a href="https://plugins.miniorange.com/guide-for-openam-as-idp-with-joomla" target="_blank" class="dropdown_option mo_dropdown_option" id="OpenAM">OpenAM</a>
                                        <a href="https://plugins.miniorange.com/guide-for-auth0-as-idp-with-joomla" target="_blank" class="dropdown_option mo_dropdown_option" id="Auth0">Auth0</a>
                                        <a href="https://plugins.miniorange.com/joomla-single-sign-sso-using-authanvil-ias-dp" target="_blank" class="dropdown_option mo_dropdown_option" id="Auth Anvil">Auth Anvil</a>
                                        <a href="https://plugins.miniorange.com/guide-to-setup-shibboleth2-as-idp-with-joomla" target="_blank" class="dropdown_option mo_dropdown_option" id="Shibboleth 2">Shibboleth 2</a>
                                        <a href="https://plugins.miniorange.com/guide-to-setup-shibboleth3-as-idp-with-joomla" target="_blank" class="dropdown_option mo_dropdown_option" id="Shibboleth 3">Shibboleth 3</a>
                                        <a href="https://plugins.miniorange.com/oracle-access-manager-as-idp-and-joomla-as-sp" target="_blank" class="dropdown_option mo_dropdown_option" id="Oracle Access Manager">Oracle Access Manager</a>
                                        <a href="https://plugins.miniorange.com/saml-single-sign-sso-joomla-using-wso2" target="_blank" class="dropdown_option mo_dropdown_option" id="WSO2">WSO2</a>
                                        <a href="https://plugins.miniorange.com/joomla-single-sign-sso-using-pingone-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="PingOne">PingOne</a>
                                        <a href="http://plugins.miniorange.com/joomla-single-sign-on-sso-using-jboss-keycloak-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="JBoss Keycloak">JBoss Keycloak</a>
                                        <a href="https://plugins.miniorange.com/joomla-saml-single-sign-on-sso-with-drupal" target="_blank" class="dropdown_option mo_dropdown_option" id="Drupal">Drupal</a>
                                        <a href="https://plugins.miniorange.com/saml-single-sign-on-sso-for-joomla-using-simplesaml-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="SimpleSAML">SimpleSAML</a>
                                        <a href="https://plugins.miniorange.com/joomla-single-sign-on-sso-using-centrify-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Centrify">Centrify</a>
                                        <a href="https://plugins.miniorange.com/guide-for-joomla-single-sign-on-sso-using-rsa-securid-as-idp" target="_blank"class="dropdown_option mo_dropdown_option" id="RSA SecureID" >RSA SecureID</a>
                                        <a href="https://plugins.miniorange.com/joomla-saml-single-sign-on-sso-using-cyberark-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="CyberArk">CyberArk</a>
                                        <a href="https://plugins.miniorange.com/joomla-saml-single-sign-on-sso-using-degreed-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Degreed">Degreed</a>
                                        <a href="https://plugins.miniorange.com/joomla-saml-single-sign-on-sso-using-duo-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Duo">Duo</a>
                                        <a href="https://plugins.miniorange.com/joomla-saml-single-sign-on-sso-using-netiq-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="NetIQ">NetIQ</a>
                                        <a href="https://plugins.miniorange.com/joomla-saml-single-sign-on-sso-using-fonteva-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Fonteva">Fonteva</a>
                                        <a href="https://plugins.miniorange.com/joomla-saml-single-sign-on-sso-using-fusionauth-as-idp"target="_blank" class="dropdown_option mo_dropdown_option" id="FusionAuth">FusionAuth</a>
                                        <a href="https://plugins.miniorange.com/joomla-saml-single-sign-on-sso-using-gluu-server-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Gluu Server">Gluu Server</a>
                                        <a href="https://plugins.miniorange.com/joomla-saml-single-sign-on-sso-using-identityserver4-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="IdentifyServer4">IdentifyServer4</a>
                                        <a href="https://plugins.miniorange.com/joomla-saml-single-sign-on-sso-using-openathens-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Openathens">Openathens</a>
                                        <a href="https://plugins.miniorange.com/joomla-saml-single-sign-on-sso-using-phenixid-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Phenixid">Phenixid</a>
                                        <a href="https://plugins.miniorange.com/joomla-saml-single-sign-on-sso-using-secureauth-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="SecureAuth">SecureAuth</a>
                                        <a href="https://plugins.miniorange.com/joomla-saml-single-sign-on-sso-using-siteminder-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Siteminder">Siteminder</a>
                                        <a href="https://plugins.miniorange.com/joomla-saml-single-sign-on-sso-using-surfconext-as-idp"target="_blank" class="dropdown_option mo_dropdown_option" id="Surfcontext">Surfcontext</a>
                                        <a href="https://plugins.miniorange.com/salesforce-community-saml-single-sign-on-sso-into-joomla" target="_blank" class="dropdown_option mo_dropdown_option" id="SF Community">SF Community</a>
                                        <a href="https://plugins.miniorange.com/saml-single-sign-on-sso-into-joomla-using-classlink-as-idp-classlink-sso-login" target="_blank" class="dropdown_option mo_dropdown_option" id="ClassLink">ClassLink</a>
                                        <a href="https://plugins.miniorange.com/wordpress-single-sign-on-sso-using-joomla" target="_blank" class="dropdown_option mo_dropdown_option" id="Wordpress">Wordpress</a>
                                        <a href="https://plugins.miniorange.com/absorb-lms-single-sign-on-sso-using-joomla" target="_blank" class="dropdown_option mo_dropdown_option" id="Absorb LMS">Absorb LMS</a>
                                        <a href="https://plugins.miniorange.com/absorb-lms-single-sign-on-sso-using-joomla"target="_blank"class="dropdown_option mo_dropdown_option" id="CAS Server">CAS Server</a>
                                        <a href="https://plugins.miniorange.com/step-by-step-guide-for-joomla-single-sign-on-sso" target="_blank" class="dropdown_option mo_dropdown_option" id="Custom IDP">Custom IDP</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-3" id="sp_entity_id_idp">
                        <div class="mo_boot_col-sm-4">
                            <strong><span style="color:red">*</span>IdP Entity ID/ Issuer <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext" >You can find the EntityID in Your Identity Provider-Metadata XML file enclosed in <code style="color:#40F7E1">EntityDescriptor</code> tag having attribute as <code style="color:#40F7E1">entityID</code></span></div></strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input type="text" class="mo_boot_form-control" name="idp_entity_id" style="border: 1px solid #868383 !important;" placeholder="Identity Provider Entity ID or Issuer" value="<?php echo $idp_entity_id; ?>" required />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-3" id="sp_nameid_format_idp">
                        <div class="mo_boot_col-sm-4">
                            <strong>NameID Format <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext">If you are using ADFS as IdP then the NameID Format should be <code style="color:#40F7E1">unspecified</code>.</span></div></strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <select class="mo_boot_form-control" id="name_id_format" name="name_id_format" style="border: 1px solid #868383 !important;">
                                <option value="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress"
                                    <?php if ($name_id_format == "urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress") echo 'selected = "selected"' ?>>
                                    urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress
                                </option>
                                <option value="urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified"
                                    <?php if ($name_id_format == "urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified") echo 'selected = "selected"' ?>>
                                    urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mo_boot_row mo_boot_mt-3" id="sp_sso_url_idp">
                        <div class="mo_boot_col-sm-4">
                            <strong><span style="color:red">*</span>Single Sign-On URL <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext">You can find the SAML Login URL in Your Identity Provider-Metadata XML file enclosed in <code style="color:#40F7E1">SingleSignOnService</code> tag (Binding type: HTTP-Redirect)</span></div></strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input class="mo_saml_table_textbox mo_boot_form-control" type="url" placeholder="Single Sign-On Service URL (Http-Redirect) binding of your IdP" name="single_signon_service_url" style="border: 1px solid #868383 !important;" value="<?php echo $single_signon_service_url; ?>" required />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-3" id="sp_certificate_idp">
                        <div class="mo_boot_col-sm-4">
                            <strong>X.509 Certificate <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext"> Format of the certificate:<br>
                                                ---BEGIN CERTIFICATE---<br>
                                                XXXXXXXXXXXXXX<br>
                                                ---END CERTIFICATE---</span></div></strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <div class="mo_boot_row">
                                <div class="mo_boot_col-lg-4">
                                    <label>   <input type="radio" name="cert"  value="text_cert" CHECKED > Enter text</label>
                                </div>
                                <div class="mo_boot_col-lg-5">
                                    <label><input type="radio" name="cert"  value="upload_cert" > Upload Certificate</label>
                                </div>
                            </div>
                            <div class="upload_cert selectt" >
                                <div class="mo_saml_border">
                                        <input type="file" id="myFile" name="myFile" style="margin-right: 75px; margin-left: 25px;margin-top: 15px; " >
                                </div>
                                <span id="uploaded_cert"></span>
                            </div>
                            <div class="text_cert selectt">
                                <textarea rows="5" cols="80" name="certificate" class="mo_boot_form-text-control" style="width: 100%; border: 1px solid #868383 !important;" placeholder="Copy and Paste the content from the downloaded certificate or copy the content enclosed in 'X509Certificate' tag (has parent tag 'KeyDescriptor use=signing') in IdP-Metadata XML file"><?php echo $certificate; ?></textarea>
                            </div>
                                        <script type="text/javascript">
                                           
                                        </script>

                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-3" id="saml_login">
                        <div class="mo_boot_col-sm-4">
                            <strong>Enable Login with SAML <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext" >You can add SSO link on login page </span></div></strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input type="checkbox" id ="login_link_check" name="login_link_check" class="mo_saml_custom_checkbox" onclick="showLink()" value="1"
                                    <?php 
                                        $count = isset($attribute['login_link_check']) ? $attribute['login_link_check'] : "";
                                        $dynamicLink=isset($attribute['dynamic_link']) && !empty($attribute['dynamic_link']) ? $attribute['dynamic_link'] : "";
                                        if($count ==1)                        
                                            echo 'checked="checked"';                           
                                        else
                                            $dynamicLink="Login with your IDP";
                                    ?>
                            >
                            <input type="text" id="dynamicText" name="dynamic_link" placeholder="Enter your IDP Name" value="<?php echo $dynamicLink; ?>" class="mo_boot_form-control mo_boot_mt-3 mo_boot_my-3" >
                            <?php
                                if($count!=1)
                                {
                                    echo '<script>document.getElementById("dynamicText").style.display="none"</script>';
                                }
                            ?>
                        </div><br><br><br>
                        <div class="mo_boot_col-sm-4">
                            <strong>SSO URL <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext" >This link is used for Single Sign-On by end users.</span></div></strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <div class="mo_saml_highlight_background_url_note">
                                <div class="mo_boot_row">
                                    <div class="mo_boot_col-10">
                                        <span id="show_sso_url" style="color:#2a69b8">
                                            <strong><?php echo  $sp_base_url . '?morequest=sso'; ?></strong>
                                        </span>
                                    </div>
                                    <div class="mo_boot_col-2">
                                        <em class="fa fa-lg fa-copy mo_copy_sso_url mo_copytooltip" onclick="copyToClipboard('#show_sso_url');"><span class="mo_copytooltiptext copied_text">Copy</span> </em>   
                                    </div>
                                </div>
                            </div>  
                        </div>
                    </div><br>
                    <details !important>
                        <summary class="mo_saml_main_summary" >Premium Features<sup><strong><a href='#' class='premium' onclick="moSAMLUpgrade();"> [Standard, Premium, Enterprise]</a></strong></sup></summary><hr>
                    <div class="mo_boot_row mo_boot_mt-3" id="sp_slo_idp">
                        <div class="mo_boot_col-sm-4">
                            <strong>Single Logout URL <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext">You can find the SAML Logout URL in Your Identity Provider-Metadata XML file enclosed in <code style="color:#40F7E1">SingleLogoutService</code> tag <strong><a href='#' class='premium' onclick="moSAMLUpgrade();">[Premium, Enterprise]</a></strong></span></div></strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input class="mo_saml_table_textbox mo_boot_form-control" type="text" name="single_logout_url" placeholder="Single Logout URL" disabled>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-3">
                        <div class="mo_boot_col-sm-4">
                            <strong>Signature Algorithm <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext">Algorithm used in the signing process. (Algorithm eg. sha256, sha384, sha512, sha1 etc) <strong><a href='#' class='premium' onclick="moSAMLUpgrade();">[Enterprise]</a></strong></span></div></strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <select class="mo_boot_form-control" style="border: 1px solid #868383 !important;" disabled>
                                <option>sha256</option>
                            </select>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-3" id="sp_binding_type">
                        <div class="mo_boot_col-sm-4">
                            <strong>Select Biniding type <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext"><strong><a href='#' class='premium' onclick="moSAMLUpgrade();"> [Standard, Premium, Enterprise]</a></strong></span></div></strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input type="radio" name="miniorange_saml_idp_sso_binding" value="HttpRedirect" checked=1 aria-invalid="false" disabled> <span>Use HTTP-Redirect Binding for SSO</span><br>
                            <input type="radio"  name="miniorange_saml_idp_sso_binding" value="HttpPost" aria-invalid="false" disabled> 
                            <span>Use HTTP-POST Binding for SSO </span>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-3" id="sp_saml_request_idp">
                        <div class="mo_boot_col-sm-4">
                            <strong>Sign SSO and SLO request <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext"><strong><a href='#' class='premium' onclick="moSAMLUpgrade();"> [Standard, Premium, Enterprise]</a></strong></span></div></strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input type="checkbox" name="saml_request_sign" style="border: 1px solid #868383 !important;" disabled>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-3" id="sp_saml_context_class">
                        <div class="mo_boot_col-sm-4">
                            <strong>Authentication context class <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext">The authentication context indicates how a user authenticated at an Identity Provider. Service Provider requests the authentication context by including the <code style="color:#40F7E1">RequestedAuthnContext</code> tag element in the authentication request to the Identity Provider.</span></div></strong>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <select class="mo_boot_form-control" style="border: 1px solid #868383 !important;" disabled>
                                <option>PasswordProtectedTransport</option>
                            </select>
                        </div>
                    </div><br>
                    </details>
                    
                    <div class="mo_boot_row mo_boot_mt-5">
                        <div class="mo_boot_col-sm-12 mo_boot_text-center">
                            <input type="submit" class="mo_boot_btn mo_boot_btn-success" value="Save"/>
                            <input  type="button" id='test-config' <?php if ($idp_entity_id) echo "enabled";else echo "disabled"; ?> title='You can only test your configuration after saving your Identity Provider Settings.' class="mo_boot_btn mo_boot_btn-saml" onclick='showTestWindow()' value="Test Configuration">
                            <a href="#import_export_form" type="button" class="mo_boot_btn mo_boot_btn-saml" onclick="show_import_export()">Import/Export</a>
                            </div>
                        </div>
                </div>
            </form>
        </div>
    </div>
    <?php
}
