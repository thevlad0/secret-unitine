import { api } from './api.js';

let groupsListContainer;
let searchDebounceTimeout;

const createUserElement = (user) => {
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
    return userElement;
};

const appendUserToGroup = (user, groupId) => {
    const groupElement = groupsListContainer.querySelector(`.group-item[data-group-id="${groupId}"]`);
    if (!groupElement) return;

    const userList = groupElement.querySelector('.user-list');
    
    const emptyMessage = userList.querySelector('.empty-message.users');
    if (emptyMessage) {
        emptyMessage.remove();
    }

    const userElement = createUserElement(user);
    userList.appendChild(userElement);
};

const createGroupElement = (group) => {
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

    const addUserContainer = document.createElement('div');
    addUserContainer.className = 'add-user-form-container';
    addUserContainer.innerHTML = `
        <div class="add-user-form collapsed">
            <div class="autocomplete-container">
                <input type="text" class="new-user-name" placeholder="Търси по име или имейл..." autocomplete="off">
                <div class="autocomplete-results"></div>
            </div>
            <button class="add-user-confirm-btn icon-btn" title="Добави потребител">
                <span class="material-symbols-outlined">add_circle</span>
            </button>
        </div>
        <button class="add-user-show-btn">
            <span class="material-symbols-outlined">person_add</span>
            Добави потребител
        </button>
    `;

    if (group.users && group.users.length > 0) {
        group.users.forEach(user => userList.appendChild(createUserElement(user)));
    } else {
        userList.innerHTML = '<p class="empty-message users">Няма потребители в тази група.</p>';
    }

    groupElement.appendChild(header);
    groupElement.appendChild(userList);
    groupElement.appendChild(addUserContainer);
    return groupElement;
};

const renderGroups = (groups) => {
    groupsListContainer.innerHTML = '';
    if (groups.length === 0) {
        groupsListContainer.innerHTML = '<p class="empty-message">Нямате създадени групи.</p>';
        return;
    }
    groups.forEach(group => groupsListContainer.appendChild(createGroupElement(group)));
};

const loadGroups = async () => {
    const result = await api.get(`${sessionData.baseURL}api/groups.php`, 'get_groups', { userId: sessionData.userId });
    if (result.status === 'success') {
        renderGroups(result.groups);
    }
};

export const initializeGroups = () => {
    const toggleGroupsPanelBtn = document.getElementById('toggle-groups-panel');
    const groupsPanel = document.getElementById('groups-panel');
    groupsListContainer = document.getElementById('groups-list');
    const addGroupBtn = document.getElementById('add-group-btn');
    const newGroupNameInput = document.getElementById('new-group-name');
    
    toggleGroupsPanelBtn.addEventListener('click', () => {
        groupsPanel.classList.toggle('collapsed');
        toggleGroupsPanelBtn.classList.toggle('collapsed');
    });

    addGroupBtn.addEventListener('click', async () => {
        const name = newGroupNameInput.value.trim();
        if (name) {
            const result = await api.post(`${sessionData.baseURL}api/groups.php`, 'add_group', { name: name, ownerId: sessionData.userId });
            if (result.status === 'success' && result.group) {
                newGroupNameInput.value = '';
                const emptyMessage = groupsListContainer.querySelector('.empty-message');
                if (emptyMessage) emptyMessage.remove();
                groupsListContainer.appendChild(createGroupElement(result.group));
            } else {
                alert(result.message || 'Възникна грешка.');
            }
        }
    });

    groupsListContainer.addEventListener('input', (e) => {
        if (e.target.matches('.new-user-name')) {
            const input = e.target;
            const resultsContainer = input.nextElementSibling;
            const term = input.value.trim();

            clearTimeout(searchDebounceTimeout);

            if (term.length < 2) {
                resultsContainer.innerHTML = '';
                return;
            }

            searchDebounceTimeout = setTimeout(async () => {
                const result = await api.get(`${sessionData.baseURL}api/search.php`, 'search_users', { term });
                resultsContainer.innerHTML = '';
                if (result.status === 'success' && result.users.length > 0) {
                    result.users.forEach(user => {
                        const item = document.createElement('div');
                        item.className = 'autocomplete-item';
                        item.dataset.userId = user.id;
                        item.dataset.userName = user.name;
                        item.innerHTML = `${user.name + ' ' + user.lastname} <span class="username">(${user.username})</span>`;
                        resultsContainer.appendChild(item);
                    });
                } else {
                    resultsContainer.innerHTML = '<div class="autocomplete-item">Няма намерени потребители.</div>';
                }
            }, 300);
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
                const result = await api.post(`${sessionData.baseURL}api/groups.php`, 'delete_group', { id: groupId });
                if (result.status === 'success') {
                    groupItem.remove();
                } else {
                     alert('Грешка при изтриване на групата.');
                }
            }
        }
        
        if (e.target.closest('.delete-user-btn')) {
            const userItem = e.target.closest('.user-item');
            const userId = userItem.dataset.userId;
            if (confirm('Наистина ли искате да премахнете този потребител от групата?')) {
                const result = await api.post(`${sessionData.baseURL}api/groups.php`, 'delete_user', { groupId: groupId, userId: userId });
                if (result.status === 'success') {
                    userItem.remove();
                } else {
                    alert('Грешка при премахване на потребителя.');
                }
            }
        }

        if (e.target.closest('.add-user-show-btn')) {
            const button = e.target.closest('.add-user-show-btn');
            const form = groupItem.querySelector('.add-user-form');
            form.classList.remove('collapsed');
            button.style.display = 'none';
        }

        if (e.target.matches('.autocomplete-item') && e.target.dataset.userId) {
            const selectedItem = e.target;
            const container = groupItem.querySelector('.autocomplete-container');
            const input = container.querySelector('.new-user-name');
            
            input.value = selectedItem.dataset.userName;
            input.dataset.selectedUserId = selectedItem.dataset.userId;
            
            container.querySelector('.autocomplete-results').innerHTML = '';
        }

        if (e.target.closest('.add-user-confirm-btn')) {
            const input = groupItem.querySelector('.new-user-name');
            const userIdToAdd = input.dataset.selectedUserId;

            if (userIdToAdd) {
                const result = await api.post(`${sessionData.baseURL}api/groups.php`, 'add_user', { userId: userIdToAdd, groupId: groupId });
                if (result.status === 'success' && result.user) {
                    appendUserToGroup(result.user, groupId);
                    input.value = '';
                    delete input.dataset.selectedUserId;
                } else {
                    alert(result.message || 'Неуспешно добавяне на потребител.');
                }
            } else {
                alert('Моля, изберете потребител от списъка, като кликнете върху него.');
            }
        }
    });

    loadGroups();
};