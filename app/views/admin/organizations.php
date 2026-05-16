<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="admin-dashboard">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-building"></i> Manage Organizations</h1>
            <a href="<?php echo APP_URL; ?>/admin/dashboard" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Organization</th>
                        <th>Type</th>
                        <th>District</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orgs as $org): ?>
                        <tr>
                            <td><?php echo $org['id']; ?></td>
                            <td><?php echo htmlspecialchars($org['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($org['email']); ?></td>
                            <td><?php echo htmlspecialchars($org['organization_name'] ?? 'Not provided'); ?></td>
                            <td><?php echo ucfirst($org['organization_type'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($org['district'] ?? 'N/A'); ?></td>
                            <td>
                                <?php if ($org['is_verified']): ?>
                                    <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified</span>
                                <?php else: ?>
                                    <span class="pending-badge"><i class="fas fa-clock"></i> <?php echo ucfirst($org['verification_status']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($org['created_at'])); ?></td>
                            <td>
                                <a href="<?php echo APP_URL; ?>/admin/view-organization/<?php echo $org['id']; ?>" class="btn btn-sm btn-outline">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
