<?php
defined('_JEXEC') or die;
/*
 * @package    miniOrange
 * @subpackage Plugins
 * @license    GNU/GPLv3
 * @copyright  Copyright 2015 miniOrange. All Rights Reserved.
*/
JHtml::_('stylesheet', JUri::base() .'components/com_miniorange_saml/assets/css/miniorange_boot.css');

function mo_saml_local_support(){
	$strJsonFileContents = file_get_contents(__DIR__ . '/../assets/json/timezones.json'); 
	$timezoneJsonArray = json_decode($strJsonFileContents, true);
    
    $current_user = JFactory::getUser();
    $result       = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_customer_details');
    $admin_email  = isset($result['email']) ? $result['email'] : '';
    $admin_phone  = isset($result['admin_phone']) ? $result['admin_phone'] : '';
	if($admin_email == '')
		$admin_email = $current_user->email;
	?>
	<div id="sp_support_saml">
		<div class="mo_boot_row mo_boot_p-3" style="border: 2px solid rgb(15, 127, 182);background-color:white">
			<div class="mo_boot_col-sm-12">
				<div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12 mo_boot_p-2">
                        <h4>Feature Request/ Contact Us (24*7 Support)</h4>
                    </div>
				</div>
				<hr>
			</div>
			<div class="mo_boot_col-sm-12">
				<form  name="f" method="post" action="<?php echo JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.contactUs');?>">
                    <div class="mo_boot_col-sm-12">	
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-2">
                                <img src="<?php echo JUri::base();?>/components/com_miniorange_saml/assets/images/phone.svg" width="27" height="27"  alt="Phone Image"> 
                            </div>
                            <div class="mo_boot_col-sm-10">
                                <p><strong>Need any help? <br>Just give us a call at <span style="color:red">+1 978 658 9387</span></strong></p><br>
                            </div>
                            
                        </div>
                    </div>
                    <p>We can help you with configuring your Identity Provider. Just send us a query and we will get back to you soon.</p>
                    <div class="mo_boot_row mo_boot_text-center">
                        <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                            <input style="border: 1px solid #868383 !important;" type="email" class="mo_saml_table_textbox mo_boot_form-control" name="query_email" value="<?php echo $admin_email; ?>" placeholder="Enter your email" required />
                        </div>
                        <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                            <input style="border: 1px solid #868383 !important;" type="text" class="mo_saml_table_textbox mo_boot_form-control" name="query_phone" pattern="[\+]\d{11,14}|[\+]\d{1,4}([\s]{0,1})(\d{0}|\d{9,10})" value="<?php echo $admin_phone; ?>" placeholder="Enter your phone number with country code"/>
                        </div>
                        <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                            <textarea  name="mo_saml_query_support" class="mo_boot_form-text-control" style="border-radius:4px;resize: vertical;width:100%; border: 1px solid #868383 !important;" cols="52" rows="7" required placeholder="Write your query here"></textarea>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_text-center mo_boot_mt-3">
                        <div class="mo_boot_col-sm-12">
                            <input type="hidden" name="option1" value="mo_saml_login_send_query"/>
                            <input type="submit" name="send_query" value="Submit Query" class="mo_boot_btn mo_boot_btn-success" />
                        </div>
                    </div><hr>
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-12">
                            <p><br>If you want custom features in the plugin, just drop an email to <a style="word-wrap:break-word!important;" href="mailto:joomlasupport@xecurify.com"> joomlasupport@xecurify.com</a> </p>
                        </div>
                    </div>
			    </form>
			</div>
		</div>
	</div>
<?php
}


function mo_saml_advertise(){
	?>
	<div id="sp_advertise" class="">
		<div class="mo_boot_row mo_boot_p-3" style="border: 2px solid rgb(15, 127, 182);background-color:white">
			<div class="mo_boot_col-sm-12">
				<div class="mo_boot_row">
                    <div class="mo_boot_col-sm-2">
                        <img src="<?php echo JUri::base();?>/components/com_miniorange_saml/assets/images/miniorange_i.ico" alt="miniorange">  
                    </div>
                    <div class="mo_boot_col-sm-10">
                        <h4>Looking for SCIM plugin on Joomla?</h4>
                    </div>
				</div><hr>
			</div>
			<div class="mo_boot_col-sm-12">
               <div class="mo_boot_px-3  mo_boot_text-center">
                     <img src="<?php echo JUri::base();?>/components/com_miniorange_saml/assets/images/scim-icon.png" width="100" height="100" alt="SCIM">
                </div>
               <p><br><br>
                SCIM User provisioning provides SCIM (System for Cross-domain Identity Management) capability to your Joomla site, converting it to a SCIM compliant endpoint that can be configured with any identity provider supporting SCIM protocol.
               </P>
               <div class="mo_boot_row mo_boot_text-center mo_boot_mt-5">
                   <div class="mo_boot_col-sm-12">
                        <input type="button" onclick="window.open('https://prod-marketing-site.s3.amazonaws.com/plugins/joomla/scim-user-provisioning-for-joomla.zip')" target="_blank" value="Download"  style="margin-right: 25px; margin-left: 25px;" class="mo_boot_btn mo_boot_btn-saml" />
                        <input type="button" onclick="window.open('https://plugins.miniorange.com/joomla-scim-user-provisioning')" target="_blank" value="Know More"  style="margin-right: 25px; margin-left: 25px;" class="mo_boot_btn mo_boot_btn-success" />
                    </div>
               </div>
			</div>
		</div>
	</div>
<?php
}

function mo_saml_adv_pagerestriction(){
    ?>
    <div id="sp_advertise" class="">
        <div class="mo_boot_row mo_boot_p-3" style="border: 2px solid rgb(15, 127, 182);background-color:white">
            <div class="mo_boot_col-sm-12">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-2">
                        <img src="<?php echo JUri::base();?>/components/com_miniorange_saml/assets/images/miniorange_i.ico" alt="miniorange">  
                    </div>
                    <div class="mo_boot_col-sm-10">
                        <h4>Looking for Page Restriction on Joomla?</h4>
                    </div>
                </div><hr>
            </div>
            <div class="mo_boot_col-sm-12 ">
                <div class="mo_boot_px-3  mo_boot_text-center">
                         <img src="<?php echo JUri::base();?>/components/com_miniorange_saml/assets/images/session-management-addon.webp" alt="Session Management" width="150"  height="150" >
                </div>
                <p><br>
                    miniOrange Page restriction allows you to prevent unauthorized users from accessing certain pages or articles, as well as redirecting users to a specific page after SSO.
                </P>
                <div class="mo_boot_row mo_boot_text-center mo_boot_mt-5">
                    <div class="mo_boot_col-sm-12">
                        <input type="button" onclick="window.open('https://plugins.miniorange.com/page-and-article-restriction-for-joomla')" target="_blank" value="Know More"  style="margin-right: 25px; margin-left: 25px;" class="mo_boot_btn mo_boot_btn-success" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function mo_saml_adv_net(){
    ?>
    <div id="sp_advertise" class="">
        <div class="mo_boot_row mo_boot_p-3" style="border: 2px solid rgb(15, 127, 182);background-color:white">
            <div class="mo_boot_col-sm-12">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-2">
                        <img src="<?php echo JUri::base();?>/components/com_miniorange_saml/assets/images/miniorange_i.ico" alt="miniorange">  
                    </div>
                    <div class="mo_boot_col-sm-10">
                        <h4>Looking for Web Security on Joomla?</h4>
                    </div>
                </div><hr>
            </div>
            <div class="mo_boot_col-sm-12 ">
                <div class="mo_boot_px-3  mo_boot_text-center">
                    <img src="<?php echo JUri::base();?>/components/com_miniorange_saml/assets/images/network.webp" alt="Web Security">
                </div><p>Plugin combines Limit Login Attempts, Encrypted Database backup with recovery, and Login Protection with Two Factor and Spam Protection.
                <div class="mo_boot_row mo_boot_text-center mo_boot_mt-5">
                    <div class="mo_boot_col-sm-12">
                        <input type="button" onclick="window.open('https://prod-marketing-site.s3.amazonaws.com/plugins/joomla/miniorange_joomla_network_security.zip')" target="_blank" value="Download"  style="margin-right: 25px; margin-left: 25px;" class="mo_boot_btn mo_boot_btn-saml" />
                        <input type="button" onclick="window.open('https://plugins.miniorange.com/joomla-network-security')" target="_blank" value="Know More"  style="margin-right: 25px; margin-left: 25px;" class="mo_boot_btn mo_boot_btn-success" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function mo_saml_adv_loginaudit(){
    ?>
    <div id="sp_advertise" class="">
        <div class="mo_boot_row mo_boot_p-3" style="border: 2px solid rgb(15, 127, 182);background-color:white">
            <div class="mo_boot_col-sm-12">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-2">
                        <img src="<?php echo JUri::base();?>/components/com_miniorange_saml/assets/images/miniorange_i.ico" alt="miniorange">  
                    </div>
                    <div class="mo_boot_col-sm-10">
                        <h4>Looking for Login Audit plugin on Joomla?</h4>
                    </div>
                </div><hr>
                </div>
                <div class="mo_boot_col-sm-12 ">
                    <p><br>Login Audit captures all the SSO users and normal users and will generate the reports which includes thier login time, browser and IP address.</P>
                   <div class="mo_boot_row mo_boot_text-center mo_boot_mt-4">
                       <div class="mo_boot_col-sm-12">
                            <input type="button" onclick="window.open('https://plugins.miniorange.com/joomla-login-audit-login-activity-report')" target="_blank" value="Know More"  style="margin-right: 25px; margin-left: 25px;" class="mo_boot_btn mo_boot_btn-success" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
}

function mo_saml_adv_idp(){
    ?>
    <div id="sp_advertise" class="">
        <div class="mo_boot_row mo_boot_p-3" style="border: 2px solid rgb(15, 127, 182);background-color:white">
            <div class="mo_boot_col-sm-12">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-2">
                        <img src="<?php echo JUri::base();?>/components/com_miniorange_saml/assets/images/miniorange_i.ico" alt="miniorange">  
                    </div>
                    <div class="mo_boot_col-sm-10">
                        <h4>Looking for SAML IDP on Joomla?</h4>
                    </div>
                </div><hr>
                </div>
                <div class="mo_boot_col-sm-12 ">
                   <div class="mo_boot_px-3  mo_boot_text-center">
                       <img src="<?php echo JUri::base();?>/components/com_miniorange_saml/assets/images/login-audit-addon.webp" alt="Login Audit"  width="150"  height="150" >
                    </div>
                   <p><br><br>
                        Plugin acts as a SAML 2.0 Identity Provider (IDP) which can be configured to establish the trust between the Joomla site and various SAML 2.0 supported Service Providers to securely authenticate the user using the Joomla site credentials.
                   </P>
                   <div class="mo_boot_row mo_boot_text-center mo_boot_mt-5">
                       <div class="mo_boot_col-sm-12">
                           <input type="button" onclick="window.open('https://prod-marketing-site.s3.amazonaws.com/plugins/joomla/miniorange-joomla-saml-idp.zip')" target="_blank" value="Download"  style="margin-right: 25px; margin-left: 25px;" class="mo_boot_btn mo_boot_btn-saml" />
                            <input type="button" onclick="window.open('https://plugins.miniorange.com/joomla-idp-saml-sso')" target="_blank" value="Know More"  style="margin-right: 25px; margin-left: 25px;" class="mo_boot_btn mo_boot_btn-success" />
                        </div>
                   </div>
                </div>
            </div>
        </div>
    <?php
}
?>