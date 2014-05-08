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
		$this->random = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 25);
	}

	public function doStuff()
	{

		$this->insertData($this->random, $this->config->get('secret'));

		$this->app->enqueueMessage($this->random . ' was inserted.');
	}

	private function insertData($data, $cryptkey)
	{
		$query = $this->db->getQuery(true);

		$columns = array(
			'plaintext',
			'aesencrypted',
			'mycrypted'
		);

		$values = array(
			$this->db->quote($data),
			'AES_ENCRYPT("' . $data . '", "' . $cryptkey . '")',
			$this->db->quote($this->mycryptData($data, $cryptkey))
		);

		$query
			->insert($this->db->quoteName('#__encrypted'))
			->columns($this->db->quoteName($columns))
			->values(implode(',', $values));

		$this->db->setQuery($query);
		$this->db->query();
	}

	/**
	 * Inserts data as plain text
	 *
	 * @param $data
	 */
	private function insertPlaintext($data)
	{
		$query = $this->db->getQuery(true);

		$query
			->insert($this->db->quoteName('#__encrypted'))
			->columns($this->db->quoteName('plaintext'))
			->values($this->db->quote($data));

		$this->db->setQuery($query);
		$this->db->query();

		return;
	}

	/**
	 * Inserts data with AES_ENCRYPTION
	 *
	 * @param $data
	 * @param $cryptkey
	 */
	private function insertAesEncrypted($data, $cryptkey)
	{
		$query = $this->db->getQuery(true);

		$query
			->insert($this->db->quoteName('#__encrypted'))
			->columns($this->db->quoteName('aesencrypted'))
			->values('AES_ENCRYPT("' . $data . '", "' . $cryptkey . '")');

		$this->db->setQuery($query);
		$this->db->query();

		return;
	}

	private function mycryptData($data, $cryptkey)
	{
		// Pad data with ascii characters to reach a 16 char length
		$paddChar = chr(16 - (strlen($data) % 16));
		$paddLen  = 16 * (1 + floor(strlen($data) / 16));
		$data     = str_pad($data, $paddLen, $paddChar);

		// Create a random IV to use with CBC encoding
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$iv      = mcrypt_create_iv($iv_size, MCRYPT_RAND);

		$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $cryptkey, $data, MCRYPT_MODE_CBC, $iv);

		// Prepend the IV for it to be available for decryption
		$ciphertext = $iv . $ciphertext;

		// Encode the resulting cipher text so it can be represented by a string
		return base64_encode($ciphertext);
	}

	private function insertMycrypted($data, $cryptkey)
	{

		// Create a new query object.
		$query = $this->db->getQuery(true);

		// Prepare the insert query.
		$query
			->insert($this->db->quoteName('#__encrypted'))
			->columns($this->db->quoteName('mycrypted'))
			->values($this->db->quote($this->mycryptData($data, $cryptkey)));

		// Set the query using our newly populated query object and execute it.
		$this->db->setQuery($query);
		$this->db->query();

	}

	private function decryptMycrypted($data, $cryptkey)
	{

		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$iv      = mcrypt_create_iv($iv_size, MCRYPT_RAND);

		$ciphertext_dec = base64_decode($data);

		$iv_dec = substr(base64_decode($data), 0, $iv_size);

		$ciphertext_dec = substr($ciphertext_dec, $iv_size);

		$plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $cryptkey, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

		$endCharVal = ord(substr($plaintext_dec, strlen($plaintext_dec) - 1, 1));

		if ($endCharVal <= 16 && $endCharVal >= 0)
		{
			$plaintext_dec = substr($plaintext_dec, 0, 0 - $endCharVal);
		}

		return $plaintext_dec;
	}

	public function selectLastRecord()
	{

		$query = $this->db->getQuery(true);

		$query
			->select('*')
			->from($this->db->quoteName('#__encrypted'))
			->order('id DESC')
			->limit('1');

		$this->db->setQuery($query);

		return 'Plain: ' . $this->db->loadObject()->plaintext .
		'<br/>Mycrypt_decrypt: ' . $this->decryptMycrypted($this->db->loadObject()->mycrypted, $this->config->get('secret'));

	}

	public function selectLastAesencrypted()
	{

		$query = $this->db->getQuery(true);

		$query
			->select('AES_DECRYPT(' . $this->db->quoteName('aesencrypted') . ', ' . $this->db->quote($this->config->get('secret')) . ')')
			->from($this->db->quoteName('#__encrypted'))
			->order('id DESC')
			->limit('1');

		$this->db->setQuery($query);

		return $this->db->loadResult();
	}
}
