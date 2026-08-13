# Handoff — bp-job-manager 3.0.0 in progress

_Written 2026-08-13. Read `CLAUDE.md` "Direction" first, then this._

Plan with locked per-screen wording:
[Job Manager Screen Spec](https://claude.ai/code/artifact/bf9da7b4-c305-4cc9-81d1-49abad7cc0c7)
Board: Basecamp "BuddyPress Job Manager" → **Scope** (open) / **Ready for Testing** (done).

---

## Where things stand

Branch `2.8.6`, version `3.0.0`. **Rename the branch at release.** Nothing is tagged.
2.8.6 was never released — its ten bug fixes ship inside 3.0.0.

| Commit | What |
|---|---|
| `92a5883` | The ten 2.8.6 bug cards (crash fix, multisite detection, apply-form, i18n readiness) |
| `c672788` | Store nothing but settings; retarget to 3.0.0 |
| `31cb2ba` | Five scattered role checks → one accessor pair |
| `1a64b2d` | My Resumes / Applied Jobs → host shortcodes |

Gates at every commit: **PHPStan 0 errors**, **PHPCS 55 errors / 8 warnings**
(baseline was 57/9 — it improved as dead code came out; do not let it rise).

---

## Done

- **Resume on profile works for every member.** The activation loop that stamped
  `bpjm_display_fields` on every existing user is gone; absence of the meta now means show.
  Renderer went 0 bytes → 2832 for a member registered after activation.
- **Writes outside settings: 4 → 0.** No auto-created page, no per-user metas, no pointer.
  `bpjm_job_application_pgid` is still READ for existing sites.
- **314 lines of re-rendered dashboards deleted** in favour of `[candidate_dashboard]` and
  `[past_applications]`. Applied Jobs gained its Status column that way.
- **POT 214 → 199.** All five locales: 199 translated / 0 untranslated / 0 fuzzy, .mo parity.
- Dead classes removed: `Bpjm_Job_Manager_Activator`, `Bpjm_Job_Manager_Deactivator`
  (empty for years), `bpjm_get_page_by_title()`.

---

## Open — in the order I'd do them

### 1. Uniform shell + final copy (card 10198887354) — the big one

All wording is already locked in the spec; this is mechanical once started. It grew during
the browser passes, so start from this list, not just the card:

- Resume block on the profile still headed **"Personal Information"** → spec says **"Resume"**.
- **Video / Description / URL(s) print labels with no values.** Omit empty rows.
  (`public/templates/profile-content-single-resume.php`)
- BuddyPress prints *"You have not yet added details to your profile"* directly above a fully
  populated resume, because our block renders after the xProfile loop. Needs a decision:
  suppress that notice when we render, or move our block.
- **Resume Manager's row actions (Edit/Hide/Delete) are in the DOM but not visible** on our
  tab — same enqueue gap I already fixed for the apply page. Fix the same way:
  `bpjm_needs_public_assets()` / `bpjm_has_wpjm_shortcode()` in
  `public/class-bp-job-manager-public.php`.
- Empty states on Saved jobs and Job alerts are bare table rows.
- Button vocabulary disagrees between adjacent tabs (solid vs full-width outline).
- Card 10198887737: remove the **Sign out** account block for the profile owner on Post Job
  and Add Resume, keep it for logged-out visitors. Do **not** filter
  `submit_job_form_show_signin` globally — that was a bug we already shipped and fixed.

### 2. Dead UI for non-posters (card 10198886598)

Blocked on a decision, see below. If the role restriction stays, fix the screen: no employer
table for a member who cannot post, land the Jobs tab on Saved jobs, and don't register the
tab at all when neither Bookmarks nor Alerts is active.

### 3. Admin cleanup (card 10198888433)

1 block violation (inline `<style>` in `admin/class-bp-job-manager-admin-feedback.php:183`)
and 18 advisory. The four `outline: none` rules with no `:focus-visible` replacement matter
most — that is invisible keyboard focus, not cosmetics. Run the dead-CSS detector **after**
the shell lands, with the allowlist, as its own commit.

### 4. Walk all 17 screens (card 10198889132) — release gate

9 screens have **never been rendered**; their spec rows are written from code and marked
*verify before applying*. No anonymous, RTL or peer-to-peer pass has been run on any screen.
Two root causes were wrong earlier in this work until the screens were actually looked at —
do not skip this.

---

## Decision needed from the owner (blocks card 10198885640 and 10198886598)

**Should every member be able to post jobs by default?**

The spec said "defer to WP Job Manager's permission". That premise is wrong: WPJM has no
role-based posting capability — `job_manager_user_can_post_job` defaults to `true` for any
logged-in user. So retiring the role arrays means *everyone can post*, which is WPJM's native
model but a behaviour change on every existing site.

- **Yes** → delete the role arrays; the dead-UI card largely dissolves with them.
- **No** → the role restriction is a deliberate Wbcom feature; keep it, and fix the non-poster
  screen instead.

`bpjm_user_can_post_job()` / `bpjm_user_can_apply_job()` exist so either answer is one edit.

---

## Environment notes

- Test site `wbcom-free.local`, Reign 8.0.4, BuddyPress 14.4.0, WPJM 2.4.2,
  Applications 3.2.0, Resumes 2.2.0, Alerts 3.2.0, Bookmarks 1.5.0.
- **Everything unrelated is deactivated.** Original active list:
  `/private/tmp/.../scratchpad/active-plugins-before.txt` (session-scoped — if gone, the
  plugins are still installed, just inactive).
- `WP_DEBUG_LOG` is on in `wp-config.php`.
- Seeded actors: `bpjm-employer` (employer), `bpjm-candidate` / `bpjm-candidate2` /
  `bpjm-candidate3` (candidate), `bpjm-noname` (subscriber, no first/last name — the case
  that exposed the apply-form validation bug), `postactivation` (registered after
  activation — the case that proved the resume fix).
- Seeded IDs tracked in `scratchpad/seeded-ids.txt`. Delete by ID, never by title.
- `?autologin=<username>` works via the dev mu-plugin.

## Traps already paid for

- **Read every card comment before diagnosing.** Two root causes here were wrong until the
  comments were read — the fixes would have been wrong too.
- **opcache lies on A/B tests.** Wait ~4s between swapping a file and re-requesting, or
  assert with `wp eval`.
- **In wp-admin, `determine_locale()` returns the *user's* profile language**, not the site's.
  An i18n test looks broken until you set the test user's locale too.
- Use the pseudo-locale trick to prove translation readiness: build a catalogue where every
  msgstr is `«msgid»`, then read the screens — anything still plain English is unwrapped or
  third-party.
