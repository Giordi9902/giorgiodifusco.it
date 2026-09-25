// Sidebar toggle logic extracted from layout.php

document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.getElementById("app-sidebar");
  const overlay = document.getElementById("sidebarOverlay");
  const toggleBtn = document.getElementById("sidebarToggle");
  const sidebarTexts = document.querySelectorAll(".sidebar-text");

  if (!sidebar || !overlay || !toggleBtn) return;

  let isMini = false; // For desktop
  let isMobileOpen = false; // For mobile

  function toggleSidebar() {
    const isDesktop = window.innerWidth >= 768;

    if (isDesktop) {
      // Desktop: Toggle mini sidebar
      isMini = !isMini;
      if (isMini) {
        sidebar.classList.remove("w-64");
        sidebar.classList.add("w-20");
        sidebarTexts.forEach((el) => (el.style.opacity = "0"));
        setTimeout(() => {
          if (isMini) sidebarTexts.forEach((el) => el.classList.add("hidden"));
        }, 150);
      } else {
        sidebar.classList.remove("w-20");
        sidebar.classList.add("w-64");
        sidebarTexts.forEach((el) => el.classList.remove("hidden"));
        setTimeout(() => {
          if (!isMini) sidebarTexts.forEach((el) => (el.style.opacity = "1"));
        }, 10);
      }
    } else {
      // Mobile: Toggle off-canvas
      isMobileOpen = !isMobileOpen;
      if (isMobileOpen) {
        sidebar.classList.remove("-translate-x-full");
        overlay.classList.remove("hidden");
        setTimeout(() => overlay.classList.remove("opacity-0"), 10);
      } else {
        sidebar.classList.add("-translate-x-full");
        overlay.classList.add("opacity-0");
        setTimeout(() => overlay.classList.add("hidden"), 300);
      }
    }
  }

  toggleBtn.addEventListener("click", toggleSidebar);

  // Clicking on overlay closes sidebar on mobile
  overlay.addEventListener("click", () => {
    if (isMobileOpen) toggleSidebar();
  });

  // Handle resize properly
  window.addEventListener("resize", () => {
    const isDesktop = window.innerWidth >= 768;
    if (isDesktop && isMobileOpen) {
      // Reset mobile state when window becomes big
      isMobileOpen = false;
      sidebar.classList.remove("-translate-x-full");
      overlay.classList.add("hidden", "opacity-0");
    } else if (
      !isDesktop &&
      !isMobileOpen &&
      !sidebar.classList.contains("-translate-x-full")
    ) {
      // Ensure it's hidden on resize past breaking point downwards
      sidebar.classList.add("-translate-x-full");
      if (isMini) {
        // Reset mini state when going mobile
        isMini = false;
        sidebar.classList.remove("w-20");
        sidebar.classList.add("w-64");
        sidebarTexts.forEach((el) => {
          el.classList.remove("hidden");
          el.style.opacity = "1";
        });
      }
    }
  });
});
