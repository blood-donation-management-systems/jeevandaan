<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="dashboard">
    <div class="container">
        <?php if (isset($flash)): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-header">
            <div class="welcome-section">
                <h1><i class="fas fa-building"></i> <?php echo htmlspecialchars($org['full_name']); ?></h1>
                <p>
                    <?php if ($org['is_verified']): ?>
                        <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified Organization</span>
                    <?php else: ?>
                        <span class="pending-badge"><i class="fas fa-clock"></i> Verification Pending</span>
                    <?php endif; ?>
                    <?php if ($org['organization_name']): ?>
                        | <strong><?php echo htmlspecialchars($org['organization_name']); ?></strong>
                    <?php endif; ?>
                </p>
            </div>
            <div class="dashboard-actions">
                <a href="<?php echo APP_URL; ?>/organization/profile" class="btn btn-outline">
                    <i class="fas fa-user-edit"></i> Edit Profile
                </a>
                <a href="<?php echo APP_URL; ?>/requests/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Request
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="stats-grid" style="margin-bottom:30px;">
            <a href="<?php echo APP_URL; ?>/organization/donors" class="stat-card" style="text-decoration:none;">
                <i class="fas fa-users"></i>
                <h3><i class="fas fa-eye"></i></h3>
                <p>View All Donors</p>
            </a>
            <a href="<?php echo APP_URL; ?>/organization/requests" class="stat-card" style="text-decoration:none;">
                <i class="fas fa-tint"></i>
                <h3><i class="fas fa-list"></i></h3>
                <p>All Blood Requests</p>
            </a>
            <a href="<?php echo APP_URL; ?>/organization/profile" class="stat-card" style="text-decoration:none;">
                <i class="fas fa-id-card"></i>
                <h3><i class="fas fa-edit"></i></h3>
                <p>Update Profile</p>
            </a>
            <div class="stat-card">
                <i class="fas fa-bell"></i>
                <h3><?php echo count($notifications); ?></h3>
                <p>Notifications</p>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Active Blood Requests -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-tint"></i> Active Blood Requests</h2>
                    <a href="<?php echo APP_URL; ?>/organization/requests">View All</a>
                </div>

                <?php if (!empty($requests)): ?>
                    <div class="request-list">
                        <?php foreach ($requests as $request): ?>
                            <div class="request-item <?php echo $request['urgency']; ?>">
                                <div class="request-blood">
                                    <span class="blood-badge"><?php echo $request['blood_group']; ?></span>
                                </div>
                                <div class="request-info">
                                    <h4><?php echo htmlspecialchars($request['patient_name']); ?></h4>
                                    <p><i class="fas fa-hospital"></i> <?php echo htmlspecialchars($request['hospital_name']); ?></p>
                                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($request['hospital_district']); ?></p>
                                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($request['contact_phone']); ?></p>
                                </div>
                                <div class="request-action">
                                    <span class="urgency-badge <?php echo $request['urgency']; ?>">
                                        <?php echo ucfirst($request['urgency']); ?>
                                    </span>
                                    <a href="<?php echo APP_URL; ?>/requests/details/<?php echo $request['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-check-circle"></i>
                        <p>No active blood requests</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Notifications -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-bell"></i> Notifications</h2>
                </div>

                <?php if (!empty($notifications)): ?>
                    <div class="notification-list">
                        <?php foreach ($notifications as $notif): ?>
                            <div class="notification-item <?php echo $notif['is_read'] ? '' : 'unread'; ?>">
                                <div class="notification-icon">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div class="notification-content">
                                    <h4><?php echo htmlspecialchars($notif['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($notif['message']); ?></p>
                                    <small><?php echo date('M d, H:i', strtotime($notif['created_at'])); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-bell-slash"></i>
                        <p>No notifications</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="dashboard-footer">
            <a href="<?php echo APP_URL; ?>/auth/logout" class="btn btn-outline">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
