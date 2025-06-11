import { initializeCompose } from './compose.js';

//import { initializeGroups } from './groups.js';

document.addEventListener('DOMContentLoaded', () => {

    initializeCompose();

    // initializeGroups();

    const sidebarNav = document.querySelector('.sidebar-nav ul');
    const mainViewTitle = document.getElementById('main-view-title');

    var userId;         //From Session or???

    function changeStarredStatusOfMessage(folderName) {
        const starParent = document.getElementById('inbox-table-body');
        starParent.addEventListener('click', (event) => {
            if (event.target.id.startsWith('star-')) {
                const starId = event.target.id;
                const starIcon = document.getElementById(starId);
                const numIdStart = 5;
                const messageId = starId.substring(numIdStart);
                var starred = true;
                if (starIcon.style.backgroundColor === "yellow") {
                    starred = false;
                    starIcon.style.backgroundColor = '';
                } else {
                    starred = true;
                    starIcon.style.backgroundColor = "yellow";
                }
                fetch('./services/star-message.php', {
                    method: "POST",
                    body: JSON.stringify({
                        "isStarred": starred, "messageId": messageId,
                        "userId": userId, "folderName": folderName
                    }),
                }
                );
            }
        });
    }

    function removeMessage(folderName) {
        const binParent = document.getElementById('inbox-table-body');
        binParent.addEventListener('click', (event) => {
            if (event.target.id.startsWith('bin-')) {
                const binId = event.target.id;
                const numIdStart = 4;
                const messageId = binId.substring(numIdStart);

                fetch('./services/remove-message.php', {
                    method: "POST",
                    body: JSON.stringify({
                        "messageId": messageId,
                        "userId": userId,
                        "folderName": folderName
                    }),
                }
                );
                const elementToRemove = document.getElementById('msg-' + messageId);
                binParent.removeChild(elementToRemove);
                location.reload();
            }
        });
    }

    function sort(folderName) {
        const sortSelect = document.getElementById('sort-options');
        sortSelect.addEventListener('change', () => {
            const tBody = document.getElementById('inbox-table-body');
            tBody.innerHTML = '';
            generateContent({ 'folderName': folderName, 'sortBy': sortSelect.value });
        });
    }

    function filter(folderName) {
        const filterSelect = document.getElementById('filter-options');
        filterSelect.addEventListener('change', () => {
            const tBody = document.getElementById('inbox-table-body');
            tBody.innerHTML = '';
            generateContent({ 'folderName': folderName, 'filterBy': filterSelect.value });
        });
    }

    function generateContent(args) {
        fetch('./services/load-user-inbox.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(args),
        })
            .then(result => result.json())
            .then(result => {
                var messages = result.messages;
                userId = result.userId;
                const tableBody = document.getElementById('inbox-table-body');
                messages.forEach(message => {
                    const messageRow = document.createElement("tr");
                    messageRow.setAttribute("class", "table-messages-rows");

                    messageRow.setAttribute("id", "msg-" + message['id']);

                    messageRow.dataset.json = JSON.stringify(message);

                    const messageSender = document.createElement("td");
                    messageSender.style.fontWeight = message['isRead'] ? "lighter" : "bold";
                    messageSender.style.fontSize = "24px";

                    var starIcon = document.createElement("span");
                    starIcon.setAttribute("class", "icon-btn material-symbols-outlined stars");
                    starIcon.setAttribute("id", "star-" + message['id']);

                    const star = document.createTextNode("star");
                    starIcon.appendChild(star);
                    starIcon.style.backgroundColor = message['isStarred'] ? "yellow" : '';

                    messageSender.appendChild(starIcon);

                    var sender;
                    if (message['isAnonymous']) {
                        sender = document.createTextNode('Анонимен');
                    } else {
                        sender = document.createTextNode(message['senderUsername']);
                    }

                    messageSender.appendChild(sender);
                    messageRow.appendChild(messageSender);

                    var binIcon = document.createElement("td");
                    binIcon.setAttribute("class", "icon-btn material-symbols-outlined bins");
                    binIcon.setAttribute("id", "bin-" + message['id']);
                    const bin = document.createTextNode("delete");
                    binIcon.appendChild(bin);

                    messageRow.appendChild(binIcon);

                    const aligningDiv = document.createElement("div");

                    const messageTopic = document.createElement("span");
                    const topic = document.createTextNode(message['topic']);
                    messageTopic.style.fontWeight = "normal";
                    messageTopic.style.fontSize = "16px";
                    messageTopic.appendChild(topic);

                    aligningDiv.appendChild(messageTopic);
                    messageSender.appendChild(aligningDiv);

                    messageSender.style.width = "85%";

                    const messageSentAt = document.createElement("td");
                    messageSentAt.style.fontWeight = "bold";
                    const sentAt = document.createTextNode(message['sentAt']);
                    messageSentAt.appendChild(sentAt);
                    messageRow.appendChild(messageSentAt);

                    tableBody.appendChild(messageRow);
                });
            })
    }

    sidebarNav.addEventListener('click', (e) => {
        const targetLi = e.target.closest('li');
        if (!targetLi) return;

        sidebarNav.querySelectorAll('li').forEach(li => li.classList.remove('active'));
        targetLi.classList.add('active');

        const view = targetLi.dataset.view;
        if (view) {
            var folderName = 'Inbox';
            if (view === 'inbox') {
                mainViewTitle.textContent = 'Входящи';
                folderName = 'Inbox';
            } else if (view === 'sent') {
                mainViewTitle.textContent = 'Изпратени';
                folderName = 'SentMessages';
            } else if (view === 'starred') {
                mainViewTitle.textContent = 'Със звезда';
                //folderName = 'Starred';                     //TO-DOOOOOO???????????????//////
            } else if (view === 'trash') {
                mainViewTitle.textContent = 'Изтрити';
                folderName = 'Deleted';
            }

            const mainViewContent = document.getElementById('inbox-table-body');
            mainViewContent.innerHTML = '';
            generateContent({'folderName': folderName});
            changeStarredStatusOfMessage(folderName);
            removeMessage(folderName);
            sort(folderName);
            filter(folderName);
        }
    });
});