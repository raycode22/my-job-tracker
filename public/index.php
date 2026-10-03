<?php
declare(strict_types=1);
//ini_set('display_errors', 1); error_reporting(E_ALL);
session_start();

use App\Models\Job;
use App\Models\JobStatus;
use App\Models\Currency;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/helpers.php';
require __DIR__ . '/../app/handlers.php';
require __DIR__ . '/../app/router.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$pdo = require __DIR__ . '/../app/database.php';
$stmt = $pdo->query("SELECT id, title, company, salary, currency, status FROM jobs");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$jobBoard = array_map(fn($row) => new Job((int) $row['id'],$row['title'],$row['company'],JobStatus::from($row['status']),$row['salary'] !== null ? (float) $row['salary'] : null, Currency::from($row['currency'])), $rows);
$search = trim($_GET['q'] ?? '');
$statusFilter = JobStatus::tryFrom($_GET['status'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handlePostRequest($pdo);
}

if ($statusFilter !== null) {
    $jobBoard = array_filter($jobBoard, fn(Job $job) => $job->status === $statusFilter);
}

if ($search !== '') {
    $jobBoard = array_filter($jobBoard, fn(Job $job) => stripos($job->title, $search) !== false || stripos($job->company, $search) !== false);
}

$currentView = 'list';
$job = null;

$route = resolveRoute($jobBoard);
$currentView = $route['view'];
$job = $route['job'];

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
        textarea::placeholder { font-style: itssalic; }
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