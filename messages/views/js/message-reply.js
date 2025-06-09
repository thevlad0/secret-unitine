//1. To do: add chained messages visualization!!!
//function for reading message content from DB needed!!!
const replyTitle = "Отговор";
const replyToAllTitle = "Отговор до всички";
const forwardTitle = "Препращане";


const cancelReplyMessageIcon = document.querySelector('#close-reply-form');  
const replyMessageContainer = document.getElementById('reply-form-container');

cancelReplyMessageIcon.addEventListener('click', () => {
    var replyForm = document.getElementById('reply-form');
    replyForm.reset();  
    replyMessageContainer.style.display = 'none';
});

const replyButton = document.getElementById('reply-message-button');
const replyToAllButton = document.getElementById('reply-to-all-button');
const forwardButton = document.getElementById('forward-button');

function showReplyMessageForm(formTitle) { 
   const replyMessageTitle = document.getElementById('reply-message-title');
   replyMessageTitle.textContent = formTitle;
   replyMessageContainer.style.display = 'block';  
   const recipientsTitle = document.getElementById('recipients-title');
   
   if (formTitle==replyTitle || formTitle==replyToAllTitle) {
     const recipients = document.getElementById('message-sender').textContent;
     recipientsTitle.value = recipients;
     //2. TO DO!!!
     //call the messages sending php script!
  } else {
     recipientsTitle.readOnly = false;
     recipientsTitle.placeholder = "Получатели";
     //3. TO DO!!!
     //get the recipients by value and calls the messages sending php script!
  }
}

replyButton.addEventListener('click', () => showReplyMessageForm(replyTitle));
replyToAllButton.addEventListener('click', ()=> showReplyMessageForm(replyToAllTitle));
forwardButton.addEventListener('click', ()=> showReplyMessageForm(forwardTitle));

