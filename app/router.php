<?php
declare(strict_types=1);

function resolveRoute(array $jobBoard): array {
  $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
  $segments = explode('/', $uri);

  $currentView = 'list';
  $job = null;

    if ($segments[0] === 'job' && isset($segments[1])) {
        $requestedId = (int) $segments[1];
        
        foreach ($jobBoard as $j) {
            if ($j->id === $requestedId) {
                $job = $j;
                break;
            }
        } 
        if ($job) {
            if (isset($segments[2]) && $segments[2] === 'edit') {
                $currentView = 'edit';
            } else {
                $currentView = 'detail';
            }
        }
    }
    return ['view' => $currentView, 'job' => $job];
}

