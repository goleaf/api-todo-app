<?php

return [
    // Time units - Lithuanian has complex pluralization rules 
    'second' => 'sekundė',
    'seconds' => '{1} :value sekundė|[2,9] :value sekundės|[10,*] :value sekundžių',
    'minute' => 'minutė',
    'minutes' => '{1} :value minutė|[2,9] :value minutės|[10,*] :value minučių',
    'hour' => 'valanda',
    'hours' => '{1} :value valanda|[2,9] :value valandos|[10,*] :value valandų',
    'day' => 'diena',
    'days' => '{1} :value diena|[2,9] :value dienos|[10,*] :value dienų',
    'week' => 'savaitė',
    'weeks' => '{1} :value savaitė|[2,9] :value savaitės|[10,*] :value savaičių',
    'month' => 'mėnuo',
    'months' => '{1} :value mėnuo|[2,9] :value mėnesiai|[10,*] :value mėnesių',
    'year' => 'metai',
    'years' => '{1} :value metai|[2,9] :value metai|[10,*] :value metų',
    
    // Time references
    'ago' => 'prieš :time',
    'from_now' => 'po :time',
    'just_now' => 'ką tik',
    'before' => ':time prieš',
    'after' => 'po :time',
    
    // Date formats
    'short_date' => 'Y-m-d',
    'long_date' => 'Y m. F d d.',
    'short_datetime' => 'Y-m-d H:i',
    'long_datetime' => 'Y m. F d d. H:i',
]; 