(() => {
  const root = document.getElementById('doc-content');
  if (!root) return;

  const docsDataset = root.dataset.docs ? JSON.parse(root.dataset.docs) : null;
  const currentVersion = root.dataset.version;
  const searchInput = document.getElementById('doc-search');
  const searchResults = document.getElementById('doc-search-results');
  const copyLinkButton = root.querySelector('[data-copy-link]');
  const feedbackTrigger = document.querySelector('[data-feedback-trigger]');
  const feedbackTemplate = document.getElementById('feedback-template');

  function buildSearchIndex() {
    if (!docsDataset) return [];
    const entries = [];
    (docsDataset.sections || []).forEach((section) => {
      (section.pages || []).forEach((page) => {
        if (currentVersion && page.version !== currentVersion) return;
        entries.push({
          section: section.title,
          id: page.id,
          title: page.title,
          summary: page.summary,
          tags: page.tags || [],
          version: page.version,
          url: `/docs/index.php?section=${encodeURIComponent(section.id)}&page=${encodeURIComponent(page.id)}&version=${encodeURIComponent(page.version)}`
        });
      });
    });
    return entries;
  }

  const index = buildSearchIndex();

  function renderResults(items) {
    if (!searchResults) return;
    if (!items.length) {
      searchResults.classList.add('hidden');
      searchResults.innerHTML = '';
      return;
    }

    const fragment = document.createDocumentFragment();
    items.slice(0, 10).forEach((item) => {
      const anchor = document.createElement('a');
      anchor.href = item.url;
      anchor.className = 'block border-b border-slate-800 px-4 py-3 text-sm hover:bg-slate-800/60';
      anchor.innerHTML = `
        <div class="flex items-center justify-between">
          <span class="font-medium text-sky-300">${item.title}</span>
          <span class="rounded-full border border-slate-700 px-2 py-0.5 text-xs text-slate-400">${item.section}</span>
        </div>
        <p class="mt-2 text-xs text-slate-400">${item.summary || ''}</p>
        <div class="mt-2 flex flex-wrap gap-1">
          ${(item.tags || []).map((tag) => `<span class="rounded bg-slate-800 px-2 py-0.5 text-[10px] uppercase tracking-wide text-slate-400">${tag}</span>`).join('')}
        </div>
      `;
      fragment.appendChild(anchor);
    });

    searchResults.innerHTML = '';
    searchResults.appendChild(fragment);
    searchResults.classList.remove('hidden');
  }

  if (searchInput) {
    searchInput.addEventListener('input', (event) => {
      const value = event.target.value.trim().toLowerCase();
      if (!value) {
        searchResults.classList.add('hidden');
        searchResults.innerHTML = '';
        return;
      }

      const filtered = index.filter((item) => {
        const haystack = [item.title, item.summary, ...(item.tags || [])]
          .filter(Boolean)
          .join(' ')
          .toLowerCase();
        return haystack.includes(value);
      });
      renderResults(filtered);
    });

    document.addEventListener('click', (event) => {
      if (!searchResults.contains(event.target) && event.target !== searchInput) {
        searchResults.classList.add('hidden');
      }
    });
  }

  if (copyLinkButton) {
    copyLinkButton.addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(window.location.href);
        copyLinkButton.textContent = 'Link Copied!';
        setTimeout(() => {
          copyLinkButton.textContent = 'Copy Link';
        }, 2000);
      } catch (error) {
        console.error('Unable to copy link', error);
      }
    });
  }

  if (feedbackTrigger && feedbackTemplate) {
    feedbackTrigger.addEventListener('click', (event) => {
      event.preventDefault();
      const dialog = document.createElement('div');
      dialog.className = 'fixed inset-0 z-20 flex items-center justify-center bg-black/70 px-4';
      dialog.innerHTML = `
        <div class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-950 p-6 shadow-2xl">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-white">Send Feedback</h2>
            <button type="button" class="text-slate-500 hover:text-slate-300" data-close>&times;</button>
          </div>
          <p class="mt-1 text-sm text-slate-400">Share context so our documentation team can iterate quickly.</p>
          <div class="mt-4">${feedbackTemplate.innerHTML}</div>
        </div>
      `;
      dialog.querySelector('[data-close]').addEventListener('click', () => dialog.remove());
      dialog.addEventListener('click', (evt) => {
        if (evt.target === dialog) dialog.remove();
      });
      dialog.querySelector('form').addEventListener('submit', (evt) => {
        evt.preventDefault();
        const formData = new FormData(evt.target);
        const payload = Object.fromEntries(formData.entries());
        console.info('Feedback submitted', payload);
        evt.target.reset();
        dialog.remove();
      });
      document.body.appendChild(dialog);
    });
  }

  window.addEventListener('load', () => {
    document.querySelectorAll('pre code').forEach((block) => {
      if (window.hljs) {
        window.hljs.highlightElement(block);
      }
    });
  });
})();
