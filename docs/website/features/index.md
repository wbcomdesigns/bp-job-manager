# Features

Everything the plugin does sits on one of two journeys: hiring (post a job, see it on your
profile, learn who applied) and looking for work (add a resume, show it on your profile,
apply from a listing, track each application). The features below are what those journeys
look like on a member profile.

## Jobs on the member profile

- A **Jobs** tab on member profiles with a **My Jobs** dashboard to post, edit, and track
  listings.
- A **Post Job** tab that keeps job submission inside the profile instead of sending members
  to a standalone page.
- Load-more on the My Jobs dashboard so long listing histories stay fast; it is author
  scoped, so non-owners only ever see published jobs.
- Nothing to configure. A member who can post a job gets the Jobs tab, and WP Job Manager
  decides who may post.

## Resumes on the member profile

Needs the **Resume Manager** add-on.

- A **Resumes** tab with **My Resumes** and **Add Resume**, so candidates can keep several
  resumes.
- Resumes are rendered by Resume Manager itself, so they gain its status column and its
  translations rather than a re-implemented copy.
- A **Send Private Message** link on candidate contact details, using BuddyPress Messages
  (shown only when the Messages component is active).

## Applications

Needs the **Applications** add-on.

- An **Applied Jobs** tab (under the Resumes tab) that lists every application with the
  status the employer set.
- A BuddyPress notification to the job author when someone applies, with its own on/off
  setting.

## Bookmarks and Job Alerts

Need their matching add-ons.

- A **My Bookmarks** tab for jobs the member saved (Bookmarks add-on).
- A **Job Alerts** tab so members manage their alerts without leaving the community (Job
  Alerts add-on).

## Activity stream

- Publish new jobs and new resumes to the BuddyPress activity stream, each with its own admin
  toggle.
- Activity is removed automatically when a job or resume is trashed or deleted, so the stream
  never keeps dead links.

## Quick access from the admin bar

- Jobs and Resumes menus in the WordPress admin bar with live counts and submenus for My
  Jobs, Post Job, My Resumes, Applied Jobs, and Add Resume.

## What it never does

- It creates no pages, owns no database tables and no post types, and adds no REST routes.
- It keeps no second list of roles. Permissions defer entirely to WP Job Manager.
- It stores nothing but its own settings and a small nav-count cache.

## Related

- [Getting started](../getting-started/index.md)
- [Usage](../usage/index.md)
- [Settings](../settings/index.md)
