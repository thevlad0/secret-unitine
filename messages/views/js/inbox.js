import { initializeCompose } from './compose.js';

//import { initializeGroups } from './groups.js';

document.addEventListener('DOMContentLoaded', () => {

    initializeCompose();

   // initializeGroups();

    const sidebarNav = document.querySelector('.sidebar-nav ul');
    const mainViewTitle = document.getElementById('main-view-title');
    const mainViewContent = document.getElementById('main-view-content');

    function generateContent() {
        fetch('./services/load-user-inbox.php')
        .then(result => result.json())
        .then(messages => {
            const tableBody = document.getElementById('inbox-table-body');
            messages.forEach(message => {
                const messageRow = document.createElement("tr");

                const messageSender = document.createElement("td");
                messageSender.style.fontWeight = "bold";
                messageSender.style.fontSize = "24px";

                var starIcon = document.createElement("span");
                starIcon.setAttribute("class", "material-symbols-outlined");
                const star = document.createTextNode("star");
                starIcon.appendChild(star);

                starIcon.style.width = "24px";
                starIcon.style.textAlign = "center";
                starIcon.style.padding = "0px";

                messageSender.appendChild(starIcon);

                var sender;
                if (message['isAnonymous']) {
                    sender = document.createTextNode('Анонимен');
                } else {
                    sender = document.createTextNode(message['senderUsername']);
                }

                messageSender.appendChild(sender);
                messageRow.appendChild(messageSender);

                const aligningDiv = document.createElement("div");
               
                //const messageTopic = document.createElement("td");
                const messageTopic = document.createElement("span");
                const topic = document.createTextNode(message['topic']);
                messageTopic.style.fontWeight = "normal";
                messageTopic.style.fontSize = "16px";
                messageTopic.appendChild(topic);

               aligningDiv.appendChild(messageTopic);
               messageSender.appendChild(aligningDiv);

               const messageSentAt = document.createElement("td");
               messageSentAt.style.fontWeight = "bold";
               const sentAt = document.createTextNode(message['sentAt']);
               messageSentAt.appendChild(sentAt);
               messageRow.appendChild(messageSentAt);

               //messageRow.appendChild(aligningDiv);

                //messageRow.appendChild(messageTopic);

                console.log(message['sentAt']);
                
                tableBody.appendChild(messageRow);
            });
        })
    }

   generateContent();

   /*const viewContent = {
        inbox: { title: 'Входящи', generateInbox: generateContent() },
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
           //mainViewContent.textContent = viewContent[view].content;
            viewContent[view].generateInbox();
        }
    });*/
});