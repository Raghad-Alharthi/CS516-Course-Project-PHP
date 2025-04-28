<?php require __DIR__ . '/../shared/header.php'; ?>

<div class="container my-5">
    <div class="card" style="border-radius: 12px; padding: 25px; background-color: #F1EFEC; box-shadow: 0 0 15px rgba(0,0,0,0.05);">
        <h3 style="font-family: Funnel Display; color: #123458; font-weight: 800;">
            Sick Leave Requests
        </h3>

        <?php if (!empty($requests)): ?>
            <table class="table table-bordered mt-4" style="background-color: #f8f7f6;">
                <thead style="background-color: #123458; color: #f8f7f6;">
                    <tr>
                        <th style="font-family: Funnel Display;">Student</th>
                        <th style="font-family: Funnel Display;">Lecture Date</th>
                        <th style="font-family: Funnel Display;">File</th>
                        <th style="font-family: Funnel Display;">Status</th>
                        <th style="font-family: Funnel Display;">Action</th>
                    </tr>
                </thead>
                <tbody style="font-family: Funnel Display; color: #123458;">
                    <?php foreach ($requests as $req): ?>
                        <tr>
                            <td><?= htmlspecialchars(User::findById($req->student_id)->first_name . ' ' . User::findById($req->student_id)->last_name) ?></td>
                            <td><?= htmlspecialchars(date('Y-m-d h:i A', strtotime($req->lecture_datetime))) ?></td>
                            <td>
                                <?php if (!empty($req->sick_leave_file)): ?>
                                    <a href="<?= htmlspecialchars($req->sick_leave_file) ?>"
                                       target="_blank"
                                       class="btn btn-info btn-sm"
                                       style="font-family: Funnel Display;">
                                        View File
                                    </a>
                                <?php else: ?>
                                    <span class="text-danger" style="font-family: Funnel Display;">No file submitted</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge 
                                    <?= $req->sick_leave_status === 'Accepted' ? 'bg-success' : ($req->sick_leave_status === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark') ?>"
                                    style="font-family: Funnel Display;">
                                    <?= htmlspecialchars($req->sick_leave_status) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($req->sick_leave_status === 'Pending'): ?>
                                    <form method="post" action="index.php?route=teacher/approvesickleave" class="d-flex gap-2">
                                        <input type="hidden" name="attendanceId" value="<?= htmlspecialchars($req->attendance_id) ?>">
                                        <textarea name="comment" class="form-control mb-2" placeholder="Rejection comment (required if rejecting)" style="font-family: Funnel Display;" rows="2"></textarea>
                                        <div class="d-flex gap-2">
                                            <button type="submit" name="decision" value="Accepted"
                                                    class="btn btn-success btn-sm"
                                                    style="font-family: Funnel Display;">
                                                Accept
                                            </button>
                                            <button type="submit" name="decision" value="Rejected"
                                                    class="btn btn-danger btn-sm"
                                                    style="font-family: Funnel Display;">
                                                Reject
                                            </button>
                                        </div>
                                    </form>
                                <?php elseif ($req->sick_leave_status === 'Rejected' && !empty($req->sick_leave_comment)): ?>
                                    <div style="background-color: #ffeeba; padding: 10px; border-radius: 8px;">
                                        <strong>Rejection Reason:</strong> <?= htmlspecialchars($req->sick_leave_comment) ?>
                                    </div>
                                <?php else: ?>
                                    <span style="font-family: Funnel Display;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted" style="font-family: Funnel Display;">No sick leave requests for this lecture.</p>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../shared/footer.php'; ?>
