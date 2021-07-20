<?php

namespace srag\CustomInputGUIs\LiveVoting\InputGUIWrapperUIInputComponent;

use ILIAS\UI\Implementation\Component\Input\Field\Renderer;
use ILIAS\UI\Implementation\Render\ResourceRegistry;
use ILIAS\UI\Implementation\Render\Template;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class AbstractRenderer
 *
 * @package srag\CustomInputGUIs\LiveVoting\InputGUIWrapperUIInputComponent
 */
abstract class AbstractRenderer extends Renderer
{

    use DICTrait;

    /**
     * @inheritDoc
     */
    public function registerResources(ResourceRegistry $registry) : void
    {
        parent::registerResources($registry);

        $dir = __DIR__;
        $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

        $registry->register($dir . "/css/InputGUIWrapperUIInputComponent.css");
    }


    /**
     * @inheritDoc
     */
    protected function getComponentInterfaceName() : array
    {
        return [
            InputGUIWrapperUIInputComponent::class
        ];
    }


    /**
     * @inheritDoc
     */
    protected function getTemplatePath(/*string*/ $name) : string
    {
        if ($name === "input.html") {
            return __DIR__ . "/templates/" . $name;
        } else {
            // return parent::getTemplatePath($name);
            return "src/UI/templates/default/Input/" . $name;
        }
    }


    /**
     * @param Template                        $tpl
     * @param InputGUIWrapperUIInputComponent $input
     *
     * @return string
     */
    protected function renderInput(Template $tpl, InputGUIWrapperUIInputComponent $input) : string
    {
        $tpl->setVariable("INPUT", self::output()->getHTML($input->getInput()));

        return self::output()->getHTML($tpl);
    }
}
