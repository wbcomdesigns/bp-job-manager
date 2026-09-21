# Troubleshooting

Symptoms and what to check. Because the plugin renders WP Job Manager and its add-ons rather
than re-implementing them, most "missing" screens trace back to an add-on that is not active
or a member who has nothing to show.

## A Jobs or Resumes tab is missing on a profile

This is usually expected, not a bug. A tab appears when the member actually has jobs or
resumes, or when they are viewing their own profile. On a stranger's profile an empty tab is
not shown. Check that the member has at least one published job or resume, and that you are
looking at the right person.

## The Resumes tab or Applied Jobs tab never appears

These tabs depend on add-ons:

- **Resumes** (My Resumes, Add Resume) needs the **Resume Manager** add-on active.
- **Applied Jobs** needs the **Applications** add-on active, and it appears under the Resumes
  tab, so Resume Manager must be active too.
- **My Bookmarks** needs **Bookmarks**; **Job Alerts** needs **Job Alerts**.

Confirm the add-on is active. The Overview settings screen reports which integrations it can
see.

## Add-on features are hidden on a multisite subsite

If the add-ons are network-activated but their tabs do not appear on a subsite, make sure you
are on 3.0.0 or later. Earlier versions read `active_plugins` directly, which reports every
network-activated add-on as inactive on every subsite. 3.0.0 detects add-ons with
`bpjm_is_plugin_active()`, which handles network activation correctly.

## The whole front end is blank after an update

3.0.0 and later stay dormant when BuddyPress or WP Job Manager is missing from the site's
files, for example after a failed update or an interrupted upload. If you saw a blank page on
older versions in that situation, reinstall the missing plugin (BuddyPress or WP Job Manager)
and the profile tabs return. The `Requires Plugins` header blocks activation but cannot stop
an already-active plugin whose dependency was deleted from disk, which is why the runtime gate
exists.

## The Submit Application button leads nowhere

Fixed in 3.0.0. The button previously pointed at an "Apply To Job" page the plugin no longer
creates. It now links to the job listing, where WP Job Manager and the Applications add-on
show the application form. If you still see a dead button, update to 3.0.0 or later.

## Styles or scripts are missing on the Job Alerts or My Bookmarks tab

Fixed in 3.0.0. The four separate lists of owned profile screens were consolidated into one,
so every tab now loads the same assets. If a custom integration adds its own profile action,
register it through the `bpjm_owned_profile_actions` filter so assets load there too.

## The Send Private Message link is missing

The link is shown only when the BuddyPress **Messages** component is active. Enable Messages
under BuddyPress components if you want members to message candidates.

## New jobs or resumes are not showing in the activity feed

Both are off unless enabled. Turn on **Post job activity** and **Post resume activity** under
**WB Plugins → Job Manager → General**. The resume toggle only appears when Resume Manager is
active.

## The job author is not notified when someone applies

Application notifications need the **Applications** add-on active and the **Application
notifications** toggle on in the General settings. Note the notification text filter is the
legacy `_bprwg_notification_text`; a custom callback on that hook can suppress the text.

## Nav counts on the tabs look stale

The tab counts are cached in the `bpjm_user_{ID}_job_listings` and
`bpjm_user_{ID}_my_resumes_count` transients and cleared automatically when a job or resume
changes status. If a count looks wrong, changing the status of one job or resume for that
member clears and rebuilds it.

## A profile screen shows in English on a translated site

The screens on the Jobs and Resumes tabs are rendered by WP Job Manager and its add-ons, so
their strings come from those plugins' translations, not this one's. Make sure the host
plugin's translation for your locale is installed. This plugin's own strings (tab labels,
notices) ship in its POT file.

## Related

- [FAQ](../faq/index.md)
- [Settings](../settings/index.md)
- [Developer guide](../developer-guide/index.md)
