<?php
/**
 * WHMCS SDK Sample Provisioning Module
 *
 * Provisioning Modules, also referred to as Product or Server Modules, allow
 * you to create modules that allow for the provisioning and management of
 * products and services in WHMCS.
 *
 * This sample file demonstrates how a provisioning module for WHMCS should be
 * structured and exercises all supported functionality.
 *
 * Provisioning Modules are stored in the /modules/servers/ directory. The
 * module name you choose must be unique, and should be all lowercase,
 * containing only letters & numbers, always starting with a letter.
 *
 * Within the module itself, all functions must be prefixed with the module
 * filename, followed by an underscore, and then the function name. For this
 * example file, the filename is "provisioningmodule" and therefore all
 * functions begin "budgetvm_".
 *
 * If your module or third party API does not support a given function, you
 * should not define that function within your module. Only the _ConfigOptions
 * function is required.
 *
 * For more information, please refer to the online documentation.
 *
 * @see http://docs.whmcs.com/Provisioning_Module_Developer_Docs
 *
 * @copyright Copyright (c) WHMCS Limited 2015
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

if (!defined("WHMCS")) {
  die("This file cannot be accessed directly");
}
require(dirname(__FILE__) . '/lib/budgetvm.class.php');
// Require any libraries needed for the module to function.
// require_once __DIR__ . '/path/to/library/loader.php';
//
// Also, perform any initialization required by the service's library.

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related abilities and
 * settings.
 *
 * @see http://docs.whmcs.com/Provisioning_Module_Meta_Data_Parameters
 *
 * @return array
 */
function budgetvm_MetaData()
{
  return array(
    'DisplayName' => 'BudgetVM Management Module',
    'APIVersion' => '1.1', // Use API Version 1.1
    'RequiresServer' => true, // Set true if module requires a server to work
    'DefaultNonSSLPort' => '80', // Default Non-SSL Connection Port
    'DefaultSSLPort' => '443', // Default SSL Connection Port
    'ServiceSingleSignOnLabel' => 'Login to Panel as User',
    'AdminSingleSignOnLabel' => 'Login to Panel as Admin',
  );
}

/**
 * Define product configuration options.
 *
 * The values you return here define the configuration options that are
 * presented to a user when configuring a product for use with the module. These
 * values are then made available in all module function calls with the key name
 * configoptionX - with X being the index number of the field from 1 to 24.
 *
 * You can specify up to 24 parameters, with field types:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each and their possible configuration parameters are provided in
 * this sample function.
 *
 * @return array
 */
function budgetvm_ConfigOptions()
{
  return array(
    "Reinstall" => array( 
     "Type" => "yesno", 
      "Description" => "Tick to allow clients to reinstall their system." 
    ),
    "Network" => array( 
      "Type" => "yesno", 
      "Description" => "Tick to allow clients to view their network graphs." 
    ),
  );
}

/**
 * Provision a new instance of a product/service.
 *
 * Attempt to provision a new instance of a given product/service. This is
 * called any time provisioning is requested inside of WHMCS. Depending upon the
 * configuration, this can be any of:
 * * When a new order is placed
 * * When an invoice for a new order is paid
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */
function budgetvm_CreateAccount(array $params)
{
  // Note this does nto create a service, but rather unsuspends it, so that you can re-use hardware for customers.
  try {
    $action               = new BudgetVM_Api($params['serveraccesshash']);
    $info->post->service  = $params['customfields']['BudgetVM Service ID'];
    $action               = $action->call("v3", "network", "port", "put", $info);
    if($action->success == true){
      return "success";
    }else{
      return $action->result;
    }
  } catch (Exception $e) {
    // Record the error in WHMCS's module log.
    logModuleCall('provisioningmodule', __FUNCTION__, $params, $e->getMessage(), $e->getTraceAsString());
    return $e->getMessage();
  }
}

/**
 * Suspend an instance of a product/service.
 *
 * Called when a suspension is requested. This is invoked automatically by WHMCS
 * when a product becomes overdue on payment or can be called manually by admin
 * user.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */
function budgetvm_SuspendAccount(array $params)
{
  try {
    $action               = new BudgetVM_Api($params['serveraccesshash']);
    $info->post->service  = $params['customfields']['BudgetVM Service ID'];
    $action               = $action->call("v3", "network", "port", "delete", $info);
    if($action->success == true){
      return "success";
    }else{
      return $action->result;
    }
  } catch (Exception $e) {
    // Record the error in WHMCS's module log.
    logModuleCall('provisioningmodule', __FUNCTION__, $params, $e->getMessage(), $e->getTraceAsString());
    return $e->getMessage();
  }
}

/**
 * Un-suspend instance of a product/service.
 *
 * Called when an un-suspension is requested. This is invoked
 * automatically upon payment of an overdue invoice for a product, or
 * can be called manually by admin user.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */
function budgetvm_UnsuspendAccount(array $params)
{
  try {
    $action               = new BudgetVM_Api($params['serveraccesshash']);
    $info->post->service  = $params['customfields']['BudgetVM Service ID'];
    $action               = $action->call("v3", "network", "port", "put", $info);
    if($action->success == true){
      return "success";
    }else{
      return $action->result;
    }
  } catch (Exception $e) {
    // Record the error in WHMCS's module log.
    logModuleCall('provisioningmodule', __FUNCTION__, $params, $e->getMessage(), $e->getTraceAsString());
    return $e->getMessage();
  }
}

/**
 * Terminate instance of a product/service.
 *
 * Called when a termination is requested. This can be invoked automatically for
 * overdue products if enabled, or requested manually by an admin user.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */
function budgetvm_TerminateAccount(array $params)
{
  // Note, this does not actually terminate the service it just suspends it.
  try {
    $action               = new BudgetVM_Api($params['serveraccesshash']);
    $info->post->service  = $params['customfields']['BudgetVM Service ID'];
    $action               = $action->call("v3", "network", "port", "delete", $info);
    if($action->success == true){
      return "success";
    }else{
      return $action->result;
    }
  } catch (Exception $e) {
    // Record the error in WHMCS's module log.
    logModuleCall('provisioningmodule', __FUNCTION__, $params, $e->getMessage(), $e->getTraceAsString());
    return $e->getMessage();
  }
}

/**
 * Change the password for an instance of a product/service.
 *
 * Called when a password change is requested. This can occur either due to a
 * client requesting it via the client area or an admin requesting it from the
 * admin side.
 *
 * This option is only available to client end users when the product is in an
 * active status.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */
function budgetvm_ChangePassword(array $params)
{
  try {
    // Call the service's change password function, using the values
    // provided by WHMCS in `$params`.
    //
    // A sample `$params` array may be defined as:
    //
    // ```
    // array(
    //   'username' => 'The service username',
    //   'password' => 'The new service password',
    // )
    // ```
  } catch (Exception $e) {
    // Record the error in WHMCS's module log.
    logModuleCall('provisioningmodule', __FUNCTION__, $params, $e->getMessage(), $e->getTraceAsString());

    return $e->getMessage();
  }

  return 'success';
}

/**
 * Upgrade or downgrade an instance of a product/service.
 *
 * Called to apply any change in product assignment or parameters. It
 * is called to provision upgrade or downgrade orders, as well as being
 * able to be invoked manually by an admin user.
 *
 * This same function is called for upgrades and downgrades of both
 * products and configurable options.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */
function budgetvm_ChangePackage(array $params)
{
  try {
    // Call the service's change password function, using the values
    // provided by WHMCS in `$params`.
    //
    // A sample `$params` array may be defined as:
    //
    // ```
    // array(
    //   'username' => 'The service username',
    //   'configoption1' => 'The new service disk space',
    //   'configoption3' => 'Whether or not to enable FTP',
    // )
    // ```
  } catch (Exception $e) {
    // Record the error in WHMCS's module log.
    logModuleCall('provisioningmodule', __FUNCTION__, $params, $e->getMessage(), $e->getTraceAsString());

    return $e->getMessage();
  }

  return 'success';
}

/**
 * Test connection with the given server parameters.
 *
 * Allows an admin user to verify that an API connection can be
 * successfully made with the given configuration parameters for a
 * server.
 *
 * When defined in a module, a Test Connection button will appear
 * alongside the Server Type dropdown when adding or editing an
 * existing server.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return array
 */
function budgetvm_TestConnection(array $params)
{
  try {
    // Call the service's connection test function.
    $action               = new BudgetVM_Api($params['serveraccesshash']);
    $action               = $action->call("v3", "test", "connection", "get", NULL);
    if($action->success == true){
      $success            = true;
      $errorMsg           = $action->result;
    }else{
      $success            = false;
      $errorMsg           = $action->result;
    }
  } catch (Exception $e) {
    // Record the error in WHMCS's module log.
    logModuleCall('provisioningmodule', __FUNCTION__, $params, $e->getMessage(), $e->getTraceAsString());

    $success  = false;
    $errorMsg = $e->getMessage();
  }

  return array(
    'success' => $success,
    'error'   => $errorMsg,
  );
}

/**
 * Additional actions an admin user can invoke.
 *
 * Define additional actions that an admin user can perform for an
 * instance of a product/service.
 *
 * @see budgetvm_buttonOneFunction()
 *
 * @return array
 */
function budgetvm_AdminCustomButtonArray()
{
  return array(
    "Power On"        => "powerOn",
    "Power Off"       => "powerOff",
    "Reboot"          => "powerReboot",
    "Reset Console"   => "resetConsole",
  );
}

/**
 * Additional actions a client user can invoke.
 *
 * Define additional actions a client user can perform for an instance of a
 * product/service.
 *
 * Any actions you define here will be automatically displayed in the available
 * list of actions within the client area.
 *
 * @return array
 */
function budgetvm_ClientAreaCustomButtonArray()
{
  return array(
    "Power On"        => "powerOn",
    "Power Off"       => "powerOff",
    "Reboot"          => "powerReboot",
    "Reset Console"   => "resetConsole",
  );
}

/**
 * Custom function for performing an additional action.
 *
 * You can define an unlimited number of custom functions in this way.
 *
 * Similar to all other module call functions, they should either return
 * 'success' or an error message to be displayed.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 * @see budgetvm_AdminCustomButtonArray()
 *
 * @return string "success" or an error message
 */
function budgetvm_powerOn(array $params)
{
  try {
    // Call the service's function, using the values provided by WHMCS in
    // `$params`.
    $update = new BudgetVM_Api($params['serveraccesshash']);
    $var = new stdclass();
    $var->post = new stdclass();
    $var->post->service = $params['customfields']['BudgetVM Service ID'];
    $var->post->action = "on";
    $return = $update->call("v3", "device", "power", "post", $var);
    if($return->success == true){
      return 'success';
    }else{
      return $return->result;
    }
  } catch (Exception $e) {
    // Record the error in WHMCS's module log.
    logModuleCall('provisioningmodule', __FUNCTION__, $params, $e->getMessage(), $e->getTraceAsString());
    return $e->getMessage();
  }
}

function budgetvm_powerOff(array $params)
{
  try {
    // Call the service's function, using the values provided by WHMCS in
    // `$params`.
    $update = new BudgetVM_Api($params['serveraccesshash']);
    $var = new stdclass();
    $var->post = new stdclass();
    $var->post->service = $params['customfields']['BudgetVM Service ID'];
    $var->post->action = "off";
    $return = $update->call("v3", "device", "power", "post", $var);
    if($return->success == true){
      return 'success';
    }else{
      return $return->result;
    }
  } catch (Exception $e) {
    // Record the error in WHMCS's module log.
    logModuleCall('provisioningmodule', __FUNCTION__, $params, $e->getMessage(), $e->getTraceAsString());
    return $e->getMessage();
  }
}

function budgetvm_powerReboot(array $params)
{
  try {
    // Call the service's function, using the values provided by WHMCS in
    // `$params`.
    $update = new BudgetVM_Api($params['serveraccesshash']);
    $var = new stdclass();
    $var->post = new stdclass();
    $var->post->service = $params['customfields']['BudgetVM Service ID'];
    $var->post->action = "reboot";
    $return = $update->call("v3", "device", "power", "post", $var);
    if($return->success == true){
      return 'success';
    }else{
      return $return->result;
    }
  } catch (Exception $e) {
    // Record the error in WHMCS's module log.
    logModuleCall('provisioningmodule', __FUNCTION__, $params, $e->getMessage(), $e->getTraceAsString());
    return $e->getMessage();
  }
}

function budgetvm_resetConsole(array $params)
{
  try {
    // Call the service's function, using the values provided by WHMCS in
    // `$params`.
    $reset_ipmi = new BudgetVM_Api($params['serveraccesshash']);
    $var = new stdclass();
    $var->post = new stdclass();
    $var->post->service = $params['customfields']['BudgetVM Service ID'];
    $return = $reset_ipmi->call("v3", "device", "console", "delete", $var);  
    if($return->success == true){
      return 'success';
    }else{
      return $return->result;
    }
  } catch (Exception $e) {
    // Record the error in WHMCS's module log.
    logModuleCall('provisioningmodule', __FUNCTION__, $params, $e->getMessage(), $e->getTraceAsString());
    return $e->getMessage();
  }
}

/**
 * Client area output logic handling.
 *
 * This function is used to define module specific client area output. It should
 * return an array consisting of a template file and optional additional
 * template variables to make available to that template.
 *
 * The template file you return can be one of two types:
 *
 * * tabOverviewModuleOutputTemplate - The output of the template provided here
 *   will be displayed as part of the default product/service client area
 *   product overview page.
 *
 * * tabOverviewReplacementTemplate - Alternatively using this option allows you
 *   to entirely take control of the product/service overview page within the
 *   client area.
 *
 * Whichever option you choose, extra template variables are defined in the same
 * way. This demonstrates the use of the full replacement.
 *
 * Please Note: Using tabOverviewReplacementTemplate means you should display
 * the standard information such as pricing and billing details in your custom
 * template or they will not be visible to the end user.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return array
 */
function budgetvm_ClientArea(array $params)
{
  // Determine the requested action and set service call parameters based on
  // the action.
  $requestedAction = isset($_REQUEST['customAction']) ? $_REQUEST['customAction'] : '';
  $service = $params['serviceid'];
  $bvmid = $params['customfields']['BudgetVM Service ID'];
  $apiKey = $params['serveraccesshash'];
  $budgetvm = new stdClass();

  
  $var = new stdclass();
  $var->post = new stdclass();
  $var->post->service = $bvmid;
  $budgetvm->bvmid = $bvmid;
  $budgetvm->service = $service;
  $type = new BudgetVM_Api($params['serveraccesshash']);
  $type = $type->call("v3", "device", "type", "get", $var); 
  $budgetvm->type = $type->result->type;

  if ($requestedAction == 'reverse') {
    if($_SERVER['REQUEST_METHOD'] == "POST" && is_array($_POST['update'])){
      $records = $_POST['update'];
      $fixed = "";
      if( isset($records) && is_array($records) && !empty($records) ){
        $budgetvm->failed = false;
        foreach($records as $ip => $ptr){
          $api = new BudgetVM_Api($params['serveraccesshash']);
          $v = new stdClass();
          $v->post = new stdClass();
          $v->post->ip = trim($ip);
          $v->post->record = trim($ptr);

          $update = $api->call("v3", "dns", "reverse", "put", $v); 

          if($update->success == true ){
            $fixed .= $ip . " - " . $ptr . " - Success<br>" . PHP_EOL; 
          }else{
            $budgetvm->failed = true;
            $fixed .= $ip . " - " . $ptr . " - Failed<br>" . PHP_EOL; 
          }
        }
      }
      $budgetvm->return = $fixed; 
    }else{
      $budgetvm->return = NULL;
    }
    $netblocks = new BudgetVM_Api($params['serveraccesshash']);
    $budgetvm->netblocks = $netblocks->call("v3", "network", "netblock", "get", $var);
      
    $serviceAction = 'get_usage';
    $templateFile = 'templates/rdns.tpl';
  }else if ($requestedAction == 'network') {
    
    $api = new BudgetVM_Api($params['serveraccesshash']);

    if($budgetvm->type == "dedicated"){
      if(isset($_GET['period'])){
        if( $_GET['period'] == 'hour' ){
          $var->post->start = strtotime("-1 hour");
        }else if( $_GET['period'] == 'day' ){
          $var->post->start = strtotime("-1 day");
        }else if( $_GET['period'] == 'week' ){
          $var->post->start = strtotime("-1 week");
        }else if( $_GET['period'] == 'month' ){
          $var->post->start = strtotime("-1 month");
        }else if( $_GET['period'] == 'year' ){
          $var->post->start = strtotime("-1 year");
        }else{
          $var->post->start = strtotime("-1 month");
        }
      }else{
        $var->post->start = strtotime("last Month");
      }
      $var->post->end = strtotime("now");
    }
    $budgetvm->bandwidth = $api->call("v3", "network", "bandwidth", "post", $var);

    $serviceAction = 'get_usage';
    $templateFile = 'templates/network.tpl';

  }elseif ($requestedAction == 'power') {
        
    if($_SERVER['REQUEST_METHOD'] == "POST"){
      if($_POST['bootorder'] != "standard"){
        // Custom Boot Order was Requested
        $bootorder = new BudgetVM_Api($params['serveraccesshash']);
        $var->post->request = $_POST['bootorder'];
        $budgetvm->bootorder = $bootorder->call("v3", "device", "power", "put", $var);
      }else{
        $budgetvm->bootorder = NULL;
      }
      // Power Action
      $update = new BudgetVM_Api($params['serveraccesshash']);
      $var->post->action = $_POST['poweraction'];
      $budgetvm->return = $update->call("v3", "device", "power", "post", $var);
      if($budgetvm->return->success == true){
        if($_POST['bootorder'] != "standard" && !empty($budgetvm->bootorder) && $budgetvm->bootorder->success == true){
          // Power Action with custom boot order
          $budgetvm->return->result = $budgetvm->bootorder->result . " & " . $budgetvm->return->result;
        }
      }
    }else{
      $budgetvm->return = NULL;
    }
    $powerStatus = new BudgetVM_Api($params['serveraccesshash']);
    $budgetvm->powerStatus = $powerStatus->call("v3", "device", "power", "get", $var);
    
    $serviceAction = 'get_usage';
    $templateFile = 'templates/power.tpl';
  }elseif ($requestedAction == 'reinstall') {
    
    if($_SERVER['REQUEST_METHOD'] == "POST"){
      $cancel = $_POST['cancel'];
      if(isset($cancel) && $cancel == true){
        $stop = new BudgetVM_Api($params['serveraccesshash']);
        $budgetvm->return = $stop->call("v3", "device", "reload", "delete", $var);
      }else{
        $provision = new BudgetVM_Api($params['serveraccesshash']);
        $var->post->value = $_POST['profile'];
        $budgetvm->return = $provision->call("v3", "device", "reload", "post", $var);
        if($budgetvm->return->success == true){
          if($type->result == "dedicated"){
            $budgetvm->return->result = "System Reinstall Started</br>Root Password: " . $budgetvm->return->result;
          }
        }
      }
    }else{
      $budgetvm->return = NULL;
    }
    if($budgetvm->type == "dedicated"){
      $status = new BudgetVM_Api($params['serveraccesshash']);
      $budgetvm->status = $status->call("v3", "device", "reload", "put", $var)->result;
    }else{
      $budgetvm->status = NULL;
    }
    $profiles = new BudgetVM_Api($params['serveraccesshash']); 
    $v = new stdclass();
    $v->post = new stdclass();
    $v->post->service = $bvmid;    
    $budgetvm->profiles = $profiles->call("v3", "device", "reload", "get", $v)->result;

    $serviceAction = 'get_usage';
    $templateFile = 'templates/reinstall.tpl';

  }elseif ($requestedAction == 'ipmi') {
    
    if($_SERVER['REQUEST_METHOD'] == "POST"){
      if($budgetvm->type == "dedicated"){
        if($_POST['ipmi_reset'] == true){
          $reset_ipmi = new BudgetVM_Api($params['serveraccesshash']);
          $budgetvm->return = $reset_ipmi->call("v3", "device", "console", "delete", $var);
        }elseif($_POST['ipmi_launch'] == true){
          $launch_ipmi = new BudgetVM_Api($params['serveraccesshash']);
          $budgetvm->return = $launch_ipmi->call("v3", "device", "console", "get", $var);
          if($budgetvm->return->success == true){
            $launch_ipmi->ipmiLaunch(base64_decode($budgetvm->return->result->base64));
            $budgetvm->return->result  = "KVM Launched, File download started.";
          }
        }elseif($_POST['image_unmount'] == true){
          $unmount_image = new BudgetVM_Api($params['serveraccesshash']);
          $budgetvm->return = $unmount_image->call("v3", "device", "iso", "delete", $var);
        }elseif($_POST['image_mount'] == true){
          $mount_image = new BudgetVM_Api($params['serveraccesshash']);
          $var->post->image = $_POST['profile'];
          $budgetvm->return = $mount_image->call("v3", "device", "iso", "post", $var);
          
          $mount_image = new BudgetVM_Api($params['serveraccesshash']);
          $budgetvm->return = $mount_image->call("v3", "device", "iso", "put", $var);
        }
      }else{
        if($_POST['ipmi_vm_launch'] == true){
          $ipmi_vm_launch = new BudgetVM_Api($params['serveraccesshash']);
          $budgetvm->return = $ipmi_vm_launch->call("v3", "device", "console", "get", $var);
          if($budgetvm->return->success == true){
            $message = "<h4>Management Console</h4>";
            $message .= "VNC Pass: " . $budgetvm->return->result->pass . "</br>" . PHP_EOL;
            $message .= "VNC Host: " . $budgetvm->return->result->host . "</br>" . PHP_EOL;
            $message .= "VNC Port: " . $budgetvm->return->result->port . "</br>" . PHP_EOL;
            $message .= "<a href='" . $budgetvm->return->result->link . "' target='_blank'>Launch Web Console</a>" . PHP_EOL;
            $budgetvm->return->result = $message;
          }
        }
      }
    }else{
      $budgetvm->return         = NULL;
    }
  
    $images                     = new BudgetVM_Api($params['serveraccesshash']);
    $budgetvm->images           = $images->call("v3", "device", "iso", "get", NULL);
    $status                     = new BudgetVM_Api($params['serveraccesshash']);
    $budgetvm->status           = $status->call("v3", "device", "iso", "get", $var);
    
    $serviceAction              = 'get_usage';
    $templateFile               = 'templates/ipmi.tpl';
  } else {
    // Service Overview

    $api = new BudgetVM_Api($params['serveraccesshash']);
    $budgetvm->device = $api->call("v3", "device", "hardware", "get", $var);
    if( $budgetvm->device->success == true ){
      $budgetvm->device = $budgetvm->device->result;
    }
    
    $api = new BudgetVM_Api($params['serveraccesshash']);
    $status = $api->call("v3", "device", "power", "get", $var);
    if($status->success == true && $status->success == true){
      $budgetvm->status = $status->result;
    }else{
      $budgetvm->status = "Unknown";
    }
    
    $api = new BudgetVM_Api($params['serveraccesshash']);
    $var->post->start = strtotime("last Month");
    $var->post->end = strtotime("now");
    $budgetvm->bandwidth = $api->call("v3", "network", "bandwidth", "post", $var);

    $serviceAction              = 'get_stats';
    $templateFile               = 'templates/overview.tpl';
  }

  try {
    // Call the service's function based on the request action, using the
    // values provided by WHMCS in `$params`.
    $pageReturn = [];
    $pageReturn['tabOverviewReplacementTemplate'] = $templateFile;
    $pageReturn['templateVariables'] = [];
    $pageReturn['templateVariables']['budgetvm'] = $budgetvm;
    if ($requestedAction == 'reinstall') {
      $pageReturn['templateVariables']['os_options'] = $budgetvm->profiles;
    }
    return $pageReturn;
    /*
    return array(
      'tabOverviewReplacementTemplate' => $templateFile,
      'templateVariables' => array(
        'budgetvm'  => $budgetvm,
      ),
    );
    */
  } catch (Exception $e) {
    // Record the error in WHMCS's module log.
    logModuleCall('provisioningmodule', __FUNCTION__, $params, $e->getMessage(), $e->getTraceAsString());

    // In an error condition, display an error page.
    return array(
      'tabOverviewReplacementTemplate' => 'error.tpl',
      'templateVariables' => array(
        'usefulErrorHelper' => $e->getMessage(),
      ),
    );
  }
}
