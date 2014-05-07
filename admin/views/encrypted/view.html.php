<?php defined('_JEXEC') or die;

/**
 * File       view.html.php
 * Created    5/6/14 3:57 PM
 * Author     Matt Thomas | matt@betweenbrain.com | http://betweenbrain.com
 * Support    https://github.com/betweenbrain/
 * Copyright  Copyright (C) 2014 betweenbrain llc. All Rights Reserved.
 * License    GNU GPL v2 or later
 */

// import Joomla view library
jimport('joomla.application.component.view');

class EncryptedViewEncrypted extends JViewLegacy
{

	// Overwriting JView display method
	function display($tpl = null)
	{
		$model = $this->getModel();

		$this->assignRef('result', $model->insertRandomEncrypted());

		parent::display($tpl);
	}
}