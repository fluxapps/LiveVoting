<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/GroupedListDropdown/class.ctrlmmEntryGroupedListDropdownGUI.php');
require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');
require_once('./Services/Style/classes/class.ilObjStyleSettings.php');
require_once('Services/Mail/classes/class.ilMailOptions.php');
require_once('class.ctrlmmSettings.php');

/**
 * ctrlmmEntryCtrlGUI
 *
 * @author                   Fabian Schmid <fs@studer-raimann.ch>
 * @version                  2.0.02
 *
 * @ilCtrl_IsCalledBy        ctrlmmEntrySettingsGUI: ilCommonActionDispatcherGUI
 */
class ctrlmmEntrySettingsGUI extends ctrlmmEntryGroupedListDropdownGUI {

	const F_SHOW_TITLE = 'show_title';
	/**
	 * @var bool
	 */
	protected $show_arrow = false;
	/**
	 * @var ctrlmmEntrySettings
	 */
	public $entry;


	/**
	 * Render main menu entry
	 *
	 * @param
	 *
	 * @return html
	 */
	protected function setGroupedListContent() {
	}


	/**
	 * @return string
	 */
	protected function getContent() {

		global $lng, $ilUser, $ilSetting, $styleDefinition;
		$lng->loadLanguageModule('mail');

		$this->tpl->addJavaScript($this->pl->getDirectory() . '/templates/js/settings.js');
		$this->tpl->addCss('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/templates/css/settings.css');

		$form = new ilPropertyFormGUI();

		$form->setId('ctrl_mm_settings_entry_form');

		//
		// Sprachen
		//
		$options = array();
		foreach ($lng->getInstalledLanguages() as $lang_key) {
			$options[$lang_key] = ilLanguage::_lookupEntry($lang_key, 'meta', 'meta_l_' . $lang_key);
		}
		$language = new ilSelectInputGUI($lng->txt('language'), ctrlmmSettings::LANGUAGE);
		$language->setValue($ilUser->getLanguage());
		$language->setDisabled($ilSetting->get('usr_settings_disable_language'));
		$language->setOptions($options);
		$form->addItem($language);

		//
		// Template/Skin
		//
		if (!$ilSetting->get('usr_settings_disable_skin_style')) {
			$templates = $styleDefinition->getAllTemplates();
			if (is_array($templates)) {
				$options = array();
				foreach ($templates as $template) {
					// get styles information of template
					$styleDef = new ilStyleDefinition($template['id']);
					$styleDef->startParsing();
					$styles = $styleDef->getStyles();

					foreach ($styles as $style) {
						if ($style['id'] == 'mobile') {
							continue;
						}
						if (!ilObjStyleSettings::_lookupActivatedStyle($template['id'], $style['id'])) {
							continue;
						}

						$options[$template['id'] . ':' . $style['id']] = $styleDef->getTemplateName() . ' / ' . $style['name'];
					}
				}

				$skin = new ilSelectInputGUI($lng->txt('skin_style'), ctrlmmSettings::SKIN);
				$skin->setValue($ilUser->skin . ':' . $ilUser->prefs['style']);
				$skin->setDisabled($ilSetting->get('usr_settings_disable_skin_style'));
				$skin->setOptions($options);
				$form->addItem($skin);
			}
		}

		//
		// Table-Results
		//
		$results = new ilSelectInputGUI($lng->txt('hits_per_page'), ctrlmmSettings::RESULTS);
		$hits_options = array( 10, 15, 20, 30, 40, 50, 100, 9999 );
		$options = array();
		foreach ($hits_options as $hits_option) {
			$hstr = ($hits_option == 9999) ? $lng->txt('no_limit') : $hits_option;
			$options[$hits_option] = $hstr;
		}
		$results->setOptions($options);
		$results->setValue($ilUser->prefs['hits_per_page']);
		$results->setDisabled($ilSetting->get('usr_settings_disable_hits_per_page'));
		$results->setOptions($options);
		$form->addItem($results);

		//
		// Mail
		//
		if ($ilSetting->get('usr_settings_disable_mail_incoming_mail') != '1') {
			$options = array(
				IL_MAIL_LOCAL => $lng->txt('mail_incoming_local'),
				IL_MAIL_EMAIL => $lng->txt('mail_incoming_smtp'),
				IL_MAIL_BOTH => $lng->txt('mail_incoming_both')
			);
			$si = new ilSelectInputGUI($lng->txt('mail_incoming'), ctrlmmSettings::INCOMING_TYPE);
			$si->setOptions($options);
			if (!strlen(ilObjUser::_lookupEmail($ilUser->getId())) OR $ilSetting->get('usr_settings_disable_mail_incoming_mail') == '1'
			) {
				$si->setDisabled(true);
			}
			$mailOptions = new ilMailOptions($ilUser->getId());
			$si->setValue($mailOptions->getIncomingType());

			$form->addItem($si);
		}

		//
		// User
		//
		$te = new ilHiddenInputGUI(ctrlmmSettings::USR_TOKEN);

		$te->setValue(ctrlmmSettings::enc($ilUser->getId()));
		$form->addItem($te);

		$form->addCommandButton('#', $this->pl->txt('settentr_button_save'));

		$setting_tpl = $this->pl->getVersionTemplate('tpl.settings_entry.html');
		$setting_tpl->setVariable('CONTENT', $form->getHTML());

		return $setting_tpl->get();
	}


	//	/**
	//	 * @param string $mode
	//	 */
	//	public function initForm($mode = 'create') {
	//		parent::initForm($mode);
	//		$te = new ilCheckboxInputGUI($this->pl->txt(self::F_SHOW_TITLE), self::F_SHOW_TITLE);
	//		$this->form->addItem($te);
	//	}
	//
	//
	//	public function setFormValuesByArray() {
	//		$values = parent::setFormValuesByArray();
	//		$values[self::F_SHOW_TITLE] = $this->entry->getShowTitle();
	//		$this->form->setValuesByArray($values);
	//	}
	//
	//
	//	public function createEntry() {
	//		parent::createEntry();
	//		$this->entry->setShowTitle($this->form->getInput(self::F_SHOW_TITLE));
	//		$this->entry->update();
	//	}
}

?>
