<?php

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption(
    'crmtime.core_path',
    null,
    $modx->getOption('core_path') . 'components/crmtime/'
);

require_once $corePath . 'model/crmtime/crmtime.class.php';

$modx->crmtime = new CrmTime($modx);
$modx->lexicon->load('crmtime:default');

$path = $modx->getOption(
    'processorsPath',
    $modx->crmtime->config,
    $corePath . 'processors/'
);

$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));