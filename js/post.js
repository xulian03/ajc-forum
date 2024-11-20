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
    
    
    function preventDefaults(event) {
        event.preventDefault()
        event.stopPropagation()
    }

    function isImage(file) {
      return file && file['type'].split('/')[0] === 'image';
    }

    const dropField = document.querySelector(".thread-content .thread-reply .drop-image");
    const fileUpload = document.getElementById("fileUpload");
    const imageContainer = document.querySelector(".thread-content .thread-reply .drop-image .container .images");
    const dropFieldErrorMessage = document.querySelector(".thread-content .thread-reply .drop-image-field .error");
    const form = document.querySelector(".thread-content .thread-reply form");

    var images = [];
    var done = false;
    const MAX_IMAGES = 10;

    ;['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropField.addEventListener(eventName, preventDefaults, false);
    });

    ;['dragenter', 'dragover'].forEach(eventName => {
        dropField.addEventListener(eventName, () => dropField.classList.add('selected'), false);
    })

    ;['dragleave', 'drop'].forEach(eventName => {
        dropField.addEventListener(eventName, () => dropField.classList.remove('selected'), false);
    })

    fileUpload.addEventListener('change', function () {
      handleFiles(this.files);
    });

    dropField.addEventListener('click', (event) => {
      if(dropField.classList.contains('uploaded') && !event.target.closest('.image-item')) {
        fileUpload.click();
      }
    });

    dropField.addEventListener('drop', function(event) {
        var dataTransfer = event.dataTransfer;
        var files = dataTransfer.files;
        handleFiles(files)
    }, false);

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        if(done == true) {
          return;
        }
        done = true;
        var formData = new FormData(form);
        images.forEach((image, index) => {
          formData.append('files[]', image);
        });

        $.ajax({
          type: 'POST',
          url: 'reply-post.php',
          async: false,
          data: formData,
          processData: false,
          contentType: false,
          success: function(data) {
            if(data === 'error') {
              dropFieldErrorMessage.innerHTML = "Ha ocurrido un error.";
              dropField.classList.add('error');
              return;
            }
            if(data.redirect) {
              window.location.href = data.redirect;
              console.log('redirect');
            }
            console.log(data);
          },
          error: function(jqXHR, textStatus, errorThrown) {
              console.log('Error al subir el hilo: ' + textStatus);
          }
        });
    });

    function handleFiles(files) {
        dropField.classList.remove('error');
                        

        files = [...files];
        var notImage = false;

        files.forEach(file => {
          if(!isImage(file)) {
            dropField.classList.add('error');
            dropFieldErrorMessage.innerHTML = "Solo puedes subir imagenes o gifs.";
            notImage = true;
          }
        });

        if(notImage) {
          return;
        }
        
        if (images.length + files.length > MAX_IMAGES) {
              dropField.classList.add('error');
              dropFieldErrorMessage.innerHTML = "No puedes subir más de " + MAX_IMAGES + " imagenes.";
              return;
        }
        
        images = images.concat(files);
        renderImages();
    }

    function deleteImage(i) {
      images.splice(i, 1);
      if(images.length == 0)  {
        dropField.classList.remove('uploaded');
        return;
      }
      renderImages();
    }

    async function renderImages() {
      dropField.classList.add('uploaded');
      imageContainer.innerHTML = '';

      var imagesCopy = [...images];

      let imagesURL = [];

      for (let i = 0; i < imagesCopy.length; i++) {
        let image = imagesCopy[i];
        const imageUrl = await new Promise((resolve, reject) => { 
            let reader = new FileReader();
            reader.readAsDataURL(image);
            reader.onloadend = () => {
                resolve(reader.result);
            };
            reader.onerror = reject; 
        });

        imagesURL.push(imageUrl);
      }

      for(let i = 0; i < imagesURL.length; i++) {
        let url = imagesURL[i];
        let div = document.createElement('div');
        let img = document.createElement('img');
        div.className = 'image-item';
        div.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 640.000000 640.000000" preserveAspectRatio="xMidYMid meet" focusable="false">
                <g transform="translate(0.000000,640.000000) scale(0.100000,-0.100000)" stroke="none">
                  <path d="M2303 5916 c-23 -8 -57 -23 -76 -35 -77 -47 -108 -98 -337 -558 l-224 -452 -336 -3 -335 -3 -56 -26 c-266 -125 -272 -484 -9 -608 50 -23 69 -26 198 -29 l142 -4 0 -1472 c0 -985 3 -1492 11 -1532 65 -368 345 -648 713 -713 82 -15 2330 -15 2412 0 368 65 648 345 713 713 8 40 11 547 11 1532 l0 1472 143 4 c128 3 147 6 197 29 263 124 257 483 -9 608 l-56 26 -335 3 -336 3 -229 460 c-204 408 -235 466 -278 505 -26 25 -69 55 -95 67 l-47 22 -870 2 c-696 1 -878 -1 -912 -11z m1590 -841 c53 -107 97 -197 97 -200 0 -3 -355 -5 -790 -5 -434 0 -790 2 -790 5 0 3 44 93 97 200 l98 195 595 0 595 0 98 -195z m577 -2311 c0 -1014 -3 -1448 -11 -1479 -16 -65 -69 -121 -131 -140 -78 -23 -2178 -23 -2256 0 -62 19 -115 75 -131 140 -8 31 -11 465 -11 1479 l0 1436 1270 0 1270 0 0 -1436z"></path>
                  <path d="M2575 3787 c-91 -30 -168 -95 -205 -172 -39 -81 -41 -127 -38 -987 l3 -833 26 -56 c125 -266 483 -272 608 -9 l26 55 0 880 0 880 -26 55 c-37 79 -81 125 -155 161 -74 37 -173 47 -239 26z"></path>
                  <path d="M3639 3785 c-69 -22 -140 -74 -177 -129 -65 -97 -63 -63 -60 -1012 l3 -859 26 -55 c125 -263 483 -257 608 9 l26 56 3 833 c2 543 -1 853 -8 890 -29 159 -152 270 -309 278 -40 2 -84 -2 -112 -11z"></path>
                </g>
              </svg>`;
        div.querySelector('svg').onclick = function () {
          deleteImage(i);
        };
        img.src = url;
        img.onclick = function() {
            openModal(this.src, imagesURL, true);
          }
        div.appendChild(img);
        imageContainer.appendChild(div);
      }
      
    }


    const content = document.querySelector(".thread-content .thread-reply .post-text .content textarea");
    const charactersContent = document.querySelector(".thread-content .thread-reply .post-text .content-field .characters");
    const submit = document.querySelector(".thread-content .thread-reply .post-text .buttons");

  
    content.addEventListener('input', contentInput);

    window.addEventListener("resize", contentInput);
    
    function contentInput() {
        if(content.value.trim() === "") {
            charactersContent.innerHTML = "0/1000";
            content.style.height = "128px";
            submit.classList.remove('done');
            return;
        }
        content.style.height = "128px";
        var height = multiple(content.scrollHeight, 16);
        content.style.height = height + "px";
        charactersContent.innerHTML = content.value.length + "/1000";
        if(content.value != "" && content.value != null) {
            submit.classList.add('done');
        }
    }
    
    content.addEventListener("focus", function (event) {
        focused = true;
        if(!content.parentElement.classList.contains('focused')) {
            content.parentElement.classList.add('focused');
        }
    });
      
    content.addEventListener("blur", function (event) {
        focused = false;
        if(content.value == "" || content.value == null) {
            content.parentElement.classList.remove('focused');
        }
    });

    function multiple(num, mul) {
        const ncertain = Math.round(num / mul);
        return ncertain * mul;
    }


    
});

