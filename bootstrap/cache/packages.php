<?php return array (
  'hotwired-laravel/turbo-laravel' => 
  array (
    'providers' => 
    array (
      0 => '\\HotwiredLaravel\\TurboLaravel\\TurboServiceProvider',
    ),
    'aliases' => 
    array (
      'Turbo' => '\\HotwiredLaravel\\TurboLaravel\\Facades\\Turbo',
      'TurboStream' => '\\HotwiredLaravel\\TurboLaravel\\Facades\\TurboStream',
    ),
  ),
  'kreait/laravel-firebase' => 
  array (
    'aliases' => 
    array (
      'Firebase' => 'Kreait\\Laravel\\Firebase\\Facades\\Firebase',
    ),
    'providers' => 
    array (
      0 => 'Kreait\\Laravel\\Firebase\\ServiceProvider',
    ),
  ),
  'laravel/breeze' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Breeze\\BreezeServiceProvider',
    ),
  ),
  'laravel/reverb' => 
  array (
    'aliases' => 
    array (
      'Output' => 'Laravel\\Reverb\\Output',
    ),
    'providers' => 
    array (
      0 => 'Laravel\\Reverb\\ApplicationManagerServiceProvider',
      1 => 'Laravel\\Reverb\\ReverbServiceProvider',
    ),
  ),
  'laravel/tinker' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Tinker\\TinkerServiceProvider',
    ),
  ),
  'nesbot/carbon' => 
  array (
    'providers' => 
    array (
      0 => 'Carbon\\Laravel\\ServiceProvider',
    ),
  ),
  'nunomaduro/termwind' => 
  array (
    'providers' => 
    array (
      0 => 'Termwind\\Laravel\\TermwindServiceProvider',
    ),
  ),
  'owen-it/laravel-auditing' => 
  array (
    'providers' => 
    array (
      0 => 'OwenIt\\Auditing\\AuditingServiceProvider',
    ),
  ),
  'spatie/laravel-permission' => 
  array (
    'providers' => 
    array (
      0 => 'Spatie\\Permission\\PermissionServiceProvider',
    ),
  ),
  'spatie/laravel-query-builder' => 
  array (
    'providers' => 
    array (
      0 => 'Spatie\\QueryBuilder\\QueryBuilderServiceProvider',
    ),
  ),
);