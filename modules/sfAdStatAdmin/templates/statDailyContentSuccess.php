<?php
/**
 * Статистика переходов, регистраций, заказов за период по объявлению, группировка по дням
 *
 * @param string       $source
 * @param DateTime     $fromDate
 * @param DateTime     $tillDate
 * @param array        $stat
 * @param sfFormFilter $filter
 */
?>

<div id="sf_admin_container">
    <h1>Статистика рекламы по объявлению "<?php echo $content ?>" (<?php echo ($fromDate) ?  $fromDate->format('d.m.Y') : '?' ?> &mdash; <?php echo ($tillDate) ? $tillDate->format('d.m.Y') : '?' ?>)</h1>
    <h3><?php echo link_to('Вернуться к общей статистике', 'sfAdStatStat') ?></h3>

    <?php include_partial('filter', array('route' => 'sfAdStatStatDailyContent', 'routeParams' => array('content' => $content), 'filter' => $filter)) ?>

    <div id="sf_admin_content">
        <div class="sf_admin_list">
            <table cellspacing="0">
                <?php include_partial('table_head', array('title' => 'Дата')) ?>
                <tbody>
                <?php foreach ($stat as $content => $row): ?>
                    <tr>
                        <td><?php echo $content ?></td>
                        <?php include_partial('table_row', array('row' => $row)) ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php include_partial('chart_line', array('stat' => $stat, 'fromDate' => $fromDate, 'tillDate' => $tillDate)) ?>
    </div>
</div>