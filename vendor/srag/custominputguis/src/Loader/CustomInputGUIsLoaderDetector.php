<?php

namespace srag\CustomInputGUIs\LiveVoting\Loader;

use Closure;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\DefaultRenderer;
use ILIAS\UI\Implementation\Render\ComponentRenderer;
use ILIAS\UI\Implementation\Render\Loader;
use ILIAS\UI\Renderer;
use Pimple\Container;
use srag\CustomInputGUIs\LiveVoting\InputGUIWrapperUIInputComponent\InputGUIWrapperUIInputComponent;
use srag\CustomInputGUIs\LiveVoting\InputGUIWrapperUIInputComponent\Renderer as InputGUIWrapperUIInputComponentRenderer;
use srag\DIC\LiveVoting\Loader\AbstractLoaderDetector;

/**
 * Class CustomInputGUIsLoaderDetector
 *
 * @package srag\CustomInputGUIs\LiveVoting\Loader
 */
class CustomInputGUIsLoaderDetector extends AbstractLoaderDetector
{

    /**
     * @var bool
     */
    protected static $has_fix_ctrl_namespace_current_url = false;
    /**
     * @var callable[]|null
     */
    protected $get_renderer_for_hooks;


    /**
     * @inheritDoc
     *
     * @param callable[]|null $get_renderer_for_hooks
     */
    public function __construct(Loader $loader,/*?*/ array $get_renderer_for_hooks = null)
    {
        parent::__construct($loader);

        $this->get_renderer_for_hooks = $get_renderer_for_hooks;
    }


    /**
     * @param callable[]|null $get_renderer_for_hooks
     *
     * @return callable
     */
    public static function exchangeUIRendererAfterInitialization(/*?*/ array $get_renderer_for_hooks = null) : callable
    {
        self::fixCtrlNamespaceCurrentUrl();

        $previous_renderer = Closure::bind(function () : callable {
            return $this->raw("ui.renderer");
        }, self::dic()->dic(), Container::class)();

        return function () use ($previous_renderer, $get_renderer_for_hooks) : Renderer {
            $previous_renderer = $previous_renderer(self::dic()->dic());

            if ($previous_renderer instanceof DefaultRenderer) {
                $previous_renderer_loader = Closure::bind(function () : Loader {
                    return $this->component_renderer_loader;
                }, $previous_renderer, DefaultRenderer::class)();
            } else {
                $previous_renderer_loader = null; // TODO:
            }

            return new DefaultRenderer(new self($previous_renderer_loader, $get_renderer_for_hooks));
        };
    }


    /**
     *
     */
    private static function fixCtrlNamespaceCurrentUrl() : void
    {
        if (!self::$has_fix_ctrl_namespace_current_url) {
            self::$has_fix_ctrl_namespace_current_url = true;

            // Fix language select meta bar which current ctrl gui has namespaces (public page)
            $_SERVER["REQUEST_URI"] = str_replace("\\", "%5C", $_SERVER["REQUEST_URI"]);
        }
    }


    /**
     * @inheritDoc
     */
    public function getRendererFor(Component $component, array $contexts) : ComponentRenderer
    {
        $renderer = null;

        if (!empty($this->get_renderer_for_hooks)) {
            foreach ($this->get_renderer_for_hooks as $get_renderer_for_hook) {
                $renderer = $get_renderer_for_hook($component, $contexts);
                if ($renderer !== null) {
                    break;
                }
            }
        }

        if ($renderer === null) {
            if ($component instanceof InputGUIWrapperUIInputComponent) {
                if (self::version()->is7()) {
                    $renderer = new InputGUIWrapperUIInputComponentRenderer(self::dic()->ui()->factory(), self::dic()->templateFactory(), self::dic()->language(), self::dic()->javaScriptBinding(),
                        self::dic()->refinery(), self::dic()->imagePathResolver());
                } else {
                    $renderer = new InputGUIWrapperUIInputComponentRenderer(self::dic()->ui()->factory(), self::dic()->templateFactory(), self::dic()->language(), self::dic()->javaScriptBinding(),
                        self::dic()->refinery());
                }
            } else {
                $renderer = parent::getRendererFor($component, $contexts);
            }
        }

        return $renderer;
    }
}
