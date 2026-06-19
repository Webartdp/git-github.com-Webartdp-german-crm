<?php

class CrmTimeMgrEmployeeUpdateProcessor extends modProcessor
{
    protected function getExtendedArray(modUserProfile $profile)
    {
        $extended = $profile->get('extended');

        if (is_array($extended)) {
            return $extended;
        }

        if (is_string($extended) && $extended !== '') {
            $decoded = json_decode($extended, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return array();
    }

    protected function normalizeDecimal($value, $default = '0.00')
    {
        $value = trim((string)$value);

        if ($value === '') {
            return $default;
        }

        $value = str_replace(',', '.', $value);

        if (!is_numeric($value)) {
            return false;
        }

        return number_format((float)$value, 2, '.', '');
    }

    public function process()
    {
        $id = (int)$this->getProperty('id');
        $crmActive = (int)$this->getProperty('crm_active');
        $color = trim((string)$this->getProperty('color'));
        $crmCode = trim((string)$this->getProperty('crm_code'));
        $crmNote = trim((string)$this->getProperty('crm_note'));

        $standardRate = $this->normalizeDecimal($this->getProperty('standard_rate'), '0.00');
        $nightCoeff = $this->normalizeDecimal($this->getProperty('night_coeff'), '1.00');
        $sundayCoeff = $this->normalizeDecimal($this->getProperty('sunday_coeff'), '1.00');
        $holidayCoeff = $this->normalizeDecimal($this->getProperty('holiday_coeff'), '1.00');
        $homeAddress = trim((string)$this->getProperty('home_address'));

        if ($id <= 0) {
            return $this->failure('Не передан ID сотрудника');
        }

        if ($color === '') {
            $color = '#3788d8';
        }

        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            return $this->failure('Цвет должен быть в формате #RRGGBB');
        }

        if ($standardRate === false) {
            return $this->failure('Стандартная оплата должна быть числом');
        }

        if ($nightCoeff === false) {
            return $this->failure('Ночной тариф должен быть числом');
        }

        if ($sundayCoeff === false) {
            return $this->failure('Воскресный коэффициент должен быть числом');
        }

        if ($holidayCoeff === false) {
            return $this->failure('Праздничный коэффициент должен быть числом');
        }

        if ((float)$nightCoeff <= 0) {
            return $this->failure('Ночной коэффициент должен быть больше 0');
        }

        if ((float)$sundayCoeff <= 0) {
            return $this->failure('Воскресный коэффициент должен быть больше 0');
        }

        if ((float)$holidayCoeff <= 0) {
            return $this->failure('Праздничный коэффициент должен быть больше 0');
        }

        /** @var modUser $user */
        $user = $this->modx->getObject('modUser', array(
            'id' => $id,
        ));

        if (!$user) {
            return $this->failure('Пользователь не найден');
        }

        /** @var modUserProfile $profile */
        $profile = $user->getOne('Profile');

        if (!$profile) {
            return $this->failure('Профиль пользователя не найден');
        }

        $extended = $this->getExtendedArray($profile);

        $extended['color'] = $color;
        $extended['crmtime_active'] = $crmActive ? 1 : 0;
        $extended['crmtime_code'] = $crmCode;
        $extended['crmtime_note'] = $crmNote;

        $extended['standard_rate'] = $standardRate;
        $extended['night_coeff'] = $nightCoeff;
        $extended['sunday_coeff'] = $sundayCoeff;
        $extended['holiday_coeff'] = $holidayCoeff;
        $extended['home_address'] = $homeAddress;

        $profile->set('extended', $extended);

        if (!$profile->save()) {
            return $this->failure('Не удалось сохранить CRM-настройки сотрудника');
        }

        return $this->success('CRM-настройки сотрудника сохранены', array(
            'id' => $id,
            'color' => $color,
            'crm_active' => $crmActive ? 1 : 0,
            'crm_code' => $crmCode,
            'crm_note' => $crmNote,
            'standard_rate' => $standardRate,
            'night_coeff' => $nightCoeff,
            'sunday_coeff' => $sundayCoeff,
            'holiday_coeff' => $holidayCoeff,
            'home_address' => $homeAddress,
        ));
    }
}

return 'CrmTimeMgrEmployeeUpdateProcessor';