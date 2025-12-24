<?php


class GXSmartyTemplate extends Smarty_Internal_Template
{
    public function _loadInheritance()
    {
        if (!isset($this->inheritance)) {
            $this->inheritance = new GXSmartyRuntimeInheritance();
        }
    }
}