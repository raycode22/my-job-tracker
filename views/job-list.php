<!-- views/job-list.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Jobs - Job Tracker</title>
    <style>
        /* Move your existing CSS here */
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
    <?php foreach ($jobs as $job): ?>
        <?php $statusClass = getStatusClass($job->status); ?>
        <a href="/job/<?= $job->id ?>" class="job-card">
            <div class="job-stat-con-left">
                <h2 class="job-title"><?= htmlspecialchars($job->title) ?></h2>
                <p class="job-company"><?= htmlspecialchars($job->company) ?></p>
            </div>
            <div class="job-stat-con-right">
                <span class="job-status <?= $statusClass ?>"><?= htmlspecialchars($job->status) ?></span>
            </div>
        </a>
    <?php endforeach; ?>
    <p style="margin-top: 2rem;"><a href="/">← Back to all jobs</a></p>
</body>
</html>