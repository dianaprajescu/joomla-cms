<?php

/**
* @version $Id$
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/**
 * Joomla Framework Factory class
 *
 * @package 	Joomla.Framework
 * @static
 * @since 1.1
 */
class JFactory
{
	/**
	 * Creates a cache object
	 *
	 * @access public
	 * @param string The cache group name
	 * @param string The cache class name
	 * @return object
	 */
	function &getCache($group='', $handler = 'function')
	{
		global $mosConfig_caching, $mosConfig_cachepath, $mosConfig_cachetime;

		jimport('joomla.cache.cache');

		$options = array(
			'cacheDir' 		=> $mosConfig_cachepath . '/',
			'caching' 		=> $mosConfig_caching,
			'defaultGroup' 	=> $group,
			'lifeTime' 		=> $mosConfig_cachetime,
			'fileNameProtection' => false
		);
		$cache =& JCache::getInstance( $handler, $options );

		return $cache;
	}

	/**
	 * Returns a reference to the global JACL object, only creating it
	 * if it doesn't already exist.
	 *
	 * @access public
	 * @return object
	 */
	function &getACL( ) 
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}

		if (empty($instances[0])) {
			$instances[0] = JFactory::_createACL();
		}

		return $instances[0];
	}

	/**
	 * Returns a reference to the global Mailer object, only creating it
	 * if it doesn't already exist
	 *
	 * @access public
	 * @return object
	 */
	function &getMailer( ) 
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}

		if (empty($instances[0])) {
			$instances[0] = JFactory::_createMailer();
		}

		return $instances[0];
	}

	/**
	 * Creates a XML document
	 *
	 * @access public
	 * @return object
	 * @param boolean If true, include lite version
	 */

	 function &getXMLParser( $type = 'DOM', $lite =  true) 
	 {
		$doc = null;
		
		switch($type)
		{
			case 'DOM'  :
			{
				if($lite) {
					jimport('domit.xml_domit_lite_include');
					$doc = new DOMIT_Lite_Document();
				} else {
					jimport('domit.xml_domit_include');
					$doc = new DOMIT_Document();
				}
			} break;

			case 'RSS'  :
			{
				jimport('domit.xml_domit_rss_lite');
				$doc = new xml_domit_rss_document_lite();
			} break;
		}

		return $doc;
	}

	/**
	 * Create an ACL object
	 *
	 * @access private
	 * @return object
	 * @since 1.1
	 */
	function &_createACL()
	{
		global $mainframe;

		jimport( 'joomla.application.user.acl' );

		$database =&  $mainframe->getDBO();

		$options = array(
			'db'				=> &$database,
			'db_table_prefix'	=> $database->getPrefix() . 'core_acl_',
			'debug'				=> 0
		);
		$acl = new JACL( $options );

		return $acl;
	}

	/**
	 * Create a mailer object
	 *
	 * @access private
	 * @return object
	 * @since 1.1
	 */
	function &_createMailer()
	{
		global $mosConfig_sendmail;
		global $mosConfig_smtpauth, $mosConfig_smtpuser;
		global $mosConfig_smtppass, $mosConfig_smtphost;
		global $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_mailer;

		jimport('phpmailer.phpmailer');

		$mail = new PHPMailer();

		$mail->PluginDir = JPATH_LIBRARIES .'/phpmailer/';
		$mail->SetLanguage( 'en', JPATH_LIBRARIES . '/includes/phpmailer/language/' );
		$mail->CharSet 	= "utf-8";
		$mail->IsMail();
		$mail->From 	= $mosConfig_mailfrom;
		$mail->FromName = $mosConfig_fromname;
		$mail->Mailer 	= $mosConfig_mailer;

		// Add smtp values if needed
		if ( $mosConfig_mailer == 'smtp' ) {
			$mail->SMTPAuth = $mosConfig_smtpauth;
			$mail->Username = $mosConfig_smtpuser;
			$mail->Password = $mosConfig_smtppass;
			$mail->Host 	= $mosConfig_smtphost;
		} else

		// Set sendmail path
		if ( $mosConfig_mailer == 'sendmail' ) {
			if (isset($mosConfig_sendmail))
				$mail->Sendmail = $mosConfig_sendmail;
		} // if

		return $mail;
	}
}
?>