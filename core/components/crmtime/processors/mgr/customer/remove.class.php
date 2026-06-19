<?php

class CrmTimeMgrCustomerRemoveProcessor extends modProcessor
{
    protected function removeTimesheetCascade($timesheetId)
    {
        $timesheetId = (int)$timesheetId;
        if ($timesheetId <= 0) {
            return;
        }

        $this->modx->removeCollection('CrmViolation', array(
            'timesheet_id' => $timesheetId,
        ));
        $this->modx->removeCollection('CrmViolation', array(
            'related_timesheet_id' => $timesheetId,
        ));

        $timesheet = $this->modx->getObject('CrmTimesheet', array(
            'id' => $timesheetId,
        ));

        if ($timesheet) {
            $timesheet->remove();
        }
    }

    protected function removeAssignmentCascade(CrmAssignment $assignment)
    {
        $assignmentId = (int)$assignment->get('id');

        $timesheets = $this->modx->getCollection('CrmTimesheet', array(
            'assignment_id' => $assignmentId,
        ));

        foreach ($timesheets as $timesheet) {
            $this->removeTimesheetCascade($timesheet->get('id'));
        }

        $assignment->remove();
    }

    public function process()
    {
        $id = (int)$this->getProperty('id');

        if ($id <= 0) {
            return $this->failure('Не передан ID заказчика');
        }

        /** @var CrmCustomer $customer */
        $customer = $this->modx->getObject('CrmCustomer', array(
            'id' => $id,
        ));

        if (!$customer) {
            return $this->failure('Заказчик не найден');
        }

        $assignments = $this->modx->getCollection('CrmAssignment', array(
            'customer_id' => $id,
        ));

        $affectedUsers = array();

        foreach ($assignments as $assignment) {
            $affectedUsers[(int)$assignment->get('user_id')] = (int)$assignment->get('user_id');
            $this->removeAssignmentCascade($assignment);
        }

        $workplaces = $this->modx->getCollection('CrmWorkplace', array(
            'customer_id' => $id,
        ));

        foreach ($workplaces as $workplace) {
            $workplace->remove();
        }

        if (!$customer->remove()) {
            return $this->failure('Не удалось удалить заказчика');
        }

        if ($this->modx->crmtime) {
            foreach ($affectedUsers as $userId) {
                if ($userId > 0) {
                    $this->modx->crmtime->rebuildUserViolations($userId);
                }
            }
        }

        return $this->success('Заказчик удалён', array(
            'id' => $id,
        ));
    }
}

return 'CrmTimeMgrCustomerRemoveProcessor';