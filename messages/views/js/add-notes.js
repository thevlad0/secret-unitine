//some notes-dragging functions:
function makeDraggable(element) {
  let isDragging = false;
  let offsetX = 0;
  let offsetY = 0;

  element.addEventListener('mousedown', function (e) {
    isDragging = true;
    offsetX = e.clientX - element.offsetLeft;
    offsetY = e.clientY - element.offsetTop;
    element.style.cursor = 'move';
  });

  document.addEventListener('mousemove', function (e) {
    if (isDragging) {
      element.style.left = (e.clientX - offsetX) + 'px';
      element.style.top = (e.clientY - offsetY) + 'px';
    }
  });

  document.addEventListener('mouseup', function () {
    isDragging = false;
    element.style.cursor = 'default';
  });
}


function handleCancelImage(noteElement) {
    const cancelImage = document.createElement('img');
    cancelImage.id = "bin-image";
    cancelImage.className = "note-corner-icon";
    cancelImage.alt = "Delete"; 
    cancelImage.src="../../img/cancel.png";
    cancelImage.style.position = "absolute";
    cancelImage.style.top = "0px";
    cancelImage.style.right = "0px";
    cancelImage.style.height = "20%";
    cancelImage.style.width = "20%";

    cancelImage.addEventListener('click', () => {
        noteElement.style.display = "none";
    });
    noteElement.appendChild(cancelImage);    
}

function saveNoteTo(note) {
    localStorage.setItem(note.id, note);
}

function handleSaveImage(noteElement) {
    const saveImage = document.createElement('img');
    saveImage.id = "save-image";
    saveImage.className = "note-corner-icon";
    saveImage.alt = "Save"; 
    saveImage.src="../../img/save-icon.png";
    saveImage.style.position = "absolute";
    saveImage.style.top = "0px";
    saveImage.style.left = "0px";
    saveImage.style.height = "20%";
    saveImage.style.width = "20%";

    saveImage.addEventListener('click', saveNoteTo(noteElement));
    noteElement.appendChild(saveImage);    
}

//adding the property for notes creation to message text and topic:
document.querySelectorAll('.annotatable').forEach(element => {
  element.addEventListener('dblclick', function (event) {
    const note = document.createElement('section');
    note.classList.add("sticky-note");

    handleCancelImage(note);   
    handleSaveImage(note);

    //add note disappearance
    const textNote = document.createElement('input');
    textNote.style.boxSizing = "border-box";
    textNote.style.height = "100%";
    textNote.style.width = "100%";
    textNote.style.backgroundColor = "#FFFAA0";
    note.appendChild(textNote);

    note.id = "note" + Date.now();
    note.contenteditable = "true";
    note.style.position = "absolute";
    note.style.width = "120px";
    note.style.height = "100px";
    note.style.backgroundColor = "yellow";
    note.style.border = "1px solid white";
    
    note.style.top = event.clientY + 'px';
    note.style.left = event.clientX + 'px';
    note.style.display = 'block';

    //mapping the nwely created note to its element
    note.dataset.target = element.getAttribute('id');
    element.appendChild(note);
    note.focus();
    makeDraggable(note);
  });
});

/*document.querySelectorAll('.annotatable').forEach(element => 
    element.addEventListener('focus', () => {
    const allNotes = document.querySelectorAll(`.sticky-note[data-target="${element.id}"]`);
    allNotes.forEach(note => {
        note.style.display = 'block';
    });
}));
*/