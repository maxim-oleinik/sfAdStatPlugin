<?php

    /**
     * Посчтитать конверсию
     *
     * @param  numeric|null $dividend
     * @param  numeric|null $divisor
     * @return numeric|string
     */
    function conversion($dividend = null, $divisor = null)
    {
        $dividend = (float) $dividend;
        $divisor  = (float) $divisor;

        if ($dividend <= 0) {
            return 0;
        }

        if ($divisor == 0) {
            return '∞';
        }

        return sprintf('%01.1f', $dividend / $divisor * 100);
    }
 
