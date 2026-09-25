document.addEventListener("DOMContentLoaded", function () {
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  initFadeIn(reduceMotion);
  initRotatingText(reduceMotion);
  document.querySelectorAll("[data-tabs], [data-stepper]").forEach(initTabs);
  document.querySelectorAll("[data-problem]").forEach(initStepper);
  initPostFilter();
});

/* Comparsa degli elementi .fade-in-up quando entrano nello schermo */
function initFadeIn(reduceMotion) {
  const elements = document.querySelectorAll(".fade-in-up");
  if (!elements.length || reduceMotion || !("IntersectionObserver" in window)) return;

  elements.forEach((el) => {
    const delay = el.getAttribute("data-delay") || 0;
    el.style.opacity = "0";
    el.style.transform = "translateY(16px)";
    el.style.transition = `opacity 600ms ease-out ${delay}ms, transform 600ms ease-out ${delay}ms`;
  });

  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = "1";
          entry.target.style.transform = "translateY(0)";
          obs.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.15 },
  );

  elements.forEach((el) => observer.observe(el));
}

/* Testo che alterna le frasi indicate in data-rotate (solo decorativo: il testo completo è in .sr-only) */
function initRotatingText(reduceMotion) {
  if (reduceMotion) return;

  document.querySelectorAll("[data-rotate]").forEach((el) => {
    let phrases;
    try {
      phrases = JSON.parse(el.getAttribute("data-rotate"));
    } catch (e) {
      return;
    }
    if (!Array.isArray(phrases) || phrases.length < 2) return;

    let index = 0;
    el.style.display = "inline-block";
    el.style.transition = "opacity 250ms ease, transform 250ms ease";

    setInterval(() => {
      el.style.opacity = "0";
      el.style.transform = "translateY(6px)";
      setTimeout(() => {
        index = (index + 1) % phrases.length;
        el.textContent = phrases[index];
        el.style.opacity = "1";
        el.style.transform = "translateY(0)";
      }, 250);
    }, 2800);
  });
}

/*
 * Schede accessibili (pattern ARIA "tabs"): clic, frecce, Home/End.
 * Senza JavaScript tutti i pannelli restano visibili uno sotto l'altro.
 */
function initTabs(container) {
  const tablist = container.querySelector('[role="tablist"]');
  if (!tablist) return;

  const tabs = Array.from(tablist.querySelectorAll('[role="tab"]'));
  const panels = tabs.map((tab) => document.getElementById(tab.getAttribute("aria-controls")));
  const vertical = tablist.getAttribute("aria-orientation") === "vertical";

  function select(index, focus) {
    tabs.forEach((tab, i) => {
      const active = i === index;
      const activeClasses = (tab.dataset.activeClass || "").split(" ").filter(Boolean);
      tab.setAttribute("aria-selected", active ? "true" : "false");
      tab.tabIndex = active ? 0 : -1;
      activeClasses.forEach((cls) => tab.classList.toggle(cls, active));
      if (panels[i]) panels[i].hidden = !active;
    });
    if (focus) tabs[index].focus();
  }

  tabs.forEach((tab, i) => {
    tab.addEventListener("click", () => select(i, false));
    tab.addEventListener("keydown", (event) => {
      const next = vertical ? ["ArrowDown", "ArrowRight"] : ["ArrowRight"];
      const prev = vertical ? ["ArrowUp", "ArrowLeft"] : ["ArrowLeft"];
      let target = null;

      if (next.includes(event.key)) target = (i + 1) % tabs.length;
      else if (prev.includes(event.key)) target = (i - 1 + tabs.length) % tabs.length;
      else if (event.key === "Home") target = 0;
      else if (event.key === "End") target = tabs.length - 1;

      if (target !== null) {
        event.preventDefault();
        select(target, true);
      }
    });
  });

  const initial = tabs.findIndex((tab) => tab.getAttribute("aria-selected") === "true");
  select(initial >= 0 ? initial : 0, false);
}

/* Soluzione rivelata un passaggio alla volta */
function initStepper(problem) {
  const steps = Array.from(problem.querySelectorAll("[data-step]"));
  const controls = problem.querySelector("[data-step-controls]");
  const nextButton = problem.querySelector("[data-step-next]");
  const resetButton = problem.querySelector("[data-step-reset]");
  const counter = problem.querySelector("[data-step-counter]");
  if (!steps.length || !controls || !nextButton || !resetButton) return;

  let revealed = 0;

  function render() {
    steps.forEach((step, i) => {
      step.hidden = i >= revealed;
    });
    const done = revealed >= steps.length;
    nextButton.disabled = done;
    nextButton.textContent =
      revealed === 0 ? "Mostra il primo passaggio" : done ? "Soluzione completa" : "Mostra il passaggio successivo";
    resetButton.hidden = revealed === 0;
    if (counter) counter.textContent = `${revealed} / ${steps.length} passaggi`;
  }

  nextButton.addEventListener("click", () => {
    if (revealed < steps.length) {
      revealed++;
      render();
    }
  });

  resetButton.addEventListener("click", () => {
    revealed = 0;
    render();
    nextButton.focus();
  });

  controls.hidden = false;
  render();
}

/* Filtro degli ultimi articoli per area */
function initPostFilter() {
  const filter = document.querySelector("[data-post-filter]");
  const grid = document.querySelector("[data-post-grid]");
  if (!filter || !grid) return;

  const buttons = Array.from(filter.querySelectorAll("[data-filter]"));
  const posts = Array.from(grid.querySelectorAll("[data-area]"));
  const activeClasses = ["bg-blue-600", "border-blue-600", "text-white"];
  const idleClasses = ["bg-white", "border-gray-200", "text-gray-600", "hover:border-blue-300", "hover:text-blue-700"];

  function apply(area) {
    buttons.forEach((button) => {
      const active = button.dataset.filter === area;
      button.setAttribute("aria-pressed", active ? "true" : "false");
      activeClasses.forEach((cls) => button.classList.toggle(cls, active));
      idleClasses.forEach((cls) => button.classList.toggle(cls, !active));
    });
    posts.forEach((post) => {
      post.hidden = area !== "" && post.dataset.area !== area;
    });
  }

  buttons.forEach((button) => button.addEventListener("click", () => apply(button.dataset.filter)));

  filter.hidden = false;
  apply("");
}
