# Role Matrix — BuddyPress Job Manager 2.8.5

> Two distinct access models: WP **capabilities** for admin pages, and **configured role lists** for member-facing tabs.

## Admin pages (capability-gated)

| Page | Admin | Editor | Author | Subscriber |
|---|---|---|---|---|
| WB Plugins / Job Manager settings | R/U | - | - | - |
| Our Plugins / Themes / Support | R | - | - | - |

Cap = `manage_options` (filterable via `bpjm_menu_page_capability`). Default: administrator only.

## Member-facing tabs (configured-role-gated, not capability-gated)

Access depends on whether the user's role is in `post_job_user_roles` /
`apply_job_user_roles` (option `bpjm_general_settings`). Defaults shown below
(`administrator,employer,guest` for posting; `administrator,candidate,subscriber`
for applying) — site owner can change these.

| Feature | post_job role | apply_job role | Other |
|---|---|---|---|
| Jobs tab (Post/My/Listings/Bookmarks/Alerts) | R/CRUD (own) | - | - |
| Resumes tab (Add/My/Applied) | - | R/CRUD (own) | requires `bpjm_resume_at_profile=yes` |
| Apply to a job | - | C | - |
| Infinite-scroll job feed (AJAX) | public (R) | public (R) | nopriv allowed |

Legend: C=Create, R=Read, U=Update, D=Delete, -=No access. "own" = limited to the member's own posts/resumes via WPJM dashboard scoping.

No custom capabilities are registered by this plugin.
