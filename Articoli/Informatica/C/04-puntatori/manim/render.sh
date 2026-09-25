#!/usr/bin/env bash
#
# Render delle scene manim dell'articolo "04 - Puntatori".
# Usa il venv condiviso in Articoli/_manim/venv e scrive le immagini finali
# (1200x630, formato cover/blog): copertina.png + le 4 figure in media/.
#
# Uso:  ./render.sh
#
set -euo pipefail

HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"        # .../<articolo>/manim
ART="$(dirname "$HERE")"                                    # .../<articolo>
MANIM_HOME="$(cd "$HERE/../../../../_manim" && pwd)"        # Articoli/_manim
MANIM="$MANIM_HOME/venv/bin/manim"
CACHE="$MANIM_HOME/render-cache"
SCRIPT="$HERE/pointers.py"
SCRIPTNAME="pointers"

# render <ClasseScena> <nome-output> <path-destinazione>
render () {
  "$MANIM" -s -r 1200,630 --format=png --media_dir "$CACHE" -o "$2" "$SCRIPT" "$1"
  cp "$CACHE/images/$SCRIPTNAME/$2.png" "$3"
  echo "  -> $3"
}

mkdir -p "$ART/media"

#        Scena                 nome-output              destinazione
render   PuntatoriCover        copertina_puntatori      "$ART/copertina.png"
render   MemoriaIndirizzi      memoria_indirizzi        "$ART/media/memoria_indirizzi.png"
render   SwapScene             swap                     "$ART/media/swap.png"
render   AritmeticaPuntatori   aritmetica_puntatori     "$ART/media/aritmetica_puntatori.png"
render   MyStrlen              my_strlen                "$ART/media/my_strlen.png"

echo "OK: copertina + 4 figure dell'articolo 04 rigenerate."
