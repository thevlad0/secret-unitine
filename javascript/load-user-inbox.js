fetch('../php/load-user-inbox.php')
  .then(res => res.json())
  .then(messages => {

    const tableBody = document.getElementById('messages-table-body');
    messages.forEach(message => {
      const messageRow = document.createElement('tr');
      const messageCellTopic = document.createElement('td'); 
      const messageCellSender = document.createElement('td');        
      $username = fetch('../php/get-user-by-id.php').then(res=>res.json()).then(result => result.username);
      
      messageCellSender.textContent = $username;
      messageCellTopic.textContent = message.topic;
      messageRow.appendChild(messageCellSender);
      messageRow.appendChild(messageCellTopic);
      tableBody.appendChild(messageRow);
    });
  });