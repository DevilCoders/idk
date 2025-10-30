<?php
$docPath = __DIR__ . '/content.json';
if (!file_exists($docPath)) {
    http_response_code(500);
    echo 'Documentation content is missing.';
    exit;
}

$content = json_decode(file_get_contents($docPath), true);
if ($content === null) {
    http_response_code(500);
    echo 'Unable to parse documentation content.';
    exit;
}

$metadata = $content['metadata'] ?? [];
$sections = $content['sections'] ?? [];
$requestedVersion = $_GET['version'] ?? ($metadata['defaultVersion'] ?? null);
$availableVersions = array_map(fn($v) => $v['id'], $metadata['versions'] ?? []);
if ($requestedVersion && !in_array($requestedVersion, $availableVersions, true)) {
    $requestedVersion = $metadata['defaultVersion'] ?? null;
}

function renderBodyBlock(array $block): string
{
    $type = $block['type'] ?? 'paragraph';
    switch ($type) {
        case 'code':
            $lang = htmlspecialchars($block['language'] ?? '');
            $code = htmlspecialchars($block['content'] ?? '');
            return "<pre class=\"code-block\"><code class=\"language-{$lang}\">{$code}</code></pre>";
        case 'checklist':
            $items = '';
            foreach ($block['items'] ?? [] as $item) {
                $items .= '<li class="flex items-start gap-2">'
                    . '<span class="mt-1 text-emerald-500">&#10003;</span>'
                    . '<span>' . htmlspecialchars($item) . '</span></li>';
            }
            return '<ul class="checklist space-y-2">' . $items . '</ul>';
        case 'list':
            $items = '';
            foreach ($block['items'] ?? [] as $item) {
                $items .= '<li>' . htmlspecialchars($item) . '</li>';
            }
            $tag = !empty($block['ordered']) ? 'ol' : 'ul';
            $classes = $tag === 'ol' ? 'list-decimal ms-5 space-y-2' : 'list-disc ms-5 space-y-2';
            return "<{$tag} class=\"{$classes}\">{$items}</{$tag}>";
        case 'quote':
            $quote = htmlspecialchars($block['content'] ?? '');
            return '<blockquote class="border-l-4 border-sky-500 bg-slate-900/40 p-4 italic">' . $quote . '</blockquote>';
        case 'table':
            $headers = $block['headers'] ?? [];
            $rows = $block['rows'] ?? [];
            $thead = '';
            if ($headers) {
                $theadCells = '';
                foreach ($headers as $header) {
                    $theadCells .= '<th class="px-4 py-2 text-left text-slate-300">' . htmlspecialchars($header) . '</th>';
                }
                $thead = '<thead class="bg-slate-800/50">' . $theadCells . '</thead>';
            }
            $tbody = '';
            foreach ($rows as $row) {
                $cells = '';
                foreach ($row as $cell) {
                    $cells .= '<td class="px-4 py-2 align-top border-t border-slate-700/60">' . htmlspecialchars($cell) . '</td>';
                }
                $tbody .= '<tr>' . $cells . '</tr>';
            }
            return '<table class="w-full text-sm border border-slate-800 rounded-lg overflow-hidden">' . $thead . '<tbody>' . $tbody . '</tbody></table>';
        case 'paragraph':
        default:
            $text = htmlspecialchars($block['content'] ?? '');
            return '<p class="leading-relaxed text-slate-200">' . $text . '</p>';
    }
}

$activeSectionId = $_GET['section'] ?? ($sections[0]['id'] ?? '');
$activePageId = $_GET['page'] ?? null;

function filterPagesByVersion(array $pages, ?string $version): array
{
    if (!$version) {
        return $pages;
    }

    return array_values(array_filter($pages, fn($page) => ($page['version'] ?? null) === $version));
}

function findActivePage(array $sections, string $sectionId, ?string $pageId, ?string $version): ?array
{
    foreach ($sections as $section) {
        if (($section['id'] ?? '') !== $sectionId) {
            continue;
        }
        $pages = filterPagesByVersion($section['pages'] ?? [], $version);
        if (!$pages) {
            return null;
        }
        if ($pageId) {
            foreach ($pages as $page) {
                if (($page['id'] ?? '') === $pageId) {
                    return $page;
                }
            }
        }
        return $pages[0];
    }
    return null;
}

$activePage = findActivePage($sections, $activeSectionId, $activePageId, $requestedVersion);
$pageTitle = $activePage['title'] ?? 'Documentation';

?><!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($metadata['projectName'] ?? 'Documentation'); ?> · <?php echo htmlspecialchars($pageTitle); ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css" integrity="sha512-wlUKgFxryuVKyX2A+qsSVqS+8CA0nVddOZXS6jttuPAHyBs+K6TfGsDz3jHK5vVsQt1zAr72Xd1LSeX776BFfQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js" integrity="sha512-VkpXBJgp7wa9u0edPFpKnz03Wx/ju0RduCMsZ/HXXIQK5vCk1vZ1tcHTul3e8DqRLVQjaxAg/P6MqxsVXniPzw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script defer src="/assets/js/docs.js"></script>
</head>
<body class="bg-slate-950 text-slate-100">
    <div class="min-h-screen flex flex-col">
        <header class="border-b border-slate-800 bg-slate-950/90 backdrop-blur">
            <div class="mx-auto max-w-7xl px-6 py-4 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <a href="/docs/index.php" class="text-2xl font-semibold text-sky-400">
                        <?php echo htmlspecialchars($metadata['projectName'] ?? 'Unified Platform'); ?> Docs
                    </a>
                    <p class="text-sm text-slate-400 mt-1">Single source of truth for guides, deep dives, and API references.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                    <form method="get" class="flex items-center gap-3">
                        <input type="hidden" name="section" value="<?php echo htmlspecialchars($activeSectionId); ?>" />
                        <input type="hidden" name="page" value="<?php echo htmlspecialchars($activePage['id'] ?? ''); ?>" />
                        <label class="text-xs uppercase tracking-wide text-slate-400">Version</label>
                        <select name="version" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <?php foreach ($metadata['versions'] ?? [] as $version): ?>
                                <option value="<?php echo htmlspecialchars($version['id']); ?>" <?php echo ($version['id'] === $requestedVersion) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($version['label']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="hidden"></button>
                    </form>
                    <div class="relative">
                        <input id="doc-search" type="search" placeholder="Search docs..." class="w-full sm:w-64 bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
                        <div id="doc-search-results" class="hidden absolute z-10 mt-2 w-full rounded-lg border border-slate-800 bg-slate-900 shadow-xl"></div>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <div class="mx-auto max-w-7xl px-6 py-10 grid gap-10 lg:grid-cols-[280px,1fr]">
                <nav class="space-y-8" aria-label="Sidebar">
                    <?php foreach ($sections as $section): ?>
                        <?php
                        $sectionId = $section['id'] ?? '';
                        $pages = filterPagesByVersion($section['pages'] ?? [], $requestedVersion);
                        if (!$pages) {
                            continue;
                        }
                        ?>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400"><?php echo htmlspecialchars($section['title'] ?? ''); ?></h2>
                                <span class="text-xs text-slate-600"><?php echo count($pages); ?> entries</span>
                            </div>
                            <ul class="space-y-1">
                                <?php foreach ($pages as $page): ?>
                                    <?php
                                    $isActive = ($sectionId === $activeSectionId) && (($page['id'] ?? '') === ($activePage['id'] ?? ''));
                                    $pageUrl = '/docs/index.php?section=' . urlencode($sectionId) . '&page=' . urlencode($page['id']) . '&version=' . urlencode($requestedVersion ?? '');
                                    ?>
                                    <li>
                                        <a href="<?php echo htmlspecialchars($pageUrl); ?>" class="block rounded-lg px-3 py-2 text-sm <?php echo $isActive ? 'bg-slate-800 text-sky-300' : 'text-slate-300 hover:bg-slate-900'; ?>">
                                            <span class="font-medium"><?php echo htmlspecialchars($page['title'] ?? ''); ?></span>
                                            <p class="text-xs text-slate-500 mt-1"><?php echo htmlspecialchars($page['summary'] ?? ''); ?></p>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </nav>

                <article class="prose prose-invert max-w-none" id="doc-content" data-docs='<?php echo json_encode($content, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>' data-version="<?php echo htmlspecialchars($requestedVersion ?? ''); ?>">
                    <div class="flex items-center justify-between gap-6">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-sky-400">Section · <?php echo htmlspecialchars(strtoupper($activeSectionId)); ?></p>
                            <h1 class="mt-2 text-3xl font-semibold text-white"><?php echo htmlspecialchars($activePage['title'] ?? 'Select a document'); ?></h1>
                            <p class="mt-3 text-slate-400 max-w-2xl"><?php echo htmlspecialchars($activePage['summary'] ?? 'Choose a page from the navigation to get started.'); ?></p>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-slate-500">
                            <span class="inline-flex items-center gap-2 rounded-full border border-slate-800 px-3 py-1">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                <?php echo htmlspecialchars(strtoupper($requestedVersion ?? ($metadata['defaultVersion'] ?? ''))); ?>
                            </span>
                            <button type="button" class="flex items-center gap-2 rounded-full border border-slate-800 px-3 py-1 hover:border-slate-600 transition" data-copy-link>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.438a3 3 0 011.403 5.308l-4.5 2.598a3 3 0 11-2.933-5.206l.115-.064" />
                                </svg>
                                Copy Link
                            </button>
                        </div>
                    </div>

                    <div class="mt-8 space-y-6">
                        <?php if ($activePage): ?>
                            <?php foreach ($activePage['body'] ?? [] as $block): ?>
                                <?php echo renderBodyBlock($block); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="rounded-xl border border-dashed border-slate-800 p-8 text-center">
                                <h2 class="text-xl font-semibold text-white">No documentation available for this selection.</h2>
                                <p class="mt-3 text-slate-400">Choose another version or section to explore curated content.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
        </main>

        <footer class="border-t border-slate-800 bg-slate-950/90">
            <div class="mx-auto max-w-7xl px-6 py-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between text-sm text-slate-500">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($metadata['projectName'] ?? 'Unified Automation Platform'); ?>. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <a href="/index.php" class="hover:text-sky-400">Control Plane</a>
                    <a href="/docs/manage.php" class="hover:text-sky-400">Docs Manager</a>
                    <a href="#" class="hover:text-sky-400" data-feedback-trigger>Send Feedback</a>
                </div>
            </div>
        </footer>
    </div>

    <template id="feedback-template">
        <form class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-300">Name</label>
                <input type="text" name="name" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100" />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300">Email</label>
                <input type="email" name="email" required class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100" />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300">Feedback</label>
                <textarea name="feedback" rows="4" required class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100"></textarea>
            </div>
            <button type="submit" class="w-full rounded-lg bg-sky-500 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-400">Submit</button>
        </form>
    </template>
</body>
</html>
