<?php

require_once dirname(__FILE__) . '/model/crmtime/crmtime.class.php';

class CrmtimeIndexManagerController extends modExtraManagerController
{
    /** @var CrmTime $crmtime */
    public $crmtime;

    public function initialize()
    {
        $this->crmtime = new CrmTime($this->modx);
        parent::initialize();
    }

    public function process(array $scriptProperties = array())
    {
    }

    public function getPageTitle()
    {
        return 'crmTime';
    }

    public function getLanguageTopics()
    {
        return array('crmtime:default');
    }

    public function checkPermissions()
    {
        return true;
    }

    public function loadCustomCssJs()
    {
        $this->addCss($this->crmtime->config['cssUrl'] . 'mgr/main.css');

        $fcJsUrl = $this->crmtime->config['assetsUrl'] . 'vendor/fullcalendar/index.global.min.js';
        $fcJsPath = MODX_BASE_PATH . ltrim(parse_url($fcJsUrl, PHP_URL_PATH), '/');
        $hasCalendarAssets = file_exists($fcJsPath);

        if ($hasCalendarAssets) {
            $this->addJavascript($fcJsUrl);
        }

        $this->addJavascript($this->crmtime->config['jsUrl'] . 'mgr/index.js');

        $config = array(
            'connector_url' => $this->crmtime->config['connectorUrl'],
            'calendar_enabled' => $hasCalendarAssets ? 1 : 0,
        );

        $this->addHtml(
            '<script>window.crmTimeConfig = ' . json_encode($config) . ';</script>'
        );
    }

    public function getTemplateFile()
    {
        return dirname(__FILE__) . '/templates/default/index.tpl';
    }
}