<?php

class CrmTimeMgrPingProcessor extends modProcessor
{
    public function process()
    {
        return $this->success('crmTime connector works', array(
            'time' => date('Y-m-d H:i:s'),
            'user' => $this->modx->user ? $this->modx->user->get('username') : '',
        ));
    }
}

return 'CrmTimeMgrPingProcessor';