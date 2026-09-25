"""
Copertina per l'articolo "01 - Introduzione al C" (Perche imparare il C nel 2026).

Render: lancia  ./render.sh  in questa cartella (usa il venv condiviso in
Articoli/_manim/venv e scrive copertina.png nell'articolo).

Mappa scena -> immagine (1200x630, formato cover blog):

    CCover  ->  copertina.png

In alternativa, a mano dalla root di Articoli:
    _manim/venv/bin/manim -s -r 1200,630 --format=png \\
        --media_dir _manim/render-cache -o copertina \\
        Informatica/C/01-introduzione-al-c/manim/cover_scene.py CCover
"""

from manim import *

# Palette del sito (favicon: gradiente indigo -> verde su sfondo slate-950)
BG = "#020617"
INDIGO = "#4f46e5"
GREEN = "#22c55e"
BLUE = "#2563eb"
SLATE_TEXT = "#e5e7eb"
SLATE_DIM = "#94a3b8"
CODE_BG = "#0b1220"
CODE_BORDER = "#1e293b"


class CCover(Scene):
    def construct(self):
        self.camera.background_color = BG

        # ---- sfondo: griglia sottile "circuito" ----
        grid = VGroup()
        for x in np.arange(-7, 7.5, 1.0):
            grid.add(Line([x, -4, 0], [x, 4, 0]))
        for y in np.arange(-4, 4.5, 1.0):
            grid.add(Line([-7, y, 0], [7, y, 0]))
        grid.set_stroke(color="#1e293b", width=0.6, opacity=0.35)
        self.add(grid)

        # ---- badge "C" a sinistra, stile logo del sito ----
        badge = Circle(radius=1.05, color=INDIGO, fill_opacity=1)
        badge.set_fill(color=[INDIGO, GREEN])
        badge_letter = Text("C", font="monospace", weight=BOLD, color=SLATE_TEXT).scale(1.6)
        badge_letter.move_to(badge.get_center())
        badge_group = VGroup(badge, badge_letter).move_to([-5.05, 0.55, 0])
        self.add(badge_group)

        # ---- titolo (due righe separate per un controllo preciso dello spacing) ----
        title_line1 = Text("Perché imparare il C", font="sans-serif", weight=BOLD, color=SLATE_TEXT)
        title_line2 = Text("nel 2026", font="sans-serif", weight=BOLD, color=SLATE_TEXT)
        title = VGroup(title_line1, title_line2).arrange(DOWN, aligned_edge=LEFT, buff=0.22).scale(0.72)
        title.next_to(badge_group, RIGHT, buff=0.55)
        title.align_to(badge_group, UP)
        title.shift(UP * 0.15)

        subtitle = Text(
            "puntatori, memoria e le fondamenta dell'informatica",
            font="sans-serif",
            color=SLATE_DIM,
        ).scale(0.38)
        subtitle.next_to(title, DOWN, buff=0.3, aligned_edge=LEFT)
        self.add(title, subtitle)

        # ---- pannello codice in basso, con puntatore che "indica" la variabile ----
        code_lines = VGroup(
            Text("int   valore = 42;", font="monospace", color=SLATE_TEXT),
            Text("int  *ptr    = &valore;", font="monospace", color=SLATE_TEXT),
        ).arrange(DOWN, aligned_edge=LEFT, buff=0.28).scale(0.42)

        panel = RoundedRectangle(
            corner_radius=0.18,
            width=code_lines.width + 1.0,
            height=code_lines.height + 0.9,
            fill_color=CODE_BG,
            fill_opacity=1,
            stroke_color=CODE_BORDER,
            stroke_width=2,
        )
        code_group = VGroup(panel, code_lines)
        code_lines.move_to(panel.get_center())
        code_group.move_to([-1.7, -1.85, 0])
        self.add(code_group)

        # evidenzia '&valore' e 'ptr' con lo stesso trucco cromatico del blog (accent blu)
        ptr_word = code_lines[1][5:8]
        addr_word = code_lines[1][14:]
        ptr_word.set_color(GREEN)
        addr_word.set_color(BLUE)

        # ---- box memoria a destra, con indirizzo esadecimale ----
        mem_box = RoundedRectangle(
            corner_radius=0.12, width=1.7, height=1.0,
            fill_color="#0f172a", fill_opacity=1,
            stroke_color=BLUE, stroke_width=2.5,
        ).move_to([4.7, -1.3, 0])
        mem_value = Text("42", font="monospace", weight=BOLD, color=SLATE_TEXT).scale(0.7)
        mem_value.move_to(mem_box.get_center() + UP * 0.12)
        mem_addr = Text("0x7ffee2a1", font="monospace", color=GREEN).scale(0.32)
        mem_addr.next_to(mem_value, DOWN, buff=0.15)
        mem_group = VGroup(mem_box, mem_value, mem_addr)
        self.add(mem_group)

        # freccia dal bordo del pannello codice alla cella di memoria: "il puntatore E l'indirizzo"
        arrow = CurvedArrow(
            start_point=panel.get_right() + RIGHT * 0.05 + UP * 0.05,
            end_point=mem_box.get_left() + LEFT * 0.05,
            angle=-0.6,
            color=BLUE,
            stroke_width=3,
        )
        self.add(arrow)

        # ---- footer ----
        footer = Text("giorgiodifusco.it — blog", font="sans-serif", color=SLATE_DIM).scale(0.3)
        footer.to_corner(DR, buff=0.35)
        self.add(footer)
