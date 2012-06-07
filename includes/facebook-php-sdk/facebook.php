<?php
/**
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require_once( dirname(__FILE__) . '/base_facebook.php' );

if ( ! class_exists( 'Facebook' ) ):

/**
 * Extends the BaseFacebook class with the intent of using
 * PHP sessions to store user ids and access tokens.
 */
class Facebook extends BaseFacebook
{
  /**
   * Identical to the parent constructor, except that
   * we store the user ID and
   * access token in user meta if during the course of execution
   * we discover them.
   *
   * @param Array $config the application configuration.
   * @see BaseFacebook::__construct in facebook.php
   */
  public function __construct($config) {
    parent::__construct($config);
  }

  protected static $kSupportedKeys =
    array('state', 'code', 'access_token', 'user_id');

  /**
   * Provides the implementations of the inherited abstract
   * methods.  The implementation uses user meta to maintain
   * a store for authorization codes, user ids, CSRF states, and
   * access tokens.
   */
  protected function setPersistentData($key, $value){
    
	    if (!in_array($key, self::$kSupportedKeys)) {
	      self::errorLog('Unsupported key passed to setPersistentData.');
	      return;   
	    }
		
		//WP 3.0+
		update_user_meta( get_current_user_id(), $key, $value);
	}

  protected function getPersistentData($key, $default = false){
    
    if (!in_array($key, self::$kSupportedKeys)) {
      self::errorLog('Unsupported key passed to getPersistentData.');
      return $default;
    }
	
	  return $usermeta = get_user_meta(get_current_user_id(), $key);
	}

  protected function clearPersistentData($key) {
    if (!in_array($key, self::$kSupportedKeys)) {
      self::errorLog('Unsupported key passed to clearPersistentData.');
      return;
    }

    delete_user_meta( get_current_user_id(), $key);
  }

  protected function clearAllPersistentData() {
    foreach (self::$kSupportedKeys as $key) {
      $this->clearPersistentData($key);
    }
  }

}
endif;