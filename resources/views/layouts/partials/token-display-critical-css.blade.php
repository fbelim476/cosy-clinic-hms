<style>
/* Critical fallback — ensures TV screen is styled if external CSS fails to load */
html.token-display-root,html.token-display-root body{margin:0;padding:0;min-height:100vh;font-family:Inter,system-ui,sans-serif}
body.token-display-screen{min-height:100vh;display:flex;flex-direction:column;color:#f8fafc;background:linear-gradient(165deg,#030712,#0c1929,#0f2744)}
.td-header{display:flex;justify-content:space-between;align-items:center;padding:1.25rem 2rem;background:rgba(3,7,18,.55);border-bottom:1px solid rgba(56,189,248,.22)}
.td-brand{font-size:1.5rem;font-weight:800;color:#38bdf8}
.td-token{font-size:clamp(6rem,18vw,12rem);font-weight:900;line-height:1;color:#fff}
.td-patient{font-size:clamp(1.5rem,4vw,2.5rem);font-weight:600;margin-top:1rem;color:#e2e8f0}
.td-layout{display:grid;grid-template-columns:1fr 38%;gap:2rem;flex:1;padding:1rem 2rem}
.token-display-body{flex:1;display:flex;flex-direction:column}
.td-now-panel,.td-next-panel{border-radius:24px;background:rgba(255,255,255,.06);border:1px solid rgba(56,189,248,.22);padding:2rem}
.td-next-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:1rem}
.td-next{padding:1rem;border-radius:16px;text-align:center;background:rgba(255,255,255,.06);border:1px solid rgba(56,189,248,.2)}
.td-next.emergency{border-color:#ef4444;background:rgba(239,68,68,.2)}
.td-next-num{font-size:2rem;font-weight:800;color:#38bdf8}
@media(max-width:992px){.td-layout{grid-template-columns:1fr}}
</style>
