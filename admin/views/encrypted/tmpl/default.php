<?php defined('_JEXEC') or die;

/**
 * File       default.php
 * Created    5/6/14 4:04 PM
 * Author     Matt Thomas | matt@betweenbrain.com | http://betweenbrain.com
 * Support    https://github.com/betweenbrain/
 * Copyright  Copyright (C) 2014 betweenbrain llc. All Rights Reserved.
 * License    GNU GPL v2 or later
 */

echo '<pre>' . print_r($this->lastRecord, true) . '</pre>';
echo '<pre>' . print_r($this->lastAesEncrypted, true) . '</pre>';