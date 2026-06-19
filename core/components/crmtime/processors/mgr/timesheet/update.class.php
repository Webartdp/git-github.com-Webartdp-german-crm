<?php

class CrmTimeMgrTimesheetUpdateProcessor extends modProcessor
{
    public function process()
    {
        $id = (int)$this->getProperty('id');
        $assignmentId = (int)$this->getProperty('assignment_id');
        $workDate = trim((string)$this->getProperty('work_date'));
        $startTime = trim((string)$this->getProperty('start_time'));
        $endTime = trim((string)$this->getProperty('end_time'));
        $isNight = (int)$this->getProperty('is_night') ? 1 : 0;
        $isSunday = (int)$this->getProperty('is_sunday') ? 1 : 0;
        $isHoliday = (int)$this->getProperty('is_holiday') ? 1 : 0;

        if ($id <= 0) {
            return $this->failure('Не передан ID записи');
        }

        if ($assignmentId <= 0) {
            return $this->failure('Не выбрано назначение');
        }

        if ($workDate === '') {
            return $this->failure('Не указана дата');
        }

        if ($startTime === '' || $endTime === '') {
            return $this->failure('Не указано время');
        }

        /** @var CrmTimesheet $timesheet */
        $timesheet = $this->modx->getObject('CrmTimesheet', array(
            'id' => $id,
        ));

        if (!$timesheet) {
            return $this->failure('Запись времени не найдена');
        }

        /** @var CrmAssignment $assignment */
        $assignment = $this->modx->getObject('CrmAssignment', array(
            'id' => $assignmentId,
        ));

        if (!$assignment) {
            return $this->failure('Назначение не найдено');
        }

        $timesheet->set('assignment_id', $assignmentId);
        $timesheet->set('work_date', $workDate);
        $timesheet->set('start_time', $startTime);
        $timesheet->set('end_time', $endTime);
        $timesheet->set('is_night', $isNight);
        $timesheet->set('is_sunday', $isSunday);
        $timesheet->set('is_holiday', $isHoliday);

        if (!$timesheet->save()) {
            return $this->failure('Не удалось обновить запись времени');
        }

        return $this->success('Запись времени обновлена', array(
            'id' => (int)$timesheet->get('id'),
        ));
    }
}

return 'CrmTimeMgrTimesheetUpdateProcessor';