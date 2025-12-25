<?php


class GXSmartyRuntimeInheritance extends Smarty_Internal_Runtime_Inheritance
{
    public function process(
        Smarty_Internal_Template $tpl,
        Smarty_Internal_Block $block,
        Smarty_Internal_Block $parent = null
    ) {
        $filename =  str_replace([DIR_FS_CATALOG, 'get_usermod:'], '',$this->sources[$block->tplIndex]->name);
        $block_name = $block->name;
        echo "\n<!--BEGIN BLOCK $block_name of file '$filename'-->";
        parent::process($tpl, $block, $parent);
        echo "\n<!--END   BLOCK $block_name of file '$filename'-->";
    }


    /**
     * Initialize inheritance
     *
     * @param \Smarty_Internal_Template $tpl        template object of caller
     * @param bool                      $initChild  if true init for child template
     * @param array                     $blockNames outer level block name
     */
    public function init(Smarty_Internal_Template $tpl, $initChild, $blockNames = array())
    {
        // if called while executing parent template it must be a sub-template with new inheritance root
        if ($initChild && $this->state === 3 && (strpos($tpl->template_resource, 'extendsall') === false)) {
            $tpl->inheritance = new static();
            $tpl->inheritance->init($tpl, $initChild, $blockNames);
            return;
        }
        parent::init($tpl, $initChild, $blockNames);
    }

}