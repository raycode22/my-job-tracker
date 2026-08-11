<form action="/" method="post" class="add-job-form">
    <input type="text" name="title" placeholder="Job title">
    <input type="text" name="company" placeholder="Company">
    <select name="status">
        <option value="Applied">Applied</option>
        <option value="Offered">Offered</option>
        <option value="Interviewing">Interviewing</option>
        <option value="Rejected">Rejected</option>
    </select>
    <button type="submit" class="btn" style="background: #4CAF50; color: white;">Add Job</button>
</form>

<?php foreach ($jobs as $job): ?>
    <?php $statusClass = getStatusClass($job->status); ?>
    <div class="job-card"> 
        <a href="/job/<?= $job->id ?>" class="job-stat-con-left">
            <h2 class="job-title"><?= htmlspecialchars($job->title) ?></h2>
            <p class="job-company"><?= htmlspecialchars($job->company) ?></p>
        </a>
        <div class="job-stat-con-right">
            <span class="<?= $statusClass ?>"><?= htmlspecialchars($job->status) ?></span>
            <a href="/job/<?= $job->id ?>/edit" class="btn btn-edit">Edit</a>
            <form action="/" method="POST" style="display: inline;">
                <input type="hidden" name="delete_id" value="<?= $job->id ?>">
                <button type="submit" class="btn btn-delete">Delete</button>
            </form>
        </div>    
    </div>
<?php endforeach; ?>