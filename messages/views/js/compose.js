let attachedFiles = [];

const renderAttachments = (attachmentsContainer) => {
    attachmentsContainer.innerHTML = ''; // Изчистваме контейнера преди ново рендериране.
    attachedFiles.forEach((file, index) => {
        const pill = document.createElement('div');
        pill.className = 'attachment-pill';
        pill.innerHTML = `
            <span>${file.name}</span>
            <button type="button" class="remove-attachment-btn" data-index="${index}" title="Премахни файла">
                <span class="material-symbols-outlined">close</span>
            </button>
        `;
        attachmentsContainer.appendChild(pill);
    });
};

const removeAttachment = (indexToRemove, attachmentsContainer) => {
    attachedFiles.splice(indexToRemove, 1);
    renderAttachments(attachmentsContainer);
};

export const initializeCompose = () => {
    const composeBtn = document.querySelector('.compose-btn');
    const composeWindow = document.getElementById('compose-window');
    const closeComposeBtn = document.querySelector('.close-compose-btn');
    const attachFilesBtn = document.getElementById('attach-files-btn');
    const fileInput = document.getElementById('file-input');
    const attachmentsContainer = document.getElementById('attachments-container');

    composeBtn.addEventListener('click', () => {
        composeWindow.classList.add('visible');
    });

    closeComposeBtn.addEventListener('click', () => {
        composeWindow.classList.remove('visible');
    });

    attachFilesBtn.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        Array.from(fileInput.files).forEach(file => attachedFiles.push(file));
        renderAttachments(attachmentsContainer);
        fileInput.value = '';
    });

    attachmentsContainer.addEventListener('click', (e) => {
        const removeBtn = e.target.closest('.remove-attachment-btn');
        if (removeBtn) {
            const indexToRemove = parseInt(removeBtn.dataset.index, 10);
            removeAttachment(indexToRemove, attachmentsContainer);
        }
    });
};