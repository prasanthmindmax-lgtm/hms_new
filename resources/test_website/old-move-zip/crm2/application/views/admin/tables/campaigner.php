<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'name',
    ];

$sIndexColumn = 'campaignerid';
$sTable       = db_prefix() . 'campaigner';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['campaignerid']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name') {
            $_data = $_data;
        }
        $row[] = $_data;
    }

    $options = icon_btn('campaigners/campaigner/' . $aRow['campaignerid'], 'fa-regular fa-pen-to-square');
    $row[]   = $options;

    $output['aaData'][] = $row;
}