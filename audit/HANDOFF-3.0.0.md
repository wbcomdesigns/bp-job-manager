# Handoff — bp-job-manager 3.0.0

_Updated 2026-08-13. Read `CLAUDE.md` "Direction" first, then this._

Plan with locked per-screen wording:
[Job Manager Screen Spec](https://claude.ai/code/artifact/bf9da7b4-c305-4cc9-81d1-49abad7cc0c7)
Board: Basecamp "BuddyPress Job Manager" → **Scope** (open) / **Ready for Testing** (done).

---

## State

Branch `3.0.0` (renamed from `2.8.6`), version `3.0.0`. Nothing is tagged yet.
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
| `7f57aab` | Dashicons to inline Lucide icons |
| `791179e` | Scaffold docs/website; sync the manifest |
| `1461c34` | Drop Youzify; breakpoints to 640/1024/1025 |
| `d3fb6a5` | Submit Application led nowhere on new installs |
| `d4ac174` | Prefix the count transients; no count for user 0 |
| `32b97e1` | Broken admin menu icon; CSS the Lucide swap orphaned |
| `ebef697` | Stop shipping 19MB of dev tooling; clean up on uninstall |

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
- Dashicons replaced with inline Lucide icons (22 sites, 12 icons, `bpjm_icon()`), except
  the `add_menu_page()` icon argument, which is a URL and must stay `dashicons-*`.
- Apply from a profile leads to the job listing, not to a page the plugin no longer creates.
- The release zip is 78 files / 933K, not 2699 files / 19MB, and was installed and walked.
- Uninstall removes every row the plugin wrote and nothing that belongs to WP Job Manager.
- Youzify support dropped; the plugin targets plain BuddyPress. That removed ~139 lines of
  theme-specific CSS and let the breakpoints consolidate to 640 / 1024 / 1025.
- `docs/website/` scaffolded (getting-started, settings, developer-guide) and the manifest
  synced: hooks re-derived from source, `user_meta` now empty, storage policy recorded.

---

## Open

Nothing blocking. To finish the release: merge to `master`, tag `v3.0.0`, attach
`dist/bp-job-manager-3.0.0.zip`, and write the release body from the readme's 3.0.0 entry.

Known and accepted:

- The build gate expects `docs/qa/.last-smoke-pass.json`; this plugin has no `docs/qa/`
  scaffold. Recorded as an exception rather than scaffolded - `/wp-plugin-release-qa` is a
  larger piece of work than this release warrants, and the surfaces were walked by hand.
- The audit's one `use-lucide` advisory is the `add_menu_page()` icon, which must stay
  `dashicons-*`. The two `outline-none` advisories are `:focus:not(:focus-visible)`, the
  correct modern pattern that the script's five-line lookahead cannot see.
- Saved settings on existing sites still contain the retired `post_job_user_roles`,
  `apply_job_user_roles` and `bpjm_resume_at_profile` keys. Nothing reads them and
  uninstall removes the whole option; there is deliberately no migration.

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
