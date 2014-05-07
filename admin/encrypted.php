<?php defined('_JEXEC') or die;

/**
 * File       encrypted.php
 * Created    5/6/14 3:13 PM
 * Author     Matt Thomas | matt@betweenbrain.com | http://betweenbrain.com
 * Support    https://github.com/betweenbrain/
 * Copyright  Copyright (C) 2014 betweenbrain llc. All Rights Reserved.
 * License    GNU GPL v2 or later
 */

// import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller
$controller = JControllerLegacy::getInstance('Encrypted');

// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();