<?php

class CrmTimeWebViolationMyListProcessor extends modProcessor
{
    protected function getOwnAssignmentIds($userId)
    {
        $ids = array();

        $c = $this->modx->newQuery('CrmAssignment');
        $c->where(array(
            'user_id' => (int)$userId,
        ));

        $assignments = $this->modx->getCollection('CrmAssignment', $c);
        /** @var CrmAssignment $assignment */
        foreach ($assignments as $assignment) {
            $ids[] = (int)$assignment->get('id');
        }

        return $ids;
    }

    protected function getOwnTimesheetIds($assignmentIds)
    {
        $ids = array();

        if (empty($assignmentIds)) {
            return $ids;
        }

        $c = $this->modx->newQuery('CrmTimesheet');
        $c->where(array(
            'assignment_id:IN' => $assignmentIds,
        ));

        $timesheets = $this->modx->getCollection('CrmTimesheet', $c);
        /** @var CrmTimesheet $timesheet */
        foreach ($timesheets as $timesheet) {
            $ids[] = (int)$timesheet->get('id');
        }

        return $ids;
    }

    public function process()
    {
        if (!$this->modx->user || !$this->modx->user->isAuthenticated($this->modx->context->key)) {
            return $this->failure('Nicht autorisiert.', array(
                'code' => 401,
            ));
        }

        $userId = (int)$this->modx->user->get('id');
        $assignmentIds = $this->getOwnAssignmentIds($userId);
        $timesheetIds = $this->getOwnTimesheetIds($assignmentIds);

        if (empty($timesheetIds)) {
            return $this->success('', array(
                'results' => array(),
                'total' => 0,
            ));
        }

        $c = $this->modx->newQuery('CrmViolation');
        $c->where(array(
            'timesheet_id:IN' => $timesheetIds,
        ));
        $c->sortby('id', 'DESC');

        $violations = $this->modx->getCollection('CrmViolation', $c);
        $rows = array();

        /** @var CrmViolation $violation */
        foreach ($violations as $violation) {
            $timesheet = $this->modx->getObject('CrmTimesheet', array(
                'id' => (int)$violation->get('timesheet_id'),
            ));

            $related = $this->modx->getObject('CrmTimesheet', array(
                'id' => (int)$violation->get('related_timesheet_id'),
            ));

            $rows[] = array(
                'id' => (int)$violation->get('id'),
                'timesheet_id' => (int)$violation->get('timesheet_id'),
                'related_timesheet_id' => (int)$violation->get('related_timesheet_id'),
                'direction' => (string)$violation->get('direction'),
                'rest_hours' => (string)$violation->get('rest_hours'),
                'required_hours' => (string)$violation->get('required_hours'),
                'message' => (string)$violation->get('message'),
                'timesheet_date' => $timesheet ? (string)$timesheet->get('work_date') : '',
                'timesheet_start' => $timesheet ? (string)$timesheet->get('start_time') : '',
                'timesheet_end' => $timesheet ? (string)$timesheet->get('end_time') : '',
                'related_date' => $related ? (string)$related->get('work_date') : '',
                'related_start' => $related ? (string)$related->get('start_time') : '',
                'related_end' => $related ? (string)$related->get('end_time') : '',
            );
        }

        return $this->success('', array(
            'results' => $rows,
            'total' => count($rows),
        ));
    }
}

return 'CrmTimeWebViolationMyListProcessor';