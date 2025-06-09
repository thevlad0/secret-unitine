<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__ . '/../../util/authenticate.php';
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo BASE_URL; ?>">
    <title>Inbox</title>
    <link rel="stylesheet" href="messages/views/css/inbox.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body>
    <div class="inbox-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="app-title">
                    <span class="material-symbols-outlined app-logo">all_inbox</span>
                    <h3>Secret Unitine</h3>
                </div>
                <a href="logout" id="logout-btn" class="icon-btn" title="Изход">
                    <span class="material-symbols-outlined">logout</span>
                </a>
            </div>

            <div class="compose-btn-container">
                <button class="compose-btn">
                    <span class="material-symbols-outlined">edit</span>
                    Ново съобщение
                </button>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="active" data-view="inbox"><span class="material-symbols-outlined">inbox</span> Входящи</li>
                    <li data-view="sent"><span class="material-symbols-outlined">send</span> Изпратени</li>
                    <li data-view="starred"><span class="material-symbols-outlined">star</span> Със звезда</li>
                    <li data-view="trash"><span class="material-symbols-outlined">delete</span> Изтрити</li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <div class="groups-widget">
                    <div class="groups-header" id="toggle-groups-panel">
                        <span class="material-symbols-outlined">group</span>
                        <span>Управление на групи</span>
                        <span class="material-symbols-outlined expand-icon">expand_less</span>
                    </div>
                    <div class="groups-panel" id="groups-panel">
                        <div class="groups-list" id="groups-list"></div>
                        <div class="add-group-container">
                            <input type="text" id="new-group-name" placeholder="Име на нова група...">
                            <button id="add-group-btn" class="icon-btn">
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <h1 id="main-view-title">Входящи</h1>
            <p id="main-view-content">Тук ще се показват имейлите.</p>
        </main>
    </div>

    <div class="compose-window" id="compose-window">
        <div class="compose-window-header">
            <span>Ново съобщение</span>
            <button class="icon-btn close-compose-btn">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="compose-window-body">
            <input type="text" placeholder="Получатели">
            <input type="text" placeholder="Тема">
            <textarea placeholder="Напишете вашето съобщение..."></textarea>
        </div>
        <div class="attachments-container" id="attachments-container">
            </div>
        <div class="compose-window-footer">
            <button class="send-btn">Изпращане</button>
            <button type="button" class="icon-btn" id="attach-files-btn" title="Прикачи файлове">
                <span class="material-symbols-outlined">attachment</span>
            </button>
        </div>
    </div>

    <input type="file" id="file-input" multiple style="display: none;">

    <script>
        const sessionData = {
            'userId': "<?php echo $_SESSION['user']['id']; ?>",
            'baseURL': "<?php echo BASE_URL; ?>",
        };
    </script>

    <script type="module" src="messages/views/js/inbox.js"></script>
</body>
</html>