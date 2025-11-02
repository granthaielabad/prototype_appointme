<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin - <?=htmlspecialchars($pageTitle ?? 'Dashboard')?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>body{padding-top:56px}</style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="/admin/dashboard">AppointMe Admin</a>
    <form class="d-flex">
      <a class="btn btn-outline-light" href="/">View site</a>
    </form>
  </div>
</nav>
<div class="container-fluid">
  <div class="row">
    <aside class="col-md-2 bg-light vh-100 py-3">
