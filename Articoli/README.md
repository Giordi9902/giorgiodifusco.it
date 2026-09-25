# Articoli

Sorgenti degli articoli del blog, organizzati per **macro area → topic → articolo**.
Per ogni articolo sono raccolti insieme: il testo, la copertina, le immagini/video e
il "tool manim" che li genera.

## Struttura

```
Articoli/
├── _manim/                          # toolchain manim CONDIVISA (venv + cache) — vedi _manim/README.md
│
└── <Macro area>/                    # es. Informatica  (in futuro: Matematica, ...)
    └── <Topic>/                     # es. C            (in futuro: Python, Algoritmi, ...)
        └── NN-slug-articolo/        # un articolo, numerato per ordine di pubblicazione
            ├── articolo.md          # il testo dell'articolo (Markdown)
            ├── copertina.png        # la copertina (1200x630, Open Graph)
            ├── media/               # immagini e video usati nell'articolo
            │   └── *.png
            └── manim/               # il "tool manim" dell'articolo
                ├── *.py             # le scene manim (copertina + figure)
                └── render.sh        # rigenera copertina.png e media/ con il venv condiviso
```

## Contenuto attuale

- **Informatica / C**
  - `01-introduzione-al-c` — Perché imparare il C nel 2026
  - `02-la-compilazione` — La pipeline di compilazione (preprocessore → linker)
  - `03-numeri-e-operatori` — Tipi interi, overflow, operatori bitwise
  - `04-puntatori` — Indirizzi, & e *, passaggio per indirizzo, aritmetica dei puntatori

## Convenzioni

- **Macro area**: cartella in `PascalCase` (es. `Informatica`).
- **Topic**: sotto-cartella della macro area (es. `C`).
- **Articolo**: `NN-slug-in-kebab-case`, con `NN` = ordine a due cifre (`01`, `02`, …).
- Ogni articolo è **auto-contenuto**: testo, copertina, media e scene manim stanno insieme.
- La copertina si chiama sempre `copertina.png`; le altre immagini stanno in `media/`.
- Il `venv` di manim e la cache di render vivono **una sola volta** in `_manim/` e sono
  condivisi da tutti gli articoli (non duplicati, non versionati).

## Rigenerare le immagini di un articolo

```bash
cd Informatica/C/03-numeri-e-operatori/manim
./render.sh
```

Lo script usa il venv condiviso in `_manim/venv` e riscrive `copertina.png` + `media/`.
Dettagli sul tool in [`_manim/README.md`](_manim/README.md).

## Aggiungere un nuovo articolo

1. Crea `<Macro area>/<Topic>/NN-slug/` con dentro `articolo.md`, `media/`, `manim/`.
2. Metti in `manim/` la scena `.py` e copia un `render.sh` esistente adattando le righe
   `render <Scena> <nome-output> <destinazione>`.
3. Lancia `./render.sh` per generare `copertina.png` e le immagini in `media/`.
