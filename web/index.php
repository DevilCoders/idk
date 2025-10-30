<?php
$manifest = json_decode(file_get_contents(__DIR__ . '/../config/dependency_manifest.json'), true);
$pipelineSnapshotPath = __DIR__ . '/assets/js/pipeline_snapshot.json';
$pipelineSnapshot = [];
if (file_exists($pipelineSnapshotPath)) {
    $pipelineSnapshot = json_decode(file_get_contents($pipelineSnapshotPath), true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Unified DevOps Control Plane</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="assets/js/dashboard.js"></script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">
    <header class="bg-slate-900 shadow-lg">
        <div class="max-w-6xl mx-auto py-6 px-4">
            <h1 class="text-3xl font-bold">Unified DevOps Control Plane</h1>
            <p class="text-slate-400">Single source of truth for multi-language delivery.</p>
        </div>
    </header>
    <main class="max-w-6xl mx-auto py-10 px-4 space-y-12">
        <section class="bg-slate-900 rounded-xl p-6 border border-slate-800">
            <h2 class="text-xl font-semibold mb-4">Deployment Modes</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php
                $editions = [
                    ['title' => 'Self-Hosted', 'desc' => 'Air-gapped friendly, enterprise integrations.'],
                    ['title' => 'Cloud Native', 'desc' => 'Multi-tenant, auto-scaling Kubernetes deployment.'],
                    ['title' => 'Developer', 'desc' => 'Lightweight local experience with a free tier.'],
                ];
                foreach ($editions as $edition): ?>
                <article class="bg-slate-950 rounded-lg p-4 border border-slate-800">
                    <h3 class="text-lg font-semibold"><?= htmlspecialchars($edition['title']) ?></h3>
                    <p class="text-slate-400 text-sm"><?= htmlspecialchars($edition['desc']) ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
        <section class="bg-slate-900 rounded-xl p-6 border border-slate-800">
            <h2 class="text-xl font-semibold mb-4">Unified Dependency Manifest</h2>
            <ul class="space-y-2">
                <?php foreach ($manifest['dependencies'] as $dependency): ?>
                    <li class="flex justify-between bg-slate-950 px-4 py-2 rounded-lg border border-slate-800">
                        <span class="font-mono"><?= htmlspecialchars($dependency['name']) ?>@<?= htmlspecialchars($dependency['version']) ?></span>
                        <span class="text-slate-400 text-sm">License: <?= htmlspecialchars($dependency['license']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <section class="bg-slate-900 rounded-xl p-6 border border-slate-800">
            <h2 class="text-xl font-semibold mb-4">Live Pipeline Snapshot</h2>
            <div id="pipeline-status" data-payload='<?= json_encode($pipelineSnapshot, JSON_HEX_APOS | JSON_HEX_TAG) ?>' class="grid gap-4 md:grid-cols-2"></div>
        </section>
    </main>
    <footer class="text-center text-slate-500 text-xs py-8">
        &copy; <?= date('Y') ?> Unified Platform. Enterprise ready automation.
    </footer>
</body>
</html>
