<?php 
    // --- Data Section ---
    $jobs = [
        ["title" => "Senior PHP Developer", "company" => "Acme Corp", "salary" => 85000, "status" => "Applied"],
        ["title" => "Full Stack Engineer", "company" => "Global Tech", "salary" => 95000, "status" => "Interviewing"],
        ["title" => "Backend Architect", "company" => "Innovate LLC", "salary" => 120000, "status" => "Offered"],
        ["title" => "Junior Dev", "company" => "StartUp Inc", "salary" => 50000, "status" => "Rejected"],
    ];

function getStatusClass(string $status) {
    // We convert the status to lowercase first.
    // Now "Applied", "APPLIED", and "applied" all become "applied".
    $status = strtolower($status);

    if ($status === 'applied') {
        return 'status-applied';
    } elseif ($status === 'interviewing') {
        return 'status-interviewing';
    } elseif ($status === 'offered') {
        return 'status-offered';
    } else {
        return 'status-rejected';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Job Tracker</title>
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      font-family: Arial, sans-serif;
    }
    th, td {
      border: 1px, solid, #ddd;
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: #f4f4f4;
    }
    .status-applied {
      color: gray;
      font-weight: bold;
    }
    .status-interviewing {
      color: blue;
      font-weight: bold;
    }
    .status-offered {
      color: green;
      font-weight: bold;
    }
    .status-rejected {
      color: red;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <h1>My Job Tracker</h1>
  <p>Welcome to your professional application tracking board.</p>
  <table>
    <thead>
      <tr>
        <th>Title</th>
        <th>Company</th>
        <th>Salary</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
     <?php foreach ($jobs as $job): ?>
                <tr>
                    <td><?php echo $job['title']; ?></td>
                    <td><?php echo $job['company']; ?></td>
                    <td>$<?php echo $job['salary']; ?></td>
                    <!-- We call our function here. It's clean, elegant, and descriptive. -->
                    <td class="<?php echo getStatusClass($job['status']); ?>">
                        <?php echo $job['status']; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>