document.addEventListener("DOMContentLoaded", function () {
    fetch("header.html")
      .then(response => response.text())
      .then(data => {
        document.getElementById("header-container").innerHTML = data;
        const currentPage = window.location.pathname.split("/").pop();
        document.querySelectorAll(".nav-link").forEach(link => {
          if (link.getAttribute("href") === currentPage) {
            link.classList.add("active");
          }
        });
      });
  });
  