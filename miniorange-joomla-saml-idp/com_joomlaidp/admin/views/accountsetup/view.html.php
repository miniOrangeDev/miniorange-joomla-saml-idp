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
defined('_JEXEC') or die('Restricted access');
JHtml::_('jquery.framework');
JHtml::_('stylesheet', JUri::base() .'components/com_joomlaidp/assets/css/miniorange_boot.css');
JHtml::_('stylesheet', JUri::base() .'components/com_joomlaidp/assets/css/miniorange_idp.css');
JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
JHtml::_('script', JUri::base() . 'components/com_joomlaidp/assets/js/bootstrap-tour-standalone.min.js');
JHtml::_('script', JUri::base() . 'components/com_joomlaidp/assets/js/mo-saml-idp-tour.js');
JHtml::_('script', JUri::base() . 'components/com_joomlaidp/assets/js/bootstrap-select-min.js');
JHtml::_('script', JUri::base() . 'components/com_joomlaidp/assets/js/utilityjs.js');
/**
 * Register/Login View
 *
 * @since  0.0.1
 */
class JoomlaIdpViewAccountSetup extends JViewLegacy
{
    function display($tpl = null)
    {
        // Get data from the model
        $this->lists = $this->get('List');
        //$this->pagination	= $this->get('Pagination');
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode('<br />', $errors), 'error');
            return false;
        }
        $this->setLayout('accountsetup');
        // Set the toolbar
        $this->addToolBar();
        // Display the template
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function addToolBar()
    {
        JToolBarHelper::title(JText::_('COM_JOOMLAIDP_PLUGIN_TITLE'), 'mo_saml_logo mo_saml_icon');
    }

    public static function showRoleRelayRestriction()
    {
        $attribute = IDP_Utilities::fetchDatabaseValues('#__miniorangesamlidp', 'loadAssoc', '*');

        if (isset($attribute['sp_entityid'])) {
            $sp_entityid = isset($attribute['sp_entityid']) ? $attribute['sp_entityid'] : '';
            $sp_name = isset($attribute['sp_name']) ? $attribute['sp_name'] : '';
        }
        ?>
        <div class="mo_boot_row mo_boot_px-4 mo_boot_py-4 " style="border: 2px solid rgb(15, 127, 182); background-color:white">
            <div class="mo_boot_col-sm-12">
                <h3>ROLE RESTRICTION<sup><a href="index.php?option=com_joomlaidp&view=accountsetup&tab-panel=license" style="text-decoration: none;"> [Premium]</a></sup></h3>
                <hr>
                <p>
                    <b style="color: #0b70cd;font-size: 14px;">Note: </b>
                    <b style="font-size: 14px;">You can use this feature if you want to restrict or allow particular roles for SSO.</b>
                </p>
                <br>
            </div>
            <div class="mo_boot_col-sm-12 mo_boot_table-responsive">
        <?php

            echo '
                <form action="" method="post" id="adminForm" name="adminForm">
                    <table class="mo_boot_table mo_boot_table-striped mo_boot_table-hover mo_boot_table-bordered">
                        <thead>
                            <tr>
                                <th width="1%">Sr.No</th>
                                <th width="15%">' . JText::_('COM_JOOMLAIDP_MULTISAMLIDPS_IDENTIFIER') . '</th>
                                <th width="43%">' . JText::_('COM_JOOMLAIDP_MULTISAMLIDPS_ISSUER') . '</th>
                                <th width="15%">Role Restriction Status</th>
                            </tr>
                        </thead>
                        <tbody>';
            if ($sp_name) :
                echo '<tr>
                        <td>1</td>
                        <td>' . $sp_name . '</td>
                        <td>' . $sp_entityid . '</td>
                        <td>Not Configured</td>
                    </tr>';
            endif;
            echo '
                </tbody>
                    </table>
                    <br><br>
                    <h3>RELAY RESTRICTION <sup><a href="index.php?option=com_joomlaidp&view=accountsetup&tab-panel=license" style="text-decoration: none;"> [Premium]</a></h3></sup><hr>
                    <p><b style="color: #0b70cd;font-size: 14px;">Note: </b> <b style="font-size: 14px;">You can use this feature to redirect on Custom Relay State after IDP Initiated Login.</b></p><br>
                    <table class="mo_boot_table mo_boot_table-striped mo_boot_table-hover mo_boot_table-bordered">
                        <thead>
                            <tr>
                                <th width="1%">Sr.No</th>
                                <th width="15%">' . JText::_('COM_JOOMLAIDP_MULTISAMLIDPS_IDENTIFIER') . '</th>
                                <th width="43%">' . JText::_('COM_JOOMLAIDP_MULTISAMLIDPS_ISSUER') . '</th>
                                <th width="15%">Relay Restriction Status</th>
                            </tr>
                        </thead>
                        <tbody>';
            if ($sp_name) :
                echo '<tr>
                        <td>1</td>
                        <td>' . $sp_name . '</td>
                        <td>' . $sp_entityid . '</td>
                        <td>Not Configured</td>
                    </tr>';
            endif;
            echo '</tbody>
                    </table>
                    <div class="mo_boot_text-center"><input type="submit" class="mo_boot_btn mo_boot_mt-4 mo_boot_btn-saml" value="Click here to Configure" disabled/></div>
                </form>';
                ?>
                </div>
        </div>
        <?php
    }

    public static function showIDPInitiatedLoginDetails()
    {
        $site_url = JURI::root();
        $attribute = IDP_Utilities::fetchDatabaseValues('#__miniorangesamlidp', 'loadAssoc','*');
        $sp_name = isset($attribute['sp_name']) ? $attribute['sp_name'] : '';
        ?>
        <div class="mo_boot_row mo_boot_px-4 mo_boot_py-4 " style="border: 2px solid rgb(15, 127, 182); background-color:white">
            <details style="width: 100%;">
                <summary class="mo_idp_summary">IDP INITIATED LOGIN <sup><a href="index.php?option=com_joomlaidp&view=accountsetup&tab-panel=license" style="text-decoration: none;"> [Premium]</a></sup></summary><hr>

                <div class="mo_boot_col-sm-12">
                    <h4>Add a link to user dashboard for login to your SP</h4>
                </div>
                <div class="mo_boot_col-sm-12  mo_boot_mt-3">
                    <?php
                    echo JText::_('COM_JOOMLAIDP_ACCOUNTSETUP_INSTRUCTIONS1');
                    ?>
                </div>
                <div class="mo_boot_col-sm-12  mo_boot_mt-3 mo_boot_table-responsive">
                    <?php
                    echo '<table class="mo_boot_table mo_boot_table-striped mo_boot_table-hover mo_boot_table-bordered">
                            <thead>
                                <tr>
                                    <th>' . JText::_('COM_JOOMLAIDP_NUM') . '</th>
                                    <th>' . JText::_('COM_JOOMLAIDP_MULTISAMLIDPS_IDENTIFIER') . '</th>
                                    <th>' . JText::_('COM_JOOMLAIDP_ACCOUNTSETUP_IDPINITIATED_LOGIN_URL') . '</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>' . $sp_name . '</td>
                                    <td>This feature is available in the <a href="index.php?option=com_joomlaidp&view=accountsetup&tab-panel=license"><b>Premium</b></a> version of the plugin.</td>
                                </tr>
                            </tbody>
                        </table>';
                    ?>
                </div>
                <div class="mo_boot_col-sm-12  mo_boot_mt-3">
                    <?php echo JText::_('COM_JOOMLAIDP_ACCOUNTSETUP_INSTRUCTIONS3'); ?>
                </div>
            </details>

            <?php
                $base_url = JUri::root();
                $current_admin_login_url = $base_url . 'administrator';
                $custom_admin_loign_url = $current_admin_login_url . '/?your_key';
            ?>

            <details style="width: 100%;">
                <summary class="mo_idp_summary">PROTECT/CUSTOMIZE ADMIN LOGIN PAGE URL <sup><a href="index.php?option=com_joomlaidp&view=accountsetup&tab-panel=license"> [Premium]</a></sup></summary><hr>
                <div class="mo_boot_col-sm-12  mo_boot_mt-4">
                    <p>This feature protects your admin login page from attacks which tries to access / login to a admin site.</p>
                </div>
                <div class="mo_boot_col-sm-12">
                    <div class="mo_boot_row  mo_boot_mt-3">
                        <div class="mo_boot_col-sm-4">
                            <p>Enable Custom Login Page URL (After enabling this you won't be able to login using <code>/administrator</code></p>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input type="checkbox" disabled/>
                        </div>
                    </div>
                    <div class="mo_boot_row  mo_boot_mt-3">
                        <div class="mo_boot_col-sm-4">
                            <p>Access Key for your Admin login URL :</p>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input class="mo_boot_form-control" type="text" placeholder="Enter Key" disabled="disable"/>
                        </div>
                    </div>
                    <div class="mo_boot_row  mo_boot_mt-3">
                        <div class="mo_boot_col-sm-4">
                            <p>Current Admin Login URL:</p>
                        </div>
                        <div class="mo_boot_col-sm-8 mo_boot_text-wrap">
                            <div disabled="disable"><?php echo $current_admin_login_url; ?></div>
                        </div>
                    </div>
                    <div class="mo_boot_row  mo_boot_mt-3">
                        <div class="mo_boot_col-sm-4">
                            <p>Custom Admin Login URL:</p>
                        </div>
                        <div class="mo_boot_col-sm-8 mo_boot_text-wrap">
                            <div id="custom_admin_url" disabled="disable"><?php echo $custom_admin_loign_url ?></div>
                        </div>
                    </div>
                    <div class="mo_boot_row  mo_boot_mt-3">
                        <div class="mo_boot_col-sm-4">
                            <p>Redirect after Failure Response :</p>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <select class="mo_boot_form-control" id="failure_response" disabled>
                                <option>Homepage</option>
                                <option>Custom 404 Message</option>
                                <option>Custom Redirect URL</option>
                            </select>
                        </div>
                    </div>
                    <div class="mo_boot_row  mo_boot_mt-3">
                        <div class="mo_boot_col-sm-4">
                            <p>Custom redirect URL after failure</p>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input class="mo_boot_form-control" disabled type="text"/>
                        </div>
                    </div>
                    <div class="mo_boot_row  mo_boot_mt-3" id="custom_message">
                        <div class="mo_boot_col-sm-4">
                            <p>Custom error message after failure</p>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <textarea style=" border: 1px solid #868383!important;" class="mo_boot_form-control" disabled></textarea>
                        </div>
                    </div>
                </div>
                <div class="mo_boot_col-sm-12  mo_boot_mt-4  mo_boot_text-center">
                    <input type="submit" class="mo_boot_btn mo_boot_btn-saml" value="Save" disabled/>
                </div>
            </details>


            <details style="width: 100%;">
                <summary class="mo_idp_summary">GENERATE CUSTOM CERTIFICATE <sup><a href="index.php?option=com_joomlaidp&view=accountsetup&tab-panel=license" style="text-decoration: none;"> [Premium]</a></sup></summary><hr>
                <div class="mo_boot_col-sm-12 mo_boot_mt-2">
                    <div id="mo_gen_tab"></div>
                    <input type="hidden" />
                </div>
                <div class="mo_boot_col-sm-12"  id="generate_certificate_form" style="background-color:#FFFFFF; border:0px solid #CCCCCC; display:none;">
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-3">
                            <b>Country code :<span style="color:red;">*</span></b>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input class="mo_saml_table_textbox  mo_boot_form-control" type="text"  placeholder="Enter your country code" disabled>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-3">
                            <b>State<span style="color:red;">*</span></b>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input class="mo_saml_table_textbox mo_boot_form-control" type="text"  placeholder="Enter State Name" disabled />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-3">
                            <b>Company<span style="color:red;">*</span></b>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input  class="mo_saml_table_textbox mo_boot_form-control" type="text"  placeholder="Enter your Company Name" disabled />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-3">
                            <b>Unit<span style="color:red;">*</span></b>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input  class="mo_saml_table_textbox mo_boot_form-control" type="text" placeholder="Unit Name(eg. section) : Information Technology" disabled />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-3">
                            <b>Common<span style="color:red;">*</span></b>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <input  class="mo_saml_table_textbox mo_boot_form-control type="text" placeholder="Common Name(eg. your name or your servers hostname)" disabled />
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-3">
                            <b>Digest Algorithm<span style="color:red;">*</span></b>
                        </div>
                        <div class="mo_boot_col-sm-8">
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
                            <b>Bits to generate the private key:<span style="color:red;">*</span></b>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <select class="mo_saml_table_textbox mo_boot_form-control">
                                <option>2048 bits</option>
                                <option>1024 bits</option>
                            </select>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-2">
                        <div class="mo_boot_col-sm-3">
                            <b>Valid days:<span style="color:red;">*</span></b>
                        </div>
                        <div class="mo_boot_col-sm-8">
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
                    </div><br>
                    <div class="mo_boot_row mo_boot_text-center mo_boot_mt-2">
                        <div class="mo_boot_col-sm-12">
                            <input type="submit" class="mo_boot_btn mo_boot_btn-danger" value="Back" onclick = "hide_gen_cert_form()"/>
                            <input type="submit" value="Generate Self-Signed Certs" disabled class="mo_boot_btn mo_boot_btn-success"; />
                        </div>
                    </div>
                </div>
                <div class="mo_boot_col-sm-12" id="mo_gen_cert">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-12">
                            <input type="hidden" value="miniorange_saml_save_custom_certificate" />
                            <a class="show_extra_information" href="#mo_saml_gen_cert_info" aria-expanded="false">Click here to know how custom certificate is useful?</a>
                            <div id="mo_saml_gen_cert_info" style="display:none">
                                <ol class = "att">
                                    <li><b>Encryption:</b> protect data transmissions (e.g. Service provider to Identity Provider or vice versa)</li>
                                    <li><b>Authentication:</b> ensure the server you’re connected to is actually the correct server.</li>
                                    <li><b>Data integrity:</b> ensure that the data that is requested or submitted is what is actually delivered.</li>
                                </ol>
                            </div>
                        </div>
                    </div><br>
                    <div class="mo_boot_row mo_boot_mt-3">
                        <div class="mo_boot_col-sm-4">
                            <b>X.509 Public Certificate</b>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <textarea disabled rows="5" cols="70" class="mo_saml_table_textbox" placeholder="Copy and Paste the content from your public certificate." style="width:100%; border: 1px solid #868383!important;border-radius:4px;"></textarea>
                        </div>
                    </div>
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-4">
                            <b>X.509 Private Certificate</b>
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <textarea disabled rows="5" cols="70" class="mo_saml_table_textbox" placeholder="Copy and Paste the content from your private certificate." style="width: 100%; border: 1px solid #868383!important;border-radius:4px;" ></textarea>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-3">
                        <div class="mo_boot_col-sm-4">
                        </div>
                        <div class="mo_boot_col-sm-8">
                            <i><b>NOTE:</b> Format of the certificate:<br/><b>-BEGIN CERTIFICATE--<br/><span style="word-wrap:break-word;">XXXXXXXXXXXXXXX</span><br/>--END CERTIFICATE--</b></i>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_my-3 mo_boot_text-center">
                        <div class="mo_boot_col-sm-12" id="save_config_element">
                            <input type="submit" name="submit" value="Upload" class="mo_boot_btn mo_boot_btn-success" disabled/>&nbsp;&nbsp;
                            <input type="button" name="submit" value="Generate" class=" mo_boot_btn mo_boot_btn-saml" id="mosaml_upload" onclick="show_gen_cert_form()"/>&nbsp;&nbsp;
                            <input type="submit" name="submit" value="Remove" class=" mo_boot_btn  mo_boot_btn-danger" disabled/>
                        </div>
                    </div>
                </div>

            </details>
        </div>
    <?php
    }

    public static function custom_certificate()
    {
        ?>
            <div class="mo_boot_row  mo_boot_p-4">

            </div>
        <?php
    }

    public static function requestfordemo()
    {
        $current_user = JFactory::getUser();
        $customerResult = IDP_Utilities::fetchDatabaseValues('#__miniorange_saml_idp_customer', 'loadAssoc', array('*'));
        $admin_email = isset($customerResult['email']) ? $customerResult['email'] : '';
        if ($admin_email == '') $admin_email = $current_user->email;
        ?>
        <div class="mo_boot_p-3" style="border: 2px solid rgb(15, 127, 182); background-color:white">
      
            <div class="mo_boot_col-sm-12 mo_boot_text-center">
                <h3>Request for Trial / Demo</h3>
                <hr>
            </div>
            <div class="mo_boot_col-sm-12" style="background-color: #e2e6ea;border-radius:5px">
                <p>
                    If you want to try the license version of the plugin then you can request for demo or trial for plugin.<br>
                    &nbsp;1) We can setup a demo Joomla site for you on our cloud and provide you with its credentials.<br> 
                    &nbsp;2) We can give you requested plugin for 7 days trial. <br>
                    You can configure it with your Servcie Provider, test the SSO and play around with the premium features.
                </p>
                <strong>Note:</strong> Please describe your use-case in the <strong>Description</strong> below.
            </div>
            <div class="mo_boot_mt-4">
                <form  name="demo_request" method="post" action="<?php echo JRoute::_('index.php?option=com_joomlaidp&view=accountsetup&task=accountsetup.requestForDemoPlan');?>">
                    <div class="mo_saml_settings_table">
                        <div class="mo_boot_col-sm-12 mo_boot_mt-4">
                            <div class="mo_boot_row">
                                <div class="mo_boot_col-lg-4">
                                        <p><strong>Email:<span style="color: red;">*</span> </strong></p>
                                </div>
                                <div class="mo_boot_col-lg-8">
                                    <input style="border: 1px solid #868383 !important;" type="email" class="mo_boot_form-control" name="email" value="<?php echo $admin_email; ?>" placeholder="person@example.com" required />
                                </div>
                            </div>
                        </div>
                        <div class="mo_boot_col-sm-12  mo_boot_mt-2">
                            <div class="mo_boot_row">
                                <div class="mo_boot_col-lg-4">
                                    <p> <strong>Request for:<span style="color: red;">*</span></strong></p>
                                </div>
                                <div class="mo_boot_col-lg-4">
                                    <label><input type="radio" name="demo"  value="Trial of 7 days" CHECKED> Trial of 7 days</label>
                                </div>
                                <div class="mo_boot_col-lg-4">
                                    <label><input type="radio" name="demo"  value="Demo" > Demo</label>
                                </div>
                            </div>
                        </div>
                        <div class="mo_boot_col-sm-12  mo_boot_mt-2">
                            <div class="mo_boot_row">
                                <div class="mo_boot_col-lg-4">
                                        <p><strong>Requested Plugin:<span style="color: red;">*</span></strong></p>
                                </div>
                                <div class="mo_boot_col-lg-8">
                                    <select required class="mo_boot_form-control" style="border: 1px solid #868383 !important;" name="plan">
                                        <option disabled selected style="text-align: center">----------------------- Select -----------------------</option>
                                        <option value="Joomla SAML IDP Premium Plugin">Joomla SAML IDP Premium Plugin</option>
                                        <option value="Not Sure">Not Sure</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                        </div>
                        <div class="mo_boot_col-sm-12 mo_boot_mt-2">
                            <div class="mo_boot_row">
                                <div class="mo_boot_col-lg-4">
                                        <p><strong>Description:<span style="color: red;">*</span> </strong></p>
                                </div>
                                <div class="mo_boot_col-lg-8">
                                    <textarea  name="description" class="mo_boot_form-text-control" style="border-radius:4px;resize: vertical;width:100%; border: 1px solid #868383 !important;" cols="52" rows="7" onkeyup="mo_saml_valid(this)"
                                    onblur="mo_saml_valid(this)" onkeypress="mo_saml_valid(this)" required placeholder="Need assistance? Write us about your requirement and we will suggest the relevant plan for you."></textarea>
                                </div>
                            </div>  
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_text-center">
                        <div class="mo_boot_col-sm-12">
                            <input type="hidden" name="option1" value="mo_saml_login_send_query"/><br>
                            <input  type="submit" name="submit" value="Submit" class="mo_boot_btn mo_boot_btn-saml"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }
    public static function showAdvanceMapping()
    {
        ?>
        <div class="mo_boot_row mo_boot_px-4 mo_boot_py-2 " style="border: 2px solid rgb(15, 127, 182); background-color:white">
            <div class="mo_boot_col-sm-12"><br>
                <h3>CUSTOM ATTRIBUTE MAPPING:<sup><a href="index.php?option=com_joomlaidp&view=accountsetup&tab-panel=license" style="text-decoration: none;"> [Premium]</a></sup></h3><hr>
            </div>
            <div class="mo_saml_highlight_background_note">
                <p><b>Joomla's User Attribute Name: </b>It is the name which you want to send to your SP. It should be unique.</p>
                <p><b>Joomla's User Attribute Value: </b>It is the user attribute whose value you want to send to SP.</p>
            </div>
            <div class="mo_boot_col-sm-12 mo_boot_mt-5">
                <div class="mo_boot_row">
                    <?php
                        for($icnt = 1; $icnt <= 5; $icnt++)
                        {
                            ?>
                            <div class="mo_boot_col-sm-6">
                                    <div class="mo_boot_row">
                                        <div class="mo_boot_col-sm-4">
                                            <b>Attribute <?php echo $icnt ?> Name:</b>
                                        </div>
                                        <div class="mo_boot_col-sm-8">
                                            <input type="text" class="mo_saml_idp_textfield mo_boot_form-control" disabled="disabled" placeholder="Enter Attribute Name."/>
                                        </div>
                                    </div>
                            </div>
                            <div class="mo_boot_col-sm-6">
                                <div class="mo_boot_row">
                                    <div class="mo_boot_col-sm-4">
                                        <b>Attribute <?php echo $icnt;?> Value:</b>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <select class="mo_saml_idp_textfield mo_boot_form-control" disabled="disabled">
                                            <option value="">Select Attribute Value</option>
                                            <option value="emailAddress">Email Address</option>
                                            <option value="username">Username</option>
                                            <option value="name">Name</option>
                                            <option value="firstname">First Name</option>
                                            <option value="lastname">Last Name</option>
                                            <option value="groups">Groups</option>
                                        </select>
                                    </div>
                                </div><br>
                            </div>
                            <?php
                        }
                    ?>
                </div>
            </div>
            <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-1">
                        <input type="checkbox" name="send_groups_in_comma_seperated" value="1" disabled>
                    </div>
                    <div class="mo_boot_col-sm-11">
                        <strong>Enable if you want to send groups in the comma seperated line.</strong>
                    </div>
                </div>
            </div>
            <div class="mo_boot_col-sm-12 mo_boot_mt-5">
                <h3 class="mo_boot_d-inline">ADDITIOANL USER ATTRIBUTES:<sup><a href="index.php?option=com_joomlaidp&view=accountsetup&tab-panel=license" style="text-decoration: none;"> [Premium]</a></h3></sup>
                <span class="mo_boot_d-inline-block">
                    <input type='button' value='+' class='mo_boot_btn mo_boot_btn-success' disabled="disabled" />
                    <input type='button' value='-' class='mo_boot_btn mo_boot_btn-danger' disabled="disabled"/>
                </span>
            </div>
            <div class="mo_boot_col-sm-6 mo_boot_mt-3">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-3">
                        <b>Attribute Name:</b>
                    </div>
                    <div class="mo_boot_col-sm-9">
                        <input type="text" class="mo_saml_idp_textfield mo_boot_form-control" disabled="disabled" placeholder="Enter Attribute Name."/>
                    </div>
                </div>
            </div>
            <div class="mo_boot_col-sm-6 mo_boot_mt-3">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-3">
                        <b>Attribute Value:</b>
                    </div>
                    <div class="mo_boot_col-sm-9">
                        <select class="mo_saml_idp_textfield mo_boot_form-control" disabled="disabled">
                            <option value="">Select Attribute</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mo_boot_col-sm-12 mo_boot_mt-5 mo_boot_mb-3 mo_boot_text-center">
                <input type="submit" class="mo_boot_btn mo_boot_btn-saml" value="Save Mapping" disabled/>
            </div><br><br>
        </div>
        <?php
    }

    public static function showIdentityProviderConfigurations()
    {
        $site_url = JURI::root();
        $idp_entity_id = $site_url . 'plugins/user/miniorangejoomlaidp/';

        $idpid = IDP_Utilities::fetchDatabaseValues('#__miniorange_saml_idp_customer', 'loadResult','idp_entity_id');

        if (!empty($idpid) && ($idp_entity_id != $idpid))
            $idp_entity_id = $idpid;
        ?>
       <div class="mo_boot_row mo_boot_px-4 mo_boot_py-4 " style="border: 2px solid rgb(15, 127, 182); background-color:white">
            <div class="mo_boot_col-12">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-10">
                        <h3>IDENTITY PROVIDER METADATA</h3>
                    </div>
                    <div class="mo_boot_col-sm-2">
                        <input type="button" id="idpconfig_end_tour" value="Start Tour" onclick="restart_touridp();" class="mo_boot_btn mo_boot_btn-saml" style="margin-top:10px;"/>
                    </div>
                </div>
                <hr>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-12">
                        <h4>
                            <?php
                                echo JText::_('COM_JOOMLAIDP_ACCOUNTSETUP_SP_HAEDING');
                                echo '<a href="index.php?option=com_joomlaidp&amp;view=accountsetup&amp;tab-panel=license"></a>'
                            ?>
                        </h4>
                    </div>
                    <div class="mo_boot_col-sm-12">
                        <form name="f" method="post" action="<?php echo JRoute::_('index.php?option=com_joomlaidp&view=accountsetup&task=accountsetup.updateIdpEntityId'); ?>">
                            <div class="mo_boot_row">
                                <div class="mo_boot_col-sm-3">
                                    <p><b>IdP EntityID / Issuer:</b></p>
                                </div>
                                <div class="mo_boot_col-sm-7">
                                    <input type="text" name="mo_saml_idp_entity_id" class="mo_saml_idp_textfield mo_boot_form-control" placeholder="Entity ID of your IdP" value="<?php echo $idp_entity_id ?>" required="">
                                </div>
                                <div class="mo_boot_col-sm-2">
                                    <input type="submit" name="submit"  value="Update" class="mo_boot_btn mo_boot_btn-success mo_boot_form-control">
                                </div>
                            </div>
                            <div class="mo_boot_row">
                                <div class="mo_boot_col-sm-9 mo_boot_offset-sm-3">
                                    <i>
                                        <span style="color:red"><b>Note:</b></span>
                                        If you have already shared the below URLs or Metadata <br>with your SP, do <b>NOT</b>
                                        change IdP EntityID. It might break your existing login flow.
                                    </i>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <hr>
                <div id="idp_metadata">
                    <div class="mo_boot_row mo_saml_idp_metadata">Provide this plugin information to your Service Provider team. You can choose any one of the below options:</div>
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-12">
                            <b>i) Provide this metadata URL to your Service Provider OR download the .xml file to upload it in your SP:</b><br><br>
                        </div>
                        <div class="mo_boot_col-sm-12">
                            <div class="mo_boot_row mo_boot_pl-4">
                                <div class="mo_boot_col-sm-11 mo_saml_highlight_background_url_note">
                                    <span id="mo_metadata_url">
                                        <?php
                                            echo '<code class="mo_boot_text-wrap" id="idp_metadata_url"><b><a  target="_blank" href="' . JURI::root() . 'plugins/system/joomlaidplogin/saml2idp/metadata/metadata.php' . '">' . JURI::root() . 'plugins/system/joomlaidplogin/saml2idp/metadata/metadata.php' . '</a></b></code>';
                                        ?>
                                    </span>
                                </div>
                                <div class="mo_boot_col-sm-1">
                                    <i class="fa fa-copy mo_copy mo_boot_p-2 mo_copytooltip" onclick="copyToClipboard('#mo_metadata_url');" style="color:red;margin-left: 2px;"><span class="mo_copytooltiptext copied_text">Copy</span></i>
                                </div>
                            </div>
                        </div>
                        <div class="mo_boot_col-sm-12"><br>
                            <h2 class="mo_boot_text-center">OR</h2><br>
                            <div class="mo_boot_row">
                                <div class="mo_boot_col-sm-7">
                                    <b>ii) Download the Plugin XML metadata and upload it on your Service Provider : </b>
                                </div>
                                <div class="mo_boot_col-sm-5">
                                    <span id="idp_download_metadata">
                                        <?php
                                            echo '<a href="' . JURI::root() . 'plugins/system/joomlaidplogin/saml2idp/metadata/metadata.php?download=true" class="btn btn-primary anchor_tag" style="padding: 4px 10px;">Download XML Metadata</a>';
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="mo_boot_col-sm-12 mo_boot_mt-4 mo_boot_table-responsive">
                            <h2 class="mo_boot_text-center">OR</h2><br>
                            <p><b>iii) Provide the following information to your Service Provider. Copy it and keep it handy.</b></p>
                            <table id="idp_saml_config" class="mo_boot_table mo_boot_table-striped mo_boot_mt-3 mo_boot_table-hover mo_boot_table-bordered">
                                <tr>
                                    <td><b><?php echo JText::_('COM_JOOMLAIDP_ACCOUNTSETUP_ISSUER'); ?></b></td>
                                    <td>
                                        <span id="issuer"><?php echo $idp_entity_id ?></span>
                                        <i class="fa fa-copy mo_copy mo_boot_p-2 mo_copytooltip" ; onclick="copyToClipboard('#issuer');" style="color:red;float:right;"><span class="mo_copytooltiptext copied_text">Copy</span></i>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b><?php echo JText::_('COM_JOOMLAIDP_ACCOUNTSETUP_SAML_LOGIN'); ?></b></td>
                                    <td>
                                        <span id="login_url"><?php echo $site_url . 'index.php' ?></span>
                                        <i class="fa fa-copy mo_copy mo_boot_p-2 mo_copytooltip" ; onclick="copyToClipboard('#login_url');" style="color:red; float:right;"><span class="mo_copytooltiptext copied_text">Copy</span> </i>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b><?php echo JText::_('COM_JOOMLAIDP_ACCOUNTSETUP_CERTIFICATE'); ?></b></td>
                                    <td>
                                        <a href="<?php echo JURI::root() . 'plugins/system/joomlaidplogin/saml2idp/cert/idp-signing.crt'; ?>"><b>Click here</b></a> to download the certificate.
                                    </td>
                                </tr>
                                <tr>
                                    <td><b><?php echo JText::_('COM_JOOMLAIDP_ACCOUNTSETUP_SAML_LOGOUT'); ?></b></td>
                                    <td><?php echo '<a href="index.php?option=com_joomlaidp&amp;view=accountsetup&amp;tab-panel=license"><b>Premium feature</b></a>' ?></td>
                                </tr>
                                <tr>
                                    <td><b><?php echo JText::_('COM_JOOMLAIDP_ACCOUNTSETUP_RESPONSE_SIGNED'); ?></b></td>
                                    <td><?php echo JText::_('COM_JOOMLAIDP_ACCOUNTSETUP_RESPONSE_SIGNED_MESSAGE'); ?></td>
                                </tr>
                                   <tr>
                                       <td><b><?php echo JText::_('COM_JOOMLAIDP_ACCOUNTSETUP_ASSERTION_SIGNED'); ?></b></td>
                                    <td><?php echo JText::_('COM_JOOMLAIDP_ACCOUNTSETUP_ASSERTION_SIGNED_MESSAGE'); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .selected-text, .selected-text > * {
                background: #2196f3;
                color: #ffffff;
            }
        </style>
        <?php
    }


    public function showLicensingPlanDetails()
    {
        $isRegistered = MoSamlIdpUtility::is_customer_registered();

        if(!$isRegistered){
            $upgradeURL = "#";
            $newTab = "";
        }
        else{
            $result       = IDP_Utilities::fetchDatabaseValues('#__miniorange_saml_idp_customer', 'loadAssoc','*');
            $userEmail  = isset($result['email']) ? $result['email'] : '';
            $upgradeURL = "https://login.xecurify.com/moas/login?username=".$userEmail."&redirectUrl=https://login.xecurify.com/moas/initializepayment&requestOrigin=joomla_saml_idp_premium_plan";
            $upgradeURL  =   "https://www.miniorange.com/contact";
            $newTab = '_blank';
        }
        ?>
        <div id="myModal" class="TC_modal">
            <div class="TC_modal-content" style="width: 40%!important;">
                <span class="TC_modal_close" onclick="hidemodal()" >&times;</span><br><br>
                <div class=" mo_boot_text-center">
                    <p>
                        You Need to Login / Register in <strong>My Account</strong> tab to Upgrade your License 
                    </p><br><br>
                    <a href="<?php echo JURI::base()?>index.php?option=com_joomlaidp&tab-panel=account" class="mo_boot_btn mo_boot_btn-primary">LOGIN / REGISTER</a>
                </div>
            </div>
        </div>
        <div class="mo_idp_divided_layout mo-idp-full" >
            <div class="mo_boot_pricing_container-fluid">
                <div class="mo_boot_row mo_boot_mt-5">
                    <div class="mo_boot_col-sm-1"></div>
                    <div class="mo_boot_col-sm-5">
                        <div class="mo_pricingTable">
                            <div class="pricingTable-header" style="height:300px" >
                                <h3 class="heading" style="text-align:center">FREE</h3>
                                <div  class="slabs mo_boot_mb-4" style="height:25px">
                                    <select name="user-slab" style="display:none">
                                            
                                    </select>
                                </div>
                                <div class="mo_boot_mt-5" style="text-align:center">
                                    <span class="price-value" >
                                        <span style="font-size:30px;">$</span>0
                                    </span>
                                </div>
                                <div style="text-align:center;">
                                    <a href="#" class="upgrade_button" style="background:white;color:black!important">ACTIVE PLAN</a>
                                </div>
                            </div>  
                            <div class="pricing-content" >
                                <ul>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Keep Users in Joomla Database</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Password will be stored in your Joomla Database</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Single-Protocol SSO Support SAML</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Basic Attribute Mapping(User Name , Email)</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Default redirect Url after Login</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Signed Assertion</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Metadata XML File</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Configure IDP Using Metadata XML File</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Configure IDP Using Metadata URL</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Step-by-Step Guide to setup IDP</li>
                                    <li><i class="fa fa-times" style="color:black;" ></i> Signed response</li>
                                    <li><i class="fa fa-times" style="color:black;" ></i> Encrypted Assertion</li>
                                    <li><i class="fa fa-times" style="color:black;" ></i> Advance Attribute Mapping</li>
                                    <li><i class="fa fa-times" style="color:black;" ></i> IDP Initiated login</li>
                                    <li><i class="fa fa-times" style="color:black;" ></i> Protect/ customize admin login page</li>
                                    <li><i class="fa fa-times" style="color:black;" ></i> Use your own existing Joomla Sign-up Form </li>
                                    <li><i class="fa fa-times" style="color:black;"></i> Use your own Joomla Domain</li>
                                    <li><i class="fa fa-times" style="color:black;"></i> Supports Multiple SPs</li>
                                    <li><i class="fa fa-times" style="color:black;" ></i> Single Logout</li>
                                    <li><i class="fa fa-times" style="color:black;" ></i>  Options to select SLO binding  type</li>
                                    <li><i class="fa fa-times" style="color:black;" ></i> Role Restriction</li>
                                    <li><i class="fa fa-times" style="color:black;" ></i> Relay Restriction</li>
                                    <li><i class="fa fa-times" style="color:black;" ></i> Generate Custom certificate</li>
                                    <li><i class="fa fa-times" style="color:black;" ></i> Custom Response Validation Time</li>
                                    <li></li>
                                    <li></li>
                                    <li></li>
                                    <li></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="mo_boot_col-sm-5">
                        <div class="mo_pricingTable">
                            <div class="pricingTable-header" style="height:300px" >
                                <h3 class="heading" style="text-align:center">PREMIUM</h3>
                                <div  class="slabs mo_boot_mb-4" style="height:25px;text-align:center">
                                    <select name="user-slab" class="mo_idp_users_details slab_dropdown" >
                                            <option value="100" selected>No of Users: 100</option>
                                            <option value="200">No of Users: 200</option>
                                            <option value="300">No of Users: 300</option>
                                            <option value="400">No of Users: 400</option>
                                            <option value="500">No of Users: 500</option>
                                            <option value="750">No of Users: 750</option>
                                            <option value="1000">No of Users: 1000</option>
                                            <option value="2000">No of Users: 2000</option>
                                            <option value="3000">No of Users: 3000</option>
                                            <option value="4000">No of Users: 4000</option>
                                            <option value="5000">No of Users: 5000</option>
                                            <option value="5000p">No of Users: 5000+</option>
                                    </select> 
                                </div>
                                <div class="mo_boot_col-12">
                                    <div class="mo_idp_plans">
                                        <div class="mo_boot_row mo_boot_text-center">
                                            <div class="mo_boot_col-4" style="color:orange;font-size:17px">
                                                Monthly
                                            </div>
                                            <div class="mo_boot_col-4" style="color:orange;font-size:17px">
                                                Quaterly
                                            </div>
                                            <div class="mo_boot_col-4" style="color:orange;font-size:17px">
                                                Yearly
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mo_boot_mt-2 mo_idp_price_slab_100" id="mo_idp_price_slab1_100">
                                        <div class="mo_boot_row mo_boot_text-center">
                                            <div class="mo_boot_col-4">
                                                <span class="price-value" >
                                                    <span style="font-size:30px;">$</span>19
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>52
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>199
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class=" mo_boot_mt-2 mo_idp_price_slab_200" style="display:none" id="mo_idp_price_slab1_200">
                                        <div class="mo_boot_row mo_boot_text-center">
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>27
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>77
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>299
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mo_boot_mt-2 mo_idp_price_slab_300" style="display:none" id="mo_idp_price_slab1_300">
                                        <div class="mo_boot_row mo_boot_text-center">
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>35
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>102
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>399
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mo_boot_mt-2 mo_idp_price_slab_400" style="display:none" id="mo_idp_price_slab1_400">
                                        <div class="mo_boot_row mo_boot_text-center">
                                            <div class="mo_boot_col-4">
                                                <span class="price-value" >
                                                    <span style="font-size:30px;">$</span>43
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>125
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>449
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mo_boot_mt-2 mo_idp_price_slab_500" style="display:none" id="mo_idp_price_slab1_500">
                                        <div class="mo_boot_row mo_boot_text-center">
                                            <div class="mo_boot_col-4">
                                                <span class="price-value mo_idp_price_slab_100">
                                                    <span style="font-size:30px;">$</span>51
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>145
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>499
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mo_boot_mt-2 mo_idp_price_slab_750"  style="display:none" id="mo_idp_price_slab1_750" >
                                        <div class="mo_boot_row mo_boot_text-center">
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>67
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>179
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>674
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mo_boot_mt-2 mo_idp_price_slab_1000"  style="display:none" id="mo_idp_price_slab1_1000">
                                        <div class="mo_boot_row mo_boot_text-center">
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>83
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>219
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>849
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mo_boot_mt-2 mo_idp_price_slab_2000" style="display:none" id="mo_idp_price_slab1_2000">
                                        <div class="mo_boot_row mo_boot_text-center">
                                            <div class="mo_boot_col-4">
                                                <span class="price-value" >
                                                    <span style="font-size:30px;">$</span>107
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value" >
                                                    <span style="font-size:30px;">$</span>299
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>1149
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mo_boot_mt-2 mo_idp_price_slab_3000" style="display:none" id="mo_idp_price_slab1_3000">
                                        <div class="mo_boot_row mo_boot_text-center">
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>130
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>368
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>1399
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mo_boot_mt-2 mo_idp_price_slab_4000" style="display:none" id="mo_idp_price_slab1_4000">
                                        <div class="mo_boot_row mo_boot_text-center">
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>151
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>424 
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>1599
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mo_boot_mt-2 mo_idp_price_slab_5000" style="display:none" id="mo_idp_price_slab1_5000">
                                        <div class="mo_boot_row mo_boot_text-center">
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>171
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>474
                                                </span>
                                            </div>
                                            <div class="mo_boot_col-4">
                                                <span class="price-value">
                                                    <span style="font-size:30px;">$</span>1749
                                                </span>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            <div class=" mo_boot_mt-5 mo_idp_price_slab_5000p mo_boot_text-center" id="mo_idp_price_slab1_5000p" style="display:none">
                                <span class="price-value">
                                    <a target="_blank" href="https://www.miniorange.com/contact" style="color:white">Contact Us</a>
                                </span>
                            </div>
                           
                            <div style="text-align:center">
                                <?php 
                                      if(!$isRegistered){ ?>
                                    <a class="upgrade_button" target="<?php echo $newTab;?>" href="<?php echo $upgradeURL ?>" onclick="showmodal()">
                                        UPGRADE NOW
                                    </a>
                                   <?php }else{?>
                                    <a class="upgrade_button" target="<?php echo $newTab;?>" href="<?php echo $upgradeURL ?>">
                                        UPGRADE NOW
                                    </a>
                                    <?php }?>
                            </div>
                            </div>
                            <div class="pricing-content" >
                                <ul>
                                    
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Keep Users in Joomla Database</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Password will be stored in your Joomla Database</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Single-Protocol SSO Support SAML</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Basic Attribute Mapping(User Name , Email)</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Default redirect Url after Login</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Signed Assertion</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Metadata XML File</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Configure IDP Using Metadata XML File</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Configure IDP Using Metadata URL</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Step-by-Step Guide to setup IDP</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Signed response</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Encrypted Assertion</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Advance Attribute Mapping</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> IDP Initiated login</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Protect/ customize admin login page</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Use your own existing Joomla Sign-up Form</li>
                                    <li><i class="fa fa-check" style="color:#007bff;"></i> Use your own Joomla Domain</li>
                                    <li><i class="fa fa-check" style="color:#007bff;"></i> Supports Multiple SPs</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Single Logout</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i>  Options to select SLO binding  type</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Role Restriction</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Relay Restriction</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Generate Custom certificate</li>
                                    <li><i class="fa fa-check" style="color:#007bff;" ></i> Custom Response Validation Time</li>
                                    <li class="advertise">Buy Premium Plan and Get Joomla 2FA license free for first three month.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="mo_boot_col-sm-1"></div>
                    
                </div>
            </div>
        </div>
        <?php echo self::mo_sliding_support(); ?>
        <br>
        <h4>Multiple SPs Supported</h4><p>There is an additional cost for the SPs if the number of SP is more than 1.</p>
        <h4>Steps to Upgrade to Premium Plugin -</h4>
        <p>1. To upgrade for Premium version of the plugins you will need to create a miniOrange account under <a href='#' onclick="moSAMLAccount();">Account Setup </a>tab. After successfully creating an account click on <b>UPGRADE NOW</b> button.</p>
        <p>2. You will be redirected to miniOrange Login Console. Enter your password with which you created an account with us. After that you will be redirected to payment page.</p>
        <p>3. Enter your card details and complete the payment. On successful payment completion, you will see the link to download the license version plugin.</p>
        <p>4. Download the license version plugin. In your Joomla site, search <i>miniorange</i> in the plugins extension manager.
        <p>5. Uninstall all the plugins, component, library, package etc. related to miniOrange IDP plugin.
        <p>6. Now install the license version of the plugin.<br><br>
        <p>Free version is recommended for setting up Proof of Concept (PoC). Try it to test the SSO connection with your SAML 2.0 compliant SP</p>

        <h3>Return Policy -</h3>
        <p>If the licensed plugin you purchased is not working as advertised and you’ve attempted to resolve any feature issues with our support team, which couldn't get resolved,
            we will refund the whole amount. If the request for issue resolution is made within 10 days of the purchase, the issue will be resolved within the period stated by the team.

        <br><br><strong>Note that this policy does not cover the following cases:</strong>
        <li>1. Change in mind or change in requirements after purchase.</li>

        <li>2. Infrastructure issues do not allow the functionality to work.</li>
        </p><br>

        If you have any doubts regarding the licensing plans, you can mail us at <b><a href='mailto:joomlasupport@xecurify.com'><i style="word-wrap:break-word;">joomlasupport@xecurify.com</i></a></b></p>

        <style>
            .cd-black :hover #singlesite_tab.is-visible {
                margin-right: 4px;
                transition: 0.4s;
                -moz-transition: 0.4s;
                -webkit-transition: 0.4s;
                border-radius: 8px;
                transform: scale(1.03);
                -ms-transform: scale(1.03); /* IE 9 */
                -webkit-transform: scale(1.03); /* Safari */
                box-shadow: 0 0 4px 1px rgba(255, 165, 0, 0.8);
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
        <?php
    }


    function mo_sliding_support()
    {
        $current_user = JFactory::getUser();
        $result = IDP_Utilities::fetchDatabaseValues('#__miniorange_saml_idp_customer', 'loadAssoc', '*');

        $admin_email = $result['email'];
        $admin_phone = $result['admin_phone'];

        if ($admin_email == '')
            $admin_email = $current_user->email;

        ?>
        <div id="mo_saml_idp_request_quote_form" class="mo_saml_idp_request_quote_form">
            <input style="font-size: 15px;cursor: pointer;text-align: center;width: 150px;height: 35px;
                background: rgba(43, 141, 65, 0.93);color: #ffffff;border-radius: 3px;transform: rotate(90deg);text-shadow: none;
                position: relative;margin-left: -92px;top: 45px;float:left"  type="submit" id="mo_saml_idp_request_quote" class="mo_saml_idp_request_quote" name="op" value="Support">

            <form name="f" method="post" action="<?php echo JRoute::_('index.php?option=com_joomlaidp&view=accountsetup&task=accountsetup.contactUs'); ?>">
                <div class="mo_boot_row mo_boot_d-block mo_boot_p-1">
                    <div class="mo_boot_col-sm-12">
                        <h3>Request a Quote</h3>
                        <hr>
                    </div>
                    <div class="mo_boot_col-sm-12">
                        <p>Need any help? Just send us a query and we will get back to you soon.</p>
                    </div>
                    <div class="mo_boot_col-sm-12" >
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-12">
                                <input type="email" class="mo_boot_form-control" id="query_email" name="mo_saml_query_email" value="<?php echo $admin_email; ?>" placeholder="Enter your email" required/>
                            </div><br><br>
                            <div class="mo_boot_col-sm-12">
                                <input type="text" style="width:100%" pattern="[\+][0-9]{7,15}" class="mo_boot_form-control" name="mo_saml_query_phone" id="query_phone" value="<?php echo $admin_phone; ?>" placeholder="Enter your phone with country code"/>
                            </div><br><br>
                            <div class="mo_boot_col-sm-12">
                                <select name="mo_saml_select_plan" style="border: 1px solid rgba(16, 16, 16, 0.6) !important;" class="mo_boot_form-control">
                                    <option value="none">Choose a plan:</option>
                                    <option value="lite_monthly">Cloud IDP Lite - Monthly Plan</option>
                                    <option value="lite_yearly">Cloud IDP Lite - Yearly Plan</option>
                                    <option value="joomla_saml_idp_premium_plan">Joomla Premium - Yearly Plan</option>
                                    <option value="all_inclusive">All Inclusive Plan</option>
                                </select>
                            </div><br><br>
                            <div class="mo_boot_col-sm-12">
                                <input type="text" style="width:100%" class="mo_boot_form-control" name="number_of_users" required placeholder="Enter Number of users:" />
                            </div><br><br>
                            <div class="mo_boot_col-sm-12">
                                <textarea id="query" name="mo_saml_query" style="width:100%; border-radius:4px;resize: vertical; border: 1px solid #868383!important;" cols="52" rows="5" placeholder="Any Special Requirements: " required></textarea>
                            </div>
                            <div class="mo_boot_col-sm-12 mo_boot_text-center">
                                <input type="submit" name="send_query" value="Submit Query"  class="mo_boot_btn mo_boot_btn-success"/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div hidden id="mosaml-feedback-overlay"></div>

        <br/>

        <?php
    }


    public static function showServiceProviderConfigurations()
    {
        $session = JFactory::getSession();
        $current_state=$session->get('show_test_config');
        $attribute = IDP_Utilities::fetchDatabaseValues('#__miniorangesamlidp', 'loadAssoc', '*');
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
        $sp_name = "";
        $sp_entityid = "";
        $acs_url = "";
        $nameid_format = "";
        $nameid_attribute = "";
        $assertion_signed = "";
        $relay_state = "";

        if (isset($attribute['sp_entityid'])) {
            $sp_entityid = $attribute['sp_entityid'];
            $acs_url = $attribute['acs_url'];
            $nameid_format = $attribute['nameid_format'];
            $sp_name = $attribute['sp_name'];
            $nameid_attribute = $attribute['nameid_attribute'];
            $assertion_signed = $attribute['assertion_signed'];
            $relay_state = $attribute['default_relay_state'];
        }

        $isSystemEnabled = JPluginHelper::isEnabled('system', 'joomlaidplogin');
        $isUserEnabled = JPluginHelper::isEnabled('user', 'miniorangejoomlaidp');
        if (!$isSystemEnabled || !$isUserEnabled) {
            ?>
            <div id="system-message-container">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <div class="alert alert-error">
                    <h4 class="alert-heading"><?php echo JText::_('COM_JOOMLAIDP_MULTISAMLIDPS_WARNING'); ?></h4>
                    <div class="alert-message">
                        <h4><?php echo JText::_('COM_JOOMLAIDP_MULTISAMLIDPS_WARNING_HEADER'); ?></h4>
                        <ul>
                            <li><?php echo JText::_('COM_JOOMLAIDP_MULTISAMLIDPS_WARNING_SYSTEM'); ?></li>
                            <li><?php echo JText::_('COM_JOOMLAIDP_MULTISAMLIDPS_WARNING_USER'); ?></li>
                        </ul>
                        <h4><?php echo JText::_('COM_JOOMLAIDP_MULTISAMLIDPS_WARNING_STEPS'); ?></h4>
                        <ul>
                            <li><?php echo JText::_('COM_JOOMLAIDP_MULTISAMLIDPS_WARNING_STEP1'); ?></li>
                            <li><?php echo JText::_('COM_JOOMLAIDP_MULTISAMLIDPS_WARNING_STEP2'); ?></li>
                            <li><?php echo JText::_('COM_JOOMLAIDP_MULTISAMLIDPS_WARNING_STEP3'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    
        <div class="mo_boot_row mo_boot_px-4 mo_boot_py-4" style="border: 2px solid rgb(15, 127, 182); background-color:white">
            <div class="mo_boot_col-sm-12 mo_boot_m-2" id="upload_metadata_form" style=" display:none ;">
                <div class="mo_boot_row mo_boot_mt-3">
                    <div class="mo_boot_col-sm-9">
                        <h3>Upload SP Metadata</h3>
                    </div>
                    <div class="mo_boot_col-sm-3">
                        <input type="button" class="mo_boot_btn mo_boot_btn-danger mo_boot_float-right" value="Cancel" onclick="hide_metadata_form()"/>
                    </div>
                </div><hr>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <form action="<?php echo JRoute::_('index.php?option=com_joomlaidp&view=accountsetup&task=accountsetup.handleUploadMetadata'); ?>" name="metadataForm" method="post" id="IDP_metadata_form" enctype="multipart/form-data">
                            <input id="mo_saml_upload_metadata_form_action" type="hidden" name="option1" value="uploadMetadata"/>
                            <div class="mo_boot_row mo_boot_mt-3">
                                <div class="mo_boot_col-lg-3">
                                    <b>Service Provider Name<span style="color: red;">*</span></b>
                                </div>
                                <div class="mo_boot_col-lg-6">
                                    <input type="text" class="mo_saml_idp_textfield mo_boot_form-control" id="sp_upload_name" name="sp_upload_name" placeholder="Ex. ADFS, Azure, anything" required>
                                </div>
                            </div><br>
                            <div class="mo_boot_row mo_boot_mt-3">
                                <div class="mo_boot_col-lg-3">
                                    <b>Upload Metadata :</b>
                                </div>
                                <div class="mo_boot_col-lg-7">
                                    <input type="hidden" name="action" value="uploadMetadata"/>
                                    <input type="file" class="mo_boot_form-control-file"  id="metadata_uploaded_file" name="metadata_file"/>
                                </div>
                                <div class="mo_boot_col-lg-2">
                                    <input type="submit" class="mo_boot_btn mo_boot_btn-saml" name="option1" method="post" id="upload_metadata_file" style="float:right" value="Upload"/>
                                </div>
                            </div><br>
                            <div class="mo_boot_row mo_boot_mt-3">
                                <div class="mo_boot_col-sm-12 mo_boot_text-center">
                                    <p style="font-size:15pt;"><b>OR</b></p><br>
                                </div>
                                <div class="mo_boot_col-lg-3">
                                    <input type="hidden" name="action" value="fetch_metadata"/>
                                    <b>Enter metadata URL:</b>
                                </div>
                                <div class="mo_boot_col-lg-6">
                                    <input type="url" name="metadata_url"  id="metadata_url" class="mo_saml_idp_textfield mo_boot_form-control" placeholder="Enter metadata URL of your IdP." />
                                </div>
                                <div class="mo_boot_col-lg-3 mo_boot_text-center">
                                    <input type="submit" class="mo_boot_btn mo_boot_btn-saml mo_boot_float-sm-right" id="fetch_metadata"  name="option1" method="post" value="Fetch Metadata"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="mo_boot_col-sm-12" id="tabhead">
                <form action="<?php echo JRoute::_('index.php?option=com_joomlaidp&view=accountsetup&task=accountsetup.saveServiceProvider'); ?>" method="post" name="adminForm" id="adminForm">
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-lg-5 mo_boot_mt-1">
                            <h3>SERVICE PROVIDER</h3>
                        </div>
                        <div class="mo_boot_col-lg-7 mo_boot_mt-1">
                            <input id="mo_saml_local_configuration_form_action" type="hidden" name="option1" value="mo_saml_save_config"/>
                            <input type="button" id="idp_sp_config_end_tour" value="Start Tour" onclick="restart_toursp();" style="float:right;margin-left:3%"  class="mo_boot_btn mo_boot_btn-saml"/>
                            <a href="https://plugins.miniorange.com/guide-to-enable-joomla-idp-saml-sso/" target="_blank" style="float:right;margin-right: 2%;  ">
                                <div class="mo_boot_p-1 mo_boot_btn mo_boot_btn-secondary" style="border-radius: 0.25rem">    
                                    <span class="fa fa-file mo_boot_text-light" > Guides</span>
                                </div> 
                            </a>
                            <!-- We don't have any videos of Joomla as IDP. So will remove comment once it created -->
                            <!-- <span style="margin-right: 4%; float:right;">
                                <a href="https://www.youtube.com/playlist?list=PL2vweZ-PcNpdkpUxUzUCo66tZsEHJJDRl"  target="_blank">
                                    <div class="mo_boot_p-1  mo_boot_btn mo_boot_btn-danger" style="border-radius: 0.25rem">
                                        <span class="fa fa-youtube mo_boot_text-light" > Videos</span>
                                    </div>
                                </a> 
                            </span>   -->
                        </div>
                    </div>
                    <hr>
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-12">
                            <div  class="mo_boot_row">
                                <div class="mo_boot_col-sm-7">
                                    <b>Enter the information gathered from your Service Provider</b>
                                </div>
                                <div class="mo_boot_col-sm-2">
                                    <b> OR </b>
                                </div>
                                <div class="mo_boot_col-sm-3">
                                    <input id="sp_upload_metadata" type="button" class='mo_boot_btn mo_boot_btn-saml' style="float:right" onclick='show_metadata_form()' value="Upload SP Metadata"/>
                                </div>
                            </div>
                            <br>
                            <div id="idpdata">
                                <div class="mo_boot_row mo_boot_mt-3">
                                    <div class="mo_boot_col-sm-4">
                                        <b>Select your Service Provider <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext" >You can select any SP from dropdown to refer its guide for configuration.</span></div></b>
                                    </div>
                                    <div class="mo_boot_col-sm-8 start_dropdown">
                                    <div class="mo_boot_form-control" style="width:100%;cursor:pointer" id="select_sp" >Select Service Provider<span style="float:right!important"><i class='fa fa-angle-down'></i></span></div>
                                        <div id="myDropdown" class=" myDropdown mo_dropdown-content">
                                            <div id="dropdown_options" class="dropdown_options" >
                                                <input type="text"   style="display:none" class="mo_boot_form-control" placeholder="Search Service Provider" id="myInput" onkeyup="filterFunction()">

                                                <div class="mo_dropdown_options" id="dropdown-test">
                                                    <a href="https://plugins.miniorange.com/configure-tableau-sso-as-sp-for-joomla-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Tableau">Tableau</a>
                                                    <a href="https://plugins.miniorange.com/zendesk-single-sign-on-sso-for-joomla-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Zendesk">Zendesk</a>
                                                    <a href="https://plugins.miniorange.com/single-sign-workplace-facebook-sp-joomla-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Workplace by Facebook">Workplace by Facebook</a>
                                                    <a href="https://plugins.miniorange.com/single-sign-owncloud-sp-joomla-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Owncloud">Owncloud</a>
                                                    <a href="https://plugins.miniorange.com/configure-inkling-as-sp-for-joomla-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Inkling">Inkling</a>
                                                    <a href="https://plugins.miniorange.com/joomla-as-idp-aws-appstream2-as-sp" target="_blank" class="dropdown_option mo_dropdown_option" id="AWS AppStream2">AWS AppStream2</a>
                                                    <a href="https://plugins.miniorange.com/canvas-lms-as-sp-and-joomla-as-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="Canvas LMS">Canvas LMS</a>
                                                    <a href="https://plugins.miniorange.com/setup-aws-cognito-as-sp-and-joomla-as-an-idp" target="_blank" class="dropdown_option mo_dropdown_option" id="AWS Cognito">AWS Cognito</a>
                                                    <a href="https://plugins.miniorange.com/zoom-single-sign-on-sso-using-joomla" target="_blank" class="dropdown_option mo_dropdown_option" id="Zoom">Zoom</a>
                                                    <a href="https://plugins.miniorange.com/saml-single-sign-on-sso-into-joomla-using-zoho" target="_blank" class="dropdown_option mo_dropdown_option" id="Zoho">Zoho</a>
                                                    <a href="https://plugins.miniorange.com/zohodesk-single-sign-on-sso-using-joomla" target="_blank" class="dropdown_option mo_dropdown_option" id="Zoho Desk">Zoho Desk</a>
                                                    <a href="https://plugins.miniorange.com/saml-single-sign-on-sso-into-joomla-using-panopto" target="_blank" class="dropdown_option mo_dropdown_option" id="Panopto">Panopto</a>
                                                    <a href="https://plugins.miniorange.com/drupal-saml-single-sign-on-sso-with-joomla" target="_blank" class="dropdown_option mo_dropdown_option" id="Drupal">Drupal</a>
                                                    <a href="https://plugins.miniorange.com/saml-single-sign-on-sso-into-joomla-using-klipfolio" target="_blank" class="dropdown_option mo_dropdown_option" id="Klipfolio">Klipfolio</a>
                                                    <a href="https://plugins.miniorange.com/saml-single-sign-on-sso-into-joomla-using-rocketchat" target="_blank" class="dropdown_option mo_dropdown_option" id="Rocketchat">Rocketchat</a>
                                                    <a href="https://plugins.miniorange.com/saml-single-sign-on-sso-into-joomla-using-salesforce" target="_blank" class="dropdown_option mo_dropdown_option" id="Salesforce">Salesforce</a>
                                                    <a href="https://plugins.miniorange.com/saml-single-sign-on-sso-into-joomla-using-deskpro" target="_blank" class="dropdown_option mo_dropdown_option" id="Deskpro">Deskpro</a>
                                                    <a href="https://plugins.miniorange.com/freshdesk-single-sign-on-sso-with-joomla" target="_blank" class="dropdown_option mo_dropdown_option" id="FreshDesk">FreshDesk</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-easy-lms" target="_blank" class="dropdown_option mo_dropdown_option" id="Easy LMS">Easy LMS</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-box" target="_blank" class="dropdown_option mo_dropdown_option" id="BOX">BOX</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-slack" target="_blank" class="dropdown_option mo_dropdown_option" id="Slack">Slack</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-linkedin" target="_blank" class="dropdown_option mo_dropdown_option" id="LinkedIn">LinkedIn</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-trello" target="_blank" class="dropdown_option mo_dropdown_option" id="Trello">Trello</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-monday-dot-com" target="_blank" class="dropdown_option mo_dropdown_option" id="Monday.com">Monday.com</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-ringcentral" target="_blank" class="dropdown_option mo_dropdown_option" id="RingCentral">RingCentral</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-bluejeans" target="_blank" class="dropdown_option mo_dropdown_option" id="BlueJeans">BlueJeans</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-jira" target="_blank" class="dropdown_option mo_dropdown_option" id="Jira">Jira</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-clicdata" target="_blank" class="dropdown_option mo_dropdown_option" id="ClickData">ClickData</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-mediasite" target="_blank" class="dropdown_option mo_dropdown_option" id="Mediasite">Mediasite</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-learnupon" target="_blank" class="dropdown_option mo_dropdown_option" id="LearnUpon">LearnUpon</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-ispring-learn-lms" target="_blank" class="dropdown_option mo_dropdown_option" id="iSpring Learn LMS">iSpring Learn LMS</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-articulate-360-lms" target="_blank" class="dropdown_option mo_dropdown_option" id="Articulate 360 LMS">Articulate 360 LMS</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-tovuti-lms" target="_blank" class="dropdown_option mo_dropdown_option" id="Tovuti LMS">Tovuti LMS</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-lessonly-lms" target="_blank" class="dropdown_option mo_dropdown_option" id="Lessonly.ly LMS">Lessonly.ly LMS</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-brightspace" target="_blank" class="dropdown_option mo_dropdown_option" id="Brightspace">Brightspace</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-blackboard" target="_blank" class="dropdown_option mo_dropdown_option" id="Blackboard">Blackboard</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-jira-joomla-sso" target="_blank" class="dropdown_option mo_dropdown_option" id="Jira 2.0">Jira 2.0</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-adobe-captive-prime" target="_blank" class="dropdown_option mo_dropdown_option" id="Adobe Captivate Prime">Adobe Captivate Prime</a>
                                                    <a href="https://plugins.miniorange.com/login-using-joomla-saml-single-sign-on-sso-into-frontline-education" target="_blank" class="dropdown_option mo_dropdown_option" id="Frontline Education">Frontline Education</a>
                                                    <a href="https://www.miniorange.com/contact"  target="_blank" class="dropdown_option" id="Other">Other</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mo_boot_row mo_boot_mt-3" id="name">
                                    <div class="mo_boot_col-sm-4">
                                        <b><span style="color:red">*</span>Service Provider Name <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext" >Enter the name of SP you are configuring.</span></div></b>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <input type="text" name="sp_name" class="mo_boot_form-control mo_saml_idp_textfield"  placeholder="Enter the name of your Service Provider like Zendesk, Zoom...anything" value="<?php echo $sp_name; ?>" required />
                                    </div>
                                </div>
                                <div class="mo_boot_row mo_boot_mt-3" id="sp_entity">
                                    <div class="mo_boot_col-sm-4">
                                        <b><span style="color:red;">*</span>SP Entity ID or Issuer <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext" >You can find the EntityID in Your SP-Metadata XML file enclosed in <code style="color:#40F7E1">EntityDescriptor</code> tag having attribute as <code style="color:#40F7E1">entityID</code></span></div></b>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <input type="text" id="sp_entityid" class="mo_saml_idp_textfield mo_boot_form-control" name="sp_entityid" placeholder="Enter SP Entity ID or Issuer" value="<?php echo $sp_entityid; ?>" required/>
                                    </div>
                                </div>
                                <div class="mo_boot_row mo_boot_mt-3" id="sp_sso_url">
                                    <div class="mo_boot_col-sm-4">
                                        <b><span style="color:red;">*</span>ACS URL  <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext" >You can find the ACS URL in Your SP-Metadata XML file enclosed in <code style="color:#40F7E1">SingleSignOnService</code> tag having attribute as <code style="color:#40F7E1">Location</code></span></div></b>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <input class="mo_saml_idp_textfield mo_boot_form-control" type="url" placeholder="Enter Assertion Consumer Service URL" id="acs_url" name="acs_url" value="<?php echo $acs_url; ?>" required/> 
                                    </div>
                                </div>

                                <div class="mo_boot_row mo_boot_mt-3" id="sp_nameid_format">
                                    <div class="mo_boot_col-sm-4">
                                        <b><span style="color:red;">*</span>NameID Format <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext" >Identifies the SAML processing rules and constraints for the assertion's subject statement. Use the default value of 'Unspecified' unless the application explicitly requires a specific format.</span></div></b>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <select id="nameid_format" name="nameid_format" class="mo_saml_idp_textfield mo_boot_form-control">
                                            <option value="urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified" <?php if ($nameid_format == 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified') echo 'selected = "selected"'; ?>>
                                                urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified
                                            </option>
                                            <option value="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress" <?php if ($nameid_format == 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress') echo 'selected = "selected"'; ?>>
                                                urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress
                                            </option>
                                            <option value="urn:oasis:names:tc:SAML:1.1:nameid-format:transient" <?php if ($nameid_format == 'urn:oasis:names:tc:SAML:1.1:nameid-format:transient') echo 'selected = "selected"'; ?>>
                                                urn:oasis:names:tc:SAML:1.1:nameid-format:transient
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mo_boot_row mo_boot_mt-3">
                                    <div class="mo_boot_col-sm-4">
                                        <b>Relay State <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext" >It specifies the landing page at the service provider once SSO completes.</span></div></b>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <input class="mo_saml_idp_textfield mo_boot_form-control" type="url" placeholder="Enter Default Relay State URL" name="default_relay_state" value="<?php echo $relay_state; ?>" />
                                    </div>
                                </div>
                                <div class="mo_boot_row mo_boot_mt-3" id="assertion_sign">
                                    <div class="mo_boot_col-sm-4">
                                        <b>Assertion Signed <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext" >Check if you want to sign the SAML Assertion.</span></div></b>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <input type="checkbox" name="assertion_signed" <?php echo($assertion_signed == 1 ? 'checked' : ''); ?>>
                                    </div>
                                </div><br>

                                <div>
                                    <details>
                                        <summary class="mo_idp_summary" >ADVANCE FEATURES <sup><a href="index.php?option=com_joomlaidp&view=accountsetup&tab-panel=license">[Premium]</a></sup></summary><hr>
                                        <div class="mo_boot_row mo_boot_mt-3" id="sp_binding_type">
                                            <div class="mo_boot_col-sm-5">
                                                <b>Binding type for Single Logout</b>
                                            </div>
                                            <div class="mo_boot_col-sm-7">
                                                <input type="radio" name="miniorange_saml_sp_sso_binding" value="HttpRedirect" checked=1 aria-invalid="false" disabled><span class="mo_boot_ml-1">HTTP-Redirect</span><br />
                                                <input type="radio" name="miniorange_saml_idp_sso_binding" value="HttpPost" aria-invalid="false" disabled><span class="mo_boot_ml-1">HTTP-Post</span>
                                            </div>
                                        </div>
                                        <div class="mo_boot_row mo_boot_mt-3" id="sp_slo">
                                            <div class="mo_boot_col-sm-5">
                                                <b>Single Logout URL:</b>
                                            </div>
                                            <div class="mo_boot_col-sm-7">
                                                <input class=" mo_boot_form-control" type="text" name="single_logout_url" disabled>
                                            </div>
                                        </div>
                                        <div class="mo_boot_row mo_boot_mt-3" id="sp_certificate_signed">
                                            <div class="mo_boot_col-sm-5">
                                                <b><div>X.509 Certificate:</div>
                                                    <i><span style='font-size:11px;'>(Required for Signed Request)</span></i>
                                                </b>
                                            </div>
                                            <div class="mo_boot_col-sm-7">
                                                <textarea rows="4" cols="80" name="certificate" style="width: 100%; border: 1px solid #868383!important;border-radius:4px;" disabled></textarea>
                                            </div>
                                        </div>
                                        <div class="mo_boot_row mo_boot_mt-3" id="sp_certificate_assertion">
                                            <div class="mo_boot_col-sm-5">
                                                <b><div>X.509 Certificate:</div>
                                                    <i><span style='font-size:11px;'>(For Encrypted Assertion)</span></i></br>
                                                </b>
                                            </div>
                                            <div class="mo_boot_col-sm-7">
                                                <textarea rows="4" cols="80" name="certificate" style="width: 100%; border: 1px solid #868383!important;border-radius:4px;" disabled></textarea>
                                            </div>
                                        </div>
                                        <div class="mo_boot_row mo_boot_mt-3">
                                            <div class="mo_boot_col-sm-5">
                                                <b>Response Signed:</b>
                                            </div>
                                            <div class="mo_boot_col-sm-7">
                                                <input type="checkbox" name="assertion_signed" disabled />
                                            </div>
                                        </div>
                                        <div class="mo_boot_row mo_boot_mt-3">
                                            <div class="mo_boot_col-sm-5">
                                                <b>Encrypted Assertion:</b>
                                            </div>
                                            <div class="mo_boot_col-sm-7">
                                                <input type="checkbox" name="assertion_signed" disabled />
                                            </div>
                                        </div>
                                        <div class="mo_boot_row mo_boot_mt-3">
                                            <div class="mo_boot_col-sm-5">
                                                <b>SAML Response validation time:</b>
                                            </div>
                                            <div class="mo_boot_col-sm-7">
                                            <input class="mo_boot_form-control" type="text" placeholder="Enter time in seconds" name="saml_response_validation_time" disabled>
                                            </div>
                                        </div>
                                    </details>
                                </div>
                                <div class="mo_boot_row mo_boot_mt-3 mo_boot_p-2" id="sp_nameid_attribute">
                                    <div class="mo_boot_col-sm-12 mo_boot_mb-4">
                                        <h3>ATTRIBUTE MAPPING</h3><hr>
                                    </div>
                                    <div class="mo_boot_col-sm-4">
                                        <b>NameID Attribute <div class="fa fa-question-circle-o mo_tooltip"><span class="mo_tooltiptext" >This attribute value is sent in SAML Response. Users in your Service Provider will be searched (existing users) or created (new users) based on this attribute. Use EmailAddress by default.</span></div></b>
                                    </div>
                                    <div class="mo_boot_col-sm-8">
                                        <select id="nameid_attribute" name="nameid_attribute" class="mo_boot_form-control">
                                            <option value="emailAddress" <?php if ($nameid_attribute == 'emailAddress') echo 'selected = "selected"'; ?>>emailAddress</option>
                                            <option value="username" <?php if ($nameid_attribute == 'username') echo 'selected = "selected"'; ?>>username</option>
                                        </select>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_mt-5 mo_boot_text-center">
                                        <input id="save_idp" type="submit" class="mo_boot_btn mo_boot_btn-success mo_boot_m-1" value="Save"/>
                                        <input type="button" id='test-config' <?php if ($sp_entityid) echo "enabled"; else echo "disabled"; ?>
                                            title='You can only test your configuration after saving your Service Provider Settings. '
                                            class='mo_boot_btn mo_boot_btn-saml mo_boot_m-1' onclick='showTestWindow()' value="Test Configuration">
                                        <input type="submit" class="mo_boot_btn mo_boot_btn-danger mo_boot_m-1" <?php if ($sp_entityid) echo "enabled"; else echo "disabled"; ?>
                                            name="mo_saml_delete" value="Delete SP Configuration"/>
                                    </div>
                                </div>
                                <input type="hidden" id="idp-initiated-url" value="<?php echo JRoute::_('index.php?option=com_idpinitiatedlogin'); ?>"/>
                            </div>
                            <?php echo JHtml::_('form.token'); ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }
}
