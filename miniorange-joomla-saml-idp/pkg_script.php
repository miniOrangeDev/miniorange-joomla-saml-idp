<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of pkg_MiniorangeJoomlaSAMLIdpSSO.
 *
 * The name of this class is dependent on the component being installed.
 * The class name should have the component's name, directly followed by
 * the text InstallerScript (ex:. com_helloWorldInstallerScript).
 *
 * This class will be called by Joomla!'s installer, if specified in your component's
 * manifest file, and is used for custom automation actions in its installation process.
 *
 * In order to use this automation script, you should reference it in your component's
 * manifest file as follows:
 * <scriptfile>pkg_script.php</scriptfile>
 *
 
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
    
class pkg_MiniorangeJoomlaSAMLIdpSSOInstallerScript
{
    /**
     * This method is called after a component is installed.
     *
     * @param  \stdClass $parent - Parent object calling this method.
     *
     * @return void
     */
    public function install($parent) 
    {

            
    }

    /**
     * This method is called after a component is uninstalled.
     *
     * @param  \stdClass $parent - Parent object calling this method.
     *
     * @return void
     */
    public function uninstall($parent) 
    {
        //echo '<p>' . JText::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
    }

    /**
     * This method is called after a component is updated.
     *
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    public function update($parent) 
    {
        //echo '<p>' . JText::sprintf('COM_HELLOWORLD_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
    }

    /**
     * Runs just before any installation action is performed on the component.
     * Verifications and pre-requisites should run in this function.
     *
     * @param  string    $type   - Type of PreFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    public function preflight($type, $parent) 
    {
        //echo '<p>' . JText::_('COM_HELLOWORLD_PREFLIGHT_' . $type . '_TEXT') . '</p>';
    }

    /**
     * Runs right after any installation action is performed on the component.
     *
     * @param  string    $type   - Type of PostFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    function postflight($type, $parent) 
    {
       // echo '<p>' . JText::_('COM_HELLOWORLD_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
       if ($type == 'uninstall') {
        return true;
        }
       $this->showInstallMessage('');
    }

    protected function showInstallMessage($messages=array()) {
        ?>
        <style>
        
	.mo-row {
		width: 100%;
		display: block;
		margin-bottom: 2%;
	}

	.mo-row:after {
		clear: both;
		display: block;
		content: "";
	}

	.mo-column-2 {
		width: 19%;
		margin-right: 1%;
		float: left;
	}

	.mo-column-10 {
		width: 80%;
		float: left;
	}
    .mo_boot_btn {
    display: inline-block;
    font-weight: 400;
    text-align: center;
    vertical-align: middle;
    user-select: none;
    background-color: transparent;
    border: 1px solid transparent;
    padding: 4px 12px;
    font-size: 0.85rem;
    line-height: 1.5;
    border-radius: 0.25rem;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .mo_boot_btn-saml{
    color:white;
    background-color:#226a8b;
    border-color: #226a8b;
    }

    .mo_boot_btn-saml:hover{
    color:white;
    background-color:#163c4e;
    }

    .mo_boot_btn-saml:focus, .mo_boot_btn-saml.mo_boot_focus {
    box-shadow: 0 0 0 0.2rem #163c4e;
    }

    .mo_boot_btn-saml.mo_boot_disabled, .mo_boot_btn-saml:disabled {
    color: #fff;
    background-color: #163c4e;
    border-color: #163c4e;
    }


    .mo_boot_btn-secondary {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
    }

    .mo_boot_btn-secondary:hover {
    color: #fff;
    background-color: #5a6268;
    border-color: #545b62;
    }
    </style>
    <p>Our plugin is compatible with Joomla 4 as well as with all the SAML 2.0 compliant Service Provider.</p>
    <h4>What this plugin does?</h4>
    <p>The Joomla SAML IDP plugin authenticates the user present on Joomla site and send the SAML response to SAML 2.0 compliant Service Providers to login. </p>
    	<div class="mo-row">
            <a class="mo_boot_btn mo_boot_btn-saml" onClick="window.location.reload();" href="index.php?option=com_joomlaidp&view=accountsetup&tab-panel=sp">Start Using miniOrange SAML IDP plugin</a>
            <a class="mo_boot_btn mo_boot_btn-secondary" href="https://plugins.miniorange.com/guide-to-enable-joomla-idp-saml-sso" target="_blank">Read the miniOrange documents</a>
		    <a class="mo_boot_btn mo_boot_btn-secondary" href="https://www.miniorange.com/contact" target="_blank">Get Support!</a>
        </div>
        <?php
    }
  
}