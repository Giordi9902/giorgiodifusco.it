// Mobile menu toggle for the public header (homepage, blog, login)

document.addEventListener("DOMContentLoaded", function () {
  const toggleBtn = document.getElementById("mobileMenuToggle");
  const menu = document.getElementById("mobileMenu");
  const iconOpen = document.getElementById("mobileMenuIconOpen");
  const iconClose = document.getElementById("mobileMenuIconClose");

  if (!toggleBtn || !menu) return;

  function closeMenu() {
    menu.classList.add("hidden");
    toggleBtn.setAttribute("aria-expanded", "false");
    if (iconOpen) iconOpen.classList.remove("hidden");
    if (iconClose) iconClose.classList.add("hidden");
  }

  function openMenu() {
    menu.classList.remove("hidden");
    toggleBtn.setAttribute("aria-expanded", "true");
    if (iconOpen) iconOpen.classList.add("hidden");
    if (iconClose) iconClose.classList.remove("hidden");
  }

  toggleBtn.addEventListener("click", function () {
    const isOpen = toggleBtn.getAttribute("aria-expanded") === "true";
    if (isOpen) {
      closeMenu();
    } else {
      openMenu();
    }
  });

  menu.querySelectorAll("a").forEach(function (link) {
    link.addEventListener("click", closeMenu);
  });

  window.addEventListener("resize", function () {
    if (window.innerWidth >= 640) closeMenu();
  });
});
