<?php
/**
 * Статистика переходов, регистраций, заказов за период по истонику, группировка по месяцам
 *
 * @param array $stat
 */

use_helper('I18N');

$stat = $stat->getRawValue();

$columns = sfConfig::get('app_ad_stat_plugin_columns');
$tableColumns = sfConfig::get('app_ad_stat_plugin_table_columns');

$total = array();
foreach ($tableColumns as $column) {
    $total[$column] = 0;
}

?>

<table id="ad_stat_monthly_source" class="table ad-stat-table" cellspacing="0">
    <?php include_partial('sfAdStat/table_head', array('title' => 'Месяц')) ?>
    <tbody>
    <?php foreach ($stat as $content => $row): ?>
        <?php
            foreach ($tableColumns as $column) {
                if (array_key_exists($column, $row)) {
                    $total[$column] += $row[$column];
                }
            }
        ?>
        <tr>
            <td class="right"><?php echo __($content) ?></td>
            <?php include_partial('sfAdStat/table_row', array('row' => $row)) ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td class="right">Всего за период</td>
            <?php include_partial('sfAdStat/table_row', array('row' => $total)) ?>
        </tr>
    </tfoot>
</table>