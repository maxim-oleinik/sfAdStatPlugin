<?php
/**
 * Регистрации/переходы
 *
 * @param array $row
 */
$row = $row->getRawValue();

echo link_to($row['clicks'], 'sfAdClick', array('filters' => $params->getRawValue()));
