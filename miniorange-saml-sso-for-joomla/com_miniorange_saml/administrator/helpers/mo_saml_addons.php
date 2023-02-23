<?php
defined('_JEXEC') or die;
/*
 * @package    miniOrange
 * @subpackage Plugins
 * @license    GNU/GPLv3
 * @copyright  Copyright 2015 miniOrange. All Rights Reserved.
*/
JHtml::_('stylesheet', JUri::base() .'components/com_miniorange_saml/assets/css/miniorange_boot.css');
function advancesetting()
{

    ?>
   
    <div class="mo_boot_row  mo_boot_mx-1 mo_boot_p-3" style="background-color: #FFFF;border: 2px solid rgb(15, 127, 182) !important;"> 
        <p class='alert alert-info' style="color: #151515;width:100%">NOTE: All these addons are paid. Please contact us at <a href="mailto:joomlasupport@xecurify.com">joomlasupport@xecurify.com</a> if you are interested in purchasing the Addons.</p>
        <div class="mo_boot_col-12">
            <details open !important>
                
                    <details !important>
                        <div class="mo_boot_row mo_saml_table_layout_1" style=" width:100% !important;margin-left:5px">
                            <div class="mo_boot_col-sm-12 mo_saml_table_layout mo_saml_container">
                                    <?php 
                                    scimconfig();
                                    ?>
                            </div>
                        </div>
                        <summary class="mo_saml_summary">
                            <strong style="">1. SCIM Configuration</strong>
                        </summary><hr>
                    </details>

                    <details>
                        <div class="mo_boot_row mo_saml_table_layout_1" style=" width:100% !important;margin-left:5px">
                            <div class="mo_boot_col-sm-12 mo_saml_table_layout mo_saml_container">
                                        <?php 
                                        scimMapping();
                                        ?>
                            </div>
                        </div>
                        <summary class="mo_saml_summary">
                            <strong>2. SCIM Mapping</strong>
                        </summary><hr>
                    </details>

                    <details>
                        <div class="mo_boot_row mo_saml_table_layout_1" style=" width:100% !important;margin-left:5px">
                            <div class="mo_boot_col-sm-12 mo_saml_table_layout mo_saml_container">
                                    <?php 
                                        scimAdvanceSettings();
                                    ?>
                                </div>
                            </div>
                        <summary class="mo_saml_summary"> 
                            <strong>3. Advance Setting</strong>
                        </summary><hr>
                    </details>
                <summary class="mo_saml_main_summary" >
                        <strong>SCIM</strong>
                        <a type="button" class="mo_summary_btn" href="https://www.miniorange.com/contact" target="_blank">Interested</a>
                </summary>
            </details>
        </div>

        <div class="mo_boot_col-12">
            <details>

                <details open !important>
                    <div class="mo_boot_row mo_saml_table_layout_1" style=" width:100% !important;margin-left:5px">
                        <div class="mo_boot_col-sm-12 mo_saml_table_layout mo_saml_container">
                            <?php 
                                clientconfig();
                            ?>
                        </div>
                    </div>
                    <summary  class="mo_saml_summary">
                        <strong>Configure Page Restriction</strong>
                    </summary>
                </details>
                        <?php
                        $page_restriction_tab = 'pagerestrictionsettings';
                        $active_tab = JFactory::getApplication()->input->get->getArray();
                        
                        if(isset($active_tab['tab-panel']) && !empty($active_tab['tab-panel'])){
                            $page_restriction_tab = $active_tab['tab-panel'];
                        }
                        ?>   
                    <summary  class="mo_saml_main_summary" >
                        <strong>Page Restriction</strong>
                        <a type="button" class="mo_summary_btn" href="https://www.miniorange.com/contact" target="_blank">Interested</a>
                    </summary>
            </details>
        </div>


        <div class="mo_boot_col-12">
            <details>
                    <details open !important>
                        <div class="mo_boot_row mo_saml_table_layout_1" style=" width:100% !important;margin-left:5px">
                            <div class="mo_boot_col-sm-12 mo_saml_table_layout mo_saml_container">
                                <?php
                                communitybuilder();
                                ?>	
                            </div>
                        </div>
                        <summary class="mo_saml_summary">
                            <strong>Community Builder Configuration</strong>
                        </summary>
                    </details>
                    <summary  class="mo_saml_main_summary">
                        <strong>Community Builder</strong>
                        <a type="button" class="mo_summary_btn" href="https://www.miniorange.com/contact" target="_blank">Interested</a>
                    </summary>
            </details>
        </div>
        
    
    </div>
<?php
}


function mo_scim_support(){
    ?>
        <div class="mo_boot_row  mo_table_layout"  id="supporttour" style="border: 2px solid rgb(15, 127, 182); background-color: #FFFFFF !important;">
            <div class="mo_boot_col-sm-12 mo_boot_text-center">
                <h3>Support</h3>
                <hr>
            </div>
            <div class="mo_boot_col-sm-12">
               
                    <div class="mo_boot_row scim-table">
                        <div class="mo_boot_col-sm-12">
                            <p >Need any help? Just send us a query and we will get back to you soon.</p>
                        </div>
                    </div>
                    <div class="mo_boot_row scim-table mo_boot_text-center">
                        <div class="mo_boot_col-sm-12">
                            <input type="email" class="mo_boot_form-control scim-table mo_scim_textfield" name="query_email" value="" placeholder="Enter your email" required />
                        </div>
                        <div class="mo_boot_col-sm-12" style="margin-top:5px">
                            <input type="text" class="mo_boot_form-control scim-table mo_scim_textfield" name="query_phone" value="" placeholder="Enter your phone with country code"/>
                        </div>
                        <div class="mo_boot_col-sm-12" style="margin-top:5px">
                            <textarea class="mo_scim_textfield" name="mo_scim_textfield" style="border-radius:4px;resize: vertical;width:100%" cols="52" rows="6" placeholder="Write your query here" required></textarea>
                        </div>
                        <div class="mo_boot_col-sm-12" style="margin-top:5px">
                            <input type="submit" name="send_query" value="Submit Query" class="mo_boot_btn mo_boot_btn-success" />
                        </div>
                    </div><br>
              
            </div>
        </div>
    <?php
    }
    function scimconfig() {
       
        $scim_url = JURI::root() . 'index.php/miniorangescim';
    
        ?>
        <div class="mo_boot_row mo_boot_m-1 mo_boot_p-3"  >
            <div class="mo_boot_col-sm-12">
                <h3>SCIM Configuration</h3><hr>
                <input type="hidden" name="option" value="" />
            </div>
            <div class="mo_boot_col-sm-12 mo_boot_table-responsive">
                <table class="mo_boot_table mo_boot_table-bordered">
                    <caption></caption>
                    
                    <tr>
                        <th id="f"><strong>SCIM Base URL</strong></th>
                        <td><?php echo $scim_url; ?></td>
                    </tr>
                    <tr>
                        <td><strong>SCIM Bearer Token</strong></td>
                        <td>xxxxxxxxxxxxxxxxxxxx</td>
                    </tr>
                </table>
            </div>
            <div class="mo_boot_col-sm-12 mo_boot_mt-4">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <h3>Instructions:</h3><hr>
                        <ol>
                            <li>1. Enter the above SCIM Base URL and Bearer Token on your IDP.</li>
                            <li>2. Once done with above configuration, you will be able perform add and delete operations on IDP.</li>
                            <li>3. Once done with above configuration, you will be able perform add, update and delete operations on IDP under User Provisioning.</li>
                        </ol>
                    </div>
                </div><br><br>
            </div>
            <div class="mo_boot_col-sm-12">
                <h3>ROLE MAPPING<sup> [Available in<a href=''> Standard, Premium</a> version]</sup></h3><hr>
            </div><hr>
            <div class="mo_boot_col-sm-12 moJoom-scimClient-supportForm"  id="moJoom-scimClient-supportForm" >
               
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-12">
                            <h4><u>New role Created by SCIM must be child of:</u></h4>
                        </div>
                        <div class="mo_boot_col-sm-6">
                            <input type="radio" disabled > Public</div>
                            <div class="mo_boot_col-sm-6">    <input type="radio" disabled checked> Registered</div>
                            <div class="mo_boot_col-sm-6"><input type="radio" disabled >Author</div>
                            <div class="mo_boot_col-sm-6"><input type="radio"disabled > Editor</div>
                            <div class="mo_boot_col-sm-6"><input type="radio" disabled > Publisher</div>
                            <div class="mo_boot_col-sm-6"><input type="radio"disabled > Manager</div>
                            <div class="mo_boot_col-sm-6"><input type="radio"disabled > Administrator</div>
                            <div class="mo_boot_col-sm-6"><input type="radio"disabled > Super Users</div>
                            <div class="mo_boot_col-sm-6"><input type="radio"disabled > Guest</div>
                        
                    </div><br>
    
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-12">
                            <h4><u>Assign these roles to all scim users</u></h4>
                        </div>
                        <div class="mo_boot_col-sm-6">
                            <input type="checkbox" disabled > Public</div>
                            <div class="mo_boot_col-sm-6">    <input type="checkbox" disabled checked> Registered</div>
                            <div class="mo_boot_col-sm-6"><input type="checkbox" disabled >Author</div>
                            <div class="mo_boot_col-sm-6"><input type="checkbox"disabled > Editor</div>
                            <div class="mo_boot_col-sm-6"><input type="checkbox" disabled > Publisher</div>
                            <div class="mo_boot_col-sm-6"><input type="checkbox"disabled > Manager</div>
                            <div class="mo_boot_col-sm-6"><input type="checkbox"disabled > Administrator</div>
                            <div class="mo_boot_col-sm-6"><input type="checkbox"disabled > Super Users</div>
                            <div class="mo_boot_col-sm-6"><input type="checkbox"disabled > Guest</div>

                        <div class="mo_boot_col-sm-12 mo_boot_text-center mo_boot_mt-5">
                            <p><input type="submit" value="Save Configurations" class="mo_tfa_input_submit mo_boot_btn mo_boot_btn-success" disabled></p>
                        </div>
                    </div>
   
            </div>
    
    
            <details style="width: 100%;">
                <summary class="mo_scim_summary">SCIM OPERATIONS</summary><hr>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <dl>
                            <dt><strong>Create:</strong></dt>
                            <dd>
                                <p class="mo_boot_mt-2">It will create user using First Name, Last Name, Email and Username.</p>
                                <p><strong>Note: </strong>If Username field is blank, it will copy email as a username, as Joomla does not accept blank Username.</p>
                            </dd>
                            <dt class="mo_boot_mt-2"><strong>Delete:</strong></dt>
                            <dd>
                                <p class="mo_boot_mt-2">Once delete a user from IDP, it would delete a user from Joomla User list as well.</p>
                            </dd>
                            <dt class="mo_boot_mt-2"><strong>Update:</strong></dt>
                            <dd>
                                <p class="mo_boot_mt-2">It will update the user fields, all attributes/fields except email and username.</p>
                            </dd>
                            <dt class="mo_boot_mt-2"><strong>Deactivate:</strong></dt>
                            <dd>
                                <p class="mo_boot_mt-2">Once delete a user from IDP, it would deactivate a user from the Joomla user list.</p>
                            </dd>
                        </dl>
                    </div>
                </div>
            </details>
        </div>
        <?php
    }
    function scimMapping(){
       
        ?>
         <div class="mo_boot_row mo_boot_m-1  mo_boot_p-3"  >
            <div class="mo_boot_col-sm-12">
                <h3>SCIM Mapping</h3><hr>
                <input type="hidden" name="option" value="" />
            </div>
            
            <div class="mo_boot_container" id="userProfileAttrDiv" style="margin-left:2%;">
                 <h4> Add Joomla's User Profile Attributes <input type="button" class="mo_boot_btn mo_boot_btn-primary" id="moScimUserProfilePlusButton"  value="+" disabled  /> </h4>
                 <p class="alert alert-info" style="color: #151515;font-size:13px">NOTE: During registration or login of the user, the value corresponding to User Profile Attributes Mapping Value from OAuth server will be updated for the User Profile Attribute field in User Profile table.</p>
    
    
                 <div class="mo_boot_row">
                     <div  style="margin-left:2%;"><h5><u >SCIM Client (IdP) Attribute Name</u></h5></div>
                     <div style="margin-left: 24%;"><h5><u>Joomla  Attribute Name</u></h5></div>
                </div><br> 
                <div class="mo_boot_row userAttr userProfileAttributeRows" style="padding-bottom:1%;" id="uparow_profile" >
                         <select type="text" class="moScimAttributeName mo_scim_textfield"  style=" margin-left: 3% !important;height:28px;width: 35% !important;border: 1px solid #ccc;background-color: #fff;" disabled>
                         <option>userName</option>
                         <option>nameformatted</option>
                         <option>displayName</option>
                         <option>title</option>
                         <option>userType</option>
                         </select>
                         <select type="text" class="moScimAttributeValue mo_scim_textfield" style=" margin-left: 10% !important;height:28px;width: 35% !important;border: 1px solid #ccc;background-color: #fff;" disabled >
                         <option>username</option>
                         <option>enail</option>
                         </select>
                         <input type="button" class="mo_boot_btn mo_boot_btn-danger" style="margin:0px 0px 8px 10px;" value="-" disabled />
                </div>
                <input type="submit" name="moOauthAttrMapSaveButton" class="mo_boot_btn mo_boot_btn-success" style="display: block;margin-left:30%;"value="Save Extended Attribute Mapping" disabled/><hr>
            </div>
            
    
             <div class="mo_boot_container" id="userProfileAttrDiv" style="margin-left:2%">
                 <h4> Advance Joomla's User Profile Attributes <sup><a href=''> Standard, Premium</a></sup><input type="button" class="mo_boot_btn mo_boot_btn-primary" style="margin-left:5%" disabled value="+" /></h4><br> 
                 <div class="mo_boot_row ">
                     <div  style="margin-left:2%;"><h5 ><u>SCIM Client (IdP) Attribute Name</u></h5></div>
                     <div style="margin-left: 24%;"><h5><u>Joomla  Attribute Name</u></h5></div>
                 </div>
                 <div class="mo_boot_row userAttr userProfileAttributeRows" style="padding-bottom:1%;" id="uparow_profile" >
                           <select type="text" class="moScimAttributeName mo_scim_textfield" disabled style=" margin-left: 3% !important;height:28px;width: 35% !important;border: 1px solid #ccc;background-color: #fff;"><option>userName</option></select>
                           <select type="text" class="moScimAttributeValue mo_scim_textfield" disabled style=" margin-left: 10% !important;height:28px;width: 35% !important;border: 1px solid #ccc;background-color: #fff;" > <option>username</option></select>
                </div><br>
                </div><input type="submit"  class="mo_boot_btn mo_boot_btn-success" disabled style="display: block;margin-left: 30%;" value="Save Extended Attribute Mapping"/>
         </div>
         <?php
    
    }
    
function scimAdvanceSettings()
{
    ?>
     <div class="mo_boot_row mo_boot_m-1  mo_boot_p-3" >
         <div class="mo_boot_col-sm-12">
             <h3>ADVANCE SETTINGS<sup><a href=''> Premium</a></sup></h3><hr>
            <input type="hidden" name="option" value="" />
         </div>
         
             <div class="mo_boot_col-sm-12 mo_boot_mt-8">
                 <p>Perform the following action on Joomla user list when user is deleted from IDP:</p>
                 <input  type="radio" checked="true" disabled/>&emsp;Do nothing<br><br>
                 <input  type="radio" disabled/>&emsp;Deactivate Users<br><br>
                 <input  type="radio" disabled/>&emsp;Delete Users
            </div><br><br>
            <div><br>
             <input type="submit" name="moOauthAttrMapSaveButton" class="mo_boot_btn mo_boot_btn-medium mo_boot_btn-success" style="display: block;margin-left:210% !important" value="Save Settings" disabled=""/></div>
   
     </div> 
     <?php
}





function clientconfig()
 {
    $backdoor_url = JURI::root().'administrator/?cusautoredirect=false <br><br>';
    ?>
			  
        <table>
           <tr id="mo_oauth_enable_page_restriction_div">
                <th><input id="ff" type="checkbox" onchange="submit()"; name="mo_oauth_enable_page_restriction" id="mo_oauth_enable_page_restriction" value="1"
                        disabled  style="margin-right:10px;"/><strong style="font-size:15px">Enable Page Restriction</strong></th>
             </tr>
        </table><hr>
    
        <input type="checkbox" value="1" name="mo_redirect_all_pages"
          style="margin-right:10px;" disabled /><strong style="font-size:15px">Auto redirect all the pages to the SSO URL</strong><br><br>
        <code style="font-size:15px">Backdoor URL:</code> <strong style="font-size:15px"> <?php echo $backdoor_url ?></strong><hr>

        <table>
            <tr>
                <th id="fff"><input type="checkbox" id="mo_enable_page_based" value="1" name="mo_enable_page_based_redirect"
                        style="margin-right:10px;" disabled /></th>
                <td><strong style="font-size:15px">Enable page based redirect</strong></td>
            </tr>
           
        </table><hr>
        <table>
            <tr>
                <th id="ffff"><input type="checkbox" value="1" id="mo_enable_ip_based" name="mo_enable_ip_based_redirect"
                       disabled  style="margin-right:10px;"/></th>
                <td><strong style="font-size:15px">Enable IP based redirect</strong></td>
            </tr>
            
        </table><hr>
        <label><strong style="font-size:15px">Enter the SSO URL:</strong></label>
        <input  name="mo_page_sso_url" placeholder="Enter the SSO URL" style="width: 80%;"  disabled /><br><br>
        <div class="mo_boot_text-center">
        <input type="submit" Disabled style="margin-left:%;margin-top:10px;" class="mo_boot_btn mo_boot_btn-large mo_boot_btn-success" value="Save"/></div><br/><br/>
    
 <?php
}

function communitybuilder() {
    ?>	  
    <div class="mo_boot_row mo_boot_mt-1" style="background:white;padding: 22px;!important;">
    
        <div class="mo_boot_col-sm-12 mo_boot_mt-2">
            <h3>Community Builder Configuration</h3><hr>
            
                <input type="checkbox" style="cursor: pointer;" name="enable_cmb" id="enable_cmb" value="1"  Disabled  />
                <strong>Enable Community Builder</strong>
 
        </div><br><br>
        <div class="mo_boot_col-sm-12 mo_boot_mt-2">
           
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <h3> 
                            Community builder fields
                            <input type="button" class="mo_boot_btn mo_boot_btn-primary" value="+" disabled/> 
                            <input type="button" class="mo_boot_btn mo_boot_btn-danger" value="-"  disabled />
                        </h3>
                    </div>
                    <div class="mo_boot_col-sm-12">
                        <p class="alert alert-info" style="font-size:13px;">NOTE: During registration or login of the user, the value corresponding to Mapping Values from IDP will be updated in the community builder comprofiler table.</p>
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-2">
                    <div class="mo_boot_col-sm-12" id="cbattributeslist">
                        <div class="mo_boot_row" id="before_attr_list_cb">
                            <div class="mo_boot_col-sm-3">
                                <strong>CB Attribute</strong>
                            </div>
                            <div class="mo_boot_col-sm-3">
                                <strong>IDP Attribute</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-2">
                    <div class="mo_boot_col-sm-12">
                      
                    </div>
                </div><br>
                <div class="mo_boot_row mo_boot_mt-2">
                    <div class="mo_boot_col-sm-12">
                        <input type="submit" class="mo_boot_btn mo_boot_btn-success" value="Save Attribute Mapping" disabled/>
                    </div>
                </div>
  
        </div>
    </div>	 
              
    <?php
}