"""
Copertina + 4 immagini di fase per l'articolo "02 - La compilazione"
(la pipeline di compilazione: preprocessore, compilazione, assemblaggio, linker).

Render: lancia  ./render.sh  in questa cartella (usa il venv condiviso in
Articoli/_manim/venv e scrive copertina.png + le fasi in media/).

Mappa scena -> immagine (1200x630, formato cover/blog):

    CompilazioneCover  ->  copertina.png            (copertina)
    PreprocessorPhase  ->  media/fase1_preprocessore.png
    CompilerPhase      ->  media/fase2_compilazione.png
    AssemblerPhase     ->  media/fase3_assemblaggio.png
    LinkerPhase        ->  media/fase4_linker.png
"""

from manim import *

# Palette del sito (favicon: gradiente indigo -> verde su sfondo slate-950)
BG = "#020617"
INDIGO = "#4f46e5"
GREEN = "#22c55e"
BLUE = "#2563eb"
AMBER = "#f59e0b"
RED = "#f43f5e"
SLATE_TEXT = "#e5e7eb"
SLATE_DIM = "#94a3b8"
CODE_BG = "#0b1220"
CODE_BORDER = "#1e293b"


# ---------------------------------------------------------------------------
# Helper condivisi
# ---------------------------------------------------------------------------

def make_grid():
    grid = VGroup()
    for x in np.arange(-7, 7.5, 1.0):
        grid.add(Line([x, -4, 0], [x, 4, 0]))
    for y in np.arange(-4, 4.5, 1.0):
        grid.add(Line([-7, y, 0], [7, y, 0]))
    grid.set_stroke(color="#1e293b", width=0.6, opacity=0.35)
    return grid


def make_footer():
    footer = Text("giorgiodifusco.it — blog", font="sans-serif", color=SLATE_DIM).scale(0.3)
    footer.to_corner(DR, buff=0.35)
    return footer


def make_heading(number, title_text, color):
    badge = Circle(radius=0.42, fill_color=color, fill_opacity=1, stroke_width=0)
    num = Text(str(number), font="sans-serif", weight=BOLD, color=BG).scale(0.55)
    num.move_to(badge.get_center())
    badge_group = VGroup(badge, num)
    title = Text(title_text, font="sans-serif", weight=BOLD, color=SLATE_TEXT).scale(0.62)
    heading = VGroup(badge_group, title).arrange(RIGHT, buff=0.3)
    return heading


def make_code_panel(lines_text, scale=0.36, colors=None, width_pad=1.0, height_pad=0.8):
    """lines_text: lista di stringhe. colors: dict opzionale {indice_riga: colore}."""
    colors = colors or {}
    lines = VGroup(*[
        Text(t if t else " ", font="monospace", color=colors.get(i, SLATE_TEXT))
        for i, t in enumerate(lines_text)
    ])
    lines.arrange(DOWN, aligned_edge=LEFT, buff=0.2).scale(scale)
    panel = RoundedRectangle(
        corner_radius=0.15,
        width=lines.width + width_pad,
        height=lines.height + height_pad,
        fill_color=CODE_BG, fill_opacity=1,
        stroke_color=CODE_BORDER, stroke_width=2,
    )
    lines.move_to(panel.get_center())
    return VGroup(panel, lines)


def make_file_tab(name, color=SLATE_DIM):
    return Text(name, font="monospace", color=color).scale(0.34)


def make_command_chip(cmd_text, color):
    text = Text(cmd_text, font="monospace", color=color).scale(0.34)
    box = RoundedRectangle(
        corner_radius=0.14,
        width=text.width + 0.7, height=text.height + 0.5,
        fill_color="#0f172a", fill_opacity=1,
        stroke_color=color, stroke_width=1.5,
    )
    text.move_to(box.get_center())
    return VGroup(box, text)


def make_arrow_with_label(start, end, label_text, color):
    arrow = Arrow(start=start, end=end, color=color, stroke_width=5, buff=0, max_tip_length_to_length_ratio=0.14)
    label = Text(label_text, font="sans-serif", color=color).scale(0.32)
    label.next_to(arrow, UP, buff=0.15)
    return VGroup(arrow, label)


# ---------------------------------------------------------------------------
# Copertina articolo
# ---------------------------------------------------------------------------

class CompilazioneCover(Scene):
    def construct(self):
        self.camera.background_color = BG
        self.add(make_grid())

        # badge "C" a sinistra, coerente con la copertina dell'articolo 1
        badge = Circle(radius=1.0, color=INDIGO, fill_opacity=1)
        badge.set_fill(color=[INDIGO, GREEN])
        badge_letter = Text("C", font="monospace", weight=BOLD, color=SLATE_TEXT).scale(1.5)
        badge_letter.move_to(badge.get_center())
        badge_group = VGroup(badge, badge_letter).move_to([-5.15, 1.15, 0])
        self.add(badge_group)

        title_line1 = Text("La pipeline di", font="sans-serif", weight=BOLD, color=SLATE_TEXT)
        title_line2 = Text("compilazione", font="sans-serif", weight=BOLD, color=SLATE_TEXT)
        title = VGroup(title_line1, title_line2).arrange(DOWN, aligned_edge=LEFT, buff=0.22).scale(0.72)
        title.next_to(badge_group, RIGHT, buff=0.55)
        title.align_to(badge_group, UP)
        title.shift(UP * 0.15)

        subtitle = Text(
            "da main.c all'eseguibile: preprocessore, compilatore, assembler, linker",
            font="sans-serif",
            color=SLATE_DIM,
        ).scale(0.34)
        subtitle.next_to(title, DOWN, buff=0.3, aligned_edge=LEFT)
        self.add(title, subtitle)

        # ---- striscia della pipeline in basso: 4 tappe collegate da frecce ----
        stage_defs = [
            ("main.c", SLATE_TEXT, CODE_BORDER),
            ("Preproc.", SLATE_TEXT, INDIGO),
            ("Compil.", SLATE_TEXT, BLUE),
            ("Assembler", SLATE_TEXT, GREEN),
            ("Linker", SLATE_TEXT, AMBER),
            ("app", BG, GREEN),
        ]
        chips = VGroup()
        for i, (label, txt_color, edge_color) in enumerate(stage_defs):
            is_last = (i == len(stage_defs) - 1)
            txt = Text(label, font="monospace", weight=BOLD if is_last else NORMAL, color=txt_color).scale(0.32)
            box = RoundedRectangle(
                corner_radius=0.12,
                width=txt.width + 0.5, height=0.62,
                fill_color=(edge_color if is_last else "#0f172a"), fill_opacity=1,
                stroke_color=edge_color, stroke_width=2,
            )
            txt.move_to(box.get_center())
            chips.add(VGroup(box, txt))
        chips.arrange(RIGHT, buff=0.55)
        chips.scale(0.95)
        chips.move_to([0.3, -2.2, 0])

        arrows = VGroup()
        for a, b in zip(chips[:-1], chips[1:]):
            arrows.add(Arrow(a.get_right(), b.get_left(), buff=0.06, color=SLATE_DIM,
                              stroke_width=2.5, max_tip_length_to_length_ratio=0.35))

        self.add(chips, arrows)

        footer = Text("giorgiodifusco.it — blog", font="sans-serif", color=SLATE_DIM).scale(0.3)
        footer.to_corner(DR, buff=0.35)
        self.add(footer)


# ---------------------------------------------------------------------------
# Fase 1: Preprocessore
# ---------------------------------------------------------------------------

class PreprocessorPhase(Scene):
    def construct(self):
        self.camera.background_color = BG
        self.add(make_grid())

        heading = make_heading(1, "Il Preprocessore", INDIGO)
        heading.to_corner(UL, buff=0.55)
        self.add(heading)

        left_tab = make_file_tab("main.c")
        left = make_code_panel([
            "#include <stdio.h>",
            "int main(void) {",
            '  printf("Ciao");',
            "  return 0;",
            "}",
        ], colors={0: INDIGO})
        left.move_to([-4.3, -0.7, 0])
        left_tab.next_to(left, UP, buff=0.15).align_to(left, LEFT)

        right_tab = make_file_tab("main.i (espanso)")
        right = make_code_panel([
            "// ...migliaia di righe...",
            "// ...copiate da stdio.h...",
            "extern int printf(...);",
            "int main(void) {",
            '  printf("Ciao");',
            "  return 0;",
            "}",
        ], scale=0.30, colors={2: SLATE_DIM})
        right.move_to([4.0, -0.7, 0])
        right_tab.next_to(right, UP, buff=0.15).align_to(right, LEFT)

        arrow = make_arrow_with_label(
            left.get_right() + RIGHT * 0.15,
            right.get_left() + LEFT * 0.15,
            "copia-incolla gli #include",
            INDIGO,
        )

        self.add(left_tab, left, right_tab, right, arrow)

        cmd = make_command_chip("gcc -E main.c -o main.i", INDIGO)
        cmd.move_to([0, -3.35, 0])
        self.add(cmd)

        self.add(make_footer())


# ---------------------------------------------------------------------------
# Fase 2: Compilazione
# ---------------------------------------------------------------------------

class CompilerPhase(Scene):
    def construct(self):
        self.camera.background_color = BG
        self.add(make_grid())

        heading = make_heading(2, "La Compilazione", BLUE)
        heading.to_corner(UL, buff=0.55)
        self.add(heading)

        left_tab = make_file_tab("main.i")
        left = make_code_panel([
            "int main(void) {",
            "  int x = 42;",
            "  return x;",
            "}",
        ])
        left.move_to([-4.3, -0.7, 0])
        left_tab.next_to(left, UP, buff=0.15).align_to(left, LEFT)

        right_tab = make_file_tab("main.s (Assembly)")
        right = make_code_panel([
            "main:",
            "  push  rbp",
            "  mov   rbp, rsp",
            "  mov   DWORD [rbp-4], 42",
            "  mov   eax, [rbp-4]",
            "  pop   rbp",
            "  ret",
        ], scale=0.30, colors={0: BLUE})
        right.move_to([4.0, -0.7, 0])
        right_tab.next_to(right, UP, buff=0.15).align_to(right, LEFT)

        arrow = make_arrow_with_label(
            left.get_right() + RIGHT * 0.15,
            right.get_left() + LEFT * 0.15,
            "traduce in Assembly",
            BLUE,
        )

        self.add(left_tab, left, right_tab, right, arrow)

        cmd = make_command_chip("gcc -S main.i -o main.s", BLUE)
        cmd.move_to([0, -3.35, 0])
        self.add(cmd)

        self.add(make_footer())


# ---------------------------------------------------------------------------
# Fase 3: Assemblaggio
# ---------------------------------------------------------------------------

class AssemblerPhase(Scene):
    def construct(self):
        self.camera.background_color = BG
        self.add(make_grid())

        heading = make_heading(3, "L'Assemblaggio", GREEN)
        heading.to_corner(UL, buff=0.55)
        self.add(heading)

        left_tab = make_file_tab("main.s")
        left = make_code_panel([
            "main:",
            "  push  rbp",
            "  mov   rbp, rsp",
            "  call  printf",
            "  pop   rbp",
            "  ret",
        ], colors={3: AMBER})
        left.move_to([-4.3, -0.7, 0])
        left_tab.next_to(left, UP, buff=0.15).align_to(left, LEFT)

        right_tab = make_file_tab("main.o (binario)")
        hex_lines = VGroup(*[
            Text(t, font="monospace", color=SLATE_TEXT).scale(0.30)
            for t in [
                "4D 5A 90 00 03 00 00 00",
                "B8 2A 00 00 00 5D C3 90",
                "00 00 00 00 ?? ?? ?? ??",
            ]
        ]).arrange(DOWN, aligned_edge=LEFT, buff=0.22)
        hole_tag = Text("printf: indirizzo mancante!", font="sans-serif", color=RED).scale(0.26)
        hole_tag.next_to(hex_lines, DOWN, buff=0.22, aligned_edge=LEFT)
        obj_body = VGroup(hex_lines, hole_tag)
        obj_panel = RoundedRectangle(
            corner_radius=0.15,
            width=obj_body.width + 1.0, height=obj_body.height + 0.8,
            fill_color=CODE_BG, fill_opacity=1,
            stroke_color=RED, stroke_width=2,
        )
        obj_body.move_to(obj_panel.get_center())
        right = VGroup(obj_panel, obj_body)
        right.move_to([4.0, -0.7, 0])
        right_tab.next_to(right, UP, buff=0.15).align_to(right, LEFT)

        arrow = make_arrow_with_label(
            left.get_right() + RIGHT * 0.15,
            right.get_left() + LEFT * 0.15,
            "genera codice oggetto",
            GREEN,
        )

        self.add(left_tab, left, right_tab, right, arrow)

        cmd = make_command_chip("gcc -c main.s -o main.o", GREEN)
        cmd.move_to([0, -3.35, 0])
        self.add(cmd)

        self.add(make_footer())


# ---------------------------------------------------------------------------
# Fase 4: Linker
# ---------------------------------------------------------------------------

class LinkerPhase(Scene):
    def construct(self):
        self.camera.background_color = BG
        self.add(make_grid())

        heading = make_heading(4, "Il Linker", AMBER)
        heading.to_corner(UL, buff=0.55)
        self.add(heading)

        def small_chip(label, color):
            txt = Text(label, font="monospace", color=SLATE_TEXT).scale(0.34)
            box = RoundedRectangle(
                corner_radius=0.13,
                width=txt.width + 0.7, height=txt.height + 0.55,
                fill_color=CODE_BG, fill_opacity=1,
                stroke_color=color, stroke_width=2,
            )
            txt.move_to(box.get_center())
            return VGroup(box, txt)

        obj_chip = small_chip("main.o", RED)
        lib_chip = small_chip("libc (printf, ...)", BLUE)
        plus = Text("+", font="sans-serif", color=SLATE_DIM).scale(0.6)

        inputs = VGroup(obj_chip, plus, lib_chip).arrange(DOWN, buff=0.35)
        inputs.move_to([-4.3, -0.6, 0])

        exe_txt = Text("app", font="monospace", weight=BOLD, color=BG).scale(0.6)
        exe_box = RoundedRectangle(
            corner_radius=0.16, width=2.4, height=1.3,
            fill_color=GREEN, fill_opacity=1,
            stroke_color=GREEN, stroke_width=2,
        )
        exe_txt.move_to(exe_box.get_center() + UP * 0.15)
        exe_sub = Text("eseguibile", font="sans-serif", color=BG).scale(0.28)
        exe_sub.next_to(exe_txt, DOWN, buff=0.12)
        right = VGroup(exe_box, exe_txt, exe_sub)
        right.move_to([4.2, -0.6, 0])

        arrow = make_arrow_with_label(
            inputs.get_right() + RIGHT * 0.2,
            right.get_left() + LEFT * 0.15,
            "risolve i simboli e unisce tutto",
            AMBER,
        )

        self.add(inputs, right, arrow)

        cmd = make_command_chip("gcc main.o -o app", AMBER)
        cmd.move_to([0, -3.35, 0])
        self.add(cmd)

        self.add(make_footer())
