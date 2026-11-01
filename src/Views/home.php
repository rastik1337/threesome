<?php

/** @var array{id: int, login: string, role: string}|null $user */
?>
<h1>Galerie</h1>

<?php if ($user !== null): ?>
    <section>
        <h2>Nahrát fotografii</h2>
        <form action="/gallery/upload" method="POST" enctype="multipart/form-data">
            <div>
                <label for="photo">Fotografie:</label>
                <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png,.gif,.bmp,.tif,.tiff" required>
            </div>
            <div>
                <label for="description">Popisek:</label>
                <textarea id="description" name="description" rows="2"></textarea>
            </div>
            <button type="submit">Nahrát</button>
        </form>
    </section>
<?php endif; ?>

<section>
    <h2>Příspěvky</h2>
    <?php if (empty($images)): ?>
        <p>Zatím nebyly nahrány žádné fotografie.</p>
    <?php else: ?>
        <div>
            <?php foreach ($images as $image): ?>
                <article>
                    <img src="/uploads/<?= htmlspecialchars($image->file_path) ?>" alt="<?= htmlspecialchars($image->description ?? 'Fotografie') ?>" width="400">
                    <div>
                        <p><strong><?= htmlspecialchars($image->author_login ?? '') ?></strong>: <?= htmlspecialchars($image->description ?? '') ?></p>
                        <small><?= $image->created_at->format('d.m.Y H:i') ?></small>
                        <?php 
                        $isOwner = $user !== null && $image->user_id === $user['id'];
                        $isAdmin = $user !== null && in_array(strtolower($user['role'] ?? ''), ['admin', 'správce'], true);
                        if ($isOwner || $isAdmin): 
                        ?>
                            <form action="/gallery/delete" method="POST" onsubmit="return confirm('Opravdu chcete smazat tuto fotografii?');">
                                <input type="hidden" name="image_id" value="<?= $image->image_id ?>">
                                <button type="submit">Smazat</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
