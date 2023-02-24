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
class AuthnRequest{
   
    private $nameIdPolicy;
    private $forceAuthn;
    private $isPassive;
    private $RequesterID = array();
    private $assertionConsumerServiceURL;
    private $protocolBinding;
    private $requestedAuthnContext;
    private $namespaceURI;
    private $destination;
    private $issuer;
    private $version;
    private $issueInstant;
    private $requestID;

    public function __construct(DOMElement $xml = null){
        $this->nameIdPolicy = array();
        $this->forceAuthn = false;
        $this->isPassive = false;
        if ($xml === null) {
            return;
        }
        $this->forceAuthn = IDP_Utilities::parseBoolean($xml, 'ForceAuthn', false);
        $this->isPassive = IDP_Utilities::parseBoolean($xml, 'IsPassive', false);
        if ($xml->hasAttribute('AssertionConsumerServiceURL')) {
            $this->assertionConsumerServiceURL = $xml->getAttribute('AssertionConsumerServiceURL');
        }
        if ($xml->hasAttribute('ProtocolBinding')) {
            $this->protocolBinding = $xml->getAttribute('ProtocolBinding');
        }
        if ($xml->hasAttribute('AttributeConsumingServiceIndex')) {
            $this->attributeConsumingServiceIndex = (int) $xml->getAttribute('AttributeConsumingServiceIndex');
        }
        if ($xml->hasAttribute('AssertionConsumerServiceIndex')) {
            $this->assertionConsumerServiceIndex = (int) $xml->getAttribute('AssertionConsumerServiceIndex');
        }
        if ($xml->hasAttribute('Destination')) {
            $this->destination = $xml->getAttribute('Destination');
        }
        if (isset($xml->namespaceURI)) {
            $this->namespaceURI = $xml->namespaceURI;
        }
        if ($xml->hasAttribute('Version')) {
            $this->version = $xml->getAttribute('Version');
        }
        if ($xml->hasAttribute('IssueInstant')) {
            $this->issueInstant = $xml->getAttribute('IssueInstant');
        }
        if ($xml->hasAttribute('ID')) {
            $this->requestID = $xml->getAttribute('ID');
        }

        $this->parseNameIdPolicy($xml);
        $this->parseIssuer($xml);
        $this->parseRequestedAuthnContext($xml);
        $this->parseScoping($xml);
    }
    
    public function getNameIdPolicy(){
        return $this->nameIdPolicy;
    }
    
    public function getForceAuthn(){
        return $this->forceAuthn;
    }

    public function getVersion(){
        return $this->version;
    }

    public function getRequestID(){
        return $this->requestID;
    }

    public function getIssueInstant(){
        return $this->issueInstant;
    }
    
    public function getDestination(){
        return $this->destination;
    }
   
    public function getIsPassive(){
        return $this->isPassive;
    }

    public function getIDPList(){
        return $this->IDPList;
    }
    
    public function getProxyCount(){
        return $this->ProxyCount;
    }
  
    public function getRequesterID(){
        return $this->RequesterID;
    }

    public function getNamespaceURI(){
        return $this->namespaceURI;
    }

    public function getIssuer(){
        return $this->issuer;    
    }
   
    public function getAssertionConsumerServiceURL(){
        return $this->assertionConsumerServiceURL;
    }
    
    public function getProtocolBinding(){
        return $this->protocolBinding;
    }
    
    public function getAttributeConsumingServiceIndex(){
        return $this->attributeConsumingServiceIndex;
    }
   
    public function getAssertionConsumerServiceIndex(){
        return $this->assertionConsumerServiceIndex;
    }
    
    public function getRequestedAuthnContext(){
        return $this->requestedAuthnContext;
    }

    protected function parseIssuer(DOMElement $xml){
        $issuer = IDP_Utilities::xpQuery($xml, './saml_assertion:Issuer');
        if (empty($issuer)) {
            throw new Exception('Missing <saml:Issuer> in assertion.');
        }
        $this->issuer = trim($issuer[0]->textContent);
    }

    protected function parseNameIdPolicy(DOMElement $xml)
    {
        $nameIdPolicy = IDP_Utilities::xpQuery($xml, './saml_protocol:NameIDPolicy');
        if (empty($nameIdPolicy)) {
            return;
        }
        $nameIdPolicy = $nameIdPolicy[0];
        if ($nameIdPolicy->hasAttribute('Format')) {
            $this->nameIdPolicy['Format'] = $nameIdPolicy->getAttribute('Format');
        }
        if ($nameIdPolicy->hasAttribute('SPNameQualifier')) {
            $this->nameIdPolicy['SPNameQualifier'] = $nameIdPolicy->getAttribute('SPNameQualifier');
        }
        if ($nameIdPolicy->hasAttribute('AllowCreate')) {
            $this->nameIdPolicy['AllowCreate'] = IDP_Utilities::parseBoolean($nameIdPolicy, 'AllowCreate', false);
        }
    }
   

    protected function parseRequestedAuthnContext(DOMElement $xml)
    {
        $requestedAuthnContext = IDP_Utilities::xpQuery($xml, './saml_protocol:RequestedAuthnContext');
        if (empty($requestedAuthnContext)) {
            return;
        }
        $requestedAuthnContext = $requestedAuthnContext[0];
        $rac = array(
            'AuthnContextClassRef' => array(),
            'Comparison'           => 'exact',
        );
        $accr = IDP_Utilities::xpQuery($requestedAuthnContext, './saml_assertion:AuthnContextClassRef');
        foreach ($accr as $i) {
            $rac['AuthnContextClassRef'][] = trim($i->textContent);
        }
        if ($requestedAuthnContext->hasAttribute('Comparison')) {
            $rac['Comparison'] = $requestedAuthnContext->getAttribute('Comparison');
        }
        $this->requestedAuthnContext = $rac;
    }
   

    protected function parseScoping(DOMElement $xml)
    {
        $scoping = IDP_Utilities::xpQuery($xml, './saml_protocol:Scoping');
        if (empty($scoping)) {
            return;
        }
        $scoping = $scoping[0];
        if ($scoping->hasAttribute('ProxyCount')) {
            $this->ProxyCount = (int) $scoping->getAttribute('ProxyCount');
        }
        $idpEntries = IDP_Utilities::xpQuery($scoping, './saml_protocol:IDPList/saml_protocol:IDPEntry');
        foreach ($idpEntries as $idpEntry) {
            if (!$idpEntry->hasAttribute('ProviderID')) {
                throw new Exception("Could not get ProviderID from Scoping/IDPEntry element in AuthnRequest object");
            }
            $this->IDPList[] = $idpEntry->getAttribute('ProviderID');
        }
        $requesterIDs = IDP_Utilities::xpQuery($scoping, './saml_protocol:RequesterID');
        foreach ($requesterIDs as $requesterID) {
            $this->RequesterID[] = trim($requesterID->textContent);
        }
    }
}