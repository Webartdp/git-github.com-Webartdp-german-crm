<?php

class CrmTimeWebTimesheetSubmitProcessor extends modProcessor
{
    protected function getCurrentUserId()
    {
        if (!$this->modx->user || !$this->modx->user->isAuthenticated($this->modx->context->key)) {
            return 0;
        }

        return (int)$this->modx->user->get('id');
    }

    protected function getOwnTimesheet($timesheetId, $userId)
    {
        $timesheet = $this->modx->getObject('CrmTimesheet', array(
            'id' => (int)$timesheetId,
        ));

        if (!$timesheet) {
            return null;
        }

        $assignment = $this->modx->getObject('CrmAssignment', array(
            'id' => (int)$timesheet->get('assignment_id'),
        ));

        if (!$assignment) {
            return null;
        }

        if ((int)$assignment->get('user_id') !== (int)$userId) {
            return null;
        }

        return $timesheet;
    }

    public function process()
    {
        $userId = $this->getCurrentUserId();
        if ($userId <= 0) {
            return $this->failure('Nicht autorisiert.', array(
                'code' => 401,
            ));
        }

        $id = (int)$this->getProperty('id');
        if ($id <= 0) {
            return $this->failure('Eintrags-ID fehlt.');
        }

        $timesheet = $this->getOwnTimesheet($id, $userId);
        if (!$timesheet) {
            return $this->failure('Zeiteintrag nicht gefunden oder kein Zugriff.');
        }

        $status = (string)$timesheet->get('status');
        if (!in_array($status, array('draft', 'rejected'), true)) {
            return $this->failure('Nur Entwürfe oder abgelehnte Einträge können eingereicht werden.');
        }

        $timesheet->set('status', 'submitted');

        if (!$timesheet->save()) {
            return $this->failure('Zeiteintrag konnte nicht eingereicht werden.');
        }

        return $this->success('Zeiteintrag wurde zur Freigabe eingereicht.', array(
            'id' => (int)$timesheet->get('id'),
            'status' => 'submitted',
        ));
    }
}

return 'CrmTimeWebTimesheetSubmitProcessor';