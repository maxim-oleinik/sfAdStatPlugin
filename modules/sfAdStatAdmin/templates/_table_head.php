<?php
/**
 * Шапка таблицы статистики
 *
 * @param string $title
 */

$columns = sfConfig::get('app_ad_stat_plugin_columns');
$tableColumns = sfConfig::get('app_ad_stat_plugin_table_columns');

?>

<thead>
    <tr>
        <th><?php echo $title ?></th>
        <?php foreach ($tableColumns as $column): ?>
        <th><?php echo isset($columns[$column]['title']) ? $columns[$column]['title'] : $column ?></th>
        <?php endforeach; ?>
    </tr>
</thead>
 
