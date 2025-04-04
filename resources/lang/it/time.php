<?php

return [
    // Time units - Italian has two pluralization forms
    'second' => 'secondo',
    'seconds' => '{1} :value secondo|[2,*] :value secondi',
    'minute' => 'minuto',
    'minutes' => '{1} :value minuto|[2,*] :value minuti',
    'hour' => 'ora',
    'hours' => '{1} :value ora|[2,*] :value ore',
    'day' => 'giorno',
    'days' => '{1} :value giorno|[2,*] :value giorni',
    'week' => 'settimana',
    'weeks' => '{1} :value settimana|[2,*] :value settimane',
    'month' => 'mese',
    'months' => '{1} :value mese|[2,*] :value mesi',
    'year' => 'anno',
    'years' => '{1} :value anno|[2,*] :value anni',
    
    // Time references
    'ago' => ':time fa',
    'from_now' => 'tra :time',
    'just_now' => 'proprio ora',
    'before' => ':time prima',
    'after' => ':time dopo',
    
    // Date formats
    'short_date' => 'd/m/Y',
    'long_date' => 'd F Y',
    'short_datetime' => 'd/m/Y H:i',
    'long_datetime' => 'd F Y H:i',
]; 