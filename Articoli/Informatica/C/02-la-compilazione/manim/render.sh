#!/usr/bin/env bash
#
# Render delle scene manim dell'articolo "02 - La compilazione".
# Usa il venv condiviso in Articoli/_manim/venv e scrive le immagini finali
# (1200x630, formato cover/blog): copertina.png + le 4 fasi in media/.
#
# Uso:  ./render.sh
#
set -euo pipefail

HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"        # .../<articolo>/manim
ART="$(dirname "$HERE")"                                    # .../<articolo>
MANIM_HOME="$(cd "$HERE/../../../../_manim" && pwd)"        # Articoli/_manim
MANIM="$MANIM_HOME/venv/bin/manim"
CACHE="$MANIM_HOME/render-cache"
SCRIPT="$HERE/pipeline_scenes.py"
SCRIPTNAME="pipeline_scenes"

# render <ClasseScena> <nome-output> <path-destinazione>
render () {
  "$MANIM" -s -r 1200,630 --format=png --media_dir "$CACHE" -o "$2" "$SCRIPT" "$1"
  cp "$CACHE/images/$SCRIPTNAME/$2.png" "$3"
  echo "  -> $3"
}

mkdir -p "$ART/media"

#        Scena               nome-output             destinazione
render   CompilazioneCover   copertina_compilazione  "$ART/copertina.png"
render   PreprocessorPhase   fase1_preprocessore     "$ART/media/fase1_preprocessore.png"
render   CompilerPhase       fase2_compilazione      "$ART/media/fase2_compilazione.png"
render   AssemblerPhase      fase3_assemblaggio      "$ART/media/fase3_assemblaggio.png"
render   LinkerPhase         fase4_linker            "$ART/media/fase4_linker.png"

echo "OK: copertina + 4 fasi dell'articolo 02 rigenerate."
