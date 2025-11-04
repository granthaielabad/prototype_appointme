<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin - <?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { padding-top: 56px; }
    .navbar-brand { font-weight: 600; }
  </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark fixed-top shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand" href="/admin/dashboard">
      <i class="bi bi-scissors"></i> AppointMe Admin
    </a>
    <div class="d-flex">
      <a href="/logout" class="btn btn-outline-light ms-2">
        <i class="bi bi-box-arrow-right"></i> Logout
      </a>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">
    <aside class="col-md-2 bg-light vh-100 py-3 border-end">
