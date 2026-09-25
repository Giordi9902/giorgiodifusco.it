# _manim — Toolchain condivisa per le copertine e le figure

Questa cartella contiene il **tool manim** condiviso da tutti gli articoli:
non è contenuto editoriale, è lo strumento che genera le immagini.

```
_manim/
├── venv/             # virtualenv Python con manim (condiviso, NON versionato)
├── render-cache/     # cache/output intermedi di manim (rigenerabile, NON versionato)
├── requirements.txt  # dipendenze per ricreare il venv
└── README.md         # questo file
```

## Come funziona

Ogni articolo ha una cartella `manim/` con:

- uno o più script `*.py` (le **scene** manim che disegnano copertina e figure);
- uno script `render.sh` che renderizza quelle scene **usando questo venv condiviso**
  e copia le immagini finali nell'articolo (`copertina.png` e `media/*.png`).

Le immagini sono renderizzate a **1200x630 px** (formato copertina blog / Open Graph).

## Rigenerare un articolo

```bash
cd Informatica/C/02-la-compilazione/manim
./render.sh
```

## Ricreare il venv da zero

Se il venv manca o si rompe (è escluso da git):

```bash
cd _manim
python3 -m venv venv
venv/bin/pip install -r requirements.txt
```

manim richiede alcune librerie di sistema (cairo, pango, ffmpeg). Su Debian/Ubuntu:

```bash
sudo apt install build-essential python3-dev libcairo2-dev libpango1.0-dev ffmpeg
```

Versione attuale: **manim 0.21.0** su Python 3.12.
