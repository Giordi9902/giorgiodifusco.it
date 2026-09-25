"""
Copertina + 4 immagini per l'articolo "04 - Puntatori"
(indirizzi di memoria, operatori & e *, passaggio per valore vs per indirizzo,
aritmetica dei puntatori, my_strlen).

Render: lancia  ./render.sh  in questa cartella (usa il venv condiviso in
Articoli/_manim/venv e scrive copertina.png + le figure in media/).

Mappa scena -> immagine (1200x630, formato cover/blog):

    PuntatoriCover       ->  copertina.png              (copertina)
    MemoriaIndirizzi     ->  media/memoria_indirizzi.png
    SwapScene            ->  media/swap.png
    AritmeticaPuntatori  ->  media/aritmetica_puntatori.png
    MyStrlen             ->  media/my_strlen.png
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
# Helper condivisi (coerenti con bitwise_scenes.py / pipeline_scenes.py)
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


def make_command_chip(cmd_text, color, scale=0.34):
    text = Text(cmd_text, font="monospace", color=color).scale(scale)
    box = RoundedRectangle(
        corner_radius=0.14,
        width=text.width + 0.7, height=text.height + 0.5,
        fill_color="#0f172a", fill_opacity=1,
        stroke_color=color, stroke_width=1.5,
    )
    text.move_to(box.get_center())
    return VGroup(box, text)


def make_cell(content="", width=0.6, height=0.6, color=None, text_color=SLATE_TEXT,
              text_scale=0.4):
    """Una casetta di memoria: rettangolo + contenuto (opzionale).
    color=None -> cella "spenta" (bordo scuro)."""
    rect = Rectangle(width=width, height=height)
    if color is None:
        rect.set_stroke(color=CODE_BORDER, width=2)
        rect.set_fill(color=CODE_BG, opacity=1)
    else:
        rect.set_stroke(color=color, width=2)
        rect.set_fill(color=color, opacity=0.18)
    group = VGroup(rect)
    if content:
        txt = Text(content, font="monospace", weight=BOLD, color=text_color).scale(text_scale)
        txt.move_to(rect.get_center())
        group.add(txt)
    return group


def make_memory_strip(n, width=0.6, height=0.6, buff=0.0):
    """Striscia di n celle vuote contigue."""
    cells = VGroup(*[make_cell(width=width, height=height) for _ in range(n)])
    cells.arrange(RIGHT, buff=buff)
    return cells


def cell_span(cells, i, j, color, label, text_scale=0.42):
    """Evidenzia le celle cells[i..j] come un'unica variabile e ci scrive label."""
    first, last = cells[i], cells[j]
    box = Rectangle(
        width=last.get_right()[0] - first.get_left()[0],
        height=first.height,
    )
    box.move_to((first.get_center() + last.get_center()) / 2)
    box.set_stroke(color=color, width=3)
    box.set_fill(color=color, opacity=0.18)
    txt = Text(label, font="monospace", weight=BOLD, color=SLATE_TEXT).scale(text_scale)
    txt.move_to(box.get_center())
    return VGroup(box, txt)


def make_pointer_arrow(start, end, color, label=None, label_dir=UP):
    arrow = Arrow(start, end, buff=0.08, color=color, stroke_width=4,
                  max_tip_length_to_length_ratio=0.18)
    group = VGroup(arrow)
    if label:
        lbl = Text(label, font="monospace", weight=BOLD, color=color).scale(0.34)
        lbl.next_to(arrow.get_start(), label_dir, buff=0.12)
        group.add(lbl)
    return group


# ---------------------------------------------------------------------------
# Copertina articolo
# ---------------------------------------------------------------------------

class PuntatoriCover(Scene):
    def construct(self):
        self.camera.background_color = BG
        self.add(make_grid())

        # badge "C" a sinistra, coerente con le copertine degli articoli precedenti
        badge = Circle(radius=1.0, color=INDIGO, fill_opacity=1)
        badge.set_fill(color=[INDIGO, GREEN])
        badge_letter = Text("C", font="monospace", weight=BOLD, color=SLATE_TEXT).scale(1.5)
        badge_letter.move_to(badge.get_center())
        badge_group = VGroup(badge, badge_letter).move_to([-5.15, 1.2, 0])
        self.add(badge_group)

        title_line1 = Text("I puntatori e", font="sans-serif", weight=BOLD, color=SLATE_TEXT)
        title_line2 = Text("la memoria", font="sans-serif", weight=BOLD, color=SLATE_TEXT)
        title = VGroup(title_line1, title_line2).arrange(DOWN, aligned_edge=LEFT, buff=0.22).scale(0.72)
        title.next_to(badge_group, RIGHT, buff=0.55)
        title.align_to(badge_group, UP)
        title.shift(UP * 0.15)

        subtitle = Text(
            "indirizzi, dereferenziazione e aritmetica dei puntatori",
            font="sans-serif",
            color=SLATE_DIM,
        ).scale(0.34)
        subtitle.next_to(title, DOWN, buff=0.3, aligned_edge=LEFT)
        self.add(title, subtitle)

        # ---- banda in basso: ptr -> casella int nella RAM ----
        cells = make_memory_strip(10, width=0.62, height=0.62)
        cells.move_to([1.3, -2.1, 0])
        self.add(cells)

        var = cell_span(cells, 4, 7, GREEN, "100")
        self.add(var)

        addrs = VGroup()
        for k in (0, 4, 8):
            a = Text(f"0x{0x7ffc + k:x}", font="monospace", color=SLATE_DIM).scale(0.24)
            a.next_to(cells[k], DOWN, buff=0.14).align_to(cells[k], LEFT)
            addrs.add(a)
        self.add(addrs)

        var_lbl = Text("int punteggio", font="monospace", color=GREEN).scale(0.28)
        var_lbl.next_to(var, UP, buff=0.14)
        self.add(var_lbl)

        ptr = make_cell("0x8000", width=1.7, height=0.62, color=INDIGO, text_scale=0.34)
        ptr.move_to([-4.5, -2.1, 0])
        ptr_lbl = Text("int *ptr", font="monospace", color=INDIGO).scale(0.28)
        ptr_lbl.next_to(ptr, UP, buff=0.14)
        self.add(ptr, ptr_lbl)

        arrow = CurvedArrow(
            ptr.get_right() + UP * 0.1,
            var.get_top() + UP * 0.45 + LEFT * 0.6,
            angle=-PI / 3.2, color=INDIGO, stroke_width=4,
        )
        self.add(arrow)

        self.add(make_footer())


# ---------------------------------------------------------------------------
# Immagine 1: indirizzi, & e *
# ---------------------------------------------------------------------------

class MemoriaIndirizzi(Scene):
    def construct(self):
        self.camera.background_color = BG
        self.add(make_grid())

        heading = make_heading(1, "La RAM è una strada di casette", INDIGO)
        heading.to_corner(UL, buff=0.55)
        self.add(heading)

        # 12 byte di memoria a partire da 0x1000
        base = 0x1000
        cells = make_memory_strip(12, width=0.78, height=0.7)
        cells.move_to([0, 0.35, 0])
        self.add(cells)

        for k, c in enumerate(cells):
            a = Text(f"{base + k:x}", font="monospace", color=SLATE_DIM).scale(0.24)
            a.next_to(c, DOWN, buff=0.12)
            self.add(a)
        addr_lbl = Text("indirizzo (hex)", font="sans-serif", color=SLATE_DIM).scale(0.24)
        addr_lbl.next_to(cells, LEFT, buff=0.2).shift(DOWN * 0.5)
        self.add(addr_lbl)

        # int punteggio = 100  ->  4 byte a partire da 0x1004
        var = cell_span(cells, 4, 7, GREEN, "100")
        var_lbl = Text("int punteggio  (4 byte)", font="monospace", color=GREEN).scale(0.3)
        var_lbl.next_to(var, UP, buff=0.2)
        self.add(var, var_lbl)

        # puntatore, in un'altra zona della memoria
        ptr = make_cell("0x1004", width=2.0, height=0.7, color=INDIGO, text_scale=0.38)
        ptr.move_to([-4.3, 2.0, 0])
        ptr_lbl = Text("int *ptr = &punteggio;", font="monospace", color=INDIGO).scale(0.3)
        ptr_lbl.next_to(ptr, RIGHT, buff=0.3)
        self.add(ptr, ptr_lbl)

        arrow = Arrow(ptr.get_bottom(), var.get_left() + UP * 0.2 + RIGHT * 0.05, buff=0.08,
                      color=INDIGO, stroke_width=4, max_tip_length_to_length_ratio=0.12)
        self.add(arrow)

        # le due operazioni
        amp = make_command_chip("&punteggio  ->  0x1004   (dove abiti?)", BLUE, scale=0.32)
        star = make_command_chip("*ptr  ->  100   (entra e guarda dentro)", GREEN, scale=0.32)
        ops = VGroup(amp, star).arrange(DOWN, buff=0.25, aligned_edge=LEFT)
        ops.move_to([0, -2.1, 0])
        self.add(ops)

        self.add(make_footer())


# ---------------------------------------------------------------------------
# Immagine 2: passaggio per valore vs per indirizzo
# ---------------------------------------------------------------------------

class SwapScene(Scene):
    def construct(self):
        self.camera.background_color = BG
        self.add(make_grid())

        heading = make_heading(2, "Fotocopie contro numeri civici", AMBER)
        heading.to_corner(UL, buff=0.55)
        self.add(heading)

        def var_box(name, value, color, w=1.1):
            box = make_cell(value, width=w, height=0.62, color=color, text_scale=0.36)
            lbl = Text(name, font="monospace", color=SLATE_DIM).scale(0.26)
            lbl.next_to(box, UP, buff=0.1)
            return VGroup(box, lbl)

        def panel(title_text, color, sig, main_vals, fn_vals, fn_color, result_text,
                  result_color, is_ptr=False):
            title = Text(title_text, font="sans-serif", weight=BOLD, color=color).scale(0.4)
            sig_txt = Text(sig, font="monospace", color=SLATE_TEXT).scale(0.28)

            main_lbl = Text("main()", font="monospace", color=SLATE_DIM).scale(0.26)
            x = var_box("x", main_vals[0], BLUE)
            y = var_box("y", main_vals[1], BLUE)
            main_vars = VGroup(x, y).arrange(RIGHT, buff=0.35)
            main_col = VGroup(main_lbl, main_vars).arrange(DOWN, buff=0.18)

            fn_lbl = Text("swap()", font="monospace", color=SLATE_DIM).scale(0.26)
            w = 1.5 if is_ptr else 1.1
            a = var_box("a", fn_vals[0], fn_color, w)
            b = var_box("b", fn_vals[1], fn_color, w)
            fn_vars = VGroup(a, b).arrange(RIGHT, buff=0.35)
            fn_col = VGroup(fn_lbl, fn_vars).arrange(DOWN, buff=0.18)

            body = VGroup(main_col, fn_col).arrange(DOWN, buff=0.75)

            arrows = VGroup()
            if is_ptr:
                # a -> x, b -> y : frecce che risalgono verso le variabili originali
                for src, dst in ((a, x), (b, y)):
                    arrows.add(Arrow(src[0].get_top(), dst[0].get_bottom(), buff=0.08,
                                     color=fn_color, stroke_width=3.5,
                                     max_tip_length_to_length_ratio=0.2))
            else:
                # x -> a, y -> b : "fotocopia"
                for src, dst in ((x, a), (y, b)):
                    arrows.add(DashedLine(src[0].get_bottom(), dst[1].get_top(), buff=0.05,
                                          color=SLATE_DIM, stroke_width=2.5).add_tip(tip_length=0.15))

            result = Text(result_text, font="monospace", weight=BOLD, color=result_color).scale(0.3)

            content = VGroup(title, sig_txt, body, result).arrange(DOWN, buff=0.28)
            frame = RoundedRectangle(
                corner_radius=0.16, width=5.6, height=content.height + 0.6,
                fill_color=CODE_BG, fill_opacity=1, stroke_color=color, stroke_width=2,
            )
            content.move_to(frame.get_center())
            return VGroup(frame, content, arrows)

        left = panel(
            "Per valore", RED, "void swap(int a, int b)",
            ("10", "99"), ("99", "10"), SLATE_DIM,
            "x = 10, y = 99   (niente scambio)", RED,
        )
        right = panel(
            "Per indirizzo", GREEN, "void swap(int *a, int *b)",
            ("99", "10"), ("&x", "&y"), GREEN,
            "x = 99, y = 10   (scambiati!)", GREEN, is_ptr=True,
        )
        panels = VGroup(left, right).arrange(RIGHT, buff=0.55)
        panels.move_to([0, -0.5, 0])
        self.add(panels)

        self.add(make_footer())


# ---------------------------------------------------------------------------
# Immagine 3: aritmetica dei puntatori (ptr + 1 dipende dal tipo)
# ---------------------------------------------------------------------------

class AritmeticaPuntatori(Scene):
    def construct(self):
        self.camera.background_color = BG
        self.add(make_grid())

        heading = make_heading(3, "ptr + 1 non vuol dire +1 byte", GREEN)
        heading.to_corner(UL, buff=0.55)
        self.add(heading)

        n_bytes = 17
        cw = 0.56
        x0 = -3.3  # bordo sinistro della striscia

        def strip_row(y, step, color, type_name):
            cells = make_memory_strip(n_bytes, width=cw, height=0.46)
            cells.move_to([x0 + cells.width / 2, y, 0])
            # colora i blocchi di "step" byte alternando l'opacità
            for k in range(0, n_bytes - 1, step):
                blk = cell_span(cells, k, k + step - 1, color, "", text_scale=0.3)
                blk[0].set_fill(color=color, opacity=0.28 if (k // step) % 2 == 0 else 0.1)
                blk[0].set_stroke(width=2)
                cells.add(blk)
            name = Text(type_name, font="monospace", weight=BOLD, color=color).scale(0.34)
            name.move_to([-5.2, y, 0])
            group = VGroup(name, cells)
            # frecce ptr, ptr+1, ptr+2 sulle celle 0, step, 2*step
            for i in range(3):
                k = i * step
                if k >= n_bytes:
                    break
                cell = cells[k]
                tip = cell.get_top() + LEFT * (cw / 2 - 0.02)
                arr = Arrow(tip + UP * 0.42, tip, buff=0, color=color, stroke_width=3,
                            max_tip_length_to_length_ratio=0.35)
                lbl = Text("ptr" if i == 0 else f"+{i}", font="monospace",
                           color=color).scale(0.24)
                lbl.move_to([arr.get_center()[0], arr.get_top()[1] + 0.14, 0])
                group.add(arr, lbl)
            return group

        rows = VGroup(
            strip_row(1.2, 1, AMBER, "char *"),
            strip_row(-0.15, 4, GREEN, "int *"),
            strip_row(-1.5, 8, BLUE, "uint64_t *"),
        )
        self.add(rows)

        # righello degli indirizzi sotto l'ultima riga
        for k in range(0, n_bytes, 4):
            a = Text(str(1000 + k), font="monospace", color=SLATE_DIM).scale(0.24)
            a.move_to([x0 + k * cw, -2.08, 0])
            self.add(a)

        formula = make_command_chip("ptr + n  =  indirizzo + n * sizeof(*ptr)", GREEN, scale=0.32)
        formula.move_to([0, -2.9, 0])
        self.add(formula)

        self.add(make_footer())


# ---------------------------------------------------------------------------
# Immagine 4: my_strlen, str - inizio
# ---------------------------------------------------------------------------

class MyStrlen(Scene):
    def construct(self):
        self.camera.background_color = BG
        self.add(make_grid())

        heading = make_heading(4, "strlen() con l'aritmetica dei puntatori", BLUE)
        heading.to_corner(UL, buff=0.55)
        self.add(heading)

        chars = list("Hello, Kernel!") + ["\\0"]
        cells = VGroup()
        for ch in chars:
            if ch == "\\0":
                c = make_cell(ch, width=0.7, height=0.7, color=RED, text_scale=0.34)
            else:
                c = make_cell(ch if ch != " " else "␣", width=0.7, height=0.7,
                              color=BLUE, text_scale=0.42)
            cells.add(c)
        cells.arrange(RIGHT, buff=0.04)
        cells.move_to([0, 0.1, 0])
        self.add(cells)

        for k, c in enumerate(cells):
            idx = Text(str(k), font="monospace", color=SLATE_DIM).scale(0.22)
            idx.next_to(c, DOWN, buff=0.1)
            self.add(idx)

        # puntatori inizio e str
        def ptr_label(cell, text, color):
            arr = Arrow(cell.get_top() + UP * 0.75, cell.get_top(), buff=0.05,
                        color=color, stroke_width=4, max_tip_length_to_length_ratio=0.3)
            lbl = Text(text, font="monospace", weight=BOLD, color=color).scale(0.32)
            lbl.next_to(arr, UP, buff=0.08)
            return VGroup(arr, lbl)

        self.add(ptr_label(cells[0], "inizio", GREEN))
        self.add(ptr_label(cells[-1], "str", AMBER))

        # graffa sotto i 14 caratteri
        brace = Brace(VGroup(*cells[:-1]), DOWN, buff=0.45, color=GREEN)
        brace_txt = Text("str - inizio = 14", font="monospace", weight=BOLD,
                         color=GREEN).scale(0.4)
        brace_txt.next_to(brace, DOWN, buff=0.15)
        self.add(brace, brace_txt)

        loop = make_command_chip("while (*str) str++;   // si ferma su '\\0'", AMBER, scale=0.32)
        loop.move_to([0, -2.45, 0])
        self.add(loop)

        self.add(make_footer())
