<?php

class CrmTimeMgrUserGetListProcessor extends modProcessor
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

    protected function normalizeMoney($value, $default = '0.00')
    {
        $value = trim((string)$value);
        if ($value === '') {
            return $default;
        }

        $value = str_replace(',', '.', $value);

        if (!is_numeric($value)) {
            return $default;
        }

        return number_format((float)$value, 2, '.', '');
    }

    protected function normalizeCoeff($value, $default = '1.00')
    {
        $value = trim((string)$value);
        if ($value === '') {
            return $default;
        }

        $value = str_replace(',', '.', $value);

        if (!is_numeric($value)) {
            return $default;
        }

        return number_format((float)$value, 2, '.', '');
    }

    public function process()
    {
        $c = $this->modx->newQuery('modUser');
        $c->where(array(
            'id:>' => 0,
        ));
        $c->sortby('username', 'ASC');

        $users = $this->modx->getCollection('modUser', $c);
        $rows = array();

        foreach ($users as $user) {
            /** @var modUser $user */
            $profile = $user->getOne('Profile');
            $extended = $profile ? $this->getExtendedArray($profile) : array();

            $rows[] = array(
                'id' => (int)$user->get('id'),
                'username' => (string)$user->get('username'),
                'fullname' => $profile ? (string)$profile->get('fullname') : '',
                'email' => $profile ? (string)$profile->get('email') : '',

                'crm_active' => isset($extended['crmtime_active']) ? (int)$extended['crmtime_active'] : 1,
                'color' => !empty($extended['color']) ? (string)$extended['color'] : '#3788d8',
                'crm_code' => !empty($extended['crmtime_code']) ? (string)$extended['crmtime_code'] : '',
                'crm_note' => !empty($extended['crmtime_note']) ? (string)$extended['crmtime_note'] : '',

                'standard_rate' => $this->normalizeMoney(
                    isset($extended['standard_rate']) ? $extended['standard_rate'] : '',
                    '0.00'
                ),
                'night_coeff' => $this->normalizeCoeff(
                    isset($extended['night_coeff']) ? $extended['night_coeff'] : '',
                    '1.00'
                ),
                'sunday_coeff' => $this->normalizeCoeff(
                    isset($extended['sunday_coeff']) ? $extended['sunday_coeff'] : '',
                    '1.00'
                ),
                'holiday_coeff' => $this->normalizeCoeff(
                    isset($extended['holiday_coeff']) ? $extended['holiday_coeff'] : '',
                    '1.00'
                ),
                'home_address' => !empty($extended['home_address']) ? (string)$extended['home_address'] : '',
            );
        }

        return $this->success('', array(
            'results' => $rows,
            'total' => count($rows),
        ));
    }
}

return 'CrmTimeMgrUserGetListProcessor';