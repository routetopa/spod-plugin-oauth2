<?php

class SPODOAUTH2CONNECT_CTRL_Edit extends BASE_CTRL_Edit {

	protected  function _removeAssignedVar($removee) {
		foreach ($this->assignedVars['questionArray'] as $form_i => $form) {
			foreach ($form as $var_i => $var) {
				if ($var['name'] === $removee) {
					unset($this->assignedVars['questionArray'][$form_i][$var_i]);
				}
			}
		}
	}

    public function index($params) {
        parent::index($params);

        // Remove the "Change apssword button"
        $this->removeComponent("changePassword");

        // Remove the "Change email" field
        $this->getForm('editForm')->deleteElement('email');
        $this->_removeAssignedVar('email');
    }
}