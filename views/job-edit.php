<h2>Edit Job</h2>
<form action="" method="POST" class="job-form">
    <input type="hidden" name="update_id" value="<?= $job->id ?>">
    <label for="title">Job Title:</label>
    <input type="text" id="title" name="title" value="<?= htmlspecialchars($job->title) ?>">
    <label for="company">Company:</label>
    <input type="text" id="company" name="company" value="<?= htmlspecialchars($job->company) ?>">
    <label for="salary">Salary:</label>
    <input type="number" id="salary" name="salary" min="0" step="0.01" value="<?= $job->salary ?? '' ?>">
    <label for="status">Status:</label>
    <select id="status" name="status">
    <?php foreach (JobStatus::cases() as $status): ?>
       <option value="<?= $status->value ?>" <?= $job->status === $status ? 'selected' : '' ?>><?= $status->value ?></option>
    <?php endforeach; ?>
    </select>
    <label for="currency"></label>
    <select name="currency" id="currency">
    <?php foreach (Currency::cases() as $cur): ?> 
        <option value="<?= $cur->value ?>" <?= $job->currency === $cur ? 'selected' : '' ?>><?= $cur->flag() ?> <?= $cur->value ?></option>
    <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-edit">Save Changes</button>
</form>
<p style="margin-top: 2rem;">
    <a href="/job/<?= $job->id ?>">← Back to Job Details</a>
</p>