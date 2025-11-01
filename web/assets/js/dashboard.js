document.addEventListener('DOMContentLoaded', () => {
  const pipelineContainer = document.getElementById('pipeline-status');
  if (pipelineContainer) {
    const payload = pipelineContainer.dataset.payload;

    if (!payload) {
      pipelineContainer.innerHTML =
        '<p class="rounded-xl border border-onyx/60 bg-onyx/40 px-4 py-3 text-sm text-slate-400">No pipeline execution recorded yet.</p>';
    } else {
      let stages = [];
      try {
        stages = JSON.parse(payload);
      } catch (error) {
        console.error('Invalid pipeline snapshot', error);
        pipelineContainer.innerHTML =
          '<p class="rounded-xl border border-ember/40 bg-ember/10 px-4 py-3 text-sm text-emberGlow">Failed to parse pipeline snapshot.</p>';
      }

      if (!stages.length) {
        pipelineContainer.innerHTML =
          '<p class="rounded-xl border border-onyx/60 bg-onyx/40 px-4 py-3 text-sm text-slate-400">No pipeline execution recorded yet.</p>';
      } else {
        pipelineContainer.innerHTML = stages
          .map(
            (stage) => `
              <article class="rounded-2xl border border-onyx bg-midnight/80 p-5 shadow-plasma">
                <div class="flex items-center justify-between">
                  <h3 class="text-base font-semibold text-slate-100">${stage.stage}</h3>
                  <span class="text-[0.65rem] uppercase tracking-[0.3em] ${stage.success ? 'text-emerald-300' : 'text-emberGlow'}">${stage.success ? 'Pass' : 'Alert'}</span>
                </div>
                <p class="mt-3 text-sm text-slate-400">${stage.summary}</p>
                <span class="mt-4 inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold ${stage.success ? 'bg-emerald-500/15 text-emerald-300' : 'bg-ember/25 text-emberGlow'}">
                  ${stage.success ? 'Healthy stage' : 'Attention required'}
                </span>
              </article>
            `
          )
          .join('');
      }
    }
  }

  const configSection = document.getElementById('dependency-config');
  if (!configSection) return;

  const dependencies = safeParse(configSection.dataset.dependencies, []);
  const dependencyTable = document.getElementById('dependency-table');
  const dependencyForm = document.getElementById('dependency-form');

  const languageMatrix = document.getElementById('language-matrix');
  const languageForm = document.getElementById('language-form');
  const languageCards = document.getElementById('language-cards');

  const languages = languageMatrix ? safeParse(languageMatrix.dataset.languages, []) : [];
  const repositoryForm = document.getElementById('repository-form');
  const repositoryTable = document.getElementById('repository-table');
  const repositories = safeParse(configSection.dataset.repositories, []);

  renderDependencies();
  renderLanguages();
  renderRepositories();

  if (dependencyForm) {
    dependencyForm.addEventListener('submit', (event) => {
      event.preventDefault();
      const formData = new FormData(dependencyForm);
      const newDependency = {
        name: formData.get('name')?.toString().trim(),
        version: formData.get('version')?.toString().trim(),
        license: formData.get('license')?.toString().trim(),
      };

      if (!newDependency.name || !newDependency.version || !newDependency.license) {
        return;
      }

      dependencies.push(newDependency);
      renderDependencies();
      dependencyForm.reset();
    });
  }

  if (languageForm) {
    languageForm.addEventListener('submit', (event) => {
      event.preventDefault();
      const formData = new FormData(languageForm);
      const newLanguage = {
        name: formData.get('name')?.toString().trim(),
        manager: formData.get('manager')?.toString().trim(),
        coverage: formData.get('coverage')?.toString().trim(),
        frameworks: formData
          .get('frameworks')
          ?.toString()
          .split(',')
          .map((item) => item.trim())
          .filter(Boolean),
      };

      if (!newLanguage.name || !newLanguage.manager || !newLanguage.coverage) {
        return;
      }

      languages.push(newLanguage);
      renderLanguages();
      languageForm.reset();
    });
  }

  if (repositoryForm) {
    repositoryForm.addEventListener('submit', (event) => {
      event.preventDefault();
      const formData = new FormData(repositoryForm);
      const newRepository = {
        name: formData.get('name')?.toString().trim(),
        type: formData.get('type')?.toString().trim(),
        location: formData.get('location')?.toString().trim(),
        scale: formData.get('scale')?.toString().trim(),
        lastScan: formData.get('lastScan')?.toString().trim(),
      };

      if (!newRepository.name || !newRepository.type || !newRepository.location || !newRepository.scale || !newRepository.lastScan) {
        return;
      }

      repositories.push(newRepository);
      renderRepositories();
      repositoryForm.reset();
    });
  }

  const tabButtons = Array.from(configSection.querySelectorAll('.tab-button'));
  const tabPanels = Array.from(configSection.querySelectorAll('.tab-panel'));

  tabButtons.forEach((button) => {
    button.addEventListener('click', () => {
      const targetId = button.dataset.target;
      if (!targetId) return;

      tabButtons.forEach((btn) => {
        btn.classList.remove('active');
        btn.classList.remove('bg-ember/20', 'border-ember/60', 'text-emberGlow');
        btn.classList.add('border-onyx', 'bg-midnight/60', 'text-slate-300');
      });

      button.classList.add('active', 'border-ember/60', 'bg-ember/20', 'text-emberGlow');
      button.classList.remove('border-onyx', 'bg-midnight/60', 'text-slate-300');

      tabPanels.forEach((panel) => {
        if (panel.id === targetId) {
          panel.classList.remove('hidden');
        } else {
          panel.classList.add('hidden');
        }
      });
    });
  });

  function renderDependencies() {
    if (!dependencyTable) return;

    if (!dependencies.length) {
      dependencyTable.innerHTML = `
        <tr>
          <td class="px-6 py-4 text-xs text-slate-500" colspan="3">No dependencies registered yet.</td>
        </tr>
      `;
      return;
    }

    dependencyTable.innerHTML = dependencies
      .map(
        (dependency) => `
          <tr>
            <td class="px-6 py-3 font-mono text-xs text-slate-200">${escapeHtml(dependency.name)}</td>
            <td class="px-6 py-3 text-xs text-slate-400">${escapeHtml(dependency.version)}</td>
            <td class="px-6 py-3 text-xs text-slate-400">${escapeHtml(dependency.license)}</td>
          </tr>
        `
      )
      .join('');
  }

  function renderLanguages() {
    if (languageMatrix) {
      languageMatrix.innerHTML = languages.length
        ? languages
            .map(
              (language) => `
                <div class="rounded-xl border border-onyx bg-onyx/90 p-4">
                  <div class="flex items-center justify-between">
                    <h4 class="text-base font-semibold text-slate-100">${escapeHtml(language.name)}</h4>
                    <span class="text-xs font-semibold text-emberGlow/80">Coverage ${escapeHtml(language.coverage)}</span>
                  </div>
                  <p class="mt-2 text-xs text-slate-400">Managers: ${escapeHtml(language.manager)}</p>
                  <p class="mt-2 text-xs text-slate-500">Frameworks: ${escapeHtml((language.frameworks || []).join(', '))}</p>
                </div>
              `
            )
            .join('')
        : '<p class="rounded-xl border border-onyx/60 bg-onyx/40 px-4 py-3 text-xs text-slate-500">No languages onboarded yet.</p>';
    }

    if (languageCards) {
      languageCards.innerHTML = languages.length
        ? languages
            .map(
              (language) => `
                <article class="rounded-2xl border border-onyx bg-midnight/80 p-5 shadow-plasma">
                  <div class="flex items-center justify-between">
                    <h4 class="text-base font-semibold text-slate-100">${escapeHtml(language.name)}</h4>
                    <span class="text-[0.65rem] uppercase tracking-[0.3em] text-emberGlow/70">${escapeHtml(language.coverage)}</span>
                  </div>
                  <p class="mt-3 text-xs text-slate-400">Managers · ${escapeHtml(language.manager)}</p>
                  <p class="mt-3 text-xs text-slate-500">Framework focus · ${escapeHtml((language.frameworks || []).join(', '))}</p>
                </article>
              `
            )
            .join('')
        : '<p class="rounded-2xl border border-onyx/60 bg-onyx/40 px-4 py-3 text-xs text-slate-500">No languages onboarded yet.</p>';
    }
  }

  function renderRepositories() {
    if (!repositoryTable) return;

    if (!repositories.length) {
      repositoryTable.innerHTML = `
        <tr>
          <td class="px-6 py-4 text-xs text-slate-500" colspan="5">No codebases enrolled yet.</td>
        </tr>
      `;
      return;
    }

    repositoryTable.innerHTML = repositories
      .map(
        (repository) => `
          <tr>
            <td class="px-6 py-3 font-mono text-xs text-slate-200">${escapeHtml(repository.name)}</td>
            <td class="px-6 py-3 text-xs text-slate-400">${escapeHtml(repository.type)}</td>
            <td class="px-6 py-3 text-xs text-slate-400">${escapeHtml(repository.location)}</td>
            <td class="px-6 py-3 text-xs text-slate-400">${escapeHtml(repository.scale)}</td>
            <td class="px-6 py-3 text-xs text-slate-400">${escapeHtml(repository.lastScan)}</td>
          </tr>
        `
      )
      .join('');
  }

  function safeParse(payload, fallback) {
    if (!payload) return fallback;
    try {
      const parsed = JSON.parse(payload);
      return Array.isArray(parsed) ? parsed : fallback;
    } catch (error) {
      console.error('Failed to parse payload', error);
      return fallback;
    }
  }

  function escapeHtml(value) {
    if (typeof value !== 'string') return value ?? '';
    return value
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }
});
