$(document).ready(function () {
    // =================================================================
    // DOM Elements
    // =================================================================
    const $dropzone = $('#dropzone');
    const $fileInput = $('#fileInput');
    const $fileList = $('#fileList');
    const $noFilesMessage = $('#noFilesMessage');
    const $successMessage = $('#successMessage');
    const $successText = $('#successText');
    const $errorMessage = $('#errorMessage');
    const $errorText = $('#errorText');
    const $storageModal = $('#storageModal');
    const $storageImagesGrid = $('#storageImagesGrid');
    const $noStorageMessage = $('#noStorageMessage');
    const $attachStorageBtn = $('#attachStorageBtn');
    const $selectedCount = $('#selectedCount');
    const $storageCount = $('#storageCount');
    const $modalLoading = $('#modalLoading');

    // =================================================================
    // State variables
    // =================================================================
    let files = [];
    let fileSourceMap = new WeakMap();
    let fileSavedMap = new WeakMap();
    let fileIdMap = new WeakMap();
    let selectedStorageImages = new Set();
    let allStorageImages = [];
    let cachedStorageImages = [];
    let isImagesCached = false;
    let isModalInitialized = false;
    let isModalLoading = false;
    let abortController = null;
    let currentVariantIndex = null;

    // =================================================================
    // Configuration
    // =================================================================
    const MAX_FILE_SIZE = 5 * 1024 * 1024;
    const MAX_TOTAL_FILES = 50;
    const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];

    // CSRF token & Universal UID
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const uid = getUidFromUrl();

    // =================================================================
    // UNIVERSAL AJAX INTERCEPTOR
    // =================================================================
    $(document).ajaxSend(function (event, xhr, settings) {
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

        if (!uid) return;

        if (settings.data instanceof FormData) {
            if (!settings.data.has('uid')) {
                settings.data.append('uid', uid);
            }
            return;
        }

        if (typeof settings.data === 'object' && settings.data !== null) {
            settings.data.uid = uid;
            return;
        }

        const separator = settings.url.includes('?') ? '&' : '?';
        if (!settings.url.includes('uid=')) {
            settings.url += `${separator}uid=${encodeURIComponent(uid)}`;
        }
    });

    // =================================================================
    // Initialize
    // =================================================================
    showNoFilesMessage();

    // =================================================================
    // Event Listeners
    // =================================================================
    $dropzone.on('click', function (e) {
        if (e.target === this) {
            $fileInput.trigger('click');
        }
    });

    $fileInput.on('change', function (e) {
        if (this.files && this.files.length > 0) {
            handleFiles(this.files, 'local').catch(handleError);
            $(this).val('');
        }
    });

    // Delegated event handler for remove buttons
    $fileList.on('click', '.btn-remove', async function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $this = $(this);
        const index = parseInt($this.data('index'));
        const imageId = $this.data('image-id');
        const isSaved = $this.data('is-saved') === 'true';
        const source = $this.data('source');

        console.log('Remove button clicked:', {
            index: index,
            imageId: imageId,
            isSaved: isSaved,
            source: source
        });

        await handleImageRemoval(index, imageId, isSaved, source, $this);
    });

    // Delegated event handler for save buttons
    $fileList.on('click', '.btn-save', async function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $this = $(this);
        const fileIndex = parseInt($this.data('index'));
        const fileToSave = files[fileIndex];

        if (fileToSave) {
            await handleImageSave(fileToSave, fileIndex, $this);
        }
    });

    // Drag and drop
    $dropzone
        .on('dragenter', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        })
        .on('dragover', function (e) {
            e.preventDefault();
            e.stopPropagation();
        })
        .on('dragleave', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (!$(this).find($(e.relatedTarget)).length) {
                $(this).removeClass('dragover');
            }
        })
        .on('drop', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');

            if (e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.files) {
                handleFiles(e.originalEvent.dataTransfer.files, 'local').catch(handleError);
            }
        });

    // Attach from storage
    $attachStorageBtn.off('click').on('click', async function () {
        if (selectedStorageImages.size === 0) {
            showError('Please select at least one image');
            return;
        }

        const originalHTML = $attachStorageBtn.html();
        $attachStorageBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

        try {
            console.log('============================');
            console.log('Current Variant Index:', currentVariantIndex);
            console.log('Selected Images Count:', selectedStorageImages.size);
            console.log('============================');

            // DEBUG: Check what type of attachment we're doing
            if (currentVariantIndex !== null && currentVariantIndex !== undefined) {
                console.log('Attaching to variant:', currentVariantIndex);
                await handleVariantImageAttachment(currentVariantIndex);
            } else {
                console.log('Attaching to regular files');
                await handleRegularFileAttachment();
            }
        } catch (error) {
            console.error('Error attaching files:', error);
            showError('Error loading images from storage. Please try again.');
        } finally {
            $attachStorageBtn.prop('disabled', false).html(originalHTML);
            $storageModal.modal('hide');
            // currentVariantIndex = null; // ⚠️ यह लाइन हटाएं या comment करें
            $('.modal-backdrop').removeClass('fade show').addClass('fade hide');
        }
    });


    // =================================================================
    // CORE FUNCTIONS - FIXED FOR MULTIPLE ATTACHMENTS
    // =================================================================
    async function handleImageRemoval(index, imageId, isSaved, source, $button) {
        console.log('handleImageRemoval called:', { index, imageId, isSaved, source });

        const file = files[index];
        const actualIsSaved = file ? fileSavedMap.get(file) : false;
        const actualImageId = file ? fileIdMap.get(file) : null;

        console.log('File details:', {
            fileName: file?.name,
            actualIsSaved: actualIsSaved,
            actualImageId: actualImageId,
            passedIsSaved: isSaved,
            passedImageId: imageId
        });

        const isImageSaved = actualIsSaved || isSaved;
        const dbImageId = actualImageId || imageId;

        if (dbImageId && dbImageId !== '' && dbImageId !== 'null' && dbImageId !== 'undefined') {
            const originalHTML = $button.html();
            $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            try {
                console.log('Deleting from server with ID:', dbImageId);
                const success = await deleteStoredImageFromServer(dbImageId);
                console.log('Delete response:', success);

                if (success) {
                    removeFile(index);
                    showSuccessMessage('Image deleted from server and removed from list');

                    // Update cache
                    const indexInStorage = allStorageImages.findIndex(img => img.id === dbImageId.toString());
                    if (indexInStorage > -1) {
                        allStorageImages.splice(indexInStorage, 1);
                        cachedStorageImages = [...allStorageImages];
                    }
                } else {
                    throw new Error('Delete operation returned false');
                }
            } catch (error) {
                console.error('Delete error:', error);
                $button.prop('disabled', false).html(originalHTML);
                showError('Failed to delete from server: ' + error.message);
                removeFileLocalOnly(index);
            }
        } else {
            console.log('No image ID found - removing from local list only');
            removeFileLocalOnly(index);
            showSuccessMessage('Image removed from list');
        }
    }

    async function handleImageSave(file, fileIndex, $button) {
        const originalHTML = $button.html();
        $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving');

        try {
            const result = await storeImageInRedis(file);

            if (result && result.success) {
                fileSavedMap.set(file, true);
                if (result.image_id) {
                    fileIdMap.set(file, result.image_id);
                }

                renderFileList();
                showSuccessMessage(`"${escapeHtml(file.name)}" saved to storage!`);

                // Invalidate cache since new image added
                isImagesCached = false;
            } else {
                $button.prop('disabled', false).html(originalHTML);
                showError(`Failed to save "${escapeHtml(file.name)}" to storage.`);
            }
        } catch (error) {
            console.error('Save error:', error);
            $button.prop('disabled', false).html(originalHTML);
            showError(`Failed to save "${escapeHtml(file.name)}" to storage.`);
        }
    }

    async function handleFiles(newFiles, source) {
        const fileArray = Array.from(newFiles);

        if (fileArray.length === 0) return;

        if (files.length + fileArray.length > MAX_TOTAL_FILES) {
            showError(`Cannot exceed ${MAX_TOTAL_FILES} images. You already have ${files.length} images selected.`);
            return;
        }

        if (source === 'storage') {
            const savedFiles = [];

            for (const file of fileArray) {
                const imageId = fileIdMap.get(file);
                const isDuplicate = imageId ? files.some(f => fileIdMap.get(f) === imageId) : false;

                if (!isDuplicate) {
                    files.push(file);
                    savedFiles.push(file.name);
                }
            }

            if (savedFiles.length > 0) {
                renderFileList();
                showNoFilesMessage();
                showSuccessMessage(`${savedFiles.length} image(s) attached from storage!`);
            }
            return;
        }

        const { validFiles, invalidFiles } = validateFiles(fileArray);

        if (invalidFiles.length > 0) {
            showError(`Some files were rejected: ${invalidFiles.join(', ')}. Only images up to ${formatFileSize(MAX_FILE_SIZE)} are allowed.`);
        }

        if (validFiles.length > 0) {
            const savedFiles = [];

            for (const file of validFiles) {
                fileSourceMap.set(file, source);
                fileSavedMap.set(file, false);

                try {
                    const result = await storeImageInRedis(file);
                    console.log('Upload result for', file.name, ':', result);

                    if (result && result.success) {
                        fileSavedMap.set(file, true);
                        if (result.image_id) {
                            console.log('Storing image ID:', result.image_id, 'for file:', file.name);
                            fileIdMap.set(file, result.image_id);
                        } else if (result.id) {
                            console.log('Storing image ID (from id):', result.id, 'for file:', file.name);
                            fileIdMap.set(file, result.id);
                        } else {
                            console.warn('No image_id or id found in response:', result);
                        }
                        savedFiles.push(file.name);

                        // Invalidate cache
                        isImagesCached = false;
                    }
                } catch (error) {
                    console.error('Auto-save error:', error);
                }
            }

            files = [...files, ...validFiles];
            renderFileList();
            showNoFilesMessage();

            if (savedFiles.length > 0) {
                showSuccessMessage(`${validFiles.length} image(s) uploaded and saved to storage!`);
            }
        }
    }

    function validateFiles(fileArray) {
        const validFiles = [];
        const invalidFiles = [];

        fileArray.forEach(file => {
            if (!ALLOWED_TYPES.includes(file.type.toLowerCase())) {
                invalidFiles.push(`${file.name} (not an image)`);
                return;
            }

            if (file.size > MAX_FILE_SIZE) {
                invalidFiles.push(`${file.name} (too large)`);
                return;
            }

            const isDuplicate = files.some(f =>
                f.name === file.name &&
                f.size === file.size &&
                f.type === file.type
            );

            if (isDuplicate) {
                invalidFiles.push(`${file.name} (duplicate)`);
                return;
            }

            validFiles.push(file);
        });

        return { validFiles, invalidFiles };
    }

    function renderFileList() {
        $fileList.empty();

        if (files.length === 0) {
            showNoFilesMessage();
            return;
        }

        files.forEach((file, index) => {
            const source = fileSourceMap.get(file) || 'local';
            const isSaved = fileSavedMap.get(file) || false;
            const imageId = fileIdMap.get(file) || null;
            const $fileItem = createFileItem(file, index, source, isSaved, imageId);
            $fileList.append($fileItem);
        });
    }

    // =================================================================
    // AJAX FUNCTIONS
    // =================================================================
    function getUidFromUrl() {
        const params = new URLSearchParams(window.location.search);
        if (params.has('uid')) return params.get('uid');

        const segments = window.location.pathname.replace(/\/$/, '').split('/');
        return segments.pop() || null;
    }

    async function storeImageInRedis(file) {
        return new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('image', file);

            $.ajax({
                url: '/admin/images/store',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success(response) {
                    if (response.success) {
                        resolve(response);
                    } else {
                        reject(new Error(response.message || 'Upload failed'));
                    }
                },
                error(xhr) {
                    reject(new Error(xhr.responseJSON?.message || 'Upload failed'));
                }
            });
        });
    }

    async function getAllStoredImagesFromServer() {
        return new Promise((resolve, reject) => {
            // Cancel previous request if exists
            if (abortController) {
                abortController.abort();
            }

            abortController = new AbortController();

            $.ajax({
                url: '/admin/images/get',
                type: 'GET',
                dataType: 'json',
                beforeSend: function (xhr) {
                    if (abortController) {
                        xhr.abort = abortController.abort.bind(abortController);
                    }
                },
                success: function (response) {
                    if (response.success) {
                        const mappedImages = (response.images || []).map(img => ({
                            id: img.id.toString(),
                            url: img.image_url,
                            name: img.image_url.split('/').pop() || `image_${img.id}`,
                            type: 'image/jpeg',
                            size: 0,
                            timestamp: new Date(img.created_at).getTime()
                        }));
                        resolve(mappedImages);
                    } else {
                        reject(new Error(response.message || 'Failed to get images'));
                    }
                },
                error: function (xhr, status, error) {
                    if (status !== 'abort') {
                        reject(new Error('AJAX error: ' + error));
                    }
                }
            });
        });
    }

    async function getStoredImageFromServer(imageId) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `/admin/images/get/${imageId}`,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        resolve(response.image);
                    } else {
                        reject(new Error(response.message || 'Failed to get image'));
                    }
                },
                error: function (xhr, status, error) {
                    reject(new Error('AJAX error: ' + error));
                }
            });
        });
    }

    async function deleteStoredImageFromServer(imageId) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `/admin/images/delete/${imageId}`,
                type: 'DELETE',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        resolve(true);
                    } else {
                        reject(new Error(response.message || 'Delete failed'));
                    }
                },
                error: function (xhr, status, error) {
                    reject(new Error('AJAX error: ' + error));
                }
            });
        });
    }

    // =================================================================
    // UI FUNCTIONS
    // =================================================================
    function createFileItem(file, index, source, isSaved, imageId) {
        const $div = $('<div>', {
            class: 'file-item',
            'data-index': index
        });

        if (source === 'storage') {
            $div.addClass('from-storage');
        } else if (isSaved) {
            $div.addClass('saved-to-storage');
        }

        const size = formatFileSize(file.size);
        const fileType = file.type.split('/')[1]?.toUpperCase() || 'UNKNOWN';
        const imageIdStr = imageId ? String(imageId) : '';

        $div.html(`
            <div class="file-info">
                <div class="file-icon">
                    <i class="fas fa-file-image text-success"></i>
                </div>
                <div class="file-details">
                    <div class="file-name" title="${escapeHtml(file.name)}">
                        ${escapeHtml(file.name)}
                        <span class="source-badge ${source === 'storage' ? 'source-storage' : isSaved ? 'saved-badge' : 'source-local'}">
                            ${source === 'storage' ? 'STORAGE' : isSaved ? 'SAVED' : 'LOCAL'}
                        </span>
                    </div>
                    <div class="file-size">${size} • ${fileType}</div>
                </div>
            </div>
            <div class="file-actions">
                ${!isSaved && source !== 'storage' ? `
                <button type="button" class="btn btn-sm btn-outline-success btn-save" data-index="${index}" title="Save to storage">
                    <i class="fas fa-save me-1"></i> Save
                </button>
                ` : ''}
                ${isSaved || source === 'storage' ? `
                <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Already in storage">
                    <i class="fas fa-check me-1"></i> Saved
                </button>
                ` : ''}
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove" 
                        data-index="${index}" 
                        data-image-id="${imageIdStr}"
                        data-is-saved="${isSaved ? 'true' : 'false'}"
                        data-source="${source}"
                        title="Remove image">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `);

        return $div;
    }

    function removeFile(index) {
        if (index >= 0 && index < files.length) {
            files.splice(index, 1);
            renderFileList();
            $fileInput.val('');
            showNoFilesMessage();
        }
    }

    function removeFileLocalOnly(index) {
        if (index >= 0 && index < files.length) {
            files.splice(index, 1);
            renderFileList();
            $fileInput.val('');
            showNoFilesMessage();
        }
    }

    function showNoFilesMessage() {
        $noFilesMessage.toggle(files.length === 0);
    }

    function renderCachedImages() {
        if (cachedStorageImages.length === 0) {
            $noStorageMessage.show();
            // $storageImagesGrid.hide();
            return;
        }

        $noStorageMessage.hide();
        $storageImagesGrid.show();
        $storageImagesGrid.empty();

        cachedStorageImages.forEach(imageData => {
            const isSelected = selectedStorageImages.has(imageData.id);
            const $item = createStorageImageItem(imageData, isSelected);
            $storageImagesGrid.append($item);
        });

        updateStorageCount(cachedStorageImages.length);
        updateSelectedCount();
    }

    async function loadStorageImages() {
        if (isModalLoading) return;

        isModalLoading = true;
        $modalLoading.show();

        // First show cached images if available
        if (isImagesCached && cachedStorageImages.length > 0) {
            renderCachedImages();
        } else {
            $storageImagesGrid.hide();
            $noStorageMessage.hide();
        }

        try {
            // Handle preselection for variants
            if (currentVariantIndex !== null && currentVariantIndex !== undefined) {
                const $inputField = $(`#variant-images-input-${currentVariantIndex}`);
                const currentValue = $inputField.val();

                if (currentValue && typeof currentValue === 'string') {
                    const preselectedIds = currentValue.split(',')
                        .map(id => {
                            if (typeof id === 'string') return id.trim();
                            if (typeof id === 'number') return id.toString().trim();
                            return null;
                        })
                        .filter(id => id && id !== '' && id !== 'null' && id !== 'undefined');

                    // Clear previous selection and set new preselection
                    selectedStorageImages.clear();
                    preselectedIds.forEach(id => {
                        if (id) selectedStorageImages.add(id);
                    });
                }
            } else {
                // For regular file attachment, clear selection
                selectedStorageImages.clear();
            }

            // Load fresh images from server
            const images = await getAllStoredImagesFromServer();
            allStorageImages = images;
            cachedStorageImages = [...images];
            isImagesCached = true;

            if (!Array.isArray(images) || images.length === 0) {
                $noStorageMessage.show();
                // $storageImagesGrid.hide();
                return;
            }

            $noStorageMessage.hide();
            $storageImagesGrid.show();

            // Sort by timestamp (newest first)
            images.sort((a, b) => b.timestamp - a.timestamp);

            // Clear and rebuild grid
            $storageImagesGrid.empty();
            images.forEach(imageData => {
                const isSelected = selectedStorageImages.has(imageData.id);
                const $item = createStorageImageItem(imageData, isSelected);
                $storageImagesGrid.append($item);
            });

            updateStorageCount(images.length);
            updateSelectedCount();

        } catch (error) {
            console.error('Load storage images error:', error);
            if (error.message && !error.message.includes('abort')) {
                // If error but we have cached images, show them
                if (cachedStorageImages.length > 0) {
                    renderCachedImages();
                    showError('Using cached images. Failed to load fresh data.');
                } else {
                    $noStorageMessage.text('Failed to load images. Please try again.').show();
                    // $storageImagesGrid.hide();
                }
            }
        } finally {
            isModalLoading = false;
            $modalLoading.hide();
            $attachStorageBtn.prop('disabled', false);
            updateSelectedCount();
        }
    }

    function createStorageImageItem(imageData, isSelected = false) {
        const $item = $(`
            <div class="storage-image-item ${isSelected ? 'selected' : ''}" data-id="${imageData.id}">
                <button class="delete-stored-image" data-id="${imageData.id}" title="Delete from storage">
                    <i class="fas fa-times"></i>
                </button>

                <div class="storage-image-preview">
                    <img 
                        src="${imageData.url}" 
                        alt="${escapeHtml(imageData.name)}"
                        loading="lazy"
                        onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23ddd%22 width=%22100% height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%23999%22%3ENo Image%3C/text%3E%3C/svg%3E';">
                </div>

                <div class="storage-image-name" title="${escapeHtml(imageData.name)}">
                    ${escapeHtml(imageData.name.length > 15 ? imageData.name.substring(0, 15) + '...' : imageData.name)}
                </div>
                
                <div class="selection-checkmark">
                    <i class="fas fa-check"></i>
                </div>
            </div>
        `);

        // Select image
        $item.on('click', function (e) {
            if ($(e.target).closest('.delete-stored-image').length) {
                return;
            }

            const $this = $(this);
            const imageId = $this.data('id');

            if ($this.hasClass('selected')) {
                $this.removeClass('selected');
                selectedStorageImages.delete(imageId);
            } else {
                $this.addClass('selected');
                selectedStorageImages.add(imageId);
            }

            updateSelectedCount();
        });

        // Delete image
        $item.find('.delete-stored-image').on('click', async function (e) {
            e.stopPropagation();
            e.preventDefault();

            const imageId = $(this).data('id');

            if (!confirm('Are you sure you want to delete this image from storage?')) return;

            try {
                const success = await deleteStoredImageFromServer(imageId);

                if (success) {
                    selectedStorageImages.delete(imageId);
                    $(this).closest('.storage-image-item').remove();
                    updateSelectedCount();

                    // Update all image lists
                    const indexAll = allStorageImages.findIndex(img => img.id === imageId);
                    if (indexAll > -1) {
                        allStorageImages.splice(indexAll, 1);
                    }

                    const indexCache = cachedStorageImages.findIndex(img => img.id === imageId);
                    if (indexCache > -1) {
                        cachedStorageImages.splice(indexCache, 1);
                    }

                    updateStorageCount(cachedStorageImages.length);

                    if ($storageImagesGrid.children().length === 0) {
                        $noStorageMessage.show();
                        // $storageImagesGrid.hide();
                    }

                    showSuccessMessage('Image deleted from storage!');
                }
            } catch (error) {
                console.error('Delete error:', error);
                showError('Failed to delete image from storage');
            }
        });

        return $item;
    }

    function updateSelectedCount() {
        $selectedCount.text(selectedStorageImages.size);
    }

    function updateStorageCount(count) {
        if ($storageCount.length) {
            $storageCount.text(count);
        }
    }

    // =================================================================
    // VARIANT HANDLING FUNCTIONS - FIXED FOR MULTIPLE ATTACHMENTS
    // =================================================================
    async function handleVariantImageAttachment(variantIndex) {
        if (selectedStorageImages.size === 0) return;

        const $inputField = $(`#variant-images-input-${variantIndex}`);
        const currentValue = $inputField.val();

        let currentIds = [];
        if (currentValue && typeof currentValue === 'string') {
            currentIds = currentValue.split(',')
                .map(id => {
                    if (typeof id === 'string') return id.trim();
                    if (typeof id === 'number') return id.toString().trim();
                    return null;
                })
                .filter(id => id && id !== '' && id !== 'null' && id !== 'undefined');
        }

        const newIds = Array.from(selectedStorageImages)
            .map(id => {
                if (typeof id === 'string') return id.trim();
                if (typeof id === 'number') return id.toString().trim();
                return null;
            })
            .filter(id => id && id !== '' && id !== 'null' && id !== 'undefined');

        // Merge and remove duplicates
        const allIds = [...new Set([...currentIds, ...newIds])];

        // Update the hidden input field
        $inputField.val(allIds.join(','));

        // Update the preview
        await updateVariantPreview(variantIndex, allIds);

        showSuccessMessage(`${newIds.length} image(s) attached to variant!`);

        // Clear selection after attaching
        selectedStorageImages.clear();
        updateSelectedCount();
    }

    async function updateVariantPreview(variantIndex, imageIds) {
        const $previewContainer = $(`#variant-images-${variantIndex}`);
        $previewContainer.empty();

        const validImageIds = imageIds
            .map(id => {
                if (typeof id === 'string') return id.trim();
                if (typeof id === 'number') return id.toString().trim();
                return null;
            })
            .filter(id => id && id !== '' && id !== 'null' && id !== 'undefined');

        const promises = validImageIds.map(async (imageId) => {
            try {
                let imageData = allStorageImages.find(img => img.id === imageId.toString());

                if (!imageData || !imageData.url) {
                    try {
                        const serverImageData = await getStoredImageFromServer(imageId);
                        if (serverImageData && serverImageData.image_url) {
                            imageData = {
                                id: imageId,
                                url: serverImageData.image_url,
                                name: serverImageData.image_url.split('/').pop() || `image_${imageId}`
                            };
                        }
                    } catch (serverError) {
                        console.warn(`Image with ID ${imageId} not found on server`);
                        return null;
                    }
                }

                if (imageData && imageData.url) {
                    return $(`
                        <div class="position-relative d-inline-block me-2 mb-2" style="width: 80px;">
                            <img src="${imageData.url}" 
                                 alt="${escapeHtml(imageData.name || 'Variant Image')}"
                                 class="img-thumbnail" 
                                 style="width: 80px; height: 80px; object-fit: cover;"
                                 onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%23999%22%3EError%3C/text%3E%3C/svg%3E';">
                            <button type="button" 
                                    class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0 remove-variant-image"
                                    style="width: 20px; height: 20px; font-size: 10px;"
                                    data-variant-index="${variantIndex}"
                                    data-image-id="${imageId}"
                                    onclick="removeVariantImage(${variantIndex}, '${imageId}', this)">
                                ×
                            </button>
                        </div>
                    `);
                }
            } catch (error) {
                console.error('Error loading variant image:', imageId, error);
            }
            return null;
        });

        const previews = await Promise.all(promises);
        previews.filter(preview => preview !== null).forEach(preview => {
            $previewContainer.append(preview);
        });
    }

    window.removeVariantImage = function (variantIndex, imageId, element) {
        const $inputField = $(`#variant-images-input-${variantIndex}`);
        const currentValue = $inputField.val();

        let currentIds = [];
        if (currentValue && typeof currentValue === 'string') {
            currentIds = currentValue.split(',')
                .map(id => {
                    if (typeof id === 'string') return id.trim();
                    if (typeof id === 'number') return id.toString().trim();
                    return null;
                })
                .filter(id => id && id !== '' && id !== 'null' && id !== 'undefined');
        }

        const imageIdStr = String(imageId).trim();
        const updatedIds = currentIds.filter(id => id !== imageIdStr);

        $inputField.val(updatedIds.join(','));

        $(element).closest('.position-relative').remove();

        showSuccessMessage('Image removed from variant');
    };

    async function handleRegularFileAttachment() {
        if (files.length + selectedStorageImages.size > MAX_TOTAL_FILES) {
            showError(`Cannot exceed ${MAX_TOTAL_FILES} total files. You already have ${files.length} files selected.`);
            return;
        }

        const newFiles = [];

        for (const imageId of selectedStorageImages) {
            try {
                const imageIdStr = String(imageId);
                let imageData = allStorageImages.find(img => img.id === imageIdStr);

                if (!imageData || !imageData.url) {
                    const serverImageData = await getStoredImageFromServer(imageId);
                    if (serverImageData) {
                        imageData = {
                            id: imageIdStr,
                            url: serverImageData.image_url,
                            name: serverImageData.image_url?.split('/').pop() || `image_${imageId}.jpg`
                        };
                    }
                }

                if (imageData && imageData.url) {
                    const filename = imageData.name || `image_${imageId}.jpg`;
                    const file = await createFileFromStorageImage(imageData.url, filename);
                    fileSourceMap.set(file, 'storage');
                    fileSavedMap.set(file, true);
                    fileIdMap.set(file, imageIdStr);
                    newFiles.push(file);
                }
            } catch (error) {
                console.error('Error loading image:', imageId, error);
            }
        }

        if (newFiles.length > 0) {
            await handleFiles(newFiles, 'storage');
        }

        // Clear selection after attaching
        selectedStorageImages.clear();
        updateSelectedCount();
    }

    // =================================================================
    // UTILITY FUNCTIONS
    // =================================================================
    async function createFileFromStorageImage(url, filename) {
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const blob = await response.blob();
            const mimeType = blob.type || 'image/jpeg';

            const file = new File([blob], filename, {
                type: mimeType,
                lastModified: Date.now()
            });

            return file;
        } catch (error) {
            console.error('Error creating file from URL:', error);

            const placeholderBlob = new Blob([''], { type: 'image/jpeg' });
            return new File([placeholderBlob], filename, {
                type: 'image/jpeg',
                lastModified: Date.now()
            });
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function showSuccessMessage(message) {
        $successText.text(message);
        $successMessage.show().removeClass('alert-danger alert-info').addClass('alert-success');
        $errorMessage.hide();

        setTimeout(() => {
            $successMessage.fadeOut();
        }, 5000);
    }

    function showError(message) {
        $errorText.text(message);
        $errorMessage.show();
        $successMessage.hide();

        setTimeout(() => {
            $errorMessage.fadeOut();
        }, 7000);
    }

    function handleError(error) {
        console.error('Error:', error);
        showError('An error occurred. Please try again.');
    }

    // =================================================================
    // BOOTSTRAP MODAL EVENTS - COMPLETELY FIXED
    // =================================================================
    $storageModal.on('show.bs.modal', function (e) {
        // Store the variant index in a global variable
        const targetVariantIndex = $(e.relatedTarget).data('variant-index');
        currentVariantIndex = targetVariantIndex;

        console.log('Modal opening, variant index:', currentVariantIndex);

        // Update button text based on context
        if (currentVariantIndex !== null && currentVariantIndex !== undefined) {
            $('#attachStorageBtn').html(`<i class="fas fa-paperclip me-2"></i>Attach to Variant`);
        } else {
            $('#attachStorageBtn').html(`<i class="fas fa-paperclip me-2"></i>Attach Selected`);
        }

        // Reset modal state
        isModalInitialized = true;

        // IMPORTANT: Clear selection when opening modal
        selectedStorageImages.clear();
        updateSelectedCount();

        // Show cached images immediately
        if (isImagesCached && cachedStorageImages.length > 0) {
            renderCachedImages();
        }

        // Then load fresh data
        loadStorageImages();
    });


    $storageModal.on('shown.bs.modal', function () {
        // Ensure modal is properly displayed
        $(this).css('display', 'block');
        $(this).addClass('show');
        $('.modal-backdrop').addClass('show');
    });

    $storageModal.on('hide.bs.modal', function () {
        // Cancel any ongoing AJAX requests
        if (abortController) {
            abortController.abort();
            abortController = null;
        }

        // Reset loading state
        isModalLoading = false;
        $modalLoading.hide();
    });

    $storageModal.on('hidden.bs.modal', function () {
        // Clean up modal but keep cached images
        selectedStorageImages.clear();
        updateSelectedCount();

        // Hide grid but don't empty it
        // $storageImagesGrid.hide();
        $noStorageMessage.hide();
        $modalLoading.hide();

        $attachStorageBtn.prop('disabled', false);
        // currentVariantIndex = null; // ⚠️ यह लाइन हटाएं या comment करें
        isModalInitialized = false;
    });


    $(document).on('click', '.alert .btn-close', function () {
        $(this).closest('.alert').hide();
    });

    $(window).on('beforeunload', function () {
        $('input[type="file"].temp-input').remove();
    });

    // =================================================================
    // ADDITIONAL FIX: Handle click events for variant attach buttons
    // =================================================================
    $(document).on('click', '.btn-attach-variant', function (e) {
        e.preventDefault();
        const variantIndex = $(this).data('variant-index');

        // Store the variant index
        currentVariantIndex = variantIndex;

        // Open modal
        $storageModal.modal('show');
    });
});