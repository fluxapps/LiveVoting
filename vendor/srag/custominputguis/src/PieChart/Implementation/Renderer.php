<?php

namespace srag\CustomInputGUIs\LiveVoting\PieChart\Implementation;

use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ILIAS\UI\Implementation\Render\ResourceRegistry;
use ILIAS\UI\Renderer as RendererInterface;
use srag\CustomInputGUIs\LiveVoting\PieChart\Component\PieChart as PieChartInterface;
use srag\DIC\LiveVoting\DICTrait;

/**
 * Class Renderer
 *
 * https://github.com/ILIAS-eLearning/ILIAS/tree/trunk/src/UI/Implementation/Component/Chart/PieChart/Renderer.php
 *
 * @package srag\CustomInputGUIs\LiveVoting\PieChart\Implementation
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Renderer extends AbstractComponentRenderer
{

    use DICTrait;

    /**
     * @inheritDoc
     */
    protected function getComponentInterfaceName() : array
    {
        return [PieChartInterface::class];
    }


    /**
     * @inheritDoc
     */
    public function render(Component $component, RendererInterface $default_renderer) : string
    {
        $this->checkComponent($component);

        return $this->renderStandard($component, $default_renderer);
    }


    /**
     * @param PieChartInterface $component
     * @param RendererInterface $default_renderer
     *
     * @return string
     */
    protected function renderStandard(PieChartInterface $component, RendererInterface $default_renderer) : string
    {
        $tpl = $this->getTemplate("tpl.piechart.html", true, true);

        foreach ($component->getSections() as $section) {
            $tpl->setCurrentBlock("section");
            $tpl->setVariable("STROKE_LENGTH", $section->getStrokeLength());
            $tpl->setVariable("OFFSET", $section->getOffset());
            $tpl->setVariable("SECTION_COLOR", $section->getColor()->asHex());
            $tpl->parseCurrentBlock();
        }

        if ($component->isShowLegend()) {
            foreach ($component->getSections() as $section) {
                $tpl->setCurrentBlock("legend");
                $tpl->setVariable("SECTION_COLOR", $section->getColor()->asHex());
                $tpl->setVariable("LEGEND_Y_PERCENTAGE", $section->getLegendEntry()->getYPercentage());
                $tpl->setVariable("LEGEND_TEXT_Y_PERCENTAGE", $section->getLegendEntry()->getTextYPercentage());
                $tpl->setVariable("LEGEND_FONT_SIZE", $section->getLegendEntry()->getTextSize());
                $tpl->setVariable("RECT_SIZE", $section->getLegendEntry()->getSquareSize());

                if ($component->isValuesInLegend()) {
                    $section_name = sprintf($section->getName() . " (%s)", $section->getValue()->getValue());
                } else {
                    $section_name = $section->getName();
                }

                $tpl->setVariable("SECTION_NAME", $section_name);
                $tpl->parseCurrentBlock();
            }
        }

        foreach ($component->getSections() as $section) {
            $tpl->setCurrentBlock("section_text");
            $tpl->setVariable("VALUE_X_PERCENTAGE", $section->getValue()->getXPercentage());
            $tpl->setVariable("VALUE_Y_PERCENTAGE", $section->getValue()->getYPercentage());
            $tpl->setVariable("SECTION_VALUE", round($section->getValue()->getValue(), 2));
            $tpl->setVariable("VALUE_FONT_SIZE", $section->getValue()->getTextSize());
            $tpl->setVariable("TEXT_COLOR", $section->getTextColor()->asHex());
            $tpl->parseCurrentBlock();
        }

        $tpl->setCurrentBlock("total");
        $total_value = $component->getCustomTotalValue();
        if (is_null($total_value)) {
            $total_value = $component->getTotalValue();
        }
        $tpl->setVariable("TOTAL_VALUE", round($total_value, 2));
        $tpl->parseCurrentBlock();

        return self::output()->getHTML($tpl);
    }


    /**
     * @inheritDoc
     */
    public function registerResources(ResourceRegistry $registry)/*: void*/
    {
        parent::registerResources($registry);

        $dir = __DIR__;
        $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1) . "/..";

        $registry->register($dir . "/css/piechart.css");
    }


    /**
     * @inheritDoc
     */
    protected function getTemplatePath(/*string*/ $name) : string
    {
        return __DIR__ . "/../templates/" . $name;
    }
}
