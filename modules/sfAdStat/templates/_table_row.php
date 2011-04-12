<?php
/**
 * Строка таблицы статистики
 *
 * @param string $clicks
 * @param string $registrations
 * @param string $orders
 */

use_helper('sfAdStat');

?>

<td class="center"><?php echo $clicks ?></td>
<td class="center"><?php echo $registrations ?></td>
<td class="center"><?php echo conversion($registrations, $clicks) ?></td>
<td class="center"><?php echo $orders ?></td>
<td class="center"><?php echo conversion($orders, $clicks) ?></td>
 
