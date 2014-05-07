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
		$this->config = JFactory::getConfig();
	}

	public function insertRandomEncrypted()
	{

		try
		{
			$pdo = new PDO('mysql:host=' . $this->config->get('host') . ';dbname=' . $this->config->get('db'), $this->config->get('user'), $this->config->get('password'));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		} catch (PDOException $e)
		{
			$result = "Error!: " . $e->getMessage() . "<br/>";
		}

		$sql = 'INSERT INTO `' . $this->config->get('dbprefix') . 'encrypted`
		(
			stuff
		)
		VALUES (
			AES_ENCRYPT(:stuff, :cryptkey)
		)';

		try
		{
			$query  = $pdo->prepare($sql);
			$random = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 25);

			$query->execute(
				array(
					':cryptkey' => $this->config->get('secret'),
					':stuff'    => $random
				)
			);

			$result = "$random inserted\n";
		} catch (PDOException $e)
		{
			$result = "Error!: " . $e->getMessage() . "<br/>";
		}

		return $result;

	}
}