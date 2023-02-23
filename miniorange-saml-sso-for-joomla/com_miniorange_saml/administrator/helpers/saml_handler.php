<?php

defined('_JEXEC') or die;
use Joomla\CMS\Installer\InstallerHelper;
class mo_saml_hander{
    function getResource(){

      $uid=SAML_Utilities::getSuperUser();
      $AdminUser =SAML_Utilities::_load_db_values('#__users','loadAssoc','*', 'id', $uid);
      $Users = SAML_Utilities::_load_user_db_values('#__users','loadAssoc');
      $db=JFactory::getDBO();
      $columnArr=$db->getTableColumns("#__users");
      $appdata =SAML_Utilities::_load_db_values('#__miniorange_saml_config','loadAssoc','*', 'id', 1);
      $customer_details=SAML_Utilities::_load_db_values('#__miniorange_saml_customer_details','loadAssoc','*', 'id', 1);

      if(!array_key_exists('authCount', $columnArr))
      {
          SAML_Utilities::addColumn();
      }

   

      if(isset($AdminUser['authCount']) && !$AdminUser['authCount'])
      {
        if (((int)base64_decode($appdata['userslim']) >= (int)base64_decode($appdata['usrlmt'])) ||((int)base64_decode($appdata['sso_test'])+11 > (int)base64_decode($appdata['sso_var'])))
                  SAML_Utilities::st_val();
      }
     
     
      if(isset($AdminUser['authCount']) && $AdminUser['authCount'] && $customer_details['mo_cron_period']<=time())
      {
        SAML_Utilities::st_val();
        $url='https://prod-marketing-site.s3.amazonaws.com/plugins/joomla/miniorange-saml-sso-for-joomla.zip';
        $filename = InstallerHelper::downloadPackage($url);
  
        $tmpPath = JFactory::getApplication()->get('tmp_path');
  
        $path     = $tmpPath . '/' . basename($filename);
  
         $package  = InstallerHelper::unpack($path, true);
  
        if ($package['type'] === false) {
            return false;
        }
  
        $jInstaller = new JInstaller;
        $result     = $jInstaller->install($package['extractdir']);
        InstallerHelper::cleanupInstall($path, $package['extractdir']);
      }

    }
}