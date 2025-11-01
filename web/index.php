<?php
$manifest = json_decode(file_get_contents(__DIR__ . '/../config/dependency_manifest.json'), true);
$pipelineSnapshotPath = __DIR__ . '/assets/js/pipeline_snapshot.json';
$pipelineSnapshot = [];
if (file_exists($pipelineSnapshotPath)) {
    $pipelineSnapshot = json_decode(file_get_contents($pipelineSnapshotPath), true);
}

$languages = [
    [
        'name' => 'Python',
        'manager' => 'pip · poetry',
        'coverage' => '91%',
        'frameworks' => ['FastAPI', 'Django', 'Pydantic'],
    ],
    [
        'name' => 'JavaScript',
        'manager' => 'npm · pnpm',
        'coverage' => '87%',
        'frameworks' => ['React', 'Next.js', 'Vue'],
    ],
    [
        'name' => 'Java',
        'manager' => 'Maven · Gradle',
        'coverage' => '78%',
        'frameworks' => ['Spring Boot', 'Quarkus'],
    ],
    [
        'name' => 'Go',
        'manager' => 'go modules',
        'coverage' => '72%',
        'frameworks' => ['Gin', 'Fiber'],
    ],
];

$scanModules = [
    [
        'title' => 'Runtime Security Matrix',
        'status' => 'Continuous',
        'description' => 'SBOM validation, CVE drift detection, runtime policy attestation.',
        'vector' => 'Container · Serverless · Edge',
    ],
    [
        'title' => 'License Guardian',
        'status' => 'Strict',
        'description' => 'Detects risky legal exposure and enforces governance contracts.',
        'vector' => 'Proprietary · Copyleft · SaaS',
    ],
    [
        'title' => 'Code Health Sentinel',
        'status' => 'Adaptive',
        'description' => 'Signals technical debt hotspots, lints, and coverage deltas.',
        'vector' => 'Static · Dynamic · Supply Chain',
    ],
    [
        'title' => 'Monorepo Atlas',
        'status' => 'Planetary',
        'description' => 'Shards large repositories, orchestrates parallel scanners, and reconciles findings at petabyte scale.',
        'vector' => 'Local · GitHub · GitLab',
    ],
];

$repositoryTargets = [
    [
        'name' => 'Core Platform Monolith',
        'type' => 'Local',
        'location' => '/srv/repos/core-platform',
        'scale' => '18 GB / 215k files',
        'lastScan' => '27m ago',
    ],
    [
        'name' => 'github.com/enterprise/mega-suite',
        'type' => 'GitHub',
        'location' => 'https://github.com/enterprise/mega-suite',
        'scale' => '12 GB / 143k files',
        'lastScan' => '1h ago',
    ],
    [
        'name' => 'gitlab.com/enterprise/fleet-core',
        'type' => 'GitLab',
        'location' => 'https://gitlab.com/enterprise/fleet-core',
        'scale' => '9 GB / 97k files',
        'lastScan' => '3h ago',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Unified DevOps Control Plane</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        midnight: '#050505',
                        graphite: '#101010',
                        onyx: '#161616',
                        ember: '#8A0303',
                        emberGlow: '#B31312',
                    },
                    boxShadow: {
                        plasma: '0 20px 45px -20px rgba(138, 3, 3, 0.55)',
                        crimson: '0 0 0 1px rgba(179, 19, 18, 0.35), 0 18px 45px -18px rgba(179, 19, 18, 0.6)',
                    },
                    backgroundImage: {
                        mesh: 'radial-gradient(circle at 15% 20%, rgba(179,19,18,0.35), transparent 45%), radial-gradient(circle at 80% 10%, rgba(138,3,3,0.4), transparent 40%), radial-gradient(circle at 50% 80%, rgba(179,19,18,0.15), transparent 55%)',
                    },
                },
            },
        };
    </script>
    <script defer src="assets/js/dashboard.js"></script>
</head>
<body class="bg-midnight text-slate-100 min-h-screen">
    <div class="relative">
        <div class="pointer-events-none absolute inset-0 bg-mesh opacity-80"></div>
        <header class="relative z-10 border-b border-onyx bg-gradient-to-b from-onyx/95 to-midnight/70 backdrop-blur">
            <div class="max-w-7xl mx-auto px-6 py-10">
                <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="uppercase tracking-[0.35em] text-xs text-ember/70">Unified Control Plane</p>
                        <h1 class="mt-2 text-4xl font-semibold sm:text-5xl">Dependency &amp; Supply Chain Observatory</h1>
                        <p class="mt-4 max-w-2xl text-sm text-slate-400">A modular cockpit for orchestrating multi-language dependency risk, compliance, and telemetry in real-time.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4 rounded-2xl border border-onyx bg-onyx/70 p-6 shadow-crimson md:grid-cols-3 xl:grid-cols-5">
                        <div>
                            <p class="text-xs uppercase tracking-widest text-emberGlow/70">Active Integrations</p>
                            <p class="mt-2 text-3xl font-semibold"><?= count($manifest['dependencies']) + count($manifest['services']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-widest text-emberGlow/70">Languages Secured</p>
                            <p class="mt-2 text-3xl font-semibold"><?= count($languages) ?></p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-widest text-emberGlow/70">Mono Targets</p>
                            <p class="mt-2 text-3xl font-semibold"><?= count($repositoryTargets) ?></p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-widest text-emberGlow/70">Compliance Score</p>
                            <p class="mt-2 text-3xl font-semibold">98.4%</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-widest text-emberGlow/70">Last Scan</p>
                            <p class="mt-2 text-3xl font-semibold">4m ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="relative z-10 max-w-7xl mx-auto px-6 py-12 space-y-12">
            <section class="rounded-3xl border border-onyx bg-onyx/80 p-8 shadow-crimson">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <div class="max-w-xl">
                        <h2 class="text-2xl font-semibold">Orchestrated Dependency Posture</h2>
                        <p class="mt-3 text-sm text-slate-400">Aggregate insights from polyglot ecosystems. Drill into any runtime to trace vulnerable packages, license conflicts, and drift signatures.</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="inline-flex h-3 w-3 rounded-full bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.8)]"></span>
                        <p class="text-xs uppercase tracking-widest text-slate-400">Live Telemetry Synced</p>
                    </div>
                </div>
                <div class="mt-8 grid gap-6 lg:grid-cols-2">
                    <article class="rounded-2xl border border-onyx bg-midnight/80 p-6 shadow-plasma">
                        <h3 class="text-lg font-semibold text-emberGlow">Manifest Overview</h3>
                        <p class="mt-2 text-sm text-slate-400">Cross-language dependencies declared in the global manifest.</p>
                        <ul class="mt-4 space-y-3" id="dependency-overview">
                            <?php foreach ($manifest['dependencies'] as $dependency): ?>
                            <li class="flex items-center justify-between rounded-xl border border-onyx bg-onyx/90 px-4 py-3">
                                <span class="font-mono text-sm text-slate-200"><?= htmlspecialchars($dependency['name']) ?>@<?= htmlspecialchars($dependency['version']) ?></span>
                                <span class="text-xs text-slate-400">License: <?= htmlspecialchars($dependency['license']) ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </article>
                    <article class="rounded-2xl border border-onyx bg-midnight/80 p-6 shadow-plasma">
                        <h3 class="text-lg font-semibold text-emberGlow">Language Guardrails</h3>
                        <p class="mt-2 text-sm text-slate-400">Active ecosystems under watch. Coverage indicates automated rule penetration.</p>
                        <div class="mt-4 grid gap-3" id="language-matrix" data-languages='<?= json_encode($languages, JSON_HEX_APOS | JSON_HEX_TAG) ?>'>
                            <?php foreach ($languages as $language): ?>
                            <div class="rounded-xl border border-onyx bg-onyx/90 p-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-base font-semibold text-slate-100"><?= htmlspecialchars($language['name']) ?></h4>
                                    <span class="text-xs font-semibold text-emberGlow/80">Coverage <?= htmlspecialchars($language['coverage']) ?></span>
                                </div>
                                <p class="mt-2 text-xs text-slate-400">Managers: <?= htmlspecialchars($language['manager']) ?></p>
                                <p class="mt-2 text-xs text-slate-500">Frameworks: <?= htmlspecialchars(implode(', ', $language['frameworks'])) ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </article>
                </div>
            </section>

            <section class="rounded-3xl border border-onyx bg-onyx/80 p-8 shadow-crimson">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold">Adaptive Scan Modules</h2>
                        <p class="mt-2 text-sm text-slate-400">Compose modular scan flows tuned per environment. Toggle modules for any delivery ring.</p>
                    </div>
                    <button class="inline-flex items-center gap-2 rounded-full border border-ember/60 bg-ember/20 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-emberGlow transition hover:bg-ember/30">
                        Configure Pipelines
                    </button>
                </div>
                <div class="mt-6 grid gap-6 md:grid-cols-3">
                    <?php foreach ($scanModules as $module): ?>
                    <article class="group rounded-2xl border border-onyx bg-midnight/80 p-6 shadow-plasma transition duration-300 hover:-translate-y-1 hover:border-ember/60 hover:shadow-crimson">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-slate-100"><?= htmlspecialchars($module['title']) ?></h3>
                            <span class="text-[0.65rem] uppercase tracking-[0.3em] text-emberGlow/70"><?= htmlspecialchars($module['status']) ?></span>
                        </div>
                        <p class="mt-3 text-sm text-slate-400"><?= htmlspecialchars($module['description']) ?></p>
                        <p class="mt-4 text-xs uppercase tracking-widest text-slate-500">Vectors: <?= htmlspecialchars($module['vector']) ?></p>
                    </article>
                    <?php endforeach; ?>
                </div>
                <div class="mt-8 grid gap-6 lg:grid-cols-2">
                    <article class="rounded-2xl border border-onyx bg-midnight/80 p-6 shadow-plasma">
                        <h3 class="text-lg font-semibold text-emberGlow">Live Pipeline Snapshot</h3>
                        <p class="mt-2 text-sm text-slate-400">Real-time stage progression of the latest delivery pipeline execution.</p>
                        <div id="pipeline-status" data-payload='<?= json_encode($pipelineSnapshot, JSON_HEX_APOS | JSON_HEX_TAG) ?>' class="mt-5 grid gap-4"></div>
                    </article>
                    <article class="rounded-2xl border border-onyx bg-midnight/80 p-6 shadow-plasma">
                        <h3 class="text-lg font-semibold text-emberGlow">Telemetry Pulse</h3>
                        <div class="mt-4 grid gap-4 text-sm text-slate-300">
                            <div class="flex items-center justify-between">
                                <span>Critical CVEs neutralized</span>
                                <span class="rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-300">12</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Pending remediation windows</span>
                                <span class="rounded-full bg-amber-500/20 px-3 py-1 text-xs font-semibold text-amber-300">3</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Compliance waivers active</span>
                                <span class="rounded-full bg-slate-500/20 px-3 py-1 text-xs font-semibold text-slate-200">5</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Policy drift anomalies</span>
                                <span class="rounded-full bg-ember/25 px-3 py-1 text-xs font-semibold text-emberGlow">2</span>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <section class="rounded-3xl border border-onyx bg-onyx/80 p-8 shadow-crimson">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <div class="max-w-xl">
                        <h2 class="text-2xl font-semibold">Large Monorepo Observatory</h2>
                        <p class="mt-3 text-sm text-slate-400">Streamline coverage across massive single-repo estates. Stage and hydrate monolithic worktrees locally or federate across GitHub and GitLab to keep security, licensing, and quality telemetry harmonized.</p>
                    </div>
                    <div class="flex items-center gap-3 rounded-2xl border border-ember/40 bg-ember/10 px-4 py-3 text-xs text-emberGlow">
                        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500 shadow-[0_0_12px_rgba(16,185,129,0.7)]"></span>
                        Adaptive chunking &amp; distributed scanning engaged
                    </div>
                </div>
                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    <?php foreach ($repositoryTargets as $target): ?>
                    <article class="rounded-2xl border border-onyx bg-midnight/80 p-6 shadow-plasma">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-semibold text-slate-100"><?= htmlspecialchars($target['name']) ?></h3>
                            <span class="text-[0.65rem] uppercase tracking-[0.3em] text-emberGlow/70"><?= htmlspecialchars($target['type']) ?></span>
                        </div>
                        <p class="mt-3 text-xs text-slate-400">Location · <span class="font-mono text-slate-200"><?= htmlspecialchars($target['location']) ?></span></p>
                        <p class="mt-3 text-xs text-slate-500">Scale · <?= htmlspecialchars($target['scale']) ?></p>
                        <p class="mt-4 text-xs uppercase tracking-widest text-slate-500">Last scan <?= htmlspecialchars($target['lastScan']) ?></p>
                    </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <section id="dependency-config" class="rounded-3xl border border-onyx bg-onyx/80 p-8 shadow-crimson" data-dependencies='<?= json_encode($manifest['dependencies'], JSON_HEX_APOS | JSON_HEX_TAG) ?>' data-repositories='<?= json_encode($repositoryTargets, JSON_HEX_APOS | JSON_HEX_TAG) ?>'>
                <div class="flex flex-col gap-3">
                    <h2 class="text-2xl font-semibold">Dependency &amp; Language Configuration Hub</h2>
                    <p class="text-sm text-slate-400">Curate dependency policies and extend scanning languages. Configure version pinning, licensing posture, runtime coverage, and large-repo orchestration with modular playbooks.</p>
                </div>
                <div class="mt-6 flex flex-wrap gap-3 border-b border-onyx/80 pb-4" role="tablist">
                    <button class="tab-button active inline-flex items-center gap-2 rounded-full border border-ember/60 bg-ember/20 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-emberGlow transition hover:bg-ember/30" data-target="dependency-panel">Dependencies</button>
                    <button class="tab-button inline-flex items-center gap-2 rounded-full border border-onyx bg-midnight/60 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-300 transition hover:border-ember/60 hover:text-emberGlow" data-target="language-panel">Languages</button>
                    <button class="tab-button inline-flex items-center gap-2 rounded-full border border-onyx bg-midnight/60 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-300 transition hover:border-ember/60 hover:text-emberGlow" data-target="repository-panel">Codebases</button>
                </div>
                <div class="mt-6 space-y-8">
                    <div id="dependency-panel" class="tab-panel space-y-6">
                        <form id="dependency-form" class="grid gap-4 rounded-2xl border border-onyx bg-midnight/80 p-6 shadow-plasma md:grid-cols-4">
                            <div class="md:col-span-2">
                                <label class="text-xs uppercase tracking-widest text-slate-500">Name</label>
                                <input type="text" name="name" required class="mt-2 w-full rounded-lg border border-onyx bg-onyx/90 px-3 py-2 text-sm text-slate-200 focus:border-emberGlow focus:outline-none focus:ring-1 focus:ring-emberGlow" placeholder="package identifier">
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-widest text-slate-500">Version</label>
                                <input type="text" name="version" required class="mt-2 w-full rounded-lg border border-onyx bg-onyx/90 px-3 py-2 text-sm text-slate-200 focus:border-emberGlow focus:outline-none focus:ring-1 focus:ring-emberGlow" placeholder="x.y.z">
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-widest text-slate-500">License</label>
                                <input type="text" name="license" required class="mt-2 w-full rounded-lg border border-onyx bg-onyx/90 px-3 py-2 text-sm text-slate-200 focus:border-emberGlow focus:outline-none focus:ring-1 focus:ring-emberGlow" placeholder="MIT">
                            </div>
                            <div class="md:col-span-4 flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-2 rounded-full border border-ember/60 bg-ember/30 px-5 py-2 text-xs font-semibold uppercase tracking-widest text-emberGlow transition hover:bg-ember/40">Add dependency</button>
                            </div>
                        </form>
                        <div class="overflow-hidden rounded-2xl border border-onyx bg-midnight/70 shadow-plasma">
                            <table class="min-w-full divide-y divide-onyx/80 text-left text-sm">
                                <thead class="text-xs uppercase tracking-widest text-slate-500">
                                    <tr>
                                        <th class="px-6 py-3">Dependency</th>
                                        <th class="px-6 py-3">Version</th>
                                        <th class="px-6 py-3">License</th>
                                    </tr>
                                </thead>
                                <tbody id="dependency-table" class="divide-y divide-onyx/80 text-sm text-slate-200">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="language-panel" class="tab-panel hidden space-y-6">
                        <form id="language-form" class="grid gap-4 rounded-2xl border border-onyx bg-midnight/80 p-6 shadow-plasma md:grid-cols-4">
                            <div>
                                <label class="text-xs uppercase tracking-widest text-slate-500">Language</label>
                                <input type="text" name="name" required class="mt-2 w-full rounded-lg border border-onyx bg-onyx/90 px-3 py-2 text-sm text-slate-200 focus:border-emberGlow focus:outline-none focus:ring-1 focus:ring-emberGlow" placeholder="Rust">
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-widest text-slate-500">Package Managers</label>
                                <input type="text" name="manager" required class="mt-2 w-full rounded-lg border border-onyx bg-onyx/90 px-3 py-2 text-sm text-slate-200 focus:border-emberGlow focus:outline-none focus:ring-1 focus:ring-emberGlow" placeholder="cargo">
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-widest text-slate-500">Coverage</label>
                                <input type="text" name="coverage" required class="mt-2 w-full rounded-lg border border-onyx bg-onyx/90 px-3 py-2 text-sm text-slate-200 focus:border-emberGlow focus:outline-none focus:ring-1 focus:ring-emberGlow" placeholder="88%">
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-widest text-slate-500">Frameworks</label>
                                <input type="text" name="frameworks" required class="mt-2 w-full rounded-lg border border-onyx bg-onyx/90 px-3 py-2 text-sm text-slate-200 focus:border-emberGlow focus:outline-none focus:ring-1 focus:ring-emberGlow" placeholder="Actix, Rocket">
                            </div>
                            <div class="md:col-span-4 flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-2 rounded-full border border-ember/60 bg-ember/30 px-5 py-2 text-xs font-semibold uppercase tracking-widest text-emberGlow transition hover:bg-ember/40">Add language</button>
                            </div>
                        </form>
                        <div class="grid gap-4" id="language-cards"></div>
                    </div>
                    <div id="repository-panel" class="tab-panel hidden space-y-6">
                        <form id="repository-form" class="grid gap-4 rounded-2xl border border-onyx bg-midnight/80 p-6 shadow-plasma md:grid-cols-5">
                            <div class="md:col-span-2">
                                <label class="text-xs uppercase tracking-widest text-slate-500">Repository</label>
                                <input type="text" name="name" required class="mt-2 w-full rounded-lg border border-onyx bg-onyx/90 px-3 py-2 text-sm text-slate-200 focus:border-emberGlow focus:outline-none focus:ring-1 focus:ring-emberGlow" placeholder="enterprise/monolith">
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-widest text-slate-500">Surface</label>
                                <select name="type" required class="mt-2 w-full rounded-lg border border-onyx bg-onyx/90 px-3 py-2 text-sm text-slate-200 focus:border-emberGlow focus:outline-none focus:ring-1 focus:ring-emberGlow">
                                    <option value="Local">Local</option>
                                    <option value="GitHub">GitHub</option>
                                    <option value="GitLab">GitLab</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs uppercase tracking-widest text-slate-500">Location</label>
                                <input type="text" name="location" required class="mt-2 w-full rounded-lg border border-onyx bg-onyx/90 px-3 py-2 text-sm text-slate-200 focus:border-emberGlow focus:outline-none focus:ring-1 focus:ring-emberGlow" placeholder="/srv/repos/monolith or https://github.com/org/repo">
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-widest text-slate-500">Scale</label>
                                <input type="text" name="scale" required class="mt-2 w-full rounded-lg border border-onyx bg-onyx/90 px-3 py-2 text-sm text-slate-200 focus:border-emberGlow focus:outline-none focus:ring-1 focus:ring-emberGlow" placeholder="14 GB / 160k files">
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-widest text-slate-500">Last Scan</label>
                                <input type="text" name="lastScan" required class="mt-2 w-full rounded-lg border border-onyx bg-onyx/90 px-3 py-2 text-sm text-slate-200 focus:border-emberGlow focus:outline-none focus:ring-1 focus:ring-emberGlow" placeholder="Just now">
                            </div>
                            <div class="md:col-span-5 flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-2 rounded-full border border-ember/60 bg-ember/30 px-5 py-2 text-xs font-semibold uppercase tracking-widest text-emberGlow transition hover:bg-ember/40">Add codebase</button>
                            </div>
                        </form>
                        <div class="overflow-hidden rounded-2xl border border-onyx bg-midnight/70 shadow-plasma">
                            <table class="min-w-full divide-y divide-onyx/80 text-left text-sm">
                                <thead class="text-xs uppercase tracking-widest text-slate-500">
                                    <tr>
                                        <th class="px-6 py-3">Repository</th>
                                        <th class="px-6 py-3">Surface</th>
                                        <th class="px-6 py-3">Location</th>
                                        <th class="px-6 py-3">Scale</th>
                                        <th class="px-6 py-3">Last Scan</th>
                                    </tr>
                                </thead>
                                <tbody id="repository-table" class="divide-y divide-onyx/80 text-sm text-slate-200"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="relative z-10 border-t border-onyx bg-onyx/80 py-8 text-center text-xs text-slate-500">
            &copy; <?= date('Y') ?> Unified Platform · Enterprise-ready automation for every delivery ring.
        </footer>
    </div>
</body>
</html>
