<?php

namespace Hcode;

class PageSite extends Page
{
    public function __construct($opts = array(), $tpl_dir = "/views/site/")
    {
        parent::__construct($opts, $tpl_dir);
    }
}

?>