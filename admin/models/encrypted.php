<?php defined('_JEXEC') or die;

/**
 * File       encrypted.php
 * Created    5/6/14 3:59 PM
 * Author     Matt Thomas | matt@betweenbrain.com | http://betweenbrain.com
 * Support    https://github.com/betweenbrain/
 * Copyright  Copyright (C) 2014 betweenbrain llc. All Rights Reserved.
 * License    GNU GPL v2 or later
 */

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

class EncryptedModelEncrypted extends JModelItem
{

	/**
	 * Construct
	 *
	 * @internal param $subject
	 * @internal param $params
	 */
	function __construct()
	{
		parent::__construct();
		$this->app    = JFactory::getApplication();
		$this->config = JFactory::getConfig();
		$this->db     = JFactory::getDbo();
	}

	/**
	 * Inserts random, aes_encrypted data
	 *
	 * @return null;
	 */
	public function insertRandomEncrypted()
	{
		$query = $this->db->getQuery(true);

		$random = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 25);

		$query
			->insert($this->db->quoteName('#__encrypted'))
			->columns($this->db->quoteName('stuff'))
			->values('AES_ENCRYPT("' . $random . '", "' . $this->config->get('secret') . '")');

		$this->db->setQuery($query);
		$this->db->query();

		$this->app->enqueueMessage($random . ' was inserted as encrypted data.');

		return;

	}
}
