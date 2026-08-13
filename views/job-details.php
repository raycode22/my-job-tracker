<h2>Job Details</h2>
<div class="job-card">
    <div class="job-stat-con-left">
        <h2 class="job-title"><?= htmlspecialchars($job->title) ?></h2>
        <p class="job-company"><?= htmlspecialchars($job->company) ?></p>
    </div>
    <div class="job-stat-con-right">
        <span class="<?= getStatusClass($job->status) ?>"><?= htmlspecialchars($job->status) ?></span>
    </div>
</div>
<p style="margin-top: 2rem;">
    <a href="/">← Back to all jobs</a>
</p>