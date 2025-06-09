document.addEventListener('DOMContentLoaded', () => {
    const composeBtn = document.querySelector('.compose-btn');
    const composeWindow = document.getElementById('compose-window');
    const closeComposeBtn = document.querySelector('.close-compose-btn');
    const toggleGroupsPanelBtn = document.getElementById('toggle-groups-panel');
    const groupsPanel = document.getElementById('groups-panel');
    const groupsListContainer = document.getElementById('groups-list');
    const addGroupBtn = document.getElementById('add-group-btn');
    const newGroupNameInput = document.getElementById('new-group-name');

    const sidebarNav = document.querySelector('.sidebar-nav ul');
    const mainViewTitle = document.getElementById('main-view-title');
    const mainViewContent = document.getElementById('main-view-content');
    const attachFilesBtn = document.getElementById('attach-files-btn');
    const fileInput = document.getElementById('file-input');
    const attachmentsContainer = document.getElementById('attachments-container');
    let attachedFiles = [];

    composeBtn.addEventListener('click', () => {
        composeWindow.classList.add('visible');
    });

    closeComposeBtn.addEventListener('click', () => {
        composeWindow.classList.remove('visible');
    });

    const viewContent = {
        inbox: { title: 'Входящи', content: 'Тук ще се показват получените съобщения.' },
        sent: { title: 'Изпратени', content: 'Тук ще намерите всички изпратени съобщения.' },
        starred: { title: 'Със звезда', content: 'Вашите важни съобщения, маркирани със звезда.' },
        trash: { title: 'Изтрити', content: 'Съобщенията тук ще бъдат изтрити перманентно след 30 дни.' }
    };

    sidebarNav.addEventListener('click', (e) => {
        const targetLi = e.target.closest('li');
        if (!targetLi) return;

        sidebarNav.querySelectorAll('li').forEach(li => li.classList.remove('active'));
        targetLi.classList.add('active');

        const view = targetLi.dataset.view;
        if (view && viewContent[view]) {
            mainViewTitle.textContent = viewContent[view].title;
            mainViewContent.textContent = viewContent[view].content;
        }
    });

    attachFilesBtn.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        Array.from(fileInput.files).forEach(file => attachedFiles.push(file));
        renderAttachments();
        
        fileInput.value = ''; 
    });
    
    const renderAttachments = () => {
        attachmentsContainer.innerHTML = '';
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

    attachmentsContainer.addEventListener('click', (e) => {
        const removeBtn = e.target.closest('.remove-attachment-btn');
        if (removeBtn) {
            const indexToRemove = parseInt(removeBtn.dataset.index, 10);
            attachedFiles.splice(indexToRemove, 1);
            renderAttachments();
        }
    });

    toggleGroupsPanelBtn.addEventListener('click', () => {
        groupsPanel.classList.toggle('collapsed');
        toggleGroupsPanelBtn.classList.toggle('collapsed');
    });

    const api = {
        get: async (action, params = {}) => {
            const url = `https://localhost/test/secret-unitine/api/groups.php?action=${action}&${new URLSearchParams(params).toString()}`;
            console.log(`Fetching: ${url}`);
            const response = await fetch(url);
            return response.json();
        },
        post: async (action, data) => {
            const formData = new FormData();
            formData.append('action', action);
            for (const key in data) {
                formData.append(key, data[key]);
            }
            const response = await fetch('https://localhost/test/secret-unitine/api/groups.php', {
                method: 'POST',
                body: formData
            });
            return response.json();
        }
    };

    const renderGroups = (groups) => {
        groupsListContainer.innerHTML = '';
        if (groups.length === 0) {
            groupsListContainer.innerHTML = '<p class="empty-message">Нямате създадени групи.</p>';
            return;
        }
        groups.forEach(group => {
            const groupElement = document.createElement('div');
            groupElement.className = 'group-item';
            groupElement.dataset.groupId = group.id;
            const header = document.createElement('div');
            header.className = 'group-item-header';
            header.innerHTML = `
                <span class="material-symbols-outlined group-expand-icon">arrow_right</span>
                <span class="group-name">${group.groupName}</span>
                <div class="group-actions">
                    <button class="icon-btn delete-group-btn" title="Изтрий групата">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </div>
            `;
            const userList = document.createElement('div');
            userList.className = 'user-list collapsed';
            if (group.users.length > 0) {
                group.users.forEach(user => {
                    const userElement = document.createElement('div');
                    userElement.className = 'user-item';
                    userElement.dataset.userId = user.id;
                    userElement.innerHTML = `
                        <span>${user.name}</span>
                        <div class="user-actions">
                            <button class="icon-btn delete-user-btn" title="Премахни потребителя">
                                <span class="material-symbols-outlined">person_remove</span>
                            </button>
                        </div>
                    `;
                    userList.appendChild(userElement);
                });
            } else {
                userList.innerHTML = '<p class="empty-message">Няма потребители в тази група.</p>';
            }
            groupElement.appendChild(header);
            groupElement.appendChild(userList);
            groupsListContainer.appendChild(groupElement);
        });
    };
    
    const loadGroups = async () => {
        const result = await api.get('get_groups', { userId: sessionData.userId });
        console.log(result.groups);
        if (result.status === 'success') {
            renderGroups(result.groups);
        }
    };

    addGroupBtn.addEventListener('click', async () => {
        const name = newGroupNameInput.value.trim();
        if (name) {
            const result = await api.post('add_group', { name });
            if (result.status === 'success') {
                newGroupNameInput.value = '';
                loadGroups();
            } else {
                alert(result.message || 'Възникна грешка.');
            }
        }
    });

    groupsListContainer.addEventListener('click', async (e) => {
        const groupItem = e.target.closest('.group-item');
        if (!groupItem) return;
        const groupId = groupItem.dataset.groupId;
        if (e.target.closest('.group-item-header')) {
            const userList = groupItem.querySelector('.user-list');
            const icon = groupItem.querySelector('.group-expand-icon');
            if (userList) {
                userList.classList.toggle('collapsed');
                icon.textContent = userList.classList.contains('collapsed') ? 'arrow_right' : 'arrow_drop_down';
            }
        }
        if (e.target.closest('.delete-group-btn')) {
            if (confirm('Наистина ли искате да изтриете тази група?')) {
                const result = await api.post('delete_group', { id: groupId });
                if (result.status === 'success') {
                    loadGroups();
                } else {
                    alert('Грешка при изтриване на групата.');
                }
            }
        }
        if (e.target.closest('.delete-user-btn')) {
             const userItem = e.target.closest('.user-item');
             const userId = userItem.dataset.userId;
             if (confirm('Наистина ли искате да премахнете този потребител от групата?')) {
                const result = await api.post('delete_user', { groupId, userId });
                if (result.status === 'success') {
                    loadGroups();
                } else {
                    alert('Грешка при премахване на потребителя.');
                }
             }
        }
    });

    loadGroups();
});