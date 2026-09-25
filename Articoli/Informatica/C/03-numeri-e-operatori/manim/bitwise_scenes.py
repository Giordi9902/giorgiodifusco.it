"""
Copertina + 4 immagini per l'articolo "03 - Numeri e operatori"
(tipi interi di stdint.h, overflow, operatori bitwise, convertitore dec->bin).

Render: lancia  ./render.sh  in questa cartella (usa il venv condiviso in
Articoli/_manim/venv e scrive copertina.png + le figure in media/).

Mappa scena -> immagine (1200x630, formato cover/blog):

    NumeriCover       ->  copertina.png              (copertina)
    TipiInteri        ->  media/tipi_interi.png
    OverflowScene     ->  media/overflow.png
    BitwiseOperators  ->  media/operatori_bitwise.png
    DecToBin          ->  media/dec_to_bin.png
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
# Helper condivisi (coerenti con cover_scene.py / pipeline_scenes.py)
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


def make_bit_row(bits, cell=0.5, on_color=GREEN, digit_scale=0.5,
                 nibble_buff=0.26, bit_buff=0.07):
    """Riga di celle-bit raggruppate a nibble (gruppi di 4).
    Ritorna (VGroup_riga, lista_celle) dove ogni cella e' VGroup(quadrato, cifra).
    """
    nibbles = [bits[i:i + 4] for i in range(0, len(bits), 4)]
    row = VGroup()
    cells = []
    for nib in nibbles:
        group = VGroup()
        for b in nib:
            on = (b == "1")
            sq = Square(side_length=cell)
            sq.set_stroke(color=(on_color if on else CODE_BORDER), width=2)
            sq.set_fill(color=(on_color if on else CODE_BG), opacity=(0.22 if on else 1.0))
            digit = Text(b, font="monospace", weight=BOLD,
                         color=(SLATE_TEXT if on else SLATE_DIM)).scale(digit_scale)
            digit.move_to(sq.get_center())
            c = VGroup(sq, digit)
            group.add(c)
            cells.append(c)
        group.arrange(RIGHT, buff=bit_buff)
        row.add(group)
    row.arrange(RIGHT, buff=nibble_buff)
    return row, cells


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


# ---------------------------------------------------------------------------
# Copertina articolo
# ---------------------------------------------------------------------------

class NumeriCover(Scene):
    def construct(self):
        self.camera.background_color = BG
        self.add(make_grid())

        # badge "C" a sinistra, coerente con le copertine degli articoli 1 e 2
        badge = Circle(radius=1.0, color=INDIGO, fill_opacity=1)
        badge.set_fill(color=[INDIGO, GREEN])
        badge_letter = Text("C", font="monospace", weight=BOLD, color=SLATE_TEXT).scale(1.5)
        badge_letter.move_to(badge.get_center())
        badge_group = VGroup(badge, badge_letter).move_to([-5.15, 1.2, 0])
        self.add(badge_group)

        title_line1 = Text("Numeri e operatori", font="sans-serif", weight=BOLD, color=SLATE_TEXT)
        title_line2 = Text("bitwise", font="sans-serif", weight=BOLD, color=SLATE_TEXT)
        title = VGroup(title_line1, title_line2).arrange(DOWN, aligned_edge=LEFT, buff=0.22).scale(0.72)
        title.next_to(badge_group, RIGHT, buff=0.55)
        title.align_to(badge_group, UP)
        title.shift(UP * 0.15)

        subtitle = Text(
            "stdint.h, overflow e manipolazione bit a bit",
            font="sans-serif",
            color=SLATE_DIM,
        ).scale(0.34)
        subtitle.next_to(title, DOWN, buff=0.3, aligned_edge=LEFT)
        self.add(title, subtitle)

        # ---- banda in basso: 42 -> rappresentazione binaria a 16 bit ----
        dec_num = Text("42", font="monospace", weight=BOLD, color=SLATE_TEXT).scale(0.9)
        dec_box = RoundedRectangle(
            corner_radius=0.14, width=1.5, height=1.1,
            fill_color=CODE_BG, fill_opacity=1,
            stroke_color=BLUE, stroke_width=2.5,
        )
        dec_num.move_to(dec_box.get_center() + UP * 0.12)
        dec_lbl = Text("decimale", font="sans-serif", color=SLATE_DIM).scale(0.28)
        dec_lbl.next_to(dec_num, DOWN, buff=0.14)
        dec_group = VGroup(dec_box, dec_num, dec_lbl)

        row, cells = make_bit_row("0000000000101010", cell=0.52, on_color=GREEN)
        bin_lbl = Text("uint16_t — 16 bit", font="sans-serif", color=SLATE_DIM).scale(0.28)
        bin_lbl.next_to(row, DOWN, buff=0.2)
        bin_group = VGroup(row, bin_lbl)

        band = VGroup(dec_group, bin_group).arrange(RIGHT, buff=0.7)
        band.move_to([0.35, -2.15, 0])

        arrow = Arrow(dec_box.get_right(), row.get_left(), buff=0.18,
                      color=SLATE_DIM, stroke_width=3, max_tip_length_to_length_ratio=0.25)
        self.add(dec_group, arrow, bin_group)

        self.add(make_footer())


# ---------------------------------------------------------------------------
# Immagine 1: i tipi interi di stdint.h
# ---------------------------------------------------------------------------

class TipiInteri(Scene):
    def construct(self):
        self.camera.background_color = BG
        self.add(make_grid())

        heading = make_heading(1, "I tipi interi di <stdint.h>", INDIGO)
        heading.to_corner(UL, buff=0.55)
        self.add(heading)

        def byte_cells(n, color):
            group = VGroup()
            for _ in range(n):
                sq = Square(side_length=0.44)
                sq.set_stroke(color=color, width=2)
                sq.set_fill(color=CODE_BG, opacity=1)
                group.add(sq)
            group.arrange(RIGHT, buff=0.09)
            return group

        rows_def = [
            ("uint8_t", 1, INDIGO, "senza segno", "0 … 255"),
            ("int32_t", 4, BLUE, "con segno", "−2,1 mld … 2,1 mld"),
            ("uint64_t", 8, GREEN, "senza segno", "0 … 1,8 × 10¹⁹"),
        ]

        # Posiziono manualmente le colonne per un allineamento pulito
        col_name_x = -5.4
        col_cells_x = -2.7
        col_range_x = 3.0
        y_positions = [1.15, -0.35, -1.85]

        built = VGroup()
        for (name, nbytes, color, sign, rng), y in zip(rows_def, y_positions):
            name_txt = Text(name, font="monospace", weight=BOLD, color=color).scale(0.5)
            name_txt.move_to([col_name_x + name_txt.width / 2, y, 0])

            cells = byte_cells(nbytes, color)
            cells.move_to([col_cells_x + cells.width / 2, y, 0])
            size_lbl = Text(f"{nbytes} byte", font="sans-serif", color=SLATE_DIM).scale(0.26)
            size_lbl.next_to(cells, UP, buff=0.12).align_to(cells, LEFT)

            rng_txt = Text(rng, font="monospace", color=SLATE_TEXT).scale(0.36)
            rng_txt.move_to([col_range_x, y + 0.14, 0])
            sign_txt = Text(sign, font="sans-serif", color=SLATE_DIM).scale(0.26)
            sign_txt.next_to(rng_txt, DOWN, buff=0.1).align_to(rng_txt, LEFT)

            built.add(name_txt, cells, size_lbl, rng_txt, sign_txt)

        self.add(built)

        note = Text(
            "Dimensioni identiche su ogni piattaforma: parli la lingua dell'hardware.",
            font="sans-serif", color=SLATE_DIM,
        ).scale(0.32)
        note.move_to([0, -3.15, 0])
        self.add(note)

        self.add(make_footer())


# ---------------------------------------------------------------------------
# Immagine 2: overflow di uint8_t
# ---------------------------------------------------------------------------

class OverflowScene(Scene):
    def construct(self):
        self.camera.background_color = BG
        self.add(make_grid())

        heading = make_heading(2, "L'overflow di uint8_t", AMBER)
        heading.to_corner(UL, buff=0.55)
        self.add(heading)

        # stato 255 (11111111)
        row255, _ = make_bit_row("11111111", cell=0.6, on_color=AMBER)
        lbl255 = Text("255", font="monospace", weight=BOLD, color=SLATE_TEXT).scale(0.6)
        lbl255.next_to(row255, UP, buff=0.22)
        g255 = VGroup(row255, lbl255).move_to([-3.75, 0.3, 0])

        # stato 0 (00000000)
        row0, _ = make_bit_row("00000000", cell=0.6, on_color=GREEN)
        lbl0 = Text("0", font="monospace", weight=BOLD, color=SLATE_TEXT).scale(0.6)
        lbl0.next_to(row0, UP, buff=0.22)
        g0 = VGroup(row0, lbl0).move_to([3.75, 0.3, 0])

        arrow = Arrow(g255.get_right() + RIGHT * 0.1, g0.get_left() + LEFT * 0.1,
                      buff=0.2, color=RED, stroke_width=5,
                      max_tip_length_to_length_ratio=0.14)
        plus = Text("+ 1", font="monospace", weight=BOLD, color=RED).scale(0.55)
        plus.next_to(arrow, UP, buff=0.18)

        self.add(g255, arrow, plus, g0)

        # bit di riporto che "cade nel vuoto": una nona cella tratteggiata,
        # a sinistra del risultato, barrata perche' non esiste in un uint8_t
        carry_box = DashedVMobject(
            Square(side_length=0.6).set_stroke(color=RED, width=2),
            num_dashes=16,
        )
        carry = Text("1", font="monospace", weight=BOLD, color=RED).scale(0.55)
        carry.move_to(carry_box.get_center())
        carry_group = VGroup(carry_box, carry).set_opacity(0.6)
        carry_group.next_to(row0, LEFT, buff=0.22)
        cross = Cross(Square(side_length=0.6), stroke_color=RED, stroke_width=4)
        cross.move_to(carry_box.get_center())
        carry_lbl = Text("il riporto cade nel vuoto", font="sans-serif", color=RED).scale(0.26)
        carry_lbl.next_to(carry_group, DOWN, buff=0.25)
        self.add(carry_group, cross, carry_lbl)

        note = Text(
            "Il contachilometri della Fiat Panda: superato il massimo, si riparte da zero.",
            font="sans-serif", color=SLATE_DIM,
        ).scale(0.32)
        note.move_to([0, -2.55, 0])
        self.add(note)

        code = make_command_chip("uint8_t x = 255;  x++;   // ora x vale 0", AMBER)
        code.move_to([0, -3.3, 0])
        self.add(code)

        self.add(make_footer())


# ---------------------------------------------------------------------------
# Immagine 3: i quattro operatori bitwise
# ---------------------------------------------------------------------------

class BitwiseOperators(Scene):
    def construct(self):
        self.camera.background_color = BG
        self.add(make_grid())

        heading = make_heading(3, "I quattro operatori bitwise", GREEN)
        heading.to_corner(UL, buff=0.55)
        self.add(heading)

        def op_card(symbol, name, a, b, res, color, is_shift=False):
            title = Text(f"{name}  ({symbol})", font="monospace", weight=BOLD, color=color).scale(0.4)

            def mini(bits, dim=False):
                r, _ = make_bit_row(bits, cell=0.34, on_color=color, digit_scale=0.34,
                                    nibble_buff=0.12, bit_buff=0.05)
                if dim:
                    r.set_opacity(0.85)
                return r

            if is_shift:
                line1 = mini(a)
                op1 = Text("<< 3", font="monospace", color=SLATE_DIM).scale(0.34)
                top = VGroup(line1, op1).arrange(RIGHT, buff=0.25)
                bar = Line(LEFT * 1.05, RIGHT * 1.05, color=CODE_BORDER, stroke_width=2)
                out = mini(res)
                body = VGroup(top, bar, out).arrange(DOWN, buff=0.16)
            else:
                line1 = mini(a)
                sym1 = Text(symbol, font="monospace", weight=BOLD, color=color).scale(0.42)
                r1 = VGroup(sym1, line1).arrange(RIGHT, buff=0.2)
                line2 = mini(b)
                r2 = VGroup(Text(" ", font="monospace").scale(0.42), line2).arrange(RIGHT, buff=0.2)
                bar = Line(LEFT * 1.15, RIGHT * 1.15, color=CODE_BORDER, stroke_width=2)
                out = mini(res)
                r3 = VGroup(Text(" ", font="monospace").scale(0.42), out).arrange(RIGHT, buff=0.2)
                body = VGroup(r1, r2, bar, r3).arrange(DOWN, aligned_edge=RIGHT, buff=0.12)

            content = VGroup(title, body).arrange(DOWN, buff=0.22)
            panel = RoundedRectangle(
                corner_radius=0.16,
                width=max(content.width + 0.7, 4.6), height=content.height + 0.7,
                fill_color=CODE_BG, fill_opacity=1,
                stroke_color=color, stroke_width=2,
            )
            content.move_to(panel.get_center())
            return VGroup(panel, content)

        and_card = op_card("&", "AND", "1100", "1010", "1000", INDIGO)
        or_card = op_card("|", "OR", "1100", "1010", "1110", BLUE)
        xor_card = op_card("^", "XOR", "1100", "1010", "0110", GREEN)
        shift_card = op_card("<<", "SHIFT", "0001", "", "1000", AMBER, is_shift=True)

        grid_cards = VGroup(
            VGroup(and_card, or_card).arrange(RIGHT, buff=0.6),
            VGroup(xor_card, shift_card).arrange(RIGHT, buff=0.6),
        ).arrange(DOWN, buff=0.45)
        grid_cards.scale(0.92).move_to([0, -0.35, 0])
        self.add(grid_cards)

        self.add(make_footer())


# ---------------------------------------------------------------------------
# Immagine 4: convertitore decimale -> binario
# ---------------------------------------------------------------------------

class DecToBin(Scene):
    def construct(self):
        self.camera.background_color = BG
        self.add(make_grid())

        heading = make_heading(4, "Da decimale a binario, bit dopo bit", BLUE)
        heading.to_corner(UL, buff=0.55)
        self.add(heading)

        # numero di partenza
        dec_num = Text("42", font="monospace", weight=BOLD, color=SLATE_TEXT).scale(0.85)
        dec_box = RoundedRectangle(
            corner_radius=0.14, width=1.4, height=1.05,
            fill_color=CODE_BG, fill_opacity=1, stroke_color=BLUE, stroke_width=2.5,
        )
        dec_num.move_to(dec_box.get_center() + UP * 0.1)
        dec_lbl = Text("uint16_t", font="monospace", color=SLATE_DIM).scale(0.3)
        dec_lbl.next_to(dec_num, DOWN, buff=0.12)
        dec_group = VGroup(dec_box, dec_num, dec_lbl).move_to([-5.05, 0.9, 0])

        # meccanismo: numero >> i & 1
        mech = make_command_chip("(numero >> i) & 1", GREEN)
        mech.move_to([-2.4, 0.9, 0])
        mech_lbl = Text("shift + maschera", font="sans-serif", color=SLATE_DIM).scale(0.28)
        mech_lbl.next_to(mech, DOWN, buff=0.16)

        arrow1 = Arrow(dec_group.get_right(), mech.get_left(), buff=0.2,
                       color=SLATE_DIM, stroke_width=3, max_tip_length_to_length_ratio=0.3)

        self.add(dec_group, arrow1, mech, mech_lbl)

        # risultato: 16 bit, con i bit accesi evidenziati
        row, cells = make_bit_row("0000000000101010", cell=0.56, on_color=GREEN)
        row.move_to([0, -1.35, 0])
        # indici dei bit accesi (dalla stringa): posizioni 10,12,14 corrispondono a 1
        set_positions = [i for i, ch in enumerate("0000000000101010") if ch == "1"]
        for i in set_positions:
            weight_val = 1 << (15 - i)
            tag = Text(str(weight_val), font="monospace", color=GREEN).scale(0.3)
            tag.next_to(cells[i], DOWN, buff=0.18)
            self.add(tag)

        res_lbl = Text("0000 0000 0010 1010", font="monospace", weight=BOLD, color=SLATE_TEXT).scale(0.42)
        res_lbl.next_to(row, UP, buff=0.35)
        self.add(row, res_lbl)

        sum_txt = Text("32 + 8 + 2 = 42", font="monospace", color=GREEN).scale(0.36)
        sum_txt.move_to([0, -2.75, 0])
        self.add(sum_txt)

        note = Text(
            "Il ciclo scorre i 16 bit dal più significativo: nessuna conversione magica, solo silicio.",
            font="sans-serif", color=SLATE_DIM,
        ).scale(0.3)
        note.move_to([0, -3.4, 0])
        self.add(note)

        self.add(make_footer())
