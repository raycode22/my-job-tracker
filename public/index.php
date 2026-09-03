<?php
declare(strict_types=1);
session_start();

enum JobStatus: string {
    case Applied = 'Applied';
    case Offered = 'Offered';
    case Interviewing = 'Interviewing';
    case Rejected = 'Rejected';
    case Hired = 'Hired';

    public function cssClass(): string {
        return match ($this) {
            self::Applied => 'job-applied',
            self::Offered => 'job-offered',
            self::Interviewing => 'job-interviewing',
            self::Rejected => 'job-rejected',
            self::Hired => 'job-hired',
        };
    }
}

enum Currency: string {
    case PHP = 'PHP';
    case USD = 'USD';
    case EUR = 'EUR';
    case JPY = 'JPY';
    case GBP = 'GBP';
    case CAD = 'CAD';
    case AUD = 'AUD';
    case NZD = 'NZD';

    public function flag(): string {
        return match ($this) {
            self::PHP => '🇵🇭',
            self::USD => '🇺🇸',
            self::EUR => '🇪🇺',
            self::JPY => '🇯🇵',
            self::GBP => '🇬🇧',
            self::CAD => '🇨🇦',
            self::AUD => '🇦🇺',
            self::NZD => '🇳🇿',
        };
    }
}

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
        public JobStatus $status,
        public ?float $salary,
        public Currency $currency
    ) {}    
}

function formatSalary(?float $amount, Currency $currency): string {
    if($amount === null) {
        return 'Not specified';
    }

    $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
    return $formatter->formatCurrency($amount, $currency->value);
}

$stmt = $pdo->query("SELECT id, title, company, salary, currency, status FROM jobs");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$jobBoard = array_map(fn($row) => new Job((int) $row['id'],$row['title'],$row['company'],JobStatus::from($row['status']),$row['salary'] !== null ? (float) $row['salary'] : null, Currency::from($row['currency'])), $rows);
$search = trim($_GET['q'] ?? '');
$statusFilter = JobStatus::tryFrom($_GET['status'] ?? '');

if ($statusFilter !== null) {
    $jobBoard = array_filter($jobBoard, fn(Job $job) => $job->status === $statusFilter);
}

if ($search !== '') {
    $jobBoard = array_filter($jobBoard, fn(Job $job) => stripos($job->title, $search) !== false || stripos($job->company, $search) !== false);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $deleteId = (int) $_POST['delete_id'];
        $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
        $stmt->execute([$deleteId]);

        $_SESSION['success'] = 'Success: Job deleted';
        header('Location: /');
        exit;
    } elseif (isset($_POST['update_id'])) {
        $updateId = (int) $_POST['update_id'];
        $title = trim($_POST['title'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $status = JobStatus::tryFrom ($_POST['status'] ?? '') ?? JobStatus::Applied;
        $salary = $_POST['salary'] === '' ? null : (float) $_POST['salary'];
        $currency = Currency::tryFrom($_POST['currency'] ?? '') ?? Currency::PHP;
        
        if ($title !== '' && $company !== '') {
            $stmt = $pdo->prepare("UPDATE jobs SET title = ?, company = ?, status = ?, salary = ?, currency = ? WHERE id = ?");
            $stmt->execute([$title, $company, $status->value, $salary, $currency->value, $updateId]);

            $_SESSION['success'] = 'Success: Job updated';
            header('Location: /');
            exit;
        } else {
            $_SESSION['error'] = 'Error: Please fill in all fields';
            header('Location: /job/' . $updateId . '/edit');
            exit;
        }
    } else {
        $title = trim($_POST['title'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $status = JobStatus::tryFrom ($_POST['status'] ?? '') ?? JobStatus::Applied;
        $salary = $_POST['salary'] === '' ? null : (float) $_POST['salary'];
        $currency = Currency::tryFrom ($_POST['currency'] ?? '') ?? Currency::PHP;

        if ($title !== '' && $company !== '') {
            $stmt = $pdo->prepare("INSERT INTO jobs (title, company, status, salary, currency) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $company, $status->value, $salary, $currency->value]); 

            $_SESSION['success'] = 'Success: Job added';
            header('Location: /');
            exit;
        } else {

            $_SESSION['error'] = 'Error: Please fill in all fields';
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
        .job-salary { font-size: 1.2rem; color: #888; margin: 0; }
        .job-applied { color: #414141; font-weight: bold; font-size: 1.2rem; }
        .job-offered { color: #2196F3; font-weight: bold; font-size: 1.2rem; }
        .job-interviewing { color: #FFC107; font-weight: bold; font-size: 1.2rem; }
        .job-rejected { color: #F44336; font-weight: bold; font-size: 1.2rem; }
        .job-hired { color: #4CAF50; font-weight: bold; font-size: 1.2rem; }
        .btn { display: inline-block; padding: 0.5rem 1rem; font-size: 0.9rem; font-weight: bold; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; line-height: 1; vertical-align: middle; }
        .btn-edit { background: #2196F3; color: white; }
        .btn-delete { background: #F44336; color: white; }
        form.job-form { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; margin-bottom: 2rem; padding: 1.5rem; border: 2px dashed #ccc; border-radius: 8px; }
        form.job-form input, form.job-form select { padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; font-size: 0.9rem; }
        input::placeholder, 
        textarea::placeholder { font-style: italic; }
    </style>
</head>
<body>
    <h1>JobTrackerPH</h1>
    <?php
    if (isset($_SESSION['success'])) {
        echo '<p style="color: green;">' . htmlspecialchars($_SESSION['success']) . '</p>';
        unset($_SESSION['success']);
    }

    if (isset($_SESSION['error'])) {
        echo '<p style="color: red;">' . htmlspecialchars($_SESSION['error']) . '</p>';
        unset($_SESSION['error']);
    }

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