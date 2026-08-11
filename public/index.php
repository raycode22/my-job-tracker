<?php
$host = 'db';
$db   = 'db';
$user = 'db';
$pass = 'db';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

class Job {
    public function __construct(
        public int $id, 
        public string $title, 
        public string $company, 
        public string $status
    ) {}
}

function getStatusClass(string $status): string {
    return match ($status) {
        'Applied'      => 'job-applied',
        'Offered'      => 'job-offered',    
        'Interviewing' => 'job-interviewing',
        'Rejected'     => 'job-rejected',
        default        => '',
    };
}

$stmt = $pdo->query("SELECT id, title, company, status FROM jobs");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$jobBoard = array_map(fn($row) => new Job(
    (int) $row['id'],
    $row['title'],
    $row['company'],
    $row['status']
), $rows);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $deleteId = (int) $_POST['delete_id'];
        $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
        $stmt->execute([$deleteId]);
        
        header('Location: /');
        exit;
    } elseif (isset($_POST['update_id'])) {
        $updateId = (int) $_POST['update_id'];
        $title = trim($_POST['title'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $status = $_POST['status'] ?? 'Applied';

        if ($title !== '' && $company !== '') {
            $stmt = $pdo->prepare("UPDATE jobs SET title = ?, company = ?, status = ? WHERE id = ?");
            $stmt->execute([$title, $company, $status, $updateId]);
            
            header('Location: /');
            exit;
        }
    } else {
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
        .job-stat-con-left { flex-grow: 1; text-decoration: none; color: inherit; }
        .job-stat-con-right { display: flex; align-items: center; gap: 1rem; }
        .job-title { font-size: 1.4rem; margin: 0; }
        .job-company { font-size: 1.2rem; color: #555; margin: 0; }
        .job-applied { color: #4CAF50; font-weight: bold; font-size: 1.2rem; }
        .job-offered { color: #2196F3; font-weight: bold; font-size: 1.2rem; }
        .job-interviewing { color: #FFC107; font-weight: bold; font-size: 1.2rem; }
        .job-rejected { color: #F44336; font-weight: bold; font-size: 1.2rem; }
        .btn { display: inline-block; padding: 0.5rem 1rem; font-size: 0.9rem; font-weight: bold; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; line-height: 1; vertical-align: middle; }
        .btn-edit { background: #2196F3; color: white; }
        .btn-delete { background: #F44336; color: white; }
        form.add-job-form { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; margin-bottom: 2rem; padding: 1.5rem; border: 2px dashed #ccc; border-radius: 8px; }
        form.add-job-form input, form.add-job-form select { padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <h1>My Job Tracker</h1>
    <?php
    if ($currentView === 'edit') {
        require __DIR__ . '/../views/job-edit.php';
    } elseif ($currentView === 'detail') {
        require __DIR__ . '/../views/job-details.php';
    } else {
        $jobs = $jobBoard;
        require __DIR__ . '/../views/job-list.php';
    }
    ?>
</body>
</html>