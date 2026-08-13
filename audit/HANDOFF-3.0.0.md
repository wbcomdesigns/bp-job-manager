# Handoff — bp-job-manager 3.0.0

_Updated 2026-08-13. Read `CLAUDE.md` "Direction" first, then this._

Plan with locked per-screen wording:
[Job Manager Screen Spec](https://claude.ai/code/artifact/bf9da7b4-c305-4cc9-81d1-49abad7cc0c7)
Board: Basecamp "BuddyPress Job Manager" → **Scope** (open) / **Ready for Testing** (done).

---

## State

Branch `2.8.6`, version `3.0.0`. **Rename the branch at release.** Nothing is tagged.
2.8.6 was never released; its ten bug fixes ship inside 3.0.0.

| Commit | What |
|---|---|
| `92a5883` | The ten 2.8.6 bug cards (crash fix, multisite detection, apply form, i18n readiness) |
| `c672788` | Store nothing but settings; retarget to 3.0.0 |
| `31cb2ba` | Five scattered role checks → one accessor pair |
| `1a64b2d` | My Resumes / Applied Jobs → host shortcodes |
| `c836f72` | Handoff notes |
| `e08db44` | Remove role mapping entirely |
| `fb1b0da` | BuddyPress heading hierarchy; hide Resumes tab when empty |
| `552b300` | Copy pass: no empty rows, honest headings, no Sign out mid-form |
| `762957b` | Admin cleanup: block violation cleared, 379 lines of dead CSS/JS |
| `aba7526` | Screen walk: anonymous + RTL, stale Getting Started copy |
| `4c9ef64` | Settings form contradicting the profile it controls |
| `933ad57` | Remove the resume-on-profile duplicate entirely |
| `e3b071c` | Name the applicant and job in notifications and activity |
| `f3f26a4` | Fix coding-standards regressions from `e3b071c` |
| `97a4f07` | Regenerate WP.org screenshots; drop an empty media block |

**Gates: PHPStan 0 errors. PHPCS 38 errors / 8 warnings** (was 57/9). Block-severity
UX violations: **0**. All five locales: 150 strings, 0 untranslated, 0 fuzzy, `.mo` parity.

---

## Done

Everything in the spec except the two advisory items below.

- Stores nothing but its settings. No activation writes, no auto-created page, no
  per-user meta.
- No role mapping. A tab appears because the member has jobs or resumes, or it is their
  own profile. Posting defers to WP Job Manager.
- My Resumes and Applied Jobs render the host shortcodes; 314 lines deleted and the
  application-status column gained.
- The resume is no longer duplicated onto the profile screen; the Resumes tab is the
  whole surface.
- Notification names the applicant (display name) and the job.
- Every screen walked, including the first anonymous and RTL passes.
- Screenshots regenerated against clean seed data.

---

## Open

### 1. Dashicons → Lucide (advisory, 10 sites)

`includes/admin/class-bp-job-manager-admin-panel.php` (5 tab icons), `hub.php` (4),
`discover.php` (1). Needs an icon helper plus inline SVGs (no CDN) and a visual pass over
every admin screen. Not started.

### 2. Front-end breakpoints (advisory)

`public/css/bp-job-manager-public.css` uses 540, 670, 768 and 769 where the standard is
three values. **Do not force the count.** Collapsing them changes layout at those widths,
and the 670 rule targets `.youzify-content` — Youzify is not installed on this stack, so
that one cannot be verified here at all. It needs either a Youzify install or a decision
to drop that rule.

`assets/css/admin.css` is already compliant (1024 / 640).

### 3. Release

- Rename branch `2.8.6` → `3.0.0`.
- Tag and build. **Not authorised yet** — confirm with the owner first.
- The build gate expects `docs/qa/.last-smoke-pass.json`; this plugin has no `docs/qa/`
  scaffold. Either scaffold it (`/wp-plugin-release-qa`) or note the exception.

---

## Environment

- `wbcom-free.local`, Reign 8.0.4, BuddyPress 14.4.0, WPJM 2.4.2, Applications 3.2.0,
  Resumes 2.2.0, Alerts 3.2.0, Bookmarks 1.5.0.
- **Everything unrelated is deactivated.** Original list:
  `scratchpad/active-plugins-before.txt` (session-scoped; the plugins are still installed).
- `WP_DEBUG_LOG` is on in `wp-config.php`.
- Seeded actors: `bpjm-employer` (7 jobs), `bpjm-candidate` (1 resume, 3 applications in
  three states), `bpjm-candidate2`, `bpjm-candidate3`, `bpjm-jobsonly`, `bpjm-empty`
  (nothing at all — the empty-state case), `bpjm-noname` (no first/last name — the case
  that exposed the apply-form validation bug), `postactivation` (registered after
  activation — the case that proved the resume fix).
- Seed content is presentation-clean: no em-dashes, no test-looking titles. Keep it that
  way; the screenshots are shot from it.
- Seeded IDs in `scratchpad/seeded-ids.txt`. **Delete by ID, never by title.**
- `?autologin=<username>` via the dev mu-plugin.

---

## Traps already paid for

- **Read every card comment before diagnosing.** Two root causes here were wrong until
  the comments were read.
- **A 200 is not proof you looked at the right screen.** The Help tab's id is `welcome`;
  requesting `tab=help` returns 200 and silently renders Overview. I recorded a pass on a
  screen I had never seen.
- **opcache lies on A/B tests.** Wait ~4s after swapping a file, or assert with `wp eval`.
- **`wp eval` is not a browser.** Anything reading `bp_displayed_user_id()` gets 0 there,
  so it reads nobody's data. It made a working opt-out look broken, and made a working
  notification look nameless. Check those over HTTP.
- **In wp-admin, `determine_locale()` returns the *user's* profile language.** Set the
  test user's locale too, or i18n looks broken when it is not.
- **`grep -cE '^msgid '` under-reports untranslated strings** — multi-line entries open
  with `msgid ""`. Use `^msgid`.
- **Read the gate output before committing, not after staging.** One commit here reported
  PHPCS unchanged when it had gone 40 → 48.
- Prove translation readiness with a pseudo-locale: rebuild the catalogue with every
  `msgstr` as `«msgid»`, switch to it, and read the screens. Anything still plain English
  is unwrapped or third-party.
