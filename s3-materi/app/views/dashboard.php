<?php
use App\Helpers\Formatter;
?>

<!-- Statistics Overview -->
<div class="grid-cols-4">
    <!-- Total Files -->
    <div class="card" style="display: flex; align-items: center; gap: 20px; padding: 24px; margin-bottom: 0;">
        <div style="background-color: #e0e7ff; color: #4f46e5; width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">
            <i class="fa-solid fa-file-invoice"></i>
        </div>
        <div>
            <div style="font-size: 14px; color: var(--text-muted); font-weight: 500;">Total Materi</div>
            <div style="font-size: 24px; font-weight: 700; color: var(--text-main); margin-top: 4px;"><?= $stats['total_files'] ?> File</div>
        </div>
    </div>
    
    <!-- Storage Used -->
    <div class="card" style="display: flex; align-items: center; gap: 20px; padding: 24px; margin-bottom: 0;">
        <div style="background-color: #ecfdf5; color: #10b981; width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">
            <i class="fa-solid fa-hard-drive"></i>
        </div>
        <div>
            <div style="font-size: 14px; color: var(--text-muted); font-weight: 500;">Penyimpanan (S3)</div>
            <div style="font-size: 24px; font-weight: 700; color: var(--text-main); margin-top: 4px;"><?= Formatter::formatBytes($stats['total_size']) ?></div>
        </div>
    </div>
    
    <!-- Unique Courses -->
    <div class="card" style="display: flex; align-items: center; gap: 20px; padding: 24px; margin-bottom: 0;">
        <div style="background-color: #fff7ed; color: #f97316; width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">
            <i class="fa-solid fa-book-bookmark"></i>
        </div>
        <div>
            <div style="font-size: 14px; color: var(--text-muted); font-weight: 500;">Mata Kuliah</div>
            <div style="font-size: 24px; font-weight: 700; color: var(--text-main); margin-top: 4px;"><?= $stats['total_courses'] ?> Matkul</div>
        </div>
    </div>
    
    <!-- Unique Lecturers -->
    <div class="card" style="display: flex; align-items: center; gap: 20px; padding: 24px; margin-bottom: 0;">
        <div style="background-color: #fef2f2; color: #ef4444; width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">
            <i class="fa-solid fa-user-tie"></i>
        </div>
        <div>
            <div style="font-size: 14px; color: var(--text-muted); font-weight: 500;">Dosen Pengampu</div>
            <div style="font-size: 24px; font-weight: 700; color: var(--text-main); margin-top: 4px;"><?= $stats['total_lecturers'] ?> Dosen</div>
        </div>
    </div>
</div>

<div style="margin-top: 24px;"></div>

<!-- Recent Files Card -->
<div class="card">
    <div class="card-header" style="border: none; margin-bottom: 0; padding-bottom: 0; display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Materi Terbaru</h3>
        <a href="<?= \App\Helpers\Url::to('/materials') ?>" class="btn btn-secondary btn-sm" style="gap: 4px;">
            <span>Lihat Semua</span>
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
    
    <div style="overflow-x: auto; margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 13px;">
                    <th style="padding: 14px 16px; font-weight: 600;">Judul Materi</th>
                    <th style="padding: 14px 16px; font-weight: 600;">Mata Kuliah</th>
                    <th style="padding: 14px 16px; font-weight: 600;">Dosen</th>
                    <th style="padding: 14px 16px; font-weight: 600;">Ukuran</th>
                    <th style="padding: 14px 16px; font-weight: 600;">Tanggal Upload</th>
                    <th style="padding: 14px 16px; font-weight: 600; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recent)): ?>
                    <tr>
                        <td colspan="6" style="padding: 40px; text-align: center; color: var(--text-muted); font-size: 14px;">
                            <i class="fa-regular fa-folder-open" style="font-size: 32px; margin-bottom: 12px; display: block; opacity: 0.5; color: var(--primary);"></i>
                            Belum ada materi kuliah yang diunggah.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recent as $item): ?>
                        <tr style="border-bottom: 1px solid var(--border-color); font-size: 14px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                            <td style="padding: 16px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <?php
                                        $ext = strtolower(pathinfo($item['file_name'], PATHINFO_EXTENSION));
                                        $iconClass = 'fa-file-lines';
                                        $iconColor = '#64748b';
                                        if (in_array($ext, ['pdf'])) { $iconClass = 'fa-file-pdf'; $iconColor = '#ef4444'; }
                                        elseif (in_array($ext, ['doc', 'docx'])) { $iconClass = 'fa-file-word'; $iconColor = '#2563eb'; }
                                        elseif (in_array($ext, ['xls', 'xlsx'])) { $iconClass = 'fa-file-excel'; $iconColor = '#16a34a'; }
                                        elseif (in_array($ext, ['ppt', 'pptx'])) { $iconClass = 'fa-file-powerpoint'; $iconColor = '#ea580c'; }
                                        elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) { $iconClass = 'fa-file-image'; $iconColor = '#06b6d4'; }
                                        elseif (in_array($ext, ['zip', 'rar', 'tar', 'gz'])) { $iconClass = 'fa-file-zipper'; $iconColor = '#8b5cf6'; }
                                    ?>
                                    <i class="fa-solid <?= $iconClass ?>" style="color: <?= $iconColor ?>; font-size: 20px;"></i>
                                    <div>
                                        <span style="font-weight: 500; display: block; color: var(--text-main);"><?= htmlspecialchars($item['title']) ?></span>
                                        <span style="font-size: 12px; color: var(--text-muted); font-family: monospace;"><?= htmlspecialchars($item['file_name']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px; font-weight: 500; color: var(--text-main);"><?= htmlspecialchars($item['course']) ?></td>
                            <td style="padding: 16px; color: var(--text-muted);"><?= htmlspecialchars($item['lecturer']) ?></td>
                            <td style="padding: 16px; color: var(--text-muted);"><?= Formatter::formatBytes($item['file_size']) ?></td>
                            <td style="padding: 16px; color: var(--text-muted);"><?= date('d M Y, H:i', strtotime($item['uploaded_at'])) ?></td>
                            <td style="padding: 16px; text-align: right;">
                                <div style="display: inline-flex; gap: 8px;">
                                    <?php if ($item['view_url'] !== ''): ?>
                                        <a href="<?= $item['view_url'] ?>" target="_blank" class="btn btn-secondary btn-sm" title="Buka File inline">
                                            <i class="fa-solid fa-eye"></i>
                                            <span>Lihat</span>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= \App\Helpers\Url::to('/download', ['id' => $item['id']]) ?>" class="btn btn-primary btn-sm" title="Download">
                                        <i class="fa-solid fa-download"></i>
                                        <span>Unduh</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
