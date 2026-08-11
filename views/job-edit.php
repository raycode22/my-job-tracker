<h1>Edit Job</h1>

  <form action="" method="POST">
    <input type="hidden" name="update_id" value="<?= $job->id ?>">
    <input type="text" name="title" value="<?=htmlspecialchars($job->title) ?>">
    <input type="text" name="company" value="<?=htmlspecialchars($job->company) ?>">
    <select name="status">
      <option value="Applied" <?= $job->status === 'Applied' ? 'selected' : '' ?>>Applied</option>
      <option value="Offered" <?= $job->status === 'Offered' ? 'selected' : '' ?>>Offered</option>
      <option value="Interviewing" <?= $job->status === 'Interviewing' ? 'selected' : '' ?>>Interviewing</option>
      <option value="Rejected" <?= $job->status === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
    </select>
    <button type="submit">Save Changes</button>
    <p style="margin-top: 2rem;"><a href="/job/<?= $job->id ?>">← Back to Job Details</a></p>
  </form>