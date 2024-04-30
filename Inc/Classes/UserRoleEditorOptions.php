<?php

namespace ROLEMASTER\Inc\Classes;

use ROLEMASTER\Inc\Base_Model;

class UserRoleEditorOptions extends UserRoleEditorModel
{
    public function __construct()
    {
        // this should be first so the default values get stored
        parent::__construct((array) get_option($this->prefix));
    }
}
