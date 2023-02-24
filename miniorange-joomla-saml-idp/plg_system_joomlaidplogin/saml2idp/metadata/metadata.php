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

defined('_JEXEC');
if (!defined('_JEXEC')) {
    define('_JEXEC', 1);
}
defined('_JEXEC') or die;
if (!defined('_JDEFINES')) {
    define('JPATH_BASE', dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
	require_once JPATH_BASE . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'defines.php';
}
require_once JPATH_BASE . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'framework.php';
jimport('miniorangejoomlaidpplugin.utility.IDP_Utilities');

$header = isset($_REQUEST['download']) && boolval($_REQUEST['download']) ? 'Content-Disposition: attachment; filename="Metadata.xml"' : 'Content-Type: text/xml';

$site_url = JURI::root();	
$site_url = substr($site_url, 0, strpos($site_url, 'plugins'));
$entity_id = $site_url . 'plugins/user/miniorangejoomlaidp/';
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select(array($db->quoteName('idp_entity_id')));
$query->from($db->quoteName('#__miniorange_saml_idp_customer'));
$query->where($db->quoteName('id').'=1');
$db->setQuery($query);
$idpid = $db->loadResult();
if(!empty($idpid)&&($entity_id!=$idpid))
    $entity_id = $idpid;
$login_url = $site_url . 'index.php';
$logout_url = $site_url . 'index.php/log-out';
$certificate = file_get_contents( JPATH_BASE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'joomlaidplogin' . DIRECTORY_SEPARATOR . 'saml2idp' . DIRECTORY_SEPARATOR .  'cert' . DIRECTORY_SEPARATOR . 'idp-signing.crt' );
$certificate = IDP_Utilities::desanitize_certificate($certificate);

header($header);
echo'<?xml version="1.0" encoding="UTF-8"?>
        <md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" entityID="'.$entity_id.'">
            <md:IDPSSODescriptor WantAuthnRequestsSigned="false" protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
                <md:KeyDescriptor use="signing">
			        <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
				        <ds:X509Data>
        					<ds:X509Certificate>'.$certificate.'</ds:X509Certificate>
		        		</ds:X509Data>
			        </ds:KeyInfo>
		        </md:KeyDescriptor>
		    <md:NameIDFormat>urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress</md:NameIDFormat>
		    <md:NameIDFormat>urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified</md:NameIDFormat>
		    <md:SingleSignOnService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="'.$login_url.'"/>
		    <md:SingleSignOnService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="'.$login_url.'"/>
	    </md:IDPSSODescriptor>
    </md:EntityDescriptor>';