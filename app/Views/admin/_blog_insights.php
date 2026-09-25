<?php
/**
 * Sezione "Insights blog" della dashboard admin.
 *
 * @var array|null $blog  dati da App\Models\BlogInsights::getDashboard()
 */
$e = fn($v) => htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
$fmt = fn($n) => number_format((int) $n, 0, ',', '.');
$card = 'bg-slate-900/80 rounded-2xl border border-slate-800 shadow-xl shadow-indigo-900/10';
?>

<section id="insights-blog" class="space-y-4 scroll-mt-6">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mt-8">
        <div>
            <h2 class="text-lg font-semibold text-slate-100">Insights blog</h2>
            <p class="text-sm text-slate-400">Letture, sorgenti di traffico e qualità dei contenuti.</p>
        </div>
        <?php if ($blog): ?>
            <nav class="inline-flex rounded-full border border-slate-700 p-0.5 text-xs font-medium self-start sm:self-auto" aria-label="Periodo">
                <?php foreach ([7 => '7 giorni', 30 => '30 giorni', 90 => '90 giorni'] as $d => $label): ?>
                    <a href="<?= BASE_URL ?>/admin?periodo=<?= $d ?>#insights-blog"
                       class="px-3 py-1.5 rounded-full transition <?= $blog['days'] === $d ? 'bg-purple-600 text-white' : 'text-slate-400 hover:text-slate-200' ?>"
                       <?= $blog['days'] === $d ? 'aria-current="page"' : '' ?>><?= $label ?></a>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>
    </div>

    <?php if (!$blog): ?>
        <div class="<?= $card ?> p-6 text-sm text-slate-400">
            Impossibile calcolare le statistiche del blog in questo momento (dettagli nel log degli errori).
        </div>
    <?php else: ?>

        <?php if (!$blog['available']): ?>
            <div class="rounded-2xl border border-amber-500/30 bg-amber-500/5 p-4 text-sm text-amber-200 flex gap-3">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <div>
                    <p class="font-medium">Il conteggio delle visite non è ancora attivo.</p>
                    <p class="text-amber-200/80 mt-0.5">Esegui una volta sul database <code class="font-mono text-[12px]">schemas/migrations/2026-09-25-blog-insights.sql</code> (es. da phpMyAdmin → SQL). I controlli sui contenuti qui sotto funzionano già.</p>
                </div>
            </div>
        <?php endif; ?>

        <?php
        $delta = null;
        if ($blog['available'] && $blog['views_prev'] > 0) {
            $delta = (int) round(($blog['views_total'] - $blog['views_prev']) / $blog['views_prev'] * 100);
        }
        $avgPerPost = $blog['published_count'] > 0 ? $blog['views_total'] / $blog['published_count'] : 0;
        ?>

        <!-- KPI -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="<?= $card ?> p-5">
                <p class="text-xs font-medium text-slate-400">Letture · <?= $blog['days'] ?> giorni</p>
                <p class="mt-2 text-3xl font-bold text-slate-100"><?= $blog['available'] ? $fmt($blog['views_total']) : '—' ?></p>
                <p class="mt-1 text-xs text-slate-500">
                    <?php if ($delta !== null): ?>
                        <span class="inline-flex items-center gap-0.5 font-medium <?= $delta >= 0 ? 'text-emerald-400' : 'text-red-400' ?>">
                            <?= $delta >= 0 ? '▲' : '▼' ?> <?= abs($delta) ?>%
                        </span> rispetto ai <?= $blog['days'] ?> giorni precedenti
                    <?php elseif ($blog['available']): ?>
                        Nessun dato per il periodo precedente
                    <?php else: ?>
                        In attesa della migrazione
                    <?php endif; ?>
                </p>
            </div>
            <div class="<?= $card ?> p-5">
                <p class="text-xs font-medium text-slate-400">Letture per articolo</p>
                <p class="mt-2 text-3xl font-bold text-slate-100"><?= $blog['available'] ? number_format($avgPerPost, 1, ',', '.') : '—' ?></p>
                <p class="mt-1 text-xs text-slate-500">Totale da sempre: <?= $blog['available'] ? $fmt($blog['views_all_time']) : '—' ?></p>
            </div>
            <div class="<?= $card ?> p-5">
                <p class="text-xs font-medium text-slate-400">Articoli pubblicati</p>
                <p class="mt-2 text-3xl font-bold text-slate-100"><?= $fmt($blog['published_count']) ?></p>
                <p class="mt-1 text-xs text-slate-500"><?= $fmt($blog['draft_count']) ?> bozze · <?= $fmt($blog['published_in_period']) ?> nuovi nel periodo</p>
            </div>
            <div class="<?= $card ?> p-5">
                <p class="text-xs font-medium text-slate-400">Lunghezza media</p>
                <p class="mt-2 text-3xl font-bold text-slate-100"><?= $fmt($blog['avg_words']) ?> <span class="text-base font-medium text-slate-400">parole</span></p>
                <p class="mt-1 text-xs text-slate-500">≈ <?= max(1, (int) ceil($blog['avg_words'] / 220)) ?> min di lettura</p>
            </div>
        </div>

        <?php if ($blog['available']): ?>
            <!-- Letture per giorno -->
            <?php
            $daily  = $blog['daily'];
            $maxVal = max(1, max(array_column($daily, 'views') ?: [0]));
            // scala "tonda" per l'asse: 1, 2, 5, 10, 20, 50...
            $step = 1;
            foreach ([1, 2, 5] as $m) {
                for ($p = 1; $p <= 100000; $p *= 10) {
                    if ($m * $p * 4 >= $maxVal) { $step = $m * $p; break 2; }
                }
            }
            $step   = max($step, (int) ceil($maxVal / 4));
            $yMax   = $step * 4;
            $w      = 960; $h = 220; $padL = 36; $padB = 24; $padT = 8;
            $plotW  = $w - $padL; $plotH = $h - $padB - $padT;
            $n      = count($daily);
            $slot   = $plotW / max(1, $n);
            $barW   = max(2, min(18, $slot - 2));
            $dateLabel = fn(string $d) => date('d/m', strtotime($d));
            ?>
            <div class="<?= $card ?> p-5">
                <div class="flex items-baseline justify-between gap-2 mb-3">
                    <h3 class="text-sm font-semibold text-slate-100">Letture giornaliere</h3>
                    <p class="text-xs text-slate-500"><?= $dateLabel($blog['from']) ?> – <?= $dateLabel($blog['to']) ?> · picco <?= $fmt($maxVal) ?></p>
                </div>
                <div class="relative" id="blogViewsChart">
                    <svg viewBox="0 0 <?= $w ?> <?= $h ?>" class="w-full h-auto" role="img"
                         aria-label="Letture giornaliere del blog negli ultimi <?= $blog['days'] ?> giorni, totale <?= $fmt($blog['views_total']) ?>">
                        <?php for ($i = 0; $i <= 4; $i++):
                            $val = $step * $i;
                            $y = $padT + $plotH - ($val / $yMax) * $plotH; ?>
                            <line x1="<?= $padL ?>" x2="<?= $w ?>" y1="<?= $y ?>" y2="<?= $y ?>"
                                  stroke="#1e293b" stroke-width="1" <?= $i === 0 ? '' : 'stroke-dasharray="2 4"' ?>/>
                            <text x="<?= $padL - 8 ?>" y="<?= $y + 4 ?>" text-anchor="end" font-size="11" fill="#64748b"><?= $fmt($val) ?></text>
                        <?php endfor; ?>

                        <?php foreach ($daily as $i => $day):
                            $bh = ($day['views'] / $yMax) * $plotH;
                            $x  = $padL + $i * $slot + ($slot - $barW) / 2;
                            $y  = $padT + $plotH - $bh;
                            $r  = min(4, $barW / 2, $bh);
                            ?>
                            <g class="chart-bar" data-label="<?= $e(date('d/m/Y', strtotime($day['date']))) ?>" data-value="<?= (int) $day['views'] ?>">
                                <!-- area di hover più larga della barra -->
                                <rect x="<?= $padL + $i * $slot ?>" y="<?= $padT ?>" width="<?= $slot ?>" height="<?= $plotH ?>" fill="transparent"/>
                                <?php if ($day['views'] > 0): ?>
                                    <path d="M<?= $x ?>,<?= $padT + $plotH ?> V<?= $y + $r ?> Q<?= $x ?>,<?= $y ?> <?= $x + $r ?>,<?= $y ?> H<?= $x + $barW - $r ?> Q<?= $x + $barW ?>,<?= $y ?> <?= $x + $barW ?>,<?= $y + $r ?> V<?= $padT + $plotH ?> Z"
                                          fill="#a78bfa" class="transition-opacity"/>
                                <?php endif; ?>
                            </g>
                        <?php endforeach; ?>

                        <?php foreach (array_unique([0, intdiv($n - 1, 2), $n - 1]) as $i):
                            if (!isset($daily[$i])) continue;
                            $x = $padL + $i * $slot + $slot / 2;
                            $anchor = $i === 0 ? 'start' : ($i === $n - 1 ? 'end' : 'middle');
                            if ($i === 0) $x = $padL; elseif ($i === $n - 1) $x = $w; ?>
                            <text x="<?= $x ?>" y="<?= $h - 6 ?>" text-anchor="<?= $anchor ?>" font-size="11" fill="#64748b"><?= $dateLabel($daily[$i]['date']) ?></text>
                        <?php endforeach; ?>
                    </svg>
                    <div id="blogViewsTooltip" class="pointer-events-none absolute hidden rounded-lg border border-slate-700 bg-slate-950/95 px-2.5 py-1.5 text-xs shadow-xl" role="status">
                        <span class="block text-slate-400" data-tt-label></span>
                        <span class="block font-semibold text-slate-100" data-tt-value></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                <!-- Articoli più letti -->
                <div class="<?= $card ?> lg:col-span-3">
                    <div class="px-5 pt-5 pb-3">
                        <h3 class="text-sm font-semibold text-slate-100">Articoli più letti</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Nel periodo selezionato · tra parentesi il totale da sempre</p>
                    </div>
                    <?php if (empty($blog['top_posts'])): ?>
                        <p class="px-5 pb-6 text-sm text-slate-500">Nessuna lettura registrata nel periodo.</p>
                    <?php else: ?>
                        <?php $topMax = max(1, (int) $blog['top_posts'][0]['views']); ?>
                        <ol class="divide-y divide-slate-800">
                            <?php foreach ($blog['top_posts'] as $i => $p): ?>
                                <li class="px-5 py-3">
                                    <div class="flex items-center justify-between gap-3 text-sm">
                                        <a href="<?= BASE_URL ?>/blog/<?= $e($p['slug']) ?>" target="_blank"
                                           class="min-w-0 truncate text-slate-200 hover:text-purple-300 transition">
                                            <span class="text-slate-500 tabular-nums mr-1"><?= $i + 1 ?>.</span><?= $e($p['title']) ?>
                                        </a>
                                        <span class="shrink-0 tabular-nums text-slate-100 font-medium">
                                            <?= $fmt($p['views']) ?> <span class="text-slate-500 font-normal">(<?= $fmt($p['views_all_time']) ?>)</span>
                                        </span>
                                    </div>
                                    <div class="mt-1.5 h-1.5 rounded-full bg-slate-800 overflow-hidden" aria-hidden="true">
                                        <div class="h-full rounded-full bg-violet-400" style="width: <?= round($p['views'] / $topMax * 100, 1) ?>%"></div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php endif; ?>
                </div>

                <!-- Sorgenti di traffico -->
                <div class="<?= $card ?> lg:col-span-2">
                    <div class="px-5 pt-5 pb-3">
                        <h3 class="text-sm font-semibold text-slate-100">Da dove arrivano i lettori</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Sito di provenienza (referrer)</p>
                    </div>
                    <?php if (empty($blog['sources'])): ?>
                        <p class="px-5 pb-6 text-sm text-slate-500">Nessun dato nel periodo.</p>
                    <?php else: ?>
                        <?php $srcTotal = max(1, array_sum(array_column($blog['sources'], 'views'))); ?>
                        <ul class="px-5 pb-5 space-y-3">
                            <?php foreach ($blog['sources'] as $src):
                                $pct = $src['views'] / $srcTotal * 100; ?>
                                <li>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-200 truncate"><?= $e($src['source']) ?></span>
                                        <span class="tabular-nums text-slate-400"><?= $fmt($src['views']) ?> · <?= number_format($pct, 0) ?>%</span>
                                    </div>
                                    <div class="mt-1.5 h-1.5 rounded-full bg-slate-800 overflow-hidden" aria-hidden="true">
                                        <div class="h-full rounded-full bg-sky-400" style="width: <?= round($pct, 1) ?>%"></div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
            <!-- Per area -->
            <div class="<?= $card ?> lg:col-span-2">
                <div class="px-5 pt-5 pb-3">
                    <h3 class="text-sm font-semibold text-slate-100">Per area</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Articoli pubblicati, bozze e letture nel periodo</p>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-slate-500">
                            <th class="px-5 py-2 text-left font-medium">Area</th>
                            <th class="px-2 py-2 text-right font-medium">Pubbl.</th>
                            <th class="px-2 py-2 text-right font-medium">Bozze</th>
                            <th class="px-5 py-2 text-right font-medium">Letture</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <?php foreach ($blog['by_course'] as $row): ?>
                            <tr>
                                <td class="px-5 py-2.5 text-slate-200"><?= $e($row['name']) ?></td>
                                <td class="px-2 py-2.5 text-right tabular-nums text-slate-200"><?= $fmt($row['published']) ?></td>
                                <td class="px-2 py-2.5 text-right tabular-nums text-slate-400"><?= $fmt($row['drafts']) ?></td>
                                <td class="px-5 py-2.5 text-right tabular-nums text-slate-200"><?= $blog['available'] ? $fmt($row['views'] ?? 0) : '—' ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($blog['by_course'])): ?>
                            <tr><td colspan="4" class="px-5 py-6 text-center text-slate-500">Nessun articolo.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Salute dei contenuti -->
            <div class="<?= $card ?> lg:col-span-3">
                <div class="px-5 pt-5 pb-3">
                    <h3 class="text-sm font-semibold text-slate-100">Da sistemare</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Controlli SEO ed editoriali sugli articoli · apri una voce per vedere quali</p>
                </div>
                <?php
                $checks = $blog['health'];
                if ($blog['available']) {
                    $checks[] = [
                        'label' => 'Nessuna lettura nel periodo',
                        'hint'  => 'Pubblicati da più di una settimana: da promuovere o aggiornare.',
                        'posts' => $blog['zero_view_posts'],
                    ];
                }
                ?>
                <ul class="divide-y divide-slate-800">
                    <?php foreach ($checks as $check):
                        $count = count($check['posts']); ?>
                        <li>
                            <details class="group">
                                <summary class="flex cursor-pointer list-none items-center gap-3 px-5 py-3 hover:bg-slate-800/30 transition <?= $count === 0 ? 'pointer-events-none' : '' ?>">
                                    <?php if ($count === 0): ?>
                                        <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/30" aria-hidden="true">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/30 text-[11px] font-bold" aria-hidden="true">!</span>
                                    <?php endif; ?>
                                    <span class="min-w-0 flex-1">
                                        <span class="block text-sm text-slate-200"><?= $e($check['label']) ?></span>
                                        <span class="block text-xs text-slate-500"><?= $e($check['hint']) ?></span>
                                    </span>
                                    <span class="shrink-0 tabular-nums text-sm <?= $count ? 'text-amber-300 font-semibold' : 'text-slate-500' ?>">
                                        <?= $count ? $count : 'OK' ?>
                                    </span>
                                    <?php if ($count): ?>
                                        <svg class="h-4 w-4 shrink-0 text-slate-500 transition-transform group-open:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                    <?php endif; ?>
                                </summary>
                                <?php if ($count): ?>
                                    <ul class="px-5 pb-3 space-y-1">
                                        <?php foreach (array_slice($check['posts'], 0, 12) as $p): ?>
                                            <li class="pl-8">
                                                <a href="<?= BASE_URL ?>/cms/blog/edit/<?= (int) $p['id'] ?>"
                                                   class="text-xs text-purple-300 hover:text-purple-200 transition">
                                                    <?= $e($p['title']) ?> →
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                        <?php if ($count > 12): ?>
                                            <li class="pl-8 text-xs text-slate-500">…e altri <?= $count - 12 ?></li>
                                        <?php endif; ?>
                                    </ul>
                                <?php endif; ?>
                            </details>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <script>
        // Tooltip del grafico letture: segue la barra sotto il cursore (o il dito)
        (function () {
            const chart = document.getElementById("blogViewsChart");
            const tip = document.getElementById("blogViewsTooltip");
            if (!chart || !tip) return;
            const label = tip.querySelector("[data-tt-label]");
            const value = tip.querySelector("[data-tt-value]");
            chart.querySelectorAll(".chart-bar").forEach((bar) => {
                bar.addEventListener("pointerenter", () => {
                    const box = bar.getBoundingClientRect();
                    const host = chart.getBoundingClientRect();
                    label.textContent = bar.dataset.label;
                    value.textContent = `${bar.dataset.value} letture`;
                    tip.classList.remove("hidden");
                    const left = Math.min(Math.max(box.left - host.left + box.width / 2 - tip.offsetWidth / 2, 0), host.width - tip.offsetWidth);
                    tip.style.left = `${left}px`;
                    tip.style.top = "0px";
                    chart.querySelectorAll(".chart-bar path").forEach((p) => p.style.opacity = p.closest(".chart-bar") === bar ? "1" : "0.45");
                });
            });
            chart.addEventListener("pointerleave", () => {
                tip.classList.add("hidden");
                chart.querySelectorAll(".chart-bar path").forEach((p) => p.style.opacity = "1");
            });
        })();
        </script>
    <?php endif; ?>
</section>
