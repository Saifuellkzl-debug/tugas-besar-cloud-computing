<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S3 Materi Kuliah - MinIO Storage</title>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #6366f1;
            /* Soft pastel indigo */
            --primary-hover: #4f46e5;
            --primary-light: #e0e7ff;
            --success: #10b981;
            --success-light: #ecfdf5;
            --danger: #ef4444;
            --danger-hover: #dc2626;
            --danger-light: #fef2f2;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --background: #f5f7fb;
            /* Soft pastel light background */
            --text-main: #1e293b;
            /* Soft dark slate for text */
            --text-muted: #64748b;
            --sidebar-bg: #ffffff;
            /* Clean light sidebar background */
            --sidebar-color: #64748b;
            --card-bg: #ffffff;
            --border-color: #eef2f6;
            /* Soft pastel border line */
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.02);
            --shadow-md: 0 4px 12px -1px rgba(99, 102, 241, 0.05), 0 2px 4px -2px rgba(99, 102, 241, 0.05);
            --shadow-lg: 0 10px 25px -3px rgba(99, 102, 241, 0.03), 0 4px 12px -4px rgba(99, 102, 241, 0.03);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: var(--text-main);
            display: flex;
            flex-direction: column;
            padding: 24px;
            flex-shrink: 0;
            border-right: 1px solid var(--border-color);
            box-shadow: 2px 0 10px rgba(99, 102, 241, 0.01);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 20px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 40px;
            text-decoration: none;
        }

        .sidebar-brand i {
            color: var(--primary);
            font-size: 24px;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--sidebar-color);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            color: var(--primary);
            background-color: #f5f7ff;
        }

        .sidebar-link.active {
            color: var(--primary);
            background-color: var(--primary-light);
            font-weight: 600;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            font-size: 12px;
            color: var(--text-muted);
            text-align: center;
        }

        /* Main content area */
        .main-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .header {
            height: 70px;
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
        }

        .header-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-main);
        }

        .header-meta {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            background-color: var(--success-light);
            color: #065f46;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #a7f3d0;
        }

        .status-badge .dot {
            width: 8px;
            height: 8px;
            background-color: var(--success);
            border-radius: 50%;
            display: inline-block;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background-color: var(--primary-light);
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .content-body {
            padding: 40px;
            flex-grow: 1;
        }

        /* Alerts */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            animation: slideIn 0.3s ease-out;
        }

        .alert-success {
            background-color: var(--success-light);
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background-color: var(--danger-light);
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Layout Cards */
        .card {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            padding: 24px;
            margin-bottom: 24px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 16px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-main);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #c7d2fe;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.12);
        }

        .btn-secondary {
            background-color: #ffffff;
            color: var(--text-muted);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background-color: #f8fafc;
            color: var(--text-main);
        }

        .btn-danger {
            background-color: var(--danger-light);
            color: var(--danger);
        }

        .btn-danger:hover {
            background-color: #fca5a5;
            color: #991b1b;
        }

        .btn-sm {
            padding: 8px 14px;
            font-size: 13px;
            border-radius: 8px;
        }

        /* Grid utilities */
        .grid-cols-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 30px;
        }

        @media (max-width: 1200px) {
            .grid-cols-4 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 16px;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
            }

            .sidebar-brand {
                margin-bottom: 20px;
            }

            .grid-cols-4 {
                grid-template-columns: 1fr;
            }

            .header {
                padding: 0 20px;
            }

            .content-body {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar Menu -->
    <aside class="sidebar">
        <a href="<?= \App\Helpers\Url::to('/') ?>" class="sidebar-brand">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>MateriS3</span>
        </a>

        <nav>
            <ul class="sidebar-menu">
                <li>
                    <a href="<?= \App\Helpers\Url::to('/') ?>" class="sidebar-link <?= ($active_page ?? '') === 'dashboard' ? 'active' : '' ?>">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?= \App\Helpers\Url::to('/materials') ?>" class="sidebar-link <?= ($active_page ?? '') === 'materials' ? 'active' : '' ?>">
                        <i class="fa-solid fa-book"></i>
                        <span>Materi Kuliah</span>
                    </a>
                </li>
                <li>
                    <a href="<?= \App\Helpers\Url::to('/upload') ?>" class="sidebar-link <?= ($active_page ?? '') === 'upload' ? 'active' : '' ?>">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Unggah Materi</span>
                    </a>
                </li>
            </ul>
        </nav>

    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <header class="header">
            <h1 class="header-title">
                <?php
                if (($active_page ?? '') === 'dashboard') echo 'Dashboard Overview';
                elseif (($active_page ?? '') === 'materials') echo 'Mata Kuliah & Materi';
                elseif (($active_page ?? '') === 'upload') echo 'Unggah Materi Baru';
                else echo 'Materi Perkuliahan';
                ?>
            </h1>

            <div class="header-meta">
                <div class="user-info">
                    <div class="user-avatar">SA</div>
                    <span style="font-size: 14px; font-weight: 500; color: var(--text-main);">Syaiful Anam</span>
                </div>
            </div>
        </header>

        <main class="content-body">
            <!-- Flash Session Alerts -->
            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span><?= htmlspecialchars($_SESSION['flash_success']) ?></span>
                </div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= htmlspecialchars($_SESSION['flash_error']) ?></span>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <!-- Core Content Inject -->
            <?= $content ?? '' ?>
        </main>
    </div>

</body>

</html>