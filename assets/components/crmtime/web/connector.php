<?php

define('MODX_API_MODE', true);

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize('web');

$corePath = $modx->getOption(
    'crmtime.core_path',
    null,
    $modx->getOption('core_path') . 'components/crmtime/'
);

require_once $corePath . 'model/crmtime/crmtime.class.php';

$modx->crmtime = new CrmTime($modx);
$modx->lexicon->load('crmtime:default');

$action = isset($_REQUEST['action']) ? trim((string)$_REQUEST['action']) : '';

header('Content-Type: application/json; charset=UTF-8');

if ($action === '') {
    echo json_encode(array(
        'success' => false,
        'message' => 'Action is required',
        'object' => array(),
    ));
    exit;
}

$response = $modx->runProcessor(
    $action,
    $_REQUEST,
    array(
        'processors_path' => $modx->crmtime->config['processorsPath'] . 'web/',
    )
);

if (!$response) {
    echo json_encode(array(
        'success' => false,
        'message' => 'Processor response is empty',
        'object' => array(),
    ));
    exit;
}

echo json_encode(array(
    'success' => !$response->isError(),
    'message' => $response->getMessage(),
    'object' => $response->getObject(),
));
exit;