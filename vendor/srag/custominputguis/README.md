# CustomInputGUIs Library for ILIAS Plugins

Custom Input-GUI's

## Usage

### Composer
First add the following to your `composer.json` file:
```json
"require": {
  "srag/custominputguis": ">=0.1.0"
},
```

And run a `composer install`.

If you deliver your plugin, the plugin has it's own copy of this library and the user doesn't need to install the library.

Tip: Because of multiple autoloaders of plugins, it could be, that different versions of this library exists and suddenly your plugin use an older or a newer version of an other plugin!

So I recommand to use [srag/librariesnamespacechanger](https://packagist.org/packages/srag/librariesnamespacechanger) in your plugin.

## Input-GUI's
* [AbstractFormBuilder](./src/FormBuilder/doc/AbstractFormBuilder.md)
* [AjaxCheckbox](./src/AjaxCheckbox/doc/AjaxCheckbox.md)
* [CheckboxInputGUI](./src/CheckboxInputGUI/doc/CheckboxInputGUI.md)
* [ColorPickerInputGUI](./src/ColorPickerInputGUI/doc/ColorPickerInputGUI.md)
* [DateDurationInputGUI](./src/DateDurationInputGUI/doc/DateDurationInputGUI.md)
* [GlyphGUI](./src/GlyphGUI/doc/GlyphGUI.md)
* [HiddenInputGUI](./src/HiddenInputGUI/doc/HiddenInputGUI.md)
* [InputGUIWrapperUIInputComponent](./src/InputGUIWrapperUIInputComponent/doc/InputGUIWrapperUIInputComponent.md)
* [LearningProgressPieUI](./src/LearningProgressPieUI/doc/LearningProgressPieUI.md)
* [MultilangualTabsInputGUI](./src/TabsInputGUI/doc/MultilangualTabsInputGUI.md)
* [MultiLineNewInputGUI](./src/MultiLineNewInputGUI/doc/MultiLineNewInputGUI.md)
* [MultiLineInputGUI](./src/MultiLineInputGUI/doc/MultiLineInputGUI.md)
* [MultiSelectSearchNewInputGUI](./src/MultiSelectSearchNewInputGUI/doc/MultiSelectSearchNewInputGUI.md)
* [MultiSelectSearchInputGUI](./src/MultiSelectSearchInputGUI/doc/MultiSelectSearchInputGUI.md)
* [MultiSelectSearchInput2GUI](./src/MultiSelectSearchInputGUI/doc/MultiSelectSearchInput2GUI.md)
* [NumberInputGUI](./src/NumberInputGUI/doc/NumberInputGUI.md)
* [PieChart](./src/PieChart/doc/PieChart.md)
* [ProgressMeter](./src/ProgressMeter/doc/ProgressMeter.md)
* [PropertyFormGUI](./src/PropertyFormGUI/doc/PropertyFormGUI.md)
* [ScreenshotsInputGUI](./src/ScreenshotsInputGUI/doc/ScreenshotsInputGUI.md)
* [StaticHTMLPresentationInputGUI](./src/StaticHTMLPresentationInputGUI/doc/StaticHTMLPresentationInputGUI.md)
* [TabsInputGUI](./src/TabsInputGUI/doc/TabsInputGUI.md)
* [TableGUI](./src/TableGUI/doc/TableGUI.md)
* [Template](./src/Template/doc/Template.md)
* [TextAreaInputGUI](./src/TextAreaInputGUI/doc/TextAreaInputGUI.md)
* [TextInputGUI](./src/TextInputGUI/doc/TextInputGUI.md)
* [TextInputGUIWithModernAutoComplete](./src/TextInputGUI/doc/TextInputGUIWithModernAutoComplete.md)
* [ViewControlModeUI](./src/ViewControlModeUI/doc/ViewControlModeUI.md)
* [UIInputComponentWrapperInputGUI](./src/UIInputComponentWrapperInputGUI/doc/UIInputComponentWrapperInputGUI.md)
* [Waiter](./src/Waiter/doc/Waiter.md)
* [WeekdayInputGUI](./src/WeekdayInputGUI/doc/WeekdayInputGUI.md)

## Requirements
* ILIAS 5.4 or ILIAS 6
* PHP >=7.0

## Adjustment suggestions
* External users can report suggestions and bugs at https://plugins.studer-raimann.ch/goto.php?target=uihk_srsu_LINP
* Adjustment suggestions by pull requests via github
* Customer of studer + raimann ag: 
	* Adjustment suggestions which are not yet worked out in detail by Jira tasks under https://jira.studer-raimann.ch/projects/LINP
	* Bug reports under https://jira.studer-raimann.ch/projects/LINP
