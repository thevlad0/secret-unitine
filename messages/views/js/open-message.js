 const tableMessages = document.getElementsByClassName('message-cell');

Array.from(tableMessages).forEach(cell => {
  cell.addEventListener('click', () => {
    setTimeout(() => {
      window.location.href = "forms/messages/open-message.html";
    }, 2000);
  });
});