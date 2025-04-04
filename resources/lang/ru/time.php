<?php

return [
    // Time units - Russian has complex pluralization rules with 3 forms
    'second' => 'секунда',
    'seconds' => '{1} :value секунда|[2,4] :value секунды|[5,*] :value секунд',
    'minute' => 'минута',
    'minutes' => '{1} :value минута|[2,4] :value минуты|[5,*] :value минут',
    'hour' => 'час',
    'hours' => '{1} :value час|[2,4] :value часа|[5,*] :value часов',
    'day' => 'день',
    'days' => '{1} :value день|[2,4] :value дня|[5,*] :value дней',
    'week' => 'неделя',
    'weeks' => '{1} :value неделя|[2,4] :value недели|[5,*] :value недель',
    'month' => 'месяц',
    'months' => '{1} :value месяц|[2,4] :value месяца|[5,*] :value месяцев',
    'year' => 'год',
    'years' => '{1} :value год|[2,4] :value года|[5,*] :value лет',
    
    // Time references
    'ago' => ':time назад',
    'from_now' => 'через :time',
    'just_now' => 'только что',
    'before' => ':time до',
    'after' => ':time после',
    
    // Date formats
    'short_date' => 'd.m.Y',
    'long_date' => 'd F Y',
    'short_datetime' => 'd.m.Y H:i',
    'long_datetime' => 'd F Y H:i',
]; 