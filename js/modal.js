const modal = document.getElementById("modal");
const modalImage = document.getElementById("modal-image");
const roll = document.querySelector('.modal .roll');

var close = document.querySelector(".close");
var imagesModal = [];

function openModal(src, images, reset) {
    modal.style.display = "block";
    document.body.classList.add("modal-open");
    modalImage.src = src;
    modalImage.ref = src;

    if(reset) {
        this.imagesModal = images;

        roll.innerHTML = "";

        var imagesCopy = [...images];


        for(let i = 0; i < imagesCopy.length; i++) {
            let url = imagesCopy[i];

            var img = document.createElement('img');
            img.src = url;
            img.ref = url;
            if(img.ref == src) {
                img.classList.add('selected');
            } else {
                img.onclick = () => {
                    openModal(url, [...images], false);
                };
            }
            
            roll.appendChild(img);

        }
        return;
    }

    for(const img of Array.from(roll.children)) {
        if(img.ref == src) {
            img.classList.add('selected');
        } else {
            img.classList.remove('selected');
            img.onclick = () => {
                openModal(img.ref, [...images], false);
            };
        }
    }
    
}
