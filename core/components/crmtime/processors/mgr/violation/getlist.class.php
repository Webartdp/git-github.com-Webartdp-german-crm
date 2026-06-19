<?php

class CrmTimeMgrViolationGetListProcessor extends modProcessor
{
    public function process()
    {
        $c = $this->modx->newQuery('CrmViolation');
        $c->sortby('id', 'DESC');

        $violations = $this->modx->getCollection('CrmViolation', $c);
        $rows = array();

        foreach ($violations as $violation) {
            $userId = (int)$violation->get('user_id');
            $timesheetId = (int)$violation->get('timesheet_id');
            $relatedTimesheetId = (int)$violation->get('related_timesheet_id');

            $user = $this->modx->getObject('modUser', array(
                'id' => $userId,
            ));
            $profile = $this->modx->getObject('modUserProfile', array(
                'internalKey' => $userId,
            ));

            $timesheet = $this->modx->getObject('CrmTimesheet', array(
                'id' => $timesheetId,
            ));
            $relatedTimesheet = $this->modx->getObject('CrmTimesheet', array(
                'id' => $relatedTimesheetId,
            ));

            $rows[] = array(
                'id' => (int)$violation->get('id'),
                'user_id' => $userId,
                'username' => $user ? $user->get('username') : '',
                'fullname' => $profile ? $profile->get('fullname') : '',
                'timesheet_id' => $timesheetId,
                'related_timesheet_id' => $relatedTimesheetId,
                'timesheet_date' => $timesheet ? $timesheet->get('work_date') : '',
                'timesheet_start' => $timesheet ? $timesheet->get('start_time') : '',
                'timesheet_end' => $timesheet ? $timesheet->get('end_time') : '',
                'related_date' => $relatedTimesheet ? $relatedTimesheet->get('work_date') : '',
                'related_start' => $relatedTimesheet ? $relatedTimesheet->get('start_time') : '',
                'related_end' => $relatedTimesheet ? $relatedTimesheet->get('end_time') : '',
                'direction' => $violation->get('direction'),
                'rest_hours' => $violation->get('rest_hours'),
                'required_hours' => $violation->get('required_hours'),
                'message' => $violation->get('message'),
                'createdon' => $violation->get('createdon'),
            );
        }

        return $this->success('', array(
            'results' => $rows,
            'total' => count($rows),
        ));
    }
}

return 'CrmTimeMgrViolationGetListProcessor';