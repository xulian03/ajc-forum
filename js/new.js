document.addEventListener('DOMContentLoaded', () => {
    var close = document.querySelector(".close"); 

    close.onclick = function (){
        closeModal();
    };
    
    window.onclick = function(event) {
        if (event.target == modal) {
          closeModal();
        }
    };
    
    document.onkeydown = function(event) {
        if (event.key === "Escape") {
          closeModal();
        }

        if(modal.style.display != "block") {
            return;
        }
    
        var imagesCopy = [...imagesModal];
    
        var next = 0;
        var previous = 0;
        for(let i = 0; i < imagesCopy.length; i++) {
            var url = imagesCopy[i];
            
            if(modalImage.ref == url) {
                next = i + 1;
                if(next == imagesCopy.length) {
                    next = i;
                }
    
                previous = i - 1;
    
                if(previous < 0) {
                    previous = 0;
                }
                break;
            }
        }
    
        if(event.key === "ArrowLeft") {
            openModal(imagesCopy[previous], imagesModal, false);
        }
    
        if(event.key === "ArrowRight") {
            openModal(imagesCopy[next], imagesModal, false);
        }
    };
    
    function closeModal() {
        modal.style.display = "none";
        document.body.classList.remove("modal-open");
    }
    

    
});

