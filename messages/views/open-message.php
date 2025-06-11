<?php

    session_start();

    //$topic = htmlspecialchars($message['topic'] ?? '', ENT_QUOTES, 'UTF-8');
    //$messageSender = htmlspecialchars($message['senderUsername'] ?? '', ENT_QUOTES, 'UTF-8');
    $message = $_SESSION['message'] ? $_SESSION['message']  : null;
    if ($message) {
       
      $_SESSION['message']['senderUsername'] = htmlspecialchars($message['isAnonymous'] ? 'Анонимен' : $message['senderUsername']);
      $_SESSION['message']['topic'] = htmlspecialchars($message['topic']);
      $_SESSION['message']['content'] = htmlspecialchars($message['content']);
    }
?>


<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open message</title>
    <!--<link rel="stylesheet" href="./messages/views/css/inbox.css"> -->
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <link href="./css/open-message.css" rel="stylesheet" />
    <!--<script src="../../javascript/new-message.js" defer></script>-->
    <script src="./js/message-reply.js" defer></script>
    <!--<script src="./js/read-message-from-db.js" defer></script>-->
    <script src="./js/add-notes.js" defer></script>    
    <script type="module" src="./js/show-new-message.js"></script>

</head>
<body>
    <div class="inbox-container">
        <aside class="sidebar">
            <div class="sidebar-header">
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

<!--TUK E MOETO-->
       <section id="messages-table-section">
           <form id="open-message-form">
               <h2 id="message-title" class="annotatable">
                   <?php echo $_SESSION['message']['topic']?>
               </h2>
               <section id="message-text">
                 <h3 id="message-sender"><?php echo $_SESSION['message']['senderUsername'] ?></h3>
                    <p id="message-paragraph" class="annotatable">
                        <?php echo $_SESSION['message']['content']?>
                    </p>

                     <section id="message-buttons-section">
                        <button id="reply-message-button" type="button">Отговор</button>
                        <button id="reply-to-all-button" type="button">Отговор до всички</button>
                        <button id="forward-button" type="button">Препращане</button>
                        <button id="chain-button" type="button">Предишно съобщение</button>
                    </section>

              </section>
           </form>    
      </section>
<!--END OF MOETO-->
    </div>


<!--PAK MOE-->
     <section id="reply-form-container" style="display: none;">
                        <form id="reply-form" class="annotatable">
                            <section class="reply-form-header">
                                <h3 id="reply-message-title">Отговор</h3>
                                <img src="../../img/cancel.png" alt="Затвори" id="close-reply-form" />
                            </section>
                            <input id="recipients-title" type="text" readonly>
                            <textarea id="reply-text-area" placeholder="Въведете съобщение" rows="6"></textarea>
                            <section class="reply-form-actions">
                                <button id="send-reply-button" type="button">Изпращане</button>
                            </section>
                      </form>
    </section>
<!--PAK KRAI-->

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

    <!--<script>
        const sessionData = {'userId': "<?php echo $_SESSION['user']['id']; ?>"};
    </script> -->


</body>
</html>
























<link href="./css/open-message.css" rel="stylesheet" />
    <!--<script src="../../javascript/new-message.js" defer></script>-->
    <script src="./js/message-reply.js" defer></script>
    <!--<script src="./js/read-message-from-db.js" defer></script>-->
    <script src="./js/add-notes.js" defer></script>




    