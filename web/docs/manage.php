<?php
declare(strict_types=1);

header('Content-Type: application/json');

const CONTENT_PATH = __DIR__ . '/content.json';
const TOKEN_HEADER = 'HTTP_X_DOCS_TOKEN';
const TOKEN_QUERY = 'token';
const ADMIN_TOKEN = 'changeme-admin-token';

if (!file_exists(CONTENT_PATH)) {
    http_response_code(500);
    echo json_encode(['error' => 'Documentation store is missing.']);
    exit;
}

$raw = file_get_contents(CONTENT_PATH);
$data = json_decode($raw, true);
if ($data === null) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to decode documentation store.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

function requireToken(): bool
{
    $provided = $_SERVER[TOKEN_HEADER] ?? ($_GET[TOKEN_QUERY] ?? null);
    if ($provided !== ADMIN_TOKEN) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid or missing admin token.']);
        return false;
    }
    return true;
}

function writeContent(array $payload): bool
{
    $encoded = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($encoded === false) {
        return false;
    }

    $handle = fopen(CONTENT_PATH, 'c+');
    if ($handle === false) {
        return false;
    }

    if (!flock($handle, LOCK_EX)) {
        fclose($handle);
        return false;
    }

    ftruncate($handle, 0);
    rewind($handle);
    fwrite($handle, $encoded);
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    return true;
}

function findSectionIndex(array $sections, string $sectionId): ?int
{
    foreach ($sections as $index => $section) {
        if (($section['id'] ?? '') === $sectionId) {
            return $index;
        }
    }
    return null;
}

switch ($method) {
    case 'GET':
        $sectionId = $_GET['section'] ?? null;
        $pageId = $_GET['page'] ?? null;
        $version = $_GET['version'] ?? null;

        if ($sectionId && $pageId) {
            foreach ($data['sections'] ?? [] as $section) {
                if (($section['id'] ?? '') !== $sectionId) {
                    continue;
                }
                foreach ($section['pages'] ?? [] as $page) {
                    if (($page['id'] ?? '') === $pageId && (!$version || ($page['version'] ?? null) === $version)) {
                        echo json_encode(['page' => $page]);
                        exit;
                    }
                }
            }
            http_response_code(404);
            echo json_encode(['error' => 'Page not found.']);
            exit;
        }

        if ($sectionId) {
            foreach ($data['sections'] ?? [] as $section) {
                if (($section['id'] ?? '') === $sectionId) {
                    echo json_encode(['section' => $section]);
                    exit;
                }
            }
            http_response_code(404);
            echo json_encode(['error' => 'Section not found.']);
            exit;
        }

        echo json_encode($data);
        exit;

    case 'POST':
        if (!requireToken()) {
            exit;
        }
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON payload.']);
            exit;
        }

        $sectionId = $payload['section'] ?? null;
        $page = $payload['page'] ?? null;

        if (!$sectionId || !is_array($page)) {
            http_response_code(422);
            echo json_encode(['error' => 'Section and page payload are required.']);
            exit;
        }

        $sectionIndex = findSectionIndex($data['sections'] ?? [], $sectionId);
        if ($sectionIndex === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Section not found.']);
            exit;
        }

        $pageId = $page['id'] ?? null;
        if (!$pageId) {
            http_response_code(422);
            echo json_encode(['error' => 'Page id is required.']);
            exit;
        }

        foreach ($data['sections'][$sectionIndex]['pages'] ?? [] as $existingPage) {
            if (($existingPage['id'] ?? '') === $pageId && ($existingPage['version'] ?? null) === ($page['version'] ?? null)) {
                http_response_code(409);
                echo json_encode(['error' => 'Page with this id and version already exists.']);
                exit;
            }
        }

        $data['sections'][$sectionIndex]['pages'][] = $page;
        if (!writeContent($data)) {
            http_response_code(500);
            echo json_encode(['error' => 'Unable to persist documentation update.']);
            exit;
        }

        echo json_encode(['status' => 'created', 'page' => $page]);
        exit;

    case 'PUT':
        if (!requireToken()) {
            exit;
        }
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON payload.']);
            exit;
        }

        $sectionId = $payload['section'] ?? null;
        $pageId = $payload['page']['id'] ?? null;
        $version = $payload['page']['version'] ?? null;
        if (!$sectionId || !$pageId || !$version) {
            http_response_code(422);
            echo json_encode(['error' => 'Section, page id, and version are required.']);
            exit;
        }

        $sectionIndex = findSectionIndex($data['sections'] ?? [], $sectionId);
        if ($sectionIndex === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Section not found.']);
            exit;
        }

        $updated = false;
        foreach ($data['sections'][$sectionIndex]['pages'] as $index => $existingPage) {
            if (($existingPage['id'] ?? '') === $pageId && ($existingPage['version'] ?? null) === $version) {
                $data['sections'][$sectionIndex]['pages'][$index] = array_merge($existingPage, $payload['page']);
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            http_response_code(404);
            echo json_encode(['error' => 'Page not found.']);
            exit;
        }

        if (!writeContent($data)) {
            http_response_code(500);
            echo json_encode(['error' => 'Unable to persist documentation update.']);
            exit;
        }

        echo json_encode(['status' => 'updated', 'page' => $payload['page']]);
        exit;

    case 'DELETE':
        if (!requireToken()) {
            exit;
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON payload.']);
            exit;
        }

        $sectionId = $payload['section'] ?? null;
        $pageId = $payload['page_id'] ?? null;
        $version = $payload['version'] ?? null;

        if (!$sectionId || !$pageId) {
            http_response_code(422);
            echo json_encode(['error' => 'Section and page_id are required.']);
            exit;
        }

        $sectionIndex = findSectionIndex($data['sections'] ?? [], $sectionId);
        if ($sectionIndex === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Section not found.']);
            exit;
        }

        $pages = $data['sections'][$sectionIndex]['pages'];
        $initialCount = count($pages);
        $pages = array_values(array_filter($pages, function ($existingPage) use ($pageId, $version) {
            $matchesId = ($existingPage['id'] ?? '') === $pageId;
            $matchesVersion = !$version || ($existingPage['version'] ?? null) === $version;
            return !($matchesId && $matchesVersion);
        }));

        if (count($pages) === $initialCount) {
            http_response_code(404);
            echo json_encode(['error' => 'Page not found.']);
            exit;
        }

        $data['sections'][$sectionIndex]['pages'] = $pages;
        if (!writeContent($data)) {
            http_response_code(500);
            echo json_encode(['error' => 'Unable to persist documentation update.']);
            exit;
        }

        echo json_encode(['status' => 'deleted']);
        exit;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed.']);
        exit;
}
