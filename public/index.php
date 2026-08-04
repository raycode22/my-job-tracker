<?php
  $host = 'db';
  $db   = 'db';
  $user = 'db';
  $pass = 'db';
  $charset = 'utf8mb4';

  $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
  $options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Default to associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements
  ];
  try {
    $pdo = new PDO($dsn, $user, $pass, $options);
  } catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
  }

  $stmt = $pdo->query("SELECT id, title, company, status FROM jobs");
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $jobBoard = array_map(fn($row) => new Job(
    (int) $row['id'],
    $row['title'],
    $row['company'],
    $row['status']
    ), $rows);

  class Job{
    public function __construct(public int $id, public string $title, public string $company, public string $status) {
    }
  }

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
  if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $deleteId = (int) $_POST['delete_id'];
        $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
        $stmt->execute([$deleteId]);
        
        header('Location: /');
        exit;
    } 
    else {
        $title = trim($_POST['title'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $status = $_POST['status'] ?? 'Applied';

        if ($title !== '' && $company !== '') {
            $stmt = $pdo->prepare("INSERT INTO jobs (title, company, status) VALUES (?, ?, ?)");
            $stmt->execute([$title, $company, $status]);
            
            header('Location: /');
            exit;
        }
    }
}
  $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
  $segments = explode('/', $uri);
  $requestedId = 0;

    if ($segments[0] === 'job' && isset($segments[1])) {
        $requestedId = (int) $segments[1];
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
      if($requestedId > 0) {
        foreach ($jobBoard as $job) {
          if($job->id === $requestedId) {
            require __DIR__ . '/../views/job-details.php';
            break;
          }
        }
      }else {
        $jobs = $jobBoard;
        require __DIR__ . '/../views/job-list.php';
      }
    ?>
    
</body>
</html>