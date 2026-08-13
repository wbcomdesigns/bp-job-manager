# Role Matrix — BuddyPress Job Manager

> **Obsolete as of 3.0.0.** This plugin no longer defines or gates on roles.
>
> It had two role arrays - `post_job_user_roles` and `apply_job_user_roles` - which were a
> second permission system running alongside WP Job Manager's. They were the direct cause
> of members being shown an employer dashboard they were then refused, and of a whole
> fallback screen that existed only to serve the gating. All of it was removed in 3.0.0.

## What decides access now

| Question | Answered by |
|---|---|
| May this member post a job? | WP Job Manager, via `job_manager_user_can_post_job` |
| May this member apply? | Any logged-in member (WP Job Manager Applications' own model) |
| Does this member get a Jobs tab? | They have jobs, or it is their own profile |
| Does this member get a Resumes tab? | They have resumes, or it is their own profile |
| Is a resume visible? | Resume Manager's own published / hidden state |

Accessors: `bpjm_user_can_post_job()` / `bpjm_user_can_apply_job()` in
`includes/class-bp-job-manager-function.php`. See
[`docs/website/developer-guide`](../docs/website/developer-guide/index.md).
