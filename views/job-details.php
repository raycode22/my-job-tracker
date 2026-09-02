<h2>Job Details</h2>
<div class="job-card">
    <div class="job-stat-con-left">
        <h2 class="job-title"><?= htmlspecialchars($job->title) ?></h2>
        <p class="job-company"><?= htmlspecialchars($job->company) ?></p>
        <p class="job-salary"><?= ($job->salary ?? "") ? number_format($job->salary, 2) : '0.00' ?></p>
    </div>
    <div class="job-stat-con-right">
        <span class="<?= $job->status->cssClass() ?>">
            <?= htmlspecialchars($job->status->value) ?>
        </span>
    </div>
</div>
<p style="margin-top: 2rem;">
    <a href="/">← Back to all jobs</a>
</p>