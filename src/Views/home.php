<?php
/** @var array{id: int, login: string, role: string}|null $user */
?>

<div class="max-w-6xl mx-auto space-y-8">

    <!-- Uvítací banner -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-6 sm:p-8 text-white shadow-md flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase bg-white/20 text-blue-100 backdrop-blur-sm mb-2">
                Fotogalerie
            </span>
            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight">Galerie příspěvků</h1>
            <p class="text-sm text-blue-100 mt-1 max-w-md">
                Prohlížejte a sdílejte fotografie s ostatními uživateli.
            </p>
        </div>

        <?php if ($user !== null): ?>
            <a href="/profile" class="px-4 py-2.5 bg-white text-blue-700 hover:bg-blue-50 font-bold text-sm rounded-xl shadow transition shrink-0">
                Můj profil &rarr;
            </a>
        <?php endif; ?>
    </div>

    <!-- 1. ROZŠÍŘENÍ: Formulář pro přihlášeného uživatele -->
    <?php if ($user !== null): ?>
        <section class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-black">+</span>
                Nahrát fotografii
            </h2>

            <form action="/gallery/upload" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="photo" class="block text-sm font-semibold text-slate-700 mb-1">Fotografie:</label>
                        <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png,.gif,.bmp,.tif,.tiff" required 
                               class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-xl p-1 bg-slate-50/50">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">Popisek:</label>
                        <textarea id="description" name="description" rows="2" placeholder="Přidejte krátký popisek..." 
                                  class="w-full px-3.5 py-2 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm text-slate-900 transition-colors"></textarea>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" 
                            class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-sm transition cursor-pointer">
                        Nahrát fotografii
                    </button>
                </div>
            </form>
        </section>
    <?php endif; ?>

    <!-- 2. ROZŠÍŘENÍ: Pinterest mřížka s příspěvky -->
    <section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-slate-900">Příspěvky</h2>
            <span class="text-xs text-slate-500 font-medium">Celkem: <?= count($images ?? []) ?></span>
        </div>

        <?php if (empty($images)): ?>
            <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-slate-200">
                <div class="mx-auto w-12 h-12 text-slate-300 mb-3 flex items-center justify-center text-3xl">🖼️</div>
                <h3 class="text-base font-bold text-slate-700">Zatím nebyly nahrány žádné fotografie</h3>
                <p class="text-xs text-slate-400 mt-1">Buďte první a přidejte fotografii z formuláře výše.</p>
            </div>
        <?php else: ?>
            <!-- Pinterest sloupce -->
            <div class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-4 space-y-4">
                <?php foreach ($images as $image): ?>
                    <article class="break-inside-avoid bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 group">
                        
                        <!-- Obrázek -->
                        <div class="relative overflow-hidden bg-slate-100">
                            <img src="/uploads/<?= htmlspecialchars($image->file_path) ?>" 
                                 alt="<?= htmlspecialchars($image->description ?? 'Fotografie') ?>" 
                                 class="w-full h-auto object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>

                        <!-- Info o autorovi a popisek -->
                        <div class="p-4 space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center shrink-0 uppercase">
                                        <?= htmlspecialchars(mb_substr($image->author_login ?? 'U', 0, 1)) ?>
                                    </div>
                                    <span class="text-xs font-bold text-slate-800 truncate">
                                        @<?= htmlspecialchars($image->author_login ?? 'anonym') ?>
                                    </span>
                                </div>
                                <small class="text-[10px] text-slate-400 font-medium shrink-0 pt-1">
                                    <?= $image->created_at->format('d.m.Y H:i') ?>
                                </small>
                            </div>

                            <?php if (!empty($image->description)): ?>
                                <p class="text-xs text-slate-600 leading-relaxed bg-slate-50/80 p-2.5 rounded-lg border border-slate-100">
                                    <?= htmlspecialchars($image->description) ?>
                                </p>
                            <?php endif; ?>

                            <!-- Smazání (pro autora/admina) -->
                            <?php 
                            $isOwner = $user !== null && $image->user_id === $user['id'];
                            $isAdmin = $user !== null && ($user['role'] ?? '') === 'admin';
                            if ($isOwner || $isAdmin): 
                            ?>
                                <div class="pt-2 border-t border-slate-100 flex justify-end">
                                    <form action="/gallery/delete" method="POST" onsubmit="return confirm('Opravdu chcete smazat tuto fotografii?');">
                                        <input type="hidden" name="image_id" value="<?= $image->image_id ?>">
                                        <button type="submit" 
                                                class="px-3 py-1.5 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition cursor-pointer flex items-center gap-1">
                                            <span>🗑️</span> Smazat
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>

                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</div>