<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once('./Modules/Cloud/classes/class.ilCloudPluginCreationGUI.php');

/**
 * Class ilOneDriveCreationGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilOneDriveCreationGUI extends ilCloudPluginCreationGUI {

	const F_BASE_FOLDER = 'base_folder';
	const F_DEFAULT_BASE_FOLDER = 'default_base_folder';
	const F_CUSTOM_FOLDER_SELECTION = 'custom_folder_selection';
	const F_CUSTOM_BASE_FOLDER_INPUT = 'custom_base_folder_input';


	/**
	 * @param ilRadioOption $option
	 */
	public function initPluginCreationFormSection(ilRadioOption $option) {
		//		$option->setInfo($this->txt('create_info1') . '</br>' . $this->txt('create_info2') . $this->getAdminConfigObject()->getAppName()
		//			. $this->txt('create_info3'));
		$sub_selection = new ilRadioGroupInputGUI($this->txt(self::F_BASE_FOLDER), self::F_BASE_FOLDER);
		$sub_selection->setRequired(true);

		$option_default = new ilRadioOption($this->txt(self::F_DEFAULT_BASE_FOLDER), self::F_DEFAULT_BASE_FOLDER);

		$option_custom = new ilRadioOption($this->txt('custom_base_folder'), self::F_CUSTOM_FOLDER_SELECTION);
		$custom_base_folder_input = new ilTextInputGUI($this->txt(self::F_CUSTOM_BASE_FOLDER_INPUT), self::F_CUSTOM_BASE_FOLDER_INPUT);
		$custom_base_folder_input->setRequired(true);
		$custom_base_folder_input->setInfo($this->txt('custom_base_folder_input_info'));
		$option_custom->addSubItem($custom_base_folder_input);

		$sub_selection->addOption($option_default);
		$sub_selection->addOption($option_custom);

		$sub_selection->setValue(self::F_DEFAULT_BASE_FOLDER);

		$option->addSubItem($sub_selection);
	}


	/**
	 * @param ilPropertyFormGUI $form
	 * @param ilObjCloud        $obj
	 */
	public function afterSavePluginCreation(ilObjCloud &$obj, ilPropertyFormGUI $form) {
		if ($form->getInput(self::F_BASE_FOLDER) == self::F_DEFAULT_BASE_FOLDER) {
			$obj->setRootFolder($obj->getTitle());
		} else {
			$obj->setRootFolder($form->getInput(self::F_CUSTOM_BASE_FOLDER_INPUT));
		}
	}
}

?>