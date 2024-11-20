const threadContents = document.querySelectorAll('.thread-content div');
const optionsList = document.querySelector('.options-list');



threadContents.forEach(thread => {



    
    const button = thread.querySelector('.options .button');
    if(button == optionsList) {
        button.addEventListener('click', async () => {
            event.stopPropagation();
            const rect = button.getBoundingClientRect();
    
            const top = rect.bottom;
            const left = rect.right - optionsList.offsetWidth;
    
            optionsList.style.top = `${top}px`;
            optionsList.style.left = `${left}px`;
    
            optionsList.classList.toggle('show');
        });
    }
    
    
    
});

const editButton = document.querySelector('.options-list .mark-closed');
editButton.addEventListener('click', async () => {
    event.stopPropagation();
    $.ajax({
        type: 'POST',
        url: 'toggle-close-thread.php',
        async: false,
        data: {
            thread: selectedElement
        },
        success: function(data) {
            if(data.redirect) {
                window.location.href = data.redirect;
              }
            
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Error al eliminar elemento: ' + textStatus);
        }
      });
});
const deleteButton = document.querySelector('.options-list .delete');
const popUpContainer = document.querySelector('.pop-up-container');
const cancel = document.querySelector(".pop-up-container .buttons .cancel");
const deletePopUpButton = document.querySelector('.pop-up-container .buttons .delete');

deleteButton.addEventListener('click', async () => {
    event.stopPropagation();
    popUpContainer.style.display = "flex";
    document.body.classList.add("pop-up");
    optionsList.classList.remove('show');
});


deletePopUpButton.onclick = function (){
    switch (true) {
        case selectedElement.includes('thread'):
            table = "thread";
            break;
        case selectedElement.includes('post'):
            table = "post";
            break;
        case selectedElement.includes('new'):
            table = "new";
            break;
        default:
            table = "post";
            break;
    }
    $.ajax({
        type: 'POST',
        url: 'delete-element.php',
        async: true,
        data: {
            table: table,
            element: selectedElement
        },
        success: function(data) {
            if(data.redirect) {
                window.location.href = data.redirect;
              }
            
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Error al eliminar elemento: ' + textStatus);
        }
      });
};

cancel.onclick = function (){
    closePopUp();
};

window.onclick = function(event) {
    if (event.target == popUpContainer) {
        closePopUp();
    }
};

document.onkeydown = function(event) {
    if (event.key === "Escape") {
        closePopUp();
    }
}

function closePopUp() {
    popUpContainer.style.display = "none";
    document.body.classList.remove("pop-up");
}


document.addEventListener('click', (event) => {
    if (!optionsList.contains(event.target)) {
        optionsList.classList.remove('show');
    }
});

window.addEventListener("resize", function () {
    optionsList.classList.remove('show');
});
