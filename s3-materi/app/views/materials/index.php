<?php
use App\Helpers\Formatter;
?>

<div class="card">
    <!-- Filter and Search Form -->
    <form method="GET" action="index.php" style="display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 24px;">
        <input type="hidden" name="r" value="/materials">
        <div style="flex-grow: 1; min-width: 280px; position: relative;">
            <input type="text" name="search" placeholder="Cari judul, dosen, atau nama file..." 
                   value="<?= htmlspecialchars($search) ?>" 
                   style="width: 100%; padding: 12px 16px 12px 40px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 14px; outline: none; transition: border-color 0.2s;"
                   onfocus="this.style.borderColor='var(--primary)'"
                   onblur="this.style.borderColor='var(--border-color)'">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 15px; color: var(--text-muted);"></i>
        </div>
        
        <div style="width: 220px;">
            <select name="course" style="width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 14px; outline: none; background-color: #ffffff; color: var(--text-main);" onchange="this.form.submit()">
                <option value="">Semua Mata Kuliah</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?= htmlspecialchars($c) ?>" <?= $selected_course === $c ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-filter"></i>
            <span>Filter</span>
        </button>
        
        <?php if ($search !== '' || $selected_course !== ''): ?>
            <a href="<?= \App\Helpers\Url::to('/materials') ?>" class="btn btn-secondary">
                <i class="fa-solid fa-arrows-rotate"></i>
                <span>Reset</span>
            </a>
        <?php endif; ?>
    </form>
    
    <!-- Table listing -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 13px;">
                    <th style="padding: 14px 16px; font-weight: 600;">Judul & Deskripsi</th>
                    <th style="padding: 14px 16px; font-weight: 600;">Mata Kuliah</th>
                    <th style="padding: 14px 16px; font-weight: 600;">Dosen</th>
                    <th style="padding: 14px 16px; font-weight: 600;">Ukuran</th>
                    <th style="padding: 14px 16px; font-weight: 600;">Tanggal Upload</th>
                    <th style="padding: 14px 16px; font-weight: 600; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($materials)): ?>
                    <tr>
                        <td colspan="6" style="padding: 50px; text-align: center; color: var(--text-muted); font-size: 14px;">
                            <i class="fa-regular fa-folder-open" style="font-size: 36px; margin-bottom: 12px; display: block; opacity: 0.5; color: var(--primary);"></i>
                            Tidak ada materi perkuliahan yang ditemukan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($materials as $item): ?>
                        <tr style="border-bottom: 1px solid var(--border-color); font-size: 14px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                            <td style="padding: 16px; max-width: 340px;">
                                <div style="display: flex; gap: 12px; align-items: flex-start;">
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
                                    <i class="fa-solid <?= $iconClass ?>" style="color: <?= $iconColor ?>; font-size: 20px; margin-top: 2px;"></i>
                                    <div>
                                        <span style="font-weight: 600; display: block; color: var(--text-main);"><?= htmlspecialchars($item['title']) ?></span>
                                        <?php if (!empty($item['description'])): ?>
                                            <p style="font-size: 12px; color: var(--text-muted); margin: 4px 0 6px 0; line-height: 1.4;"><?= htmlspecialchars($item['description']) ?></p>
                                        <?php endif; ?>
                                        <span style="font-size: 11px; color: var(--text-muted); font-family: monospace; display: block;"><?= htmlspecialchars($item['file_name']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px; font-weight: 500; color: var(--text-main);"><?= htmlspecialchars($item['course']) ?></td>
                            <td style="padding: 16px; color: var(--text-muted);"><?= htmlspecialchars($item['lecturer']) ?></td>
                            <td style="padding: 16px; color: var(--text-muted);"><?= Formatter::formatBytes($item['file_size']) ?></td>
                            <td style="padding: 16px; color: var(--text-muted);"><?= date('d M Y, H:i', strtotime($item['uploaded_at'])) ?></td>
                            <td style="padding: 16px; text-align: right;">
                                <div style="display: inline-flex; gap: 8px; align-items: center;">
                                    <?php if ($item['view_url'] !== ''): ?>
                                        <a href="<?= $item['view_url'] ?>" target="_blank" class="btn btn-secondary btn-sm" title="Buka File inline">
                                            <i class="fa-solid fa-eye"></i>
                                            <span>Lihat</span>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="<?= \App\Helpers\Url::to('/download', ['id' => $item['id']]) ?>" class="btn btn-primary btn-sm" title="Unduh File">
                                        <i class="fa-solid fa-download"></i>
                                        <span>Unduh</span>
                                    </a>
                                    
                                    <form method="POST" action="<?= \App\Helpers\Url::to('/delete') ?>" onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini dari MinIO S3 dan database?');" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 10px;" title="Hapus">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
