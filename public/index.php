<?php
  $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
  $segments = explode('/', $uri);
  $requestedId = 0;

    if ($segments[0] === 'job' && isset($segments[1])) {
        $requestedId = (int) $segments[1];
    }

  class Job{
    public function __construct(public int $id, public string $title, public string $company, public string $status) {
    }
  }
  $jobBoard = [
    new Job(1, "Senior PHP Developer", "Acme Corp.", "Applied"),
    new Job(2, "Junior PHP Developer", "Century Royale", "Interviewing"),
    new Job(3, "PHP Intern", "Tron Technologies", "Offered"),
    new Job(4, "PHP Intern", "Adena Inc.", "Rejected"),
  ];
  function getStatusClass(string $status): string 
    {
      return match ($status) {
          'Applied'      => 'job-applied',
          'Offered'      => 'job-offered',    
          'Interviewing' => 'job-interviewing',
          'Rejected'     => 'job-rejected',
          default        => '',
      };
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Tracker</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; padding: 2em 4em; font-family: system-ui, -apple-system, sans-serif; line-height: 1.5; color-scheme: light dark; }
        h1 { font-size: 2rem; margin-bottom: 1rem; }
        .job-card { display: flex; align-items: center; padding: 1.5rem; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 0.75rem; }
        .job-stat-con-left { flex-grow: 1; }
        .job-title { font-size: 1.4rem; margin: 0; }
        .job-company { font-size: 1.2rem; color: #555; margin: 0; }
        .job-applied { color: #4CAF50; font-weight: bold; font-size: 1.2rem; }
        .job-offered { color: #2196F3; font-weight: bold; font-size: 1.2rem; }
        .job-interviewing { color: #FFC107; font-weight: bold; font-size: 1.2rem; }
        .job-rejected { color: #F44336; font-weight: bold; font-size: 1.2rem; }
    </style>
</head>
<body>
    <h1>My Job Tracker</h1>
    <?php
      function renderJob (Job $job): string {
        $dynamicClass = getStatusClass($job->status);
        return <<<HTML
          <div class="job-card">
            <div class="job-stat-con-left">
              <h2 class="job-title">$job->title</h2>
              <p class="job-company">$job->company</p>
            </div>
            <div class="job-stat-con-right">
              <span class="job-status {$dynamicClass}">$job->status</span>
            </div>
          </div>
        HTML;
      }
      if($requestedId > 0) {
        foreach ($jobBoard as $job) {
          if($job->id === $requestedId) {
            echo renderJob($job);
            break;
          }
        }
      }else {
        foreach ($jobBoard as $job){
          echo renderJob($job);
        }
      }
    ?>
    <p style="margin-top: 2rem;"><a href="/">← Back to all jobs</a></p>
</body>
</html>