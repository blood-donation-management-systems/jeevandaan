<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="dashboard">
    <div class="container">
        <?php if (isset($flash)): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <?php if (($user['blood_group'] ?? '') === 'O-'): ?>
            <div class="universal-donor-banner">
                <div class="universal-donor-icon">🩸</div>
                <div class="universal-donor-text">
                    <strong>You are a Universal Donor</strong>
                    <span>Your O- blood can save anyone in an emergency. We may contact you directly in critical situations.</span>
                </div>
                <div class="universal-donor-badge">Universal Donor</div>
            </div>
        <?php endif; ?>
        
        <div class="dashboard-header">
            <div class="welcome-section">
                <h1>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h1>
                <p>
                    <?php if ($user['is_verified']): ?>
                        <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified Donor</span>
                    <?php else: ?>
                        <span class="pending-badge"><i class="fas fa-clock"></i> Verification Pending</span>
                    <?php endif; ?>
                    <?php if (($user['blood_group'] ?? '') === 'O-'): ?>
                        <span class="universal-donor-pill" title="O- is the universal donor blood type">🩸 Universal Donor</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="dashboard-actions">
                <a href="<?php echo APP_URL; ?>/user/profile" class="btn btn-outline">
                    <i class="fas fa-user-edit"></i> Edit Profile
                </a>
                <a href="<?php echo APP_URL; ?>/user/donate-blood" class="btn btn-success">
                    <i class="fas fa-hand-holding-heart"></i> Donate Blood
                </a>
                <a href="<?php echo APP_URL; ?>/requests/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Request Blood
                </a>
            </div>
        </div>
        
        <div class="stats-grid" style="margin-bottom:30px;">
            <div class="stat-card">
                <i class="fas fa-tint"></i>
                <h3><?php echo $user['blood_group'] ?? 'Not Set'; ?></h3>
                <p>Blood Group</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-calendar"></i>
                <h3><?php echo $user['last_donation_date'] ? date('M d, Y', strtotime($user['last_donation_date'])) : 'Never'; ?></h3>
                <p>Last Donation</p>
            </div>
            <div class="stat-card <?php echo $canDonate ? '' : 'warning'; ?>">
                <i class="fas <?php echo $canDonate ? 'fa-check' : 'fa-clock'; ?>"></i>
                <h3><?php echo $canDonate ? 'Ready' : 'Wait'; ?></h3>
                <p>Donation Status</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-bell"></i>
                <h3><?php echo $unreadCount; ?></h3>
                <p>Notifications</p>
            </div>
        </div>
        
        <div class="dashboard-grid">
            <div class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-heartbeat"></i> People Need Your Blood</h2>
                </div>
                
                <?php if (!empty($matchingRequests)): ?>
                    <div class="request-list">
                        <?php foreach ($matchingRequests as $request): ?>
                            <div class="request-item <?php echo $request['urgency']; ?>">
                                <div class="request-blood">
                                    <span class="blood-badge"><?php echo $request['blood_group']; ?></span>
                                </div>
                                <div class="request-info">
                                    <h4><?php echo htmlspecialchars($request['patient_name']); ?></h4>
                                    <p><i class="fas fa-hospital"></i> <?php echo htmlspecialchars($request['hospital_name']); ?></p>
                                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($request['hospital_district']); ?></p>
                                </div>
                                <div class="request-action">
                                    <span class="urgency-badge <?php echo $request['urgency']; ?>"><?php echo ucfirst($request['urgency']); ?></span>
                                    <a href="<?php echo APP_URL; ?>/requests/details/<?php echo $request['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-phone"></i> Contact
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-check-circle"></i>
                        <p>No matching requests</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-bell"></i> Notifications</h2>
                </div>
                
                <?php if (!empty($notifications)): ?>
                    <div class="notification-list">
                        <?php foreach ($notifications as $notif): ?>
                            <div class="notification-item <?php echo $notif['is_read'] ? '' : 'unread'; ?> <?php echo $notif['type'] === 'universal_donor' ? 'universal-donor-notif' : ''; ?>">
                                <div class="notification-icon">
                                    <?php if ($notif['type'] === 'universal_donor'): ?>
                                        <span style="font-size:20px;">🩸</span>
                                    <?php else: ?>
                                        <i class="fas fa-bell"></i>
                                    <?php endif; ?>
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