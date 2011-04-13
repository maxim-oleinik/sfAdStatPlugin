<?php
/**
 * Графики статистики переходов, регистраций, заказов за период
 *
 * @param array  $stat
 * @param DateTime $fromDate
 * @param DateTime $tillDate
 */

use_javascript('/sfAdStatPlugin/js/highcharts.js');

$stat = $stat->getRawValue();

$columns = sfConfig::get('app_ad_stat_plugin_columns');
$chartColumns = sfConfig::get('app_ad_stat_plugin_chart_columns');

?>

<div id="chart_container" style="height: 400px;">
</div>

<script type="text/javascript">
var chart;
$(document).ready(function() {
    chart = new Highcharts.Chart({
        chart: {
            renderTo: 'chart_container',
            defaultSeriesType: 'column'
        },
        title: {
            text: 'Статистика рекламы'
        },
        subtitle: {
            text: '<?php echo ($fromDate) ?  $fromDate->format('d.m.Y') : '?' ?> - <?php echo ($tillDate) ? $tillDate->format('d.m.Y') : '?' ?>'
        },
        xAxis: {
            categories: [
            <?php foreach ($chartColumns as $column): ?>
                '<?php echo isset($columns[$column]['title']) ? $columns[$column]['title'] : $column ?>',
            <?php endforeach; ?>
            ]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Количество'
            }
        },
        legend: {
            layout: 'vertical',
            backgroundColor: '#FFFFFF',
            align: 'left',
            verticalAlign: 'top',
            x: 100,
            y: 70,
            floating: true,
            shadow: true
        },
        tooltip: {
            formatter: function() {
                return ''+
                    this.x +': '+ this.y;
            }
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [
            <?php foreach ($stat as $key => $row): ?>
            {
                name: '<?php echo $key ?>',
                data: [
                <?php foreach ($chartColumns as $column): ?>
                    <?php echo $row[$column] ?>,
                <?php endforeach; ?>
                ]
            },
            <?php endforeach; ?>
        ]
    });
});
</script>