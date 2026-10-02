<?php
declare(strict_types=1);

use App\Models\JobStatus;
use App\Models\Currency;

function handlePostRequest(PDO $pdo): void {
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