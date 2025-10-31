<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?> | AppointMe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { display: flex; min-height: 100vh; }
        aside { width: 240px; background: #212529; color: #fff; }
        aside a { color: #adb5bd; text-decoration: none; display: block; padding: 10px 20px; }
        aside a:hover, aside a.active { background: #343a40; color: #fff; }
        main { flex: 1; padding: 20px; background: #f8f9fa; }
    </style>
</head>
<body>
