// Global modal utilities for admin/student area
// Requires Tailwind CSS and BASE_URL / CSRF_TOKEN globals defined in layout.php

(function () {
  let modalRoot = null;
  let modalOverlay = null;
  let modalTitle = null;
  let modalMessage = null;
  let modalInputWrapper = null;
  let modalInput = null;
  let modalPrimaryBtn = null;
  let modalSecondaryBtn = null;
  let currentResolve = null;
  let currentMode = "alert"; // 'alert' | 'confirm' | 'prompt'

  function ensureModal() {
    if (modalRoot) return;

    modalOverlay = document.createElement("div");
    modalOverlay.id = "app-modal-overlay";
    modalOverlay.className =
      "fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden items-center justify-center p-4";

    const card = document.createElement("div");
    card.className =
      "bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-sm shadow-2xl p-5 m-auto";

    modalTitle = document.createElement("h3");
    modalTitle.className = "text-lg font-semibold text-slate-50 mb-3";
    modalTitle.textContent = "Messaggio";

    modalMessage = document.createElement("p");
    modalMessage.id = "app-modal-message";
    modalMessage.className = "text-sm text-slate-300 mb-6";

    modalInputWrapper = document.createElement("div");
    modalInputWrapper.className = "mb-4 hidden";

    modalInput = document.createElement("input");
    modalInput.type = "text";
    modalInput.className =
      "mt-1 block w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none";

    modalInputWrapper.appendChild(modalInput);

    const buttons = document.createElement("div");
    buttons.className = "flex justify-end gap-3";

    modalSecondaryBtn = document.createElement("button");
    modalSecondaryBtn.type = "button";
    modalSecondaryBtn.className =
      "px-4 py-2 rounded-lg border border-slate-700 text-slate-300 hover:bg-slate-800 transition hidden";
    modalSecondaryBtn.textContent = "Annulla";

    modalPrimaryBtn = document.createElement("button");
    modalPrimaryBtn.type = "button";
    modalPrimaryBtn.className =
      "px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500 transition";
    modalPrimaryBtn.textContent = "OK";

    buttons.appendChild(modalSecondaryBtn);
    buttons.appendChild(modalPrimaryBtn);

    card.appendChild(modalTitle);
    card.appendChild(modalMessage);
    card.appendChild(modalInputWrapper);
    card.appendChild(buttons);

    modalOverlay.appendChild(card);
    document.body.appendChild(modalOverlay);

    modalRoot = modalOverlay;

    modalOverlay.addEventListener("click", (e) => {
      if (e.target === modalOverlay && currentMode === "alert") {
        closeModal(false);
      }
    });

    document.addEventListener("keydown", (e) => {
      if (!modalRoot || modalRoot.classList.contains("hidden")) return;
      if (e.key === "Escape") {
        closeModal(false);
      }
    });

    modalSecondaryBtn.addEventListener("click", () => closeModal(false));
    modalPrimaryBtn.addEventListener("click", () => closeModal(true));
  }

  function openModal({
    mode = "alert",
    title = "Messaggio",
    message = "",
    primaryText = "OK",
    secondaryText = "Annulla",
    variant = "default", // 'default' | 'success' | 'error' | 'danger'
    inputPlaceholder = "",
    inputInitialValue = "",
  }) {
    ensureModal();

    currentMode = mode;
    modalTitle.textContent = title;
    modalMessage.textContent = message;

    if (modalInputWrapper && modalInput) {
      if (mode === "prompt") {
        modalInputWrapper.classList.remove("hidden");
        modalInput.placeholder = inputPlaceholder || "";
        modalInput.value = inputInitialValue || "";
        setTimeout(() => {
          try {
            modalInput.focus();
            modalInput.select();
          } catch (e) {}
        }, 0);
      } else {
        modalInputWrapper.classList.add("hidden");
        modalInput.value = "";
        modalInput.placeholder = "";
      }
    }

    // Reset classes for primary button based on variant
    let basePrimary = "px-4 py-2 rounded-lg text-white transition";
    let variantClass = "bg-indigo-600 hover:bg-indigo-500";
    if (variant === "success") {
      variantClass = "bg-emerald-600 hover:bg-emerald-500";
    } else if (variant === "error" || variant === "danger") {
      variantClass = "bg-red-600 hover:bg-red-500";
    }
    modalPrimaryBtn.className = basePrimary + " " + variantClass;

    modalPrimaryBtn.textContent = primaryText;

    if (mode === "confirm" || mode === "prompt") {
      modalSecondaryBtn.textContent = secondaryText;
      modalSecondaryBtn.classList.remove("hidden");
    } else {
      modalSecondaryBtn.classList.add("hidden");
    }

    modalOverlay.classList.remove("hidden");
    // Force flex layout when visible
    modalOverlay.classList.add("flex");
  }

  function closeModal(confirmed) {
    if (!modalOverlay) return;
    modalOverlay.classList.add("hidden");
    modalOverlay.classList.remove("flex");

    const resolve = currentResolve;
    const mode = currentMode;
    currentResolve = null;
    if (!resolve) return;

    if (mode === "confirm") {
      resolve(!!confirmed);
      return;
    }

    if (mode === "prompt") {
      if (!confirmed) {
        resolve(null);
      } else {
        const value = modalInput ? modalInput.value : "";
        resolve(value);
      }
      return;
    }

    // alert
    resolve(true);
  }

  window.showAppAlert = function (message, options) {
    options = options || {};
    return new Promise((resolve) => {
      currentResolve = resolve;
      openModal({
        mode: "alert",
        title: options.title || "Messaggio",
        message: message || "",
        primaryText: options.primaryText || "OK",
        variant: options.variant || "default",
      });
    });
  };

  window.showAppConfirm = function (message, options) {
    options = options || {};
    return new Promise((resolve) => {
      currentResolve = resolve;
      openModal({
        mode: "confirm",
        title: options.title || "Conferma azione",
        message: message || "",
        primaryText: options.primaryText || "Conferma",
        secondaryText: options.secondaryText || "Annulla",
        variant: options.variant || "danger",
      });
    });
  };

  window.showAppPrompt = function (message, options) {
    options = options || {};
    return new Promise((resolve) => {
      currentResolve = resolve;
      openModal({
        mode: "prompt",
        title: options.title || "Inserisci testo",
        message: message || "",
        primaryText: options.primaryText || "Salva",
        secondaryText: options.secondaryText || "Annulla",
        variant: options.variant || "default",
        inputPlaceholder: options.placeholder || "",
        inputInitialValue: options.initialValue || "",
      });
    });
  };
})();
