<?php
/**
 * Статистика переходов, регистраций, заказов за период по истонику, группировка по месяцам
 *
 * @param array $stat
 */

use_helper('I18N');

// итого
$total = array(
    'clicks' => 0,
    'registrations'   => 0,
    'orders' => 0,
);

?>

<table id="ad_stat_monthly_source" class="table ad-stat-table" cellspacing="0">
    <?php include_partial('sfAdStat/table_head', array('title' => 'Месяц')) ?>
    <tbody>
    <?php foreach ($stat as $content => $row): ?>
        <?php
            $total['clicks']        += $row['clicks'];
            $total['registrations'] += $row['registrations'];
            $total['orders']        += $row['orders'];
        ?>
        <tr>
            <td class="right"><?php echo __($content) ?></td>
            <?php include_partial('sfAdStat/table_row', $row) ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td class="right">Всего за период</td>
            <?php include_partial('sfAdStat/table_row', $total) ?>
        </tr>
    </tfoot>
</table>