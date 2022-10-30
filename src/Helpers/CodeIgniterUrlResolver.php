<?php

namespace Ngekoding\CodeIgniterApiQueryParser\Helpers;

class CodeIgniterUrlResolver
{
    protected $ciVersion;

    public function __construct($ciVersion)
    {
        $this->ciVersion = $ciVersion;
    }

    public function currentUrl()
    {
        if ($this->ciVersion == 3) {
            $ci =& get_instance();
            $ci->load->helper('url');
        }
        return current_url();
    }
}
