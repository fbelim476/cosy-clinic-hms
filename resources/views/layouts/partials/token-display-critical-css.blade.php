<style>
/* Critical fallback — TV screen styled if external CSS fails */
html.token-display-root,html.token-display-root body{margin:0;padding:0;min-height:100vh;font-family:Inter,system-ui,sans-serif}
body.token-display-screen{min-height:100vh;display:flex;flex-direction:column;color:#f8fafc;background:linear-gradient(165deg,#030712,#0c1929,#0f2744)}
body.token-display-screen.td-theme-day{color:#0f172a;background:linear-gradient(165deg,#f0f9ff,#e0f2fe,#f8fafc)}
.td-header{display:flex;justify-content:space-between;align-items:center;padding:1rem 1.75rem;background:rgba(3,7,18,.72);border-bottom:1px solid rgba(56,189,248,.28)}
body.td-theme-day .td-header{background:rgba(255,255,255,.92)}
.td-brand{font-size:1.4rem;font-weight:800;color:#38bdf8}
.token-display-main{flex:1;display:flex;min-height:0}
.td-multi-body{flex:1;display:flex;align-items:center;padding:1rem}
.td-multi-grid{display:grid;gap:1.25rem;width:100%}
.td-multi-grid[data-count="1"]{grid-template-columns:1fr;max-width:680px;margin:0 auto}
.td-multi-grid[data-count="2"]{grid-template-columns:repeat(2,1fr)}
.td-multi-grid[data-count="3"]{grid-template-columns:repeat(3,1fr)}
.td-doctor-panel{border-radius:20px;padding:1.5rem;text-align:center;background:rgba(255,255,255,.07);border:2px solid rgba(56,189,248,.28)}
body.td-theme-day .td-doctor-panel{background:#fff;border-color:rgba(14,165,233,.35)}
.td-doctor-token{font-size:clamp(3rem,8vw,5rem);font-weight:900;color:#7dd3fc;line-height:1}
body.td-theme-day .td-doctor-token{color:#0369a1}
.td-doctor-name{font-weight:800;font-size:1.2rem}
.td-doctor-patient{font-size:1.1rem;margin-top:.5rem}
@media(max-width:900px){.td-multi-grid[data-count="2"],.td-multi-grid[data-count="3"]{grid-template-columns:1fr}}
</style>
