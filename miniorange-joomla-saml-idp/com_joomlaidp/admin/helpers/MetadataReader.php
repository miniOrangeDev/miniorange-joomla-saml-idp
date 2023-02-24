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

defined('_JEXEC') or die;

class MetadataReader
{
    private $serviceProviders;

    public function __construct(\DOMNode $xml = NULL) {
        $this->serviceProviders = array();
        $entityDescriptors = MoSamlIdpUtility::xpQuery($xml, './saml_metadata:EntityDescriptor');
        foreach ($entityDescriptors as $entityDescriptor) {
            $SPSSODescriptor = MoSamlIdpUtility::xpQuery($entityDescriptor, './saml_metadata:SPSSODescriptor');
            if(isset($SPSSODescriptor) && !empty($SPSSODescriptor)){
                array_push($this->serviceProviders, new ServiceProviders($entityDescriptor));
            }
        }
    }

    public function getServiceProviders(){
        return $this->serviceProviders;
    }
}

class ServiceProviders{

    private $entityID;
    private $acsURL;
    private $assertionsSigned;

    public function __construct(\DOMElement $xml = NULL) {

        if ($xml->hasAttribute('entityID')) {
            $this->entityID = $xml->getAttribute('entityID');
        }

        $SPSSODescriptor = MoSamlIdpUtility::xpQuery($xml, './saml_metadata:SPSSODescriptor');

        if (count($SPSSODescriptor) > 1) {
            throw new Exception('More than one <SPSSODescriptor> in <EntityDescriptor>.');
        } elseif (empty($SPSSODescriptor)) {
            throw new Exception('Missing required <SPSSODescriptor> in <EntityDescriptor>.');
        }

        $this->parseAcsURL($SPSSODescriptor);
        $this->assertionsSigned($SPSSODescriptor);
    }

    private function parseAcsURL($SPSSODescriptor){

        $AssertionConsumerService = MoSamlIdpUtility::xpQuery($SPSSODescriptor[0], './saml_metadata:AssertionConsumerService');
        foreach ($AssertionConsumerService as $sign) {
            if($sign->hasAttribute('Location')){
                $this->acsURL = $sign->getAttribute('Location');
            }
        }
    }

    private function assertionsSigned($SPSSODescriptor){

        foreach ($SPSSODescriptor as $sign) {
            if($sign->hasAttribute('WantAssertionsSigned')){
                $this->assertionsSigned = $sign->getAttribute('WantAssertionsSigned');
            }
        }
    }

    public function getEntityID(){
        return $this->entityID;
    }

    public function getAcsURL(){
        return $this->acsURL;
    }

    public function getAssertionsSigned(){
        return $this->assertionsSigned;
    }
}
