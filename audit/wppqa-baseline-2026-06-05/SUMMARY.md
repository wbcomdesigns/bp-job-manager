# wppqa Baseline — bp-job-manager 2.8.5

Date: 2026-06-05
Branch: 2.8.5
Tool: wp-plugin-qa MCP (Phase 0, run before manifest generation)

## Checks run

| Check | Passed | Failed | Skipped |
|---|---:|---:|---:|
| `wppqa_check_plugin_dev_rules`   | 4 | 5 | 0 |
| `wppqa_check_rest_js_contract`   | 0 | 0 | 1 (no REST routes) |
| `wppqa_check_wiring_completeness`| 0 | 0 | 1 (no `templates/` settings reads) |

## plugin-dev-rules — 5 high findings (all `nonce-no-cap`)

Triaged below. Per the documented MCP quirk, `wppqa_check_plugin_dev_rules`
does not recognize `nopriv` AJAX actions and false-positives `nonce-no-cap`
on intentionally-public endpoints. Each finding pre-triaged:

| # | File:line | Verdict | Reason |
|---|---|---|---|
| 1 | `admin/wbcom/wbcom-admin-settings.php:43` | FALSE POSITIVE (low) | Shared Wbcom promo-card AJAX. Handler only echoes back a posted string; no privileged action, no DB write. Nonce-gated. Cosmetic. |
| 2 | `bp-job-manager.php:175` | FALSE POSITIVE | `bpjm_activation_redirect_settings()` runs on the `activated_plugin` hook, which only fires inside an already cap-gated plugin-activation request. Nonce verify is defensive; it performs only a redirect. |
| 3 | `public/class-bp-job-manager-public.php:205` | FALSE POSITIVE (by design) | `bpjm_load_more_jobs()` is registered as BOTH `wp_ajax_` and `wp_ajax_nopriv_` (public infinite-scroll). Public read-only pagination of published jobs — capability check would break the public feature. `check_ajax_referer` is the correct gate. This is exactly the nopriv blind spot noted in the quirk. |
| 4 | `public/class-bp-job-manager-public.php:1398` | FALSE POSITIVE | `custom_bp_init()` front-end xProfile settings save, gated by `check_admin_referer('bp_xprofile_settings')` / Youzify nonce. Writes only the CURRENT logged-in user's own user-meta (`bp_loggedin_user_id()`), so per-user ownership is the authorization, not a site capability. |
| 5 | `public/class-bp-job-manager-public.php:1400` | FALSE POSITIVE | Second `check_admin_referer` branch of the same `custom_bp_init()` save path as #4. Same justification. |

Net real security findings from this check: **0**. All 5 are the documented
nopriv / self-meta / activation-hook false-positive pattern.

## rest-js-contract — skipped
Plugin exposes no REST routes (`register_rest_route` count = 0). Nothing to drift.

## wiring-completeness — skipped
Heuristic only inspects `templates/`. This plugin reads `bpjm_general_settings`
in the service/globals layer (`includes/class-bp-job-manager-globals.php`) and
in `public/class-bp-job-manager-public.php`, not in `public/templates/`, so the
tool has nothing to match. Manual cross-check: every `bpjm_general_settings`
sub-key written by the admin form IS read in the globals class — fully wired.

## Release-readiness gate
`failed > 0` on plugin-dev-rules, BUT all failures are pre-triaged false
positives (nopriv / activation-hook / self-meta). No real high-severity bug
surfaced by wppqa. Manifest generation proceeds with this context.
