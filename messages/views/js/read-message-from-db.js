//assuming that the messageId has been sent as a url parameter by the inbox page
//Steps:
//extract the messageId as an url parameter
//fetch the read-message-from-db.script using messageId as a query parameter in a GET request


const urlParams = new URLSearchParams(window.location.search);  //!!!
const messageId = urlParams.get("messageId");


fetch(`../../../api/messages/read-message-from-db.php?messageId=${messageId}`)   //To test in postman!!!
  .then(async res => {
    const message = res.json();

    if (!res.ok) {
        console.error(message.error);
        return;
    }

    document.getElementById('message-title').textContent = message.topic;
    if (message.isAnonymous) {
        document.getElementById('message-sender').textContent = message.senderName;   //only if it isn't anonymous
    } else {
        document.getElementById('message-sender').textContent = "Анонимен"; 
    }
    document.getElementById('message-paragraph').textContent = message.content;
  })
  .catch(err => console.error("Error while loading message! ", err));
