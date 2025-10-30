document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('pipeline-status');
  if (!container) return;

  const payload = container.dataset.payload;
  if (!payload) {
    container.innerHTML = '<p class="text-slate-400">No pipeline execution recorded yet.</p>';
    return;
  }

  let stages = [];
  try {
    stages = JSON.parse(payload);
  } catch (error) {
    console.error('Invalid pipeline snapshot', error);
    container.innerHTML = '<p class="text-red-400">Failed to parse pipeline snapshot.</p>';
    return;
  }

  if (!stages.length) {
    container.innerHTML = '<p class="text-slate-400">No pipeline execution recorded yet.</p>';
    return;
  }

  container.innerHTML = stages
    .map((stage) => `
      <article class="bg-slate-950 rounded-lg p-4 border border-slate-800">
        <h3 class="font-semibold">${stage.stage}</h3>
        <p class="text-sm text-slate-400">${stage.summary}</p>
        <span class="inline-flex items-center mt-3 px-2 py-1 text-xs rounded-full ${stage.success ? 'bg-emerald-500/20 text-emerald-300' : 'bg-red-500/20 text-red-300'}">
          ${stage.success ? 'Healthy' : 'Attention required'}
        </span>
      </article>
    `)
    .join('');
});
