<h2>Job List</h2>
<form action="/" method="POST" class="job-form">
    <label for="title">Job Title:</label>
    <input type="text" name="title" placeholder="enter job title...">
    <label for="company">Company:</label>
    <input type="text" name="company" placeholder="enter company name...">
    <label for="salary">Salary:</label>
    <input type="number" id="salary" name="salary" min="0" step="0.01" placeholder="enter salary...">
    <label for="status">Status:</label>
    <select name="status" id="status">
        <?php foreach (JobStatus::cases() as $status): ?>
            <option value="<?= $status->value ?>"><?= $status->value ?></option>
        <?php endforeach; ?>
    </select>
    <label for="currency">Currency:</label>
    <select name="currency" id="currency">
        <?php foreach (Currency::cases() as $currency): ?>
            <option value="<?= $currency->value ?>"><?= $currency->flag() ?> <?= $currency->value ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn" style="background: #4CAF50; color: white;">Add Job</button>
</form>
<?php foreach ($jobs as $job): ?>
    <?php $statusClass = $job->status->cssClass(); ?>
    <div class="job-card"> 
        <a href="/job/<?= $job->id ?>" class="job-stat-con-left">
            <h2 class="job-title"><?= htmlspecialchars($job->title) ?></h2>
            <p class="job-company"><?= htmlspecialchars($job->company) ?></p>
            <p class="job-salary"><?= formatSalary($job->salary, $job->currency) ?></p>
        </a>
        <div class="job-stat-con-right">
            <span class="<?= $statusClass ?>"><?= htmlspecialchars($job->status->value) ?></span>
            <a href="/job/<?= $job->id ?>/edit" class="btn btn-edit">Edit</a>
            <form action="/" method="POST" style="display: inline;">
                <input type="hidden" name="delete_id" value="<?= $job->id ?>">
                <button type="submit" class="btn btn-delete">Delete</button>
            </form>
        </div>    
    </div>
<?php endforeach; ?>