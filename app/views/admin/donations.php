<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="admin-dashboard">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-hand-holding-heart" style="color:var(--success);"></i> Donation Offers</h1>
            <a href="<?php echo APP_URL; ?>/admin/dashboard" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <?php if (!empty($donations)): ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Donor Name</th>
                            <th>Blood Group</th>
                            <th>Available Date</th>
                            <th>Location</th>
                            <th>District</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($donations as $donation): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($donation['full_name']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($donation['email']); ?></small>
                                </td>
                                <td><span class="blood-badge small"><?php echo $donation['blood_group']; ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($donation['available_date'])); ?></td>
                                <td><?php echo htmlspecialchars($donation['location']); ?></td>
                                <td><?php echo htmlspecialchars($donation['district'] ?? 'N/A'); ?></td>
                                <td>
                                    <a href="tel:<?php echo $donation['phone']; ?>" style="color:var(--primary);">
                                        <?php echo htmlspecialchars($donation['phone']); ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $donation['status']; ?>" style="
                                        padding:4px 10px; border-radius:15px; font-size:0.8rem;
                                        background:<?php echo $donation['status'] === 'available' ? '#d4edda' : ($donation['status'] === 'donated' ? '#cce5ff' : ($donation['status'] === 'matched' ? '#fff3cd' : '#f8d7da')); ?>;
                                        color:<?php echo $donation['status'] === 'available' ? '#155724' : ($donation['status'] === 'donated' ? '#004085' : ($donation['status'] === 'matched' ? '#856404' : '#721c24')); ?>;
                                    ">
                                        <?php echo ucfirst($donation['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d', strtotime($donation['created_at'])); ?></td>
                                <td>
                                    <form method="POST" action="<?php echo APP_URL; ?>/admin/update-donation-status" style="display:inline;">
                                        <input type="hidden" name="donation_id" value="<?php echo $donation['id']; ?>">
                                        <select name="status" onchange="this.form.submit()" class="form-control" style="padding:5px;font-size:0.85rem;">
                                            <option value="available" <?php echo $donation['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                                            <option value="matched" <?php echo $donation['status'] === 'matched' ? 'selected' : ''; ?>>Matched</option>
                                            <option value="donated" <?php echo $donation['status'] === 'donated' ? 'selected' : ''; ?>>Donated</option>
                                            <option value="cancelled" <?php echo $donation['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p style="margin-top:15px;color:var(--secondary);">
                <i class="fas fa-info-circle"></i> Total: <?php echo count($donations); ?> donation offers
            </p>
        <?php else: ?>
            <div class="no-data" style="background:white;padding:60px;border-radius:10px;box-shadow:var(--shadow);">
                <i class="fas fa-hand-holding-heart" style="font-size:3rem;color:var(--success);"></i>
                <h3>No Donation Offers Yet</h3>
                <p>When users register to donate blood, they will appear here.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
