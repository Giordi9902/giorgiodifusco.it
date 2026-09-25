#!/usr/bin/env bash
#
# Render delle scene manim dell'articolo "01 - Introduzione al C".
# Usa il venv condiviso in Articoli/_manim/venv e scrive le immagini finali
# (1200x630, formato cover blog) nella cartella dell'articolo.
#
# Uso:  ./render.sh
#
set -euo pipefail

HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"        # .../<articolo>/manim
ART="$(dirname "$HERE")"                                    # .../<articolo>
MANIM_HOME="$(cd "$HERE/../../../../_manim" && pwd)"        # Articoli/_manim
MANIM="$MANIM_HOME/venv/bin/manim"
CACHE="$MANIM_HOME/render-cache"
SCRIPT="$HERE/cover_scene.py"
SCRIPTNAME="cover_scene"

# render <ClasseScena> <nome-output> <path-destinazione>
render () {
  "$MANIM" -s -r 1200,630 --format=png --media_dir "$CACHE" -o "$2" "$SCRIPT" "$1"
  cp "$CACHE/images/$SCRIPTNAME/$2.png" "$3"
  echo "  -> $3"
}

mkdir -p "$ART/media"

#        Scena     nome-output   destinazione
render   CCover    copertina     "$ART/copertina.png"

echo "OK: copertina dell'articolo 01 rigenerata."
