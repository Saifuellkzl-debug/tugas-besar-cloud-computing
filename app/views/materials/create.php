<div class="card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header" style="border: none; padding-bottom: 0;">
        <h3 class="card-title">Unggah Materi Perkuliahan</h3>
    </div>
    
    <form method="POST" action="<?= \App\Helpers\Url::to('/upload') ?>" enctype="multipart/form-data" style="margin-top: 24px;">
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Title -->
            <div>
                <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">Judul Materi <span style="color: var(--danger);">*</span></label>
                <input type="text" name="title" required placeholder="Contoh: Pertemuan 1 - Pengenalan Basis Data" 
                       style="width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 14px; outline: none; transition: border-color 0.2s;"
                       onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
            </div>

            <!-- Grid: Course & Lecturer -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">Mata Kuliah <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="course" required list="courses-list" placeholder="Masukkan atau pilih mata kuliah" 
                           style="width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 14px; outline: none; transition: border-color 0.2s;"
                           onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
                    <datalist id="courses-list">
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= htmlspecialchars($c) ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">Dosen Pengampu <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="lecturer" required placeholder="Contoh: Dr. M. Syaiful Anam" 
                           style="width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 14px; outline: none; transition: border-color 0.2s;"
                           onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">Deskripsi Materi (Opsional)</label>
                <textarea name="description" placeholder="Penjelasan singkat mengenai materi perkuliahan ini..." rows="3"
                          style="width: 100%; padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 14px; outline: none; transition: border-color 0.2s; resize: vertical; font-family: inherit;"
                          onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'"></textarea>
            </div>

            <!-- File Upload Area -->
            <div>
                <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">File Materi <span style="color: var(--danger);">*</span></label>
                <div id="dropzone" style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 32px; text-align: center; cursor: pointer; background-color: #f8fafc; transition: all 0.2s;">
                    <input type="file" name="file" id="file-input" required style="display: none;">
                    <i class="fa-solid fa-cloud-arrow-up" style="font-size: 40px; color: #94a3b8; margin-bottom: 12px;"></i>
                    <p style="font-size: 14px; font-weight: 500; color: var(--text-main); margin-bottom: 4px;" id="filename-display">Tarik & lepas file Anda di sini, atau <span style="color: var(--primary);">telusuri</span></p>
                    <p style="font-size: 12px; color: var(--text-muted);">PDF, Word, Excel, PowerPoint, ZIP (Maksimal 50MB)</p>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                <a href="<?= \App\Helpers\Url::to('/materials') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Unggah Materi</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file-input');
    const filenameDisplay = document.getElementById('filename-display');

    dropzone.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            updateFilename(fileInput.files[0].name);
        }
    });

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = 'var(--primary)';
        dropzone.style.backgroundColor = '#e0e7ff';
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.style.borderColor = '#cbd5e1';
        dropzone.style.backgroundColor = '#f8fafc';
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = '#cbd5e1';
        dropzone.style.backgroundColor = '#f8fafc';
        
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            updateFilename(e.dataTransfer.files[0].name);
        }
    });

    function updateFilename(name) {
        filenameDisplay.innerHTML = `File terpilih: <strong style="color: var(--primary); font-family: monospace;">${name}</strong>`;
        dropzone.style.borderColor = 'var(--success)';
        dropzone.style.backgroundColor = '#ecfdf5';
    }
</script>
