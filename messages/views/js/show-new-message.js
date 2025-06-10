import { initializeCompose } from './compose.js';
//import { initializeGroups } from './groups.js';

document.addEventListener('DOMContentLoaded', () => {

    initializeCompose();
    //initializeGroups();

    const sidebarNav = document.querySelector('.sidebar-nav ul');

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
    });
});