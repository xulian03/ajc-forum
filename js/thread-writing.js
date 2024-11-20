const title = document.querySelector(".create-thread-content .form-container .title textarea");
const charactersTitle = document.querySelector(".create-thread-content .form-container .title-field .characters");
const content = document.querySelector(".create-thread-content .form-container .content textarea");
const charactersContent = document.querySelector(".create-thread-content .form-container .content-field .characters");
const survey = document.querySelector(".create-thread-content .form-container .survey");
const textContainer = document.querySelector(".sidebar .threads-list .creating .content .text-container a");
const titleSurvey = document.querySelector(".create-thread-content .form-container .survey .title");
const optionSurvey = document.querySelector(".create-thread-content .form-container .survey .option");
const optionsList = document.querySelectorAll(".create-thread-content .form-container .survey .option input");
const submit = document.querySelector(".create-thread-content .form-container .submit");

var close = document.querySelector(".close");

let focused = false;
const MAX_OPTIONS = 12;

title.addEventListener('input', titleInput);

window.addEventListener("resize", titleInput);

function titleInput() {
    if(title.value.trim() === "") {
        textContainer.innerHTML = '...';
        charactersTitle.innerHTML = "0/200";
        title.style.height = "32px";
        submit.classList.remove('done');
        return;
    }
    title.style.height = "32px";
    var height = multiple(title.scrollHeight, 16);
    title.style.height = height + "px";
    if(textContainer != null) {
        textContainer.innerHTML = title.value;
    }
    charactersTitle.innerHTML = title.value.length + "/200";
    if(title.value != "" && title.value != null && content.value != "" && content.value != null) {
        submit.classList.add('done');
    }
}

document.onkeydown = function(event) {
    if (event.key === "Enter") {
        if (focused && document.activeElement === title) {
            event.preventDefault();
        }

    }
}

title.addEventListener("focus", function (event) {
    focused = true;
    if(!title.parentElement.classList.contains('focused')) {
        title.parentElement.classList.add('focused');
    }
});
  
title.addEventListener("blur", function (event) {
    focused = false;
    if(title.value == "" || title.value == null) {
        title.parentElement.classList.remove('focused');
    }
});

if(survey != null) {
    survey.addEventListener("toggle", function (event) {
        if(survey.hasAttribute('open')) {
            survey.querySelector('summary svg').innerHTML = `<g transform="translate(0.000000,1280.000000) scale(0.100000,-0.100000)" stroke="none">
            <path d="M2220 12793 c-596 -44 -1093 -277 -1522 -710 -394 -398 -600 -815 -680 -1378 -19 -134 -19 -8474 0 -8620 67 -510 277 -955 619 -1307 427 -441 846 -659 1448 -755 104 -17 356 -18 4330 -18 3978 0 4227 1 4334 18 444 69 797 222 1136 493 108 86 331 310 412 414 268 341 424 712 484 1155 20 143 21 8482 1 8620 -81 570 -295 997 -699 1397 -191 189 -325 291 -531 408 -234 131 -482 215 -797 267 -92 16 -420 17 -4295 18 -2307 1 -4215 0 -4240 -2z m8370 -1615 c105 -32 170 -59 222 -95 179 -122 302 -279 362 -463 l21 -65 3 -4103 c2 -3426 0 -4119 -12 -4195 -23 -157 -84 -281 -201 -406 -124 -132 -284 -217 -454 -240 -117 -15 -8124 -16 -8237 0 -276 38 -516 211 -629 452 -70 150 -65 -212 -65 4301 0 2842 3 4098 11 4152 13 93 54 212 100 290 42 72 163 196 247 253 104 70 219 113 352 131 30 4 1893 7 4140 6 3791 -1 4089 -2 4140 -18z"/>
            <path d="M3807 7169 c-271 -66 -518 -297 -582 -544 -38 -143 -28 -352 24 -502 73 -212 247 -397 450 -479 126 -51 12 -49 2686 -49 2706 0 2565 -3 2705 52 150 59 306 184 394 315 83 125 122 280 113 448 -10 177 -49 303 -133 431 -108 163 -249 263 -459 326 -57 17 -194 18 -2590 20 -2469 2 -2532 1 -2608 -18z"/>
        </g>`;
        return;
        }
        survey.querySelector('summary svg').innerHTML = `<g transform="translate(0.000000,1280.000000) scale(0.100000,-0.100000)" stroke="none">
            <path d="M2220 12793 c-596 -44 -1093 -277 -1522 -710 -394 -398 -600 -815 -680 -1378 -19 -134 -19 -8474 0 -8620 67 -510 277 -955 619 -1307 427 -441 846 -659 1448 -755 104 -17 356 -18 4330 -18 3978 0 4227 1 4334 18 444 69 797 222 1136 493 108 86 331 310 412 414 268 341 424 712 484 1155 20 143 21 8482 1 8620 -81 570 -295 997 -699 1397 -191 189 -325 291 -531 408 -234 131 -482 215 -797 267 -92 16 -420 17 -4295 18 -2307 1 -4215 0 -4240 -2z m8370 -1615 c105 -32 170 -59 222 -95 179 -122 302 -279 362 -463 l21 -65 3 -4103 c2 -3426 0 -4119 -12 -4195 -23 -157 -84 -281 -201 -406 -124 -132 -284 -217 -454 -240 -117 -15 -8124 -16 -8237 0 -276 38 -516 211 -629 452 -70 150 -65 -212 -65 4301 0 2842 3 4098 11 4152 13 93 54 212 100 290 42 72 163 196 247 253 104 70 219 113 352 131 30 4 1893 7 4140 6 3791 -1 4089 -2 4140 -18z"></path>
            <path d="M6210 9580 c-151 -41 -254 -104 -371 -226 -79 -83 -119 -141 -165 -237 -65 -137 -64 -121 -64 -1069 l0 -858 -857 0 c-824 0 -861 -1 -942 -20 -273 -66 -521 -296 -586 -545 -38 -143 -28 -352 24 -502 73 -212 247 -397 450 -479 116 -47 113 -47 1034 -51 l877 -4 0 -853 c0 -969 -3 -924 84 -1101 150 -303 462 -468 806 -426 55 7 125 21 155 31 260 89 467 319 531 590 16 68 18 153 21 917 l4 842 857 4 c928 4 900 2 1035 59 144 60 296 185 381 310 132 197 152 497 51 747 -75 183 -238 348 -415 418 -151 60 -98 57 -1042 60 l-867 4 -4 862 c-4 955 -1 910 -71 1062 -42 90 -95 162 -177 241 -165 157 -338 230 -563 240 -91 3 -125 1 -186 -16z"></path>
        </g>`;
        
    });
}

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
    if(title.value != "" && title.value != null && content.value != "" && content.value != null) {
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

if(survey != null) {
    titleSurvey.querySelector('input').addEventListener("focus", function (event) {
        focused = true;
        if(!titleSurvey.classList.contains('focused')) {
            titleSurvey.classList.add('focused');
        }
    });
    
    titleSurvey.querySelector('input').addEventListener("blur", function (event) {
        focused = false;
        const text = titleSurvey.querySelector('input');
        if(text.value == "" || text.value == null) {
            titleSurvey.classList.remove('focused');
        }
    });

    optionsList.forEach((option) => {
        var name = option.getAttribute('name');
        if (!name.includes('-')) {
            return;
        }
        if (Number.isNaN(name.split('-')[1])) {
            return;
        }
        var i = Number.parseInt(name.split('-')[1]);
        if (i == optionsList.length && i < MAX_OPTIONS) {
            option.addEventListener('input', inputHandler);
        }
    });
}

function inputHandler(event) {
    var option = event.target;
    var name = option.getAttribute('name');
    var i = Number.parseInt(name.split('-')[1]);
    if (option.value != "" && option.value != null) {
        if (i < MAX_OPTIONS) {
            inputOption(option, i);
        }
    }
}

function inputOption(lastOption, i) {
    var option = document.createElement('input');
    option.setAttribute('type', 'text');
    option.setAttribute('name', 'option-' + (i + 1));
    option.setAttribute('maxlength', '50');
    option.setAttribute('placeholder', 'Opción ' + (i + 1));
    
    option.addEventListener('input', inputHandler);
    optionSurvey.appendChild(option);
    lastOption.removeEventListener('input', inputHandler);
}

function multiple(num, mul) {
    const ncertain = Math.round(num / mul);
    return ncertain * mul;
}

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(function() {
        if(title.value != "" && title.value != null) {
            title.parentElement.classList.add('focused');
        }

        if(content.value != "" && content.value != null) {
            content.parentElement.classList.add('focused');
        }
        if(survey != null) {
            let text = titleSurvey.querySelector('input');
            if(text.value != "" && text.value != null) {
                titleSurvey.classList.add('focused');
            }
        }
        
        if(title.value != "" && title.value != null && content.value != "" && content.value != null) {
            submit.classList.add('done');
        }
    }, 100);
});

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

function readImg(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onloadend = function () {
          resolve(reader.result);
        };
        reader.onerror = reject;
        reader.readAsDataURL(file);
      });
}