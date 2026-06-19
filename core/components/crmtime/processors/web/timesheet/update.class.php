<?php

class CrmTimeWebTimesheetUpdateProcessor extends modProcessor
{
    public function process()
    {
        if (!$this->modx->user || !$this->modx->user->isAuthenticated('web')) {
            return $this->failure('Требуется авторизация');
        }

        $userId = (int)$this->modx->user->get('id');

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

        /** @var CrmTimesheet $timesheet */
        $timesheet = $this->modx->getObject('CrmTimesheet', array(
            'id' => $id,
        ));

        if (!$timesheet) {
            return $this->failure('Запись не найдена');
        }

        /** @var CrmAssignment $currentAssignment */
        $currentAssignment = $this->modx->getObject('CrmAssignment', array(
            'id' => (int)$timesheet->get('assignment_id'),
        ));

        if (!$currentAssignment || (int)$currentAssignment->get('user_id') !== $userId) {
            return $this->failure('Нет доступа к записи');
        }

        /** @var CrmAssignment $assignment */
        $assignment = $this->modx->getObject('CrmAssignment', array(
            'id' => $assignmentId,
        ));

        if (!$assignment) {
            return $this->failure('Назначение не найдено');
        }

        if ((int)$assignment->get('user_id') !== $userId) {
            return $this->failure('Назначение не принадлежит текущему сотруднику');
        }

        if ($workDate === '' || $startTime === '' || $endTime === '') {
            return $this->failure('Заполните дату и время');
        }

        $timesheet->set('assignment_id', $assignmentId);
        $timesheet->set('work_date', $workDate);
        $timesheet->set('start_time', $startTime);
        $timesheet->set('end_time', $endTime);
        $timesheet->set('is_night', $isNight);
        $timesheet->set('is_sunday', $isSunday);
        $timesheet->set('is_holiday', $isHoliday);

        if (!$timesheet->save()) {
            return $this->failure('Не удалось обновить запись');
        }

        return $this->success('Запись обновлена', array(
            'id' => (int)$timesheet->get('id'),
        ));
    }
}

return 'CrmTimeWebTimesheetUpdateProcessor';