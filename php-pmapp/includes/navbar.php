<?php
require_once __DIR__ . '/../config/session.php';
requireLogin();
$user     = getCurrentUser();
$initials = strtoupper(substr($user['name'], 0, 1));
$dir      = basename(dirname($_SERVER['PHP_SELF']));
?>

<!-- Mobile Topbar -->
<div class="mobile-topbar">
    <button class="hamburger-btn" id="hamburgerBtn" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>
    <div class="mobile-brand">
        Task<span style="color:var(--teal-400)">Nest</span>
    </div>
    <div style="width:40px"></div>
</div>

<!-- Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-name">
            <span class="brand-icon-sm"><i class="bi bi-hexagon-fill"></i></span>
            Task<span style="color:var(--teal-400)">Nest</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-section-label">Main</div>
        <a href="/dashboard/index.php" class="nav-link <?= ($dir === 'dashboard') ? 'active' : '' ?>" onclick="closeSidebar()">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <div class="sidebar-section-label">Work</div>
        <a href="/projects/index.php" class="nav-link <?= ($dir === 'projects') ? 'active' : '' ?>" onclick="closeSidebar()">
            <i class="bi bi-folder2-open"></i> Projects
        </a>
        <a href="/tasks/index.php" class="nav-link <?= ($dir === 'tasks') ? 'active' : '' ?>" onclick="closeSidebar()">
            <i class="bi bi-check2-square"></i> My Tasks
        </a>

        <?php if (isAdmin()): ?>
        <div class="sidebar-section-label">Admin</div>
        <a href="/projects/create.php" class="nav-link" onclick="closeSidebar()">
            <i class="bi bi-plus-circle"></i> New Project
        </a>
        <a href="/tasks/create.php" class="nav-link" onclick="closeSidebar()">
            <i class="bi bi-plus-square"></i> New Task
        </a>
        <a href="/auth/register.php" class="nav-link" onclick="closeSidebar()">
            <i class="bi bi-person-plus"></i> Add User
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar"><?= $initials ?></div>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($user['name']) ?></div>
                <div class="user-role"><?= $user['role'] ?></div>
            </div>
        </div>
        <a href="/auth/logout.php" class="nav-link mt-2" style="color:rgba(248,113,113,0.75)!important">
            <i class="bi bi-box-arrow-right"></i> Sign Out
        </a>
    </div>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('sidebar-open');
    overlay.classList.toggle('overlay-active');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('sidebar-open');
    document.getElementById('sidebarOverlay').classList.remove('overlay-active');
}
</script>
