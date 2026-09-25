#!/usr/bin/env bash
#
# Render delle scene manim dell'articolo "03 - Numeri e operatori".
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
SCRIPT="$HERE/bitwise_scenes.py"
SCRIPTNAME="bitwise_scenes"

# render <ClasseScena> <nome-output> <path-destinazione>
render () {
  "$MANIM" -s -r 1200,630 --format=png --media_dir "$CACHE" -o "$2" "$SCRIPT" "$1"
  cp "$CACHE/images/$SCRIPTNAME/$2.png" "$3"
  echo "  -> $3"
}

mkdir -p "$ART/media"

#        Scena              nome-output         destinazione
render   NumeriCover        copertina_numeri    "$ART/copertina.png"
render   TipiInteri         tipi_interi         "$ART/media/tipi_interi.png"
render   OverflowScene      overflow            "$ART/media/overflow.png"
render   BitwiseOperators   operatori_bitwise   "$ART/media/operatori_bitwise.png"
render   DecToBin           dec_to_bin          "$ART/media/dec_to_bin.png"

echo "OK: copertina + 4 figure dell'articolo 03 rigenerate."
