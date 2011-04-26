<?php
/**
 * Строка таблицы статистики
 *
 * @param array $row
 */

$columns = sfConfig::get('app_ad_stat_plugin_columns');
$tableColumns = sfConfig::get('app_ad_stat_plugin_table_columns');

$row = $row->getRawValue();

?>

<?php foreach ($tableColumns as $column): ?>
<td>
    <?php if (isset($columns[$column]['partial'])): ?>
        <?php include_partial($columns[$column]['partial'], array('row' => $row, 'params' => $params->getRawValue())) ?>
    <?php elseif (array_key_exists($column, $row)): ?>
        <?php echo $row[$column] ?>
    <?php else: ?>
        &mdash;
    <?php endif; ?>
</td>
<?php endforeach; ?>
