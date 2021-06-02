<?php

namespace srag\CustomInputGUIs\LiveVoting\InputGUIWrapperUIInputComponent;

use ilHiddenInputGUI;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Component\Input\Field\Input as InputInterface;
use ILIAS\UI\Implementation\Component\Input\Field\Input;
use ILIAS\UI\Implementation\Render\Template;
use ILIAS\UI\Renderer as RendererInterface;
use srag\DIC\LiveVoting\DICStatic;

if (DICStatic::version()->is6()) {
    /**
     * Class Renderer
     *
     * @package srag\CustomInputGUIs\LiveVoting\InputGUIWrapperUIInputComponent
     */
    class Renderer extends AbstractRenderer
    {

        /**
         * @inheritDoc
         */
        public function render(Component $component, RendererInterface $default_renderer) : string
        {
            if ($component->getInput() instanceof ilHiddenInputGUI) {
                return "";
            }

            $input_tpl = $this->getTemplate("input.html", true, true);

            $html = $this->renderInputFieldWithContext($default_renderer, $input_tpl, $component, null, null);

            return $html;
        }


        /**
         * @inheritDoc
         */
        protected function renderInputField(Template $tpl, Input $input, $id, RendererInterface $default_renderer) : string
        {
            return $this->renderInput($tpl, $input);
        }
    }
} else {
    /**
     * Class Renderer
     *
     * @package srag\CustomInputGUIs\LiveVoting\InputGUIWrapperUIInputComponent
     */
    class Renderer extends AbstractRenderer
    {

        /**
         * @inheritDoc
         */
        protected function renderNoneGroupInput(InputInterface $input, RendererInterface $default_renderer) : string
        {
            if ($input->getInput() instanceof ilHiddenInputGUI) {
                return "";
            }

            $input_tpl = $this->getTemplate("input.html", true, true);

            $html = $this->renderInputFieldWithContext($input_tpl, $input, null, null);

            return $html;
        }


        /**
         * @inheritDoc
         */
        protected function renderInputField(Template $tpl, Input $input, $id) : string
        {
            return $this->renderInput($tpl, $input);
        }
    }
}
