<?php

class CrmTimeMgrTimesheetCreateProcessor extends modProcessor
{
    public function process()
    {
        $assignmentId = (int)$this->getProperty('assignment_id');
        $workDate = trim((string)$this->getProperty('work_date'));
        $startTime = trim((string)$this->getProperty('start_time'));
        $endTime = trim((string)$this->getProperty('end_time'));
        $isNight = (int)$this->getProperty('is_night') ? 1 : 0;
        $isSunday = (int)$this->getProperty('is_sunday') ? 1 : 0;
        $isHoliday = (int)$this->getProperty('is_holiday') ? 1 : 0;

        if ($assignmentId <= 0) {
            return $this->failure('Не выбрано назначение');
        }

        if ($workDate === '') {
            return $this->failure('Не указана дата');
        }

        if ($startTime === '' || $endTime === '') {
            return $this->failure('Не указано время');
        }

        /** @var CrmAssignment $assignment */
        $assignment = $this->modx->getObject('CrmAssignment', array(
            'id' => $assignmentId,
        ));

        if (!$assignment) {
            return $this->failure('Назначение не найдено');
        }

        /** @var CrmTimesheet $timesheet */
        $timesheet = $this->modx->newObject('CrmTimesheet');
        if (!$timesheet) {
            return $this->failure('Не удалось создать запись времени');
        }

        $timesheet->set('assignment_id', $assignmentId);
        $timesheet->set('work_date', $workDate);
        $timesheet->set('start_time', $startTime);
        $timesheet->set('end_time', $endTime);
        $timesheet->set('is_night', $isNight);
        $timesheet->set('is_sunday', $isSunday);
        $timesheet->set('is_holiday', $isHoliday);

        if ($timesheet->get('status') === null || $timesheet->get('status') === '') {
            $timesheet->set('status', 'draft');
        }

        if (!$timesheet->save()) {
            return $this->failure('Не удалось сохранить запись времени');
        }

        return $this->success('Запись времени сохранена', array(
            'id' => (int)$timesheet->get('id'),
        ));
    }
}

return 'CrmTimeMgrTimesheetCreateProcessor';