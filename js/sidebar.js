const sidebar = document.querySelector(".sidebar");
const options = document.querySelector(".options");
const textContainerSidebar = document.querySelectorAll(".text-container");

options.addEventListener("click", () => {
  sidebar.classList.toggle("show");
});

document.querySelector("*").addEventListener("click", (event) => {
  if (
    sidebar.classList.contains("show") &&
    options.lastElementChild != event.target
  ) {
    sidebar.classList.toggle("show");
  }
});

window.addEventListener("scroll", (event) => {
  if (
    sidebar.classList.contains("show") &&
    options.lastElementChild != event.target
  ) {
    sidebar.classList.toggle("show");
  }
});

window.addEventListener("resize", function () {
  sidebar.classList.remove("show");
});

textContainerSidebar.forEach(function (item) {
  var textContent = item.firstElementChild.textContent.trim();
  item.setAttribute("title", textContent);
});
