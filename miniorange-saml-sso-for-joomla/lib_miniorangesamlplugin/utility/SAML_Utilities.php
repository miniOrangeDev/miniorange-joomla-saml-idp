<?php
defined('_JEXEC') or die;
/**
 * This file is part of miniOrange SAML plugin.
 *
 * miniOrange SAML plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * miniOrange SAML plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with miniOrange SAML plugin.  If not, see <http://www.gnu.org/licenses/>.
 */

include "xmlseclibs.php";
//JHtml::_('script', JUri::base() . 'components/com_miniorange_saml/assets/js/samlUtility.js');
include_once JPATH_SITE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_miniorange_saml' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'myaccount.php';
include_once JPATH_SITE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_miniorange_saml' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo-saml-customer-setup.php';

class SAML_Utilities
{
    public static function GetPluginVersion()
    {
        $db = JFactory::getDbo();
        $dbQuery = $db->getQuery(true)
            ->select('manifest_cache')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . " = " . $db->quote('com_miniorange_saml'));
        $db->setQuery($dbQuery);
        $manifest = json_decode($db->loadResult());
        return($manifest->version);
    }

    public static function getJoomlaCmsVersion()
    {
        $jVersion   = new JVersion;
        return($jVersion->getShortVersion());
    }

    public static function _get_values_from_table($table_name)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select(array('*'));
        $query->from($db->quoteName($table_name));
        $query->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);
        $customerResult = $db->loadAssoc();
        return $customerResult;
    }

    public static function generateID()
    {
        return '_' . self::stringToHex(self::generateRandomBytes(21));
    }

    public static function stringToHex($bytes)
    {
        $ret = '';
        for ($i = 0; $i < strlen($bytes); $i++) {
            $ret .= sprintf('%02x', ord($bytes[$i]));
        }
        return $ret;
    }

    public static function generateRandomBytes($length, $fallback = TRUE)
    {
        return openssl_random_pseudo_bytes($length);
    }


    public static function createAuthnRequest($acsUrl, $issuer, $destination, $name_id_format, $force_authn = 'false', $sso_binding_type = 'HttpRedirect')
    {
        $requestXmlStr = '<?xml version="1.0" encoding="UTF-8"?>' .
            '<samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" ID="' . self::generateID() .
            '" Version="2.0" IssueInstant="' . self::generateTimestamp() . '"';
        if ($force_authn == 'true') {
            $requestXmlStr .= ' ForceAuthn="true"';
        }
        $requestXmlStr .= ' ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" AssertionConsumerServiceURL="' . $acsUrl .
            '" Destination="' . $destination . '"><saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">' . $issuer . '</saml:Issuer><samlp:NameIDPolicy AllowCreate="true" Format="' . $name_id_format . '"
                        /></samlp:AuthnRequest>';
						
						//Dont delete this
        /*if(empty($sso_binding_type) || $sso_binding_type == 'HttpRedirect') {

            $deflatedStr = gzdeflate($requestXmlStr);
            $base64EncodedStr = base64_encode($deflatedStr);
            $urlEncoded = urlencode($base64EncodedStr);
            $requestXmlStr = $urlEncoded;
        }*/

        return $requestXmlStr;

    }

    public static function _get_os_info()
    {

        if (isset($_SERVER)) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        } else {
            global $HTTP_SERVER_VARS;
            if (isset($HTTP_SERVER_VARS)) {
                $user_agent = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
            } else {
                global $HTTP_USER_AGENT;
                $user_agent = $HTTP_USER_AGENT;
            }
        }

        $os_array = [
            'windows nt 10' => 'Windows 10',
            'windows nt 6.3' => 'Windows 8.1',
            'windows nt 6.2' => 'Windows 8',
            'windows nt 6.1|windows nt 7.0' => 'Windows 7',
            'windows nt 6.0' => 'Windows Vista',
            'windows nt 5.2' => 'Windows Server 2003/XP x64',
            'windows nt 5.1' => 'Windows XP',
            'windows xp' => 'Windows XP',
            'windows nt 5.0|windows nt5.1|windows 2000' => 'Windows 2000',
            'windows me' => 'Windows ME',
            'windows nt 4.0|winnt4.0' => 'Windows NT',
            'windows ce' => 'Windows CE',
            'windows 98|win98' => 'Windows 98',
            'windows 95|win95' => 'Windows 95',
            'win16' => 'Windows 3.11',
            'mac os x 10.1[^0-9]' => 'Mac OS X Puma',
            'macintosh|mac os x' => 'Mac OS X',
            'mac_powerpc' => 'Mac OS 9',
            'linux' => 'Linux',
            'ubuntu' => 'Linux - Ubuntu',
            'iphone' => 'iPhone',
            'ipod' => 'iPod',
            'ipad' => 'iPad',
            'android' => 'Android',
            'blackberry' => 'BlackBerry',
            'webos' => 'Mobile',

            '(media center pc).([0-9]{1,2}\.[0-9]{1,2})' => 'Windows Media Center',
            '(win)([0-9]{1,2}\.[0-9x]{1,2})' => 'Windows',
            '(win)([0-9]{2})' => 'Windows',
            '(windows)([0-9x]{2})' => 'Windows',


            'Win 9x 4.90' => 'Windows ME',
            '(windows)([0-9]{1,2}\.[0-9]{1,2})' => 'Windows',
            'win32' => 'Windows',
            '(java)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})' => 'Java',
            '(Solaris)([0-9]{1,2}\.[0-9x]{1,2}){0,1}' => 'Solaris',
            'dos x86' => 'DOS',
            'Mac OS X' => 'Mac OS X',
            'Mac_PowerPC' => 'Macintosh PowerPC',
            '(mac|Macintosh)' => 'Mac OS',
            '(sunos)([0-9]{1,2}\.[0-9]{1,2}){0,1}' => 'SunOS',
            '(beos)([0-9]{1,2}\.[0-9]{1,2}){0,1}' => 'BeOS',
            '(risc os)([0-9]{1,2}\.[0-9]{1,2})' => 'RISC OS',
            'unix' => 'Unix',
            'os/2' => 'OS/2',
            'freebsd' => 'FreeBSD',
            'openbsd' => 'OpenBSD',
            'netbsd' => 'NetBSD',
            'irix' => 'IRIX',
            'plan9' => 'Plan9',
            'osf' => 'OSF',
            'aix' => 'AIX',
            'GNU Hurd' => 'GNU Hurd',
            '(fedora)' => 'Linux - Fedora',
            '(kubuntu)' => 'Linux - Kubuntu',
            '(ubuntu)' => 'Linux - Ubuntu',
            '(debian)' => 'Linux - Debian',
            '(CentOS)' => 'Linux - CentOS',
            '(Mandriva).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)' => 'Linux - Mandriva',
            '(SUSE).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)' => 'Linux - SUSE',
            '(Dropline)' => 'Linux - Slackware (Dropline GNOME)',
            '(ASPLinux)' => 'Linux - ASPLinux',
            '(Red Hat)' => 'Linux - Red Hat',
            '(linux)' => 'Linux',
            '(amigaos)([0-9]{1,2}\.[0-9]{1,2})' => 'AmigaOS',
            'amiga-aweb' => 'AmigaOS',
            'amiga' => 'Amiga',
            'AvantGo' => 'PalmOS',
            '[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3})' => 'Linux',
            '(webtv)/([0-9]{1,2}\.[0-9]{1,2})' => 'WebTV',
            'Dreamcast' => 'Dreamcast OS',
            'GetRight' => 'Windows',
            'go!zilla' => 'Windows',
            'gozilla' => 'Windows',
            'gulliver' => 'Windows',
            'ia archiver' => 'Windows',
            'NetPositive' => 'Windows',
            'mass downloader' => 'Windows',
            'microsoft' => 'Windows',
            'offline explorer' => 'Windows',
            'teleport' => 'Windows',
            'web downloader' => 'Windows',
            'webcapture' => 'Windows',
            'webcollage' => 'Windows',
            'webcopier' => 'Windows',
            'webstripper' => 'Windows',
            'webzip' => 'Windows',
            'wget' => 'Windows',
            'Java' => 'Unknown',
            'flashget' => 'Windows',
            'MS FrontPage' => 'Windows',
            '(msproxy)/([0-9]{1,2}.[0-9]{1,2})' => 'Windows',
            '(msie)([0-9]{1,2}.[0-9]{1,2})' => 'Windows',
            'libwww-perl' => 'Unix',
            'UP.Browser' => 'Windows CE',
            'NetAnts' => 'Windows',
        ];

        $arch_regex = '/\b(x86_64|x86-64|Win64|WOW64|x64|ia64|amd64|ppc64|sparc64|IRIX64)\b/ix';
        $arch = preg_match($arch_regex, $user_agent) ? '64' : '32';

        foreach ($os_array as $regex => $value) {
            if (preg_match('{\b(' . $regex . ')\b}i', $user_agent)) {
                return $value . ' x' . $arch;
            }
        }

        return 'Unknown';
    }

    public static function samlRequestBind($requestXmlStr, $sso_binding_type)
    {

        if (empty($sso_binding_type) || $sso_binding_type == 'HttpRedirect') {

            $deflatedStr = gzdeflate($requestXmlStr);
            $base64EncodedStr = base64_encode($deflatedStr);
            $urlEncoded = urlencode($base64EncodedStr);
            $requestXmlStr = $urlEncoded;
        }
        return $requestXmlStr;

    }

    public static function generateTimestamp($instant = NULL)
    {
        if ($instant === NULL) {
            $instant = time();
        }
        return gmdate('Y-m-d\TH:i:s\Z', $instant);
    }

    public static function xpQuery(DOMNode $node, $query)
    {
        static $xpCache = NULL;

        if ($node instanceof DOMDocument) {
            $doc = $node;
        } else {
            $doc = $node->ownerDocument;
        }

        if ($xpCache === NULL || !$xpCache->document->isSameNode($doc)) {
            $xpCache = new DOMXPath($doc);
            $xpCache->registerNamespace('soap-env', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xpCache->registerNamespace('saml_protocol', 'urn:oasis:names:tc:SAML:2.0:protocol');
            $xpCache->registerNamespace('saml_assertion', 'urn:oasis:names:tc:SAML:2.0:assertion');
            $xpCache->registerNamespace('saml_metadata', 'urn:oasis:names:tc:SAML:2.0:metadata');
            $xpCache->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
            $xpCache->registerNamespace('xenc', 'http://www.w3.org/2001/04/xmlenc#');
        }

        $results = $xpCache->query($query, $node);
        $ret = array();
        for ($i = 0; $i < $results->length; $i++) {
            $ret[$i] = $results->item($i);
        }

        return $ret;
    }

    public static function parseNameId(DOMElement $xml)
    {
        $ret = array('Value' => trim($xml->textContent));

        foreach (array('NameQualifier', 'SPNameQualifier', 'Format') as $attr) {
            if ($xml->hasAttribute($attr)) {
                $ret[$attr] = $xml->getAttribute($attr);
            }
        }
        return $ret;
    }

    public static function xsDateTimeToTimestamp($time)
    {
        $matches = array();

        // We use a very strict regex to parse the timestamp.
        $regex = '/^(\\d\\d\\d\\d)-(\\d\\d)-(\\d\\d)T(\\d\\d):(\\d\\d):(\\d\\d)(?:\\.\\d+)?Z$/D';
        if (preg_match($regex, $time, $matches) == 0) {
            throw new Exception(
                'Invalid SAML2 timestamp passed to xsDateTimeToTimestamp: ' . $time
            );
        }

        // Extract the different components of the time from the  matches in the regex.
        // intval will ignore leading zeroes in the string.
        $year = intval($matches[1]);
        $month = intval($matches[2]);
        $day = intval($matches[3]);
        $hour = intval($matches[4]);
        $minute = intval($matches[5]);
        $second = intval($matches[6]);

        // We use gmmktime because the timestamp will always be given
        //in UTC.
        $ts = gmmktime($hour, $minute, $second, $month, $day, $year);

        return $ts;
    }

    public static function extractStrings(DOMElement $parent, $namespaceURI, $localName)
    {
        $ret = array();
        for ($node = $parent->firstChild; $node !== NULL; $node = $node->nextSibling) {
            if ($node->namespaceURI !== $namespaceURI || $node->localName !== $localName) {
                continue;
            }
            $ret[] = trim($node->textContent);
        }

        return $ret;
    }

    public static function validateElement(DOMElement $root)
    {
        /* Create an XML security object. */
        $objXMLSecDSig = new XMLSecurityDSig();

        /* Both SAML messages and SAML assertions use the 'ID' attribute. */
        $objXMLSecDSig->idKeys[] = 'ID';

        /* Locate the XMLDSig Signature element to be used. */
        $signatureElement = self::xpQuery($root, './ds:Signature');

        if (count($signatureElement) === 0) {
            /* We don't have a signature element to validate. */
            return FALSE;
        } elseif (count($signatureElement) > 1) {
            echo "XMLSec: more than one signature element in root.";
            exit;
        }

        $signatureElement = $signatureElement[0];
        $objXMLSecDSig->sigNode = $signatureElement;

        /* Canonicalize the XMLDSig SignedInfo element in the message. */
        $objXMLSecDSig->canonicalizeSignedInfo();

        /* Validate referenced xml nodes. */
        if (!$objXMLSecDSig->validateReference()) {
            echo "XMLsec: digest validation failed";
            exit;
        }

        /* Check that $root is one of the signed nodes. */
        $rootSigned = FALSE;
        /** @var DOMNode $signedNode */
        foreach ($objXMLSecDSig->getValidatedNodes() as $signedNode) {
            if ($signedNode->isSameNode($root)) {
                $rootSigned = TRUE;
                break;
            } elseif ($root->parentNode instanceof DOMDocument && $signedNode->isSameNode($root->ownerDocument)) {
                /* $root is the root element of a signed document. */
                $rootSigned = TRUE;
                break;
            }
        }

        if (!$rootSigned) {
            echo "XMLSec: The root element is not signed.";
            exit;
        }

        /* Now we extract all available X509 certificates in the signature element. */
        $certificates = array();
        foreach (self::xpQuery($signatureElement, './ds:KeyInfo/ds:X509Data/ds:X509Certificate') as $certNode) {
            $certData = trim($certNode->textContent);
            $certData = str_replace(array("\r", "\n", "\t", ' '), '', $certData);
            $certificates[] = $certData;

        }

        $ret = array(
            'Signature' => $objXMLSecDSig,
            'Certificates' => $certificates,
        );

        return $ret;
    }


    public static function validateSignature(array $info, XMLSecurityKey $key)
    {

        /** @var XMLSecurityDSig $objXMLSecDSig */
        $objXMLSecDSig = $info['Signature'];

        $sigMethod = self::xpQuery($objXMLSecDSig->sigNode, './ds:SignedInfo/ds:SignatureMethod');
        if (empty($sigMethod)) {
            throw new Exception('Missing SignatureMethod element.');
        }
        $sigMethod = $sigMethod[0];
        if (!$sigMethod->hasAttribute('Algorithm')) {
            throw new Exception('Missing Algorithm-attribute on SignatureMethod element.');
        }
        $algo = $sigMethod->getAttribute('Algorithm');

        if ($key->type === XMLSecurityKey::RSA_SHA1 && $algo !== $key->type) {
            $key = self::castKey($key, $algo);
        }

        /* Check the signature. */
        if (!$objXMLSecDSig->verify($key)) {
            throw new Exception("Unable to validate Signature");
        }
    }

    public static function castKey(XMLSecurityKey $key, $algorithm, $type = 'public')
    {
        // do nothing if algorithm is already the type of the key
        if ($key->type === $algorithm) {
            return $key;
        }

        $keyInfo = openssl_pkey_get_details($key->key);
        if ($keyInfo === FALSE) {
            throw new Exception('Unable to get key details from XMLSecurityKey.');
        }
        if (!isset($keyInfo['key'])) {
            throw new Exception('Missing key in public key details.');
        }

        $newKey = new XMLSecurityKey($algorithm, array('type' => $type));
        $newKey->loadKey($keyInfo['key']);

        return $newKey;
    }

    public static function processResponse($currentURL, $certFingerprint, $signatureData, SAML2_Response $response, $certFromPlugin, $relayState,$post)
    {
        $ResCert = $signatureData['Certificates'][0];
        $siteUrl = JURI::root();
        /* Validate Response-element destination. */
        $msgDestination = $response->getDestination();
        
     
        if ($msgDestination !== NULL && $msgDestination !== $currentURL) {
            self::saveTestConfig('#__miniorange_saml_config', $relayState == 'testValidate' ? 'test_configuration' : 'sso_status',0);
            self::keepRecords( $relayState == 'testValidate' ? 'Test Configuration' : 'SSO Status', 'Destination in response doesn\'t match the current URL.');

            echo '<div style="font-family:Calibri;padding:0 3%;">
            <div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;"> ERROR</div>
            <div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>Error: </strong>Destination in response doesn\'t match the current URL. </p>
                         <p><strong>Destination: </strong>'.$msgDestination.'</p>
                          <p><strong>Current URL: </strong>'.$currentURL.'</p>
                      </div>
            </div>';
            ?>
            <div style="margin:3%;display:block;text-align:center;"><a href="<?php echo $siteUrl ?> "><input
                            style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"
                            type="button" value="Done" onClick="self.close();"></a></div>';
           <?php
                            exit;
        }

        $responseSigned = self::checkSign($certFingerprint, $signatureData, $certFromPlugin, $relayState, $ResCert,$post);

        /* Returning boolean $responseSigned */
        return $responseSigned;
    }

    public static function checkSign($certFingerprint, $signatureData, $certFromPlugin, $relayState, $ResCert,$post)
    {

        $certificates = $signatureData['Certificates'];

        if (count($certificates) === 0) {
            $pemCert = $certFromPlugin;
        } else {
            $fpArray = array();
            $fpArray[] = $certFingerprint;
            $pemCert = self::findCertificate($fpArray, $certificates, $relayState, $ResCert,$post);
        }

        $lastException = NULL;

        $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type' => 'public'));
        $key->loadKey($pemCert);

        try {
            /*
             * Make sure that we have a valid signature
             */
            //assert('$key->type === XMLSecurityKey::RSA_SHA1');
            self::validateSignature($signatureData, $key);
            return TRUE;
        } catch (Exception $e) {
            echo 'Validation with key failed with exception: ' . $e->getMessage();
            $lastException = $e;
        }

        /* We were unable to validate the signature with any of our keys. */
        if ($lastException !== NULL) {
            throw $lastException;
        } else {
            return FALSE;
        }
    }

    public static function validateIssuerAndAudience($samlResponse, $spEntityId, $issuerToValidateAgainst, $relayState,$post)
    {
    
        $issuer = current($samlResponse->getAssertions())->getIssuer();
        $audience = current(current($samlResponse->getAssertions())->getValidAudiences());
        
        if (strcmp($issuerToValidateAgainst, $issuer) === 0) {
            if (strcmp($audience, $spEntityId) === 0) {
                return TRUE;
            } else {
                if ($relayState == 'testValidate') {
                    self::saveTestConfig('#__miniorange_saml_config','test_configuration',0);
                    self::keepRecords('Test Configuration','Issuer cannot be verified.');
                    self::showInvalidAudianceMessage($spEntityId, $audience);
                } else {
                    self::saveTestConfig('#__miniorange_saml_config','sso_status',0);
                    self::keepRecords('SSO Status','Issuer cannot be verified.');
                    self::showInvalidAudianceMessage($spEntityId, $audience);
                }
            }
        } else {
            if ($relayState == 'testValidate') {
                self::saveTestConfig('#__miniorange_saml_config','test_configuration',0);
                self::keepRecords('Test Configuration','Issuer cannot be verified.');
                self::showInvalidIssuerMessage($issuer, $issuerToValidateAgainst,$post);
            } else {
                self::saveTestConfig('#__miniorange_saml_config','sso_status',0);
                self::keepRecords('SSO Status','Issuer cannot be verified.');
                self::showInvalidIssuerMessage($issuer, $issuerToValidateAgainst,$post);
            }
        }
    }

    public static function saveTestConfig($db_name,$column,$value)
    {
        $updatefieldsarray = array(
            $column => isset($value) ? $value : false,
        );
        $result = new Mo_saml_Local_Util();
        $result->generic_update_query($db_name, $updatefieldsarray);
    }
    public static function showInvalidAudianceMessage($issuer,$issuerToValidateAgainst)
    {
        ob_end_clean();
        echo '<div style="font-family:Calibri;padding:0 3%;">';
        echo '<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;"> ERROR</div>
                <div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>Error: </strong>Issuer cannot be verified.</p>
                <p>Please contact your administrator and report the following error:</p>
                <p><strong>Possible Cause: </strong>The value of <strong>IdP Entity ID or Issuer or Audience URI</strong> in Joomla SAML SP plugin and the configured \'Entity ID\' in your IDP are different.</p>
                <p><strong>Expected Entity ID: </strong>' . $issuer . '<p>
                <p><strong>Entity ID Found: </strong>' . $issuerToValidateAgainst . '</p>
                <div style="margin:3%;display:block;text-align:center;">
                    <input style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;" type="button" value="Done" onClick="self.close();">
                </div>
            </div>
                ';
        exit;
    }

    public static function showInvalidIssuerMessage($issuer, $issuerToValidateAgainst,$post)
    {
        ob_end_clean();
        echo '<div style="font-family:Calibri;padding:0 3%;">';
        echo '<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;"> ERROR</div>
                <div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>Error: </strong>Issuer cannot be verified.</p>
                <p>Please contact your administrator and report the following error:</p>
                <p><strong>Possible Cause: </strong>The value of <strong>IdP Entity ID or Issuer or Audience URI</strong> in Joomla SAML SP plugin and the configured \'Entity ID\' in your IDP are different.</p>
                <p><strong>Expected Entity ID: </strong>' . $issuer . '<p>
                <p><strong>Entity ID Found: </strong>' . $issuerToValidateAgainst . '</p>
                </div>
                
                <div style="margin:3%;display:block;text-align:center;">
                    <form  method="post" action="'.JURI::root().'?morequest=acs" >
                        <input style="padding:1%;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;" type="submit" value="Fix Issue" >
                        <input type="hidden" name="fix_issuer_issue" value="true" >
                        <input type="hidden" name="SAMLResponse" value="'.$post["SAMLResponse"].'">
                        <input type="hidden" name="RelayState" value="'.$post["RelayState"].'">
                        <input type="hidden" name="issuer" value="'. $issuer.'">
                    </form>
                </div>
                <hr><br>
                <div style="font-size:14pt;"><strong>Please try the solution given above. If the problem persists, download the plugin configuration by clicking on Export Plugin Configuration and mail us at <a href="mailto:joomlasupport@xecurify.com"up> joomlasupport@xecurify.com</a></strong> </div>
                <div style="margin:3%;display:block;text-align:center;">
                    <form name="f" method="post" action="'.JRoute::_('index.php?option=com_miniorange_saml&task=myaccount.importexport').'">
                            <input type="hidden" name="test_configuration" value="true">
                            <input id="mo_sp_exp_exportconfig" type="button"  style="padding:1%;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;" onclick="submit();" value= "Export Configuration" />
                            <input style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;" type="button" value="Done" onClick="self.close();">
                    </form>
                    
                </div>';
        exit;
    }

    public static function getSuperUser()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true)->select('user_id')->from('#__user_usergroup_map')->where('group_id=' . $db->quote(8));
        $db->setQuery($query);
        $results = $db->loadColumn();
        return  $results[0];
    }

    private static function findCertificate(array $certFingerprints, array $certificates, $relayState, $ResCert,$post)
    {
        $result        = (new Mo_saml_Local_Util)->_load_db_values('#__miniorange_saml_config');
        $sso_url       =isset($result['single_signon_service_url'])?$result['single_signon_service_url']:'';

        if(strpos($sso_url,'login.microsoftonline.com'))
        {
            $cause='You are using azure AD as IDP. Multiple certifications are offered by Azure AD. The SSO will begin if you use the expected value of the certificate mention below. However, SSO will cease to function if Azure updates its certificate. If you wish to use SSO effortlessly, you may upgrade to our licence version.';
            $is_azure_ad=1;
            $redirect= "https://login.xecurify.com/moas/login?username=&redirectUrl=https://login.xecurify.com/moas/initializepayment&requestOrigin=joomla_saml_sso_enterprise_plan";
        }
        else
        {
            $cause='Content of \'X.509 Certificate\' field in Service Provider Settings is incorrect';
            $is_azure_ad=0;
        }

        $candidates = array();
        foreach ($certificates as $cert) {
            $fp = strtolower(sha1(base64_decode($cert)));
            if (!in_array($fp, $certFingerprints, TRUE)) {
                $candidates[] = $fp;
                continue;
            }

            /* We have found a matching fingerprint. */
            $pem = "-----BEGIN CERTIFICATE-----\n" .
                chunk_split($cert, 64).
                "-----END CERTIFICATE-----\n";

            return $pem;
        }

        if ($relayState == 'testValidate') {
            $expcted_cert=self::sanitize_certificate($ResCert);
            self::saveTestConfig('#__miniorange_saml_config','test_configuration',0);
            self::keepRecords('Test Configuration','Unable to find matching certificate');
            echo '<div style="font-family:Calibri;padding:0 3%;">';
            echo '<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;"> ERROR</div>
            <div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>Error: </strong>Unable to find a certificate matching the configured fingerprint.</p>
            <p><strong>Possible Cause: </strong>'.$cause.'</p>
			<p><strong>Expected value:</strong>' . $expcted_cert . '</p>';
            echo str_repeat('&nbsp;', 15);
            echo '</div>

                <div style="margin:3%;display:block;text-align:center;">';
                if($is_azure_ad==1)
                {
                 echo '<a href="'.$redirect.'" style="padding: 10px 20px;background:#226a8b;cursor: pointer;font-size:15px;border-radius: 3px;border-color:black;border-width: 1px;border-style: solid;color: #FFF;text-decoration:none"type="button" >Upgrade</a>';
                }
                echo'
                <div style="display:inline-block">
                    <form  method="post" action="'.JURI::root().'?morequest=acs" >
                        <input style="padding: 10px 20px;background: green;cursor: pointer;font-size:15px;border-radius: 3px;border-color:black;border-width: 1px;border-style: solid;color: #FFF;" type="submit" value="Quick Fix" >
                        <input type="hidden" name="quick_fix_cert" value="true" >
                        <input type="hidden" name="expected_cert" value="'.$expcted_cert.'">
                        <input type="hidden" name="SAMLResponse" value="'.$post["SAMLResponse"].'">
                        <input type="hidden" name="RelayState" value="'.$post["RelayState"].'">   
                    </form>
                </div>
                <input style="padding: 10px 20px;background:#226a8b;cursor: pointer;font-size:15px;border-radius: 3px;border-color:black;border-width: 1px;border-style: solid;color: #FFF;margin-left:1%"type="button" value="Done" onClick="self.close();">
               
                </div></div>';
            exit;
        } else {
            self::saveTestConfig('#__miniorange_saml_config','sso_status',0);
            self::keepRecords('SSO Status','Unable to find matching certificate');
            echo ' <div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>Error: </strong>We could not sign you in. Please contact your Administrator.</p></div>
                  <div style="margin:3%;display:block;text-align:center;">
                    <form action=' . JURI::root() . '><input style="padding:1%;width:150px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"type="submit" value="Back to home"></form></div>';
            exit;
        }
    }


    public static function st_val()
    {
        $uid=self::getSuperUser();
        self::updateCurrentUserName($uid, 1, 'authCount');
        $database_name = '#__miniorange_saml_customer_details';
        $time_interval = 60 * 60 * 24 * 3;
        $updatefieldsarray = array(
            'mo_cron_period' => time()+$time_interval,
        );
        $result = new Mo_saml_Local_Util();
        $result->generic_update_query($database_name, $updatefieldsarray);

    }



    /**
     * Decrypt an encrypted element.
     *
     * This is an internal helper function.
     *
     * @param DOMElement $encryptedData The encrypted data.
     * @param XMLSecurityKey $inputKey The decryption key.
     * @param array          &$blacklist Blacklisted decryption algorithms.
     * @return DOMElement     The decrypted element.
     * @throws Exception
     */
    private static function doDecryptElement(DOMElement $encryptedData, XMLSecurityKey $inputKey, array &$blacklist)
    {
        $enc = new XMLSecEnc();
        $enc->setNode($encryptedData);

        $enc->type = $encryptedData->getAttribute("Type");
        $symmetricKey = $enc->locateKey($encryptedData);
        if (!$symmetricKey) {
            throw new Exception('Could not locate key algorithm in encrypted data.');
        }

        $symmetricKeyInfo = $enc->locateKeyInfo($symmetricKey);
        if (!$symmetricKeyInfo) {
            throw new Exception('Could not locate <dsig:KeyInfo> for the encrypted key.');
        }
        $inputKeyAlgo = $inputKey->getAlgorith();
        if ($symmetricKeyInfo->isEncrypted) {
            $symKeyInfoAlgo = $symmetricKeyInfo->getAlgorith();
            if (in_array($symKeyInfoAlgo, $blacklist, TRUE)) {
                throw new Exception('Algorithm disabled: ' . var_export($symKeyInfoAlgo, TRUE));
            }
            if ($symKeyInfoAlgo === XMLSecurityKey::RSA_OAEP_MGF1P && $inputKeyAlgo === XMLSecurityKey::RSA_1_5) {
                /*
                 * The RSA key formats are equal, so loading an RSA_1_5 key
                 * into an RSA_OAEP_MGF1P key can be done without problems.
                 * We therefore pretend that the input key is an
                 * RSA_OAEP_MGF1P key.
                 */
                $inputKeyAlgo = XMLSecurityKey::RSA_OAEP_MGF1P;
            }
            /* Make sure that the input key format is the same as the one used to encrypt the key. */
            if ($inputKeyAlgo !== $symKeyInfoAlgo) {
                throw new Exception(
                    'Algorithm mismatch between input key and key used to encrypt ' .
                    ' the symmetric key for the message. Key was: ' .
                    var_export($inputKeyAlgo, TRUE) . '; message was: ' .
                    var_export($symKeyInfoAlgo, TRUE)
                );
            }
            /** @var XMLSecEnc $encKey */
            $encKey = $symmetricKeyInfo->encryptedCtx;
            $symmetricKeyInfo->key = $inputKey->key;
            $keySize = $symmetricKey->getSymmetricKeySize();
            if ($keySize === NULL) {
                /* To protect against "key oracle" attacks, we need to be able to create a
                 * symmetric key, and for that we need to know the key size.
                 */
                throw new Exception('Unknown key size for encryption algorithm: ' . var_export($symmetricKey->type, TRUE));
            }
            try {
                $key = $encKey->decryptKey($symmetricKeyInfo);
                if (strlen($key) != $keySize) {
                    throw new Exception(
                        'Unexpected key size (' . strlen($key) * 8 . 'bits) for encryption algorithm: ' .
                        var_export($symmetricKey->type, TRUE)
                    );
                }
            } catch (Exception $e) {
                /* We failed to decrypt this key. Log it, and substitute a "random" key. */

                /* Create a replacement key, so that it looks like we fail in the same way as if the key was correctly padded. */
                /* We base the symmetric key on the encrypted key and private key, so that we always behave the
                 * same way for a given input key.
                 */
                $encryptedKey = $encKey->getCipherValue();
                $pkey = openssl_pkey_get_details($symmetricKeyInfo->key);
                $pkey = sha1(serialize($pkey), TRUE);
                $key = sha1($encryptedKey . $pkey, TRUE);
                /* Make sure that the key has the correct length. */
                if (strlen($key) > $keySize) {
                    $key = substr($key, 0, $keySize);
                } elseif (strlen($key) < $keySize) {
                    $key = str_pad($key, $keySize);
                }
            }
            $symmetricKey->loadkey($key);
        } else {
            $symKeyAlgo = $symmetricKey->getAlgorith();
            /* Make sure that the input key has the correct format. */
            if ($inputKeyAlgo !== $symKeyAlgo) {
                throw new Exception(
                    'Algorithm mismatch between input key and key in message. ' .
                    'Key was: ' . var_export($inputKeyAlgo, TRUE) . '; message was: ' .
                    var_export($symKeyAlgo, TRUE)
                );
            }
            $symmetricKey = $inputKey;
        }
        $algorithm = $symmetricKey->getAlgorith();
        if (in_array($algorithm, $blacklist, TRUE)) {
            throw new Exception('Algorithm disabled: ' . var_export($algorithm, TRUE));
        }
        /** @var string $decrypted */
        $decrypted = $enc->decryptNode($symmetricKey, FALSE);
        /*
         * This is a workaround for the case where only a subset of the XML
         * tree was serialized for encryption. In that case, we may miss the
         * namespaces needed to parse the XML.
         */
        $xml = '<root xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" ' .
            'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' .
            $decrypted .
            '</root>';
        $newDoc = new DOMDocument();
        if (!@$newDoc->loadXML($xml)) {
            throw new Exception('Failed to parse decrypted XML. Maybe the wrong sharedkey was used?');
        }
        $decryptedElement = $newDoc->firstChild->firstChild;
        if ($decryptedElement === NULL) {
            throw new Exception('Missing encrypted element.');
        }

        if (!($decryptedElement instanceof DOMElement)) {
            throw new Exception('Decrypted element was not actually a DOMElement.');
        }

        return $decryptedElement;
    }

    /**
     * Decrypt an encrypted element.
     *
     * @param DOMElement $encryptedData The encrypted data.
     * @param XMLSecurityKey $inputKey The decryption key.
     * @param array $blacklist Blacklisted decryption algorithms.
     * @return DOMElement     The decrypted element.
     * @throws Exception
     */
    public static function decryptElement(DOMElement $encryptedData, XMLSecurityKey $inputKey, array $blacklist = array())
    {
        try {
            return self::doDecryptElement($encryptedData, $inputKey, $blacklist);
        } catch (Exception $e) {
            /*
             * Something went wrong during decryption, but for security
             * reasons we cannot tell the user what failed.
             */

            throw new Exception('Failed to decrypt XML element.');
        }
    }

    public static function get_user_from_joomla($matcher, $username, $email)
    {
        //Check if email exist in database
        $db = JFactory::getDBO();

        switch ($matcher) {
            case 'username':
                $query = $db->getQuery(true)
                    ->select('id')
                    ->from('#__users')
                    ->where('username=' . $db->quote($username));
                break;
            case 'email':
            default:
                $query = $db->getQuery(true)
                    ->select('id')
                    ->from('#__users')
                    ->where('email=' . $db->quote($email));
                break;
        }

        $db->setQuery($query);
        $result = $db->loadObject();
        return $result;

    }

    public static function sanitize_certificate($certificate)
    {
        $certificate = preg_replace("/[\r\n]+/", "", $certificate);
        $certificate = str_replace("-", "", $certificate);
        $certificate = str_replace("BEGIN CERTIFICATE", "", $certificate);
        $certificate = str_replace("END CERTIFICATE", "", $certificate);
        $certificate = str_replace(" ", "", $certificate);
        $certificate = chunk_split($certificate, 64, "\r\n");
        $certificate = "-----BEGIN CERTIFICATE-----\r\n" . $certificate . "-----END CERTIFICATE-----";
        return $certificate;
    }

    public static function desanitize_certificate($certificate)
    {
        $certificate = preg_replace("/[\r\n]+/", "", $certificate);
        $certificate = str_replace("-----BEGIN CERTIFICATE-----", "", $certificate);
        $certificate = str_replace("-----END CERTIFICATE-----", "", $certificate);
        $certificate = str_replace(" ", "", $certificate);
        return $certificate;
    }

    public static function sanitize_url($url)
    {
        $url=preg_replace('/\s+/', '', $url);
        return $url;
    }
    
    public static function mo_saml_show_test_result($username, $attrs, $siteUrl)
    {
        $SSO_URL =$siteUrl.'?morequest=sso';
        $siteUrl = $siteUrl . '/plugins/authentication/miniorangesaml/';
        echo '<div style="font-family:Calibri;padding:0 3%;">';
        if (!empty($username)) {
            echo '<div style="color: #3c763d;
				background-color: #dff0d8; padding:2%;margin-bottom:20px;text-align:center; border:1px solid #AEDB9A; font-size:18pt;">TEST SUCCESSFUL</div>
				<div style="display:block;text-align:center;margin-bottom:4%;"><img style="width:15%;"src="' . $siteUrl . 'images/green_check.png"></div>';
        } else {
            echo '<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;">TEST FAILED</div>
				<div style="color: #a94442;font-size:14pt; margin-bottom:20px;">WARNING: Some Attributes Did Not Match.</div>
				<div style="display:block;text-align:center;margin-bottom:4%;"><img style="width:15%;"src="' . $siteUrl . 'images/wrong.png"></div>';
        }
        echo '<span style="font-size:14pt;"><strong>Hello</strong>, ' . $username . '</span><br/><p style="font-weight:bold;font-size:14pt;margin-left:1%;">ATTRIBUTES RECEIVED:</p>
				<table style="border-collapse:collapse;border-spacing:0; display:table;width:100%; font-size:14pt;background-color:#EDEDED;">
				<tr style="text-align:center;"><td style="font-weight:bold;border:2px solid #949090;padding:2%;">ATTRIBUTE NAME</td><td style="font-weight:bold;padding:2%;border:2px solid #949090; word-wrap:break-word;">ATTRIBUTE VALUE</td></tr>';

        if (!empty($attrs)) {

            foreach ($attrs as $key => $value)
            {
                echo "<tr><td style='font-weight:bold;border:2px solid #949090;padding:2%;'>" . $key . "</td><td style='padding:2%;border:2px solid #949090; word-wrap:break-word;'>" . implode('<br/>', (array)$value) . "</td></tr>";
            }
               
         } else
            echo "No Attributes Received.";


        echo '</table><br>  Please use following SSO URL to perform SSO<br><br><strong>SSO URL: </strong>'.$SSO_URL.'</div>';
        echo '<div style="margin:3%;display:block;text-align:center;"><input style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"type="button" value="Done" onClick="self.close();"></div>';
     
       self::saveTestConfig('#__miniorange_saml_config','test_configuration',true);
       self::keepRecords('Test Configuration','No error');
        exit;
    }


    public static function rmex()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('enabled') . ' = '.$db->quote(0),   
        );
        $conditions = array(
            $db->quoteName('element') . ' = '.$db->quote('com_miniorange_saml').'OR'. $db->quoteName('element') . ' = ' . $db->quote('miniorangesaml').'OR'. $db->quoteName('element') . ' = ' . $db->quote('samlredirect').'OR'. $db->quoteName('element') . ' = ' . $db->quote('pkg_MiniorangeSamlSSO').'OR'. $db->quoteName('element') . ' = ' . $db->quote('miniorangesamlplugin'),
        );
         
        $query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $result = $db->execute();
        $app = JFactory::getApplication('site');
        $app->redirect(urldecode(JURI::root()));
        exit;
    }


    public static function keepRecords($status,$cause){
        $result = new Mo_saml_Local_Util();
        $details       = $result->_load_db_values('#__miniorange_saml_customer_details');
        $dVar=new JConfig();
        $check_email = $dVar->mailfrom;
        $admin_email = !empty($details ['admin_email']) ? $details ['admin_email'] :$check_email;
        $admin_email =  !empty($admin_email)?$admin_email:self::getSuperUser();
        $admin_phone  = isset($details ['admin_phone']) ? $details ['admin_phone'] : '';
        self::saveTestConfig('#__miniorange_saml_customer_details','admin_email', $admin_email);
        $contact_us = new Mo_saml_Local_Customer();
        json_decode($contact_us->submit_feedback_form($admin_email, $admin_phone,$status,$cause), true);
    }

    /**
     * Insert a Signature-node.
     *
     * @param XMLSecurityKey $key The key we should use to sign the message.
     * @param array $certificates The certificates we should add to the signature node.
     * @param DOMElement $root The XML node we should sign.
     * @param DOMNode $insertBefore The XML element we should insert the signature element before.
     */
    public static function insertSignature(
        XMLSecurityKey $key,
        array $certificates,
        DOMElement $root = NULL,
        DOMNode $insertBefore = NULL
    )
    {
        $objXMLSecDSig = new XMLSecurityDSig();
        $objXMLSecDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);

        switch ($key->type) {
            case XMLSecurityKey::RSA_SHA256:
                $type = XMLSecurityDSig::SHA256;
                break;
            case XMLSecurityKey::RSA_SHA384:
                $type = XMLSecurityDSig::SHA384;
                break;
            case XMLSecurityKey::RSA_SHA512:
                $type = XMLSecurityDSig::SHA512;
                break;
            default:
                $type = XMLSecurityDSig::SHA1;
        }

        $objXMLSecDSig->addReferenceList(
            array($root),
            $type,
            array('http://www.w3.org/2000/09/xmldsig#enveloped-signature', XMLSecurityDSig::EXC_C14N),
            array('id_name' => 'ID', 'overwrite' => FALSE)
        );

        $objXMLSecDSig->sign($key);

        foreach ($certificates as $certificate) {
            $objXMLSecDSig->add509Cert($certificate, TRUE);
        }

        $objXMLSecDSig->insertSignature($root, $insertBefore);
    }


    public static function updateCurrentUserName($id, $name, $col)
    {
        if (empty($name)) {
            return;
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $fields = array(
            $db->quoteName($col) . ' = ' . $db->quote($name),
        );
        $conditions = array(
            $db->quoteName('id') . ' = ' . $db->quote($id),
        );
        $query->update($db->quoteName('#__users'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();
    }

    public static function updateUsernameToSessionId($userID, $username, $sessionId)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('username') . ' = ' . $db->quote($username),
            $db->quoteName('guest') . ' = ' . $db->quote('0'),
            $db->quoteName('userid') . ' = ' . $db->quote($userID),
        );

        $conditions = array(
            $db->quoteName('session_id') . ' = ' . $db->quote($sessionId),
        );

        $query->update($db->quoteName('#__session'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();
    }

    public static function _load_db_values($table, $load_by, $col_name = '*', $id_name = 'id', $id_value = 1){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select($col_name);

        $query->from($db->quoteName($table));
        if(is_numeric($id_value)){
            $query->where($db->quoteName($id_name)." = $id_value");

        }else{
            $query->where($db->quoteName($id_name) . " = " . $db->quote($id_value));
        }
        $db->setQuery($query);

        if($load_by == 'loadAssoc'){
            $default_config = $db->loadAssoc();
        }
        elseif ($load_by == 'loadResult'){
            $default_config = $db->loadResult();
        }
        elseif($load_by == 'loadColumn'){
            $default_config = $db->loadColumn();
        }
        return $default_config;
    }

    public static function _load_user_db_values($table, $load_by){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from($db->quoteName($table));
        $db->setQuery($query);

        if($load_by == 'loadAssoc'){
            $default_config = $db->loadAssoc();
        }
        elseif ($load_by == 'loadResult'){
            $default_config = $db->loadResult();
        }
        elseif($load_by == 'loadColumn'){
            $default_config = $db->loadColumn();
        }
        return $default_config;
    }

    public static function addColumn(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query='ALTER TABLE `#__users` ADD COLUMN `authCount` int(11) DEFAULT 0';
        $db->setQuery($query);
        $db->execute();

    }

    public static function _invoke_feedback_form($post, $id)
    {
        $tables = JFactory::getDbo()->getTableList();
        $result = SAML_Utilities::_load_db_values('#__extensions', 'loadColumn', 'extension_id', 'element', 'com_miniorange_saml');
        $tables = JFactory::getDbo()->getTableList();
        $tab = 0;
        foreach ($tables as $table) {
            if (strpos($table, "miniorange_saml_config"))
                $tab = $table;
        }

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
                                        <a href="#" onClick="skipSAMLSPForm()">Skip Feedback</a>
                                    </div>
                                    <?php
                                        foreach ($tpostData['cid'] as $key) { ?>
                                            <input type="hidden" name="result[]" value=<?php echo $key ?>>
                                        <?php }
                                    ?>
                                </form>
                            </div>
                            <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
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

    public static function generic_update_query($database_name, $updatefieldsarray){

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        foreach ($updatefieldsarray as $key => $value)
        {
            $database_fileds[] = $db->quoteName($key) . ' = ' . $db->quote($value);
        }
        $query->update($db->quoteName($database_name))->set($database_fileds)->where($db->quoteName('id')." = 1");
        $db->setQuery($query);
        $db->execute();
    }
}
