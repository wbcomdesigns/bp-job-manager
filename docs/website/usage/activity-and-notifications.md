# Activity and notifications

The plugin can post to the BuddyPress activity stream and send BuddyPress notifications. All three options are off until you save them once. See [Settings](../settings/index.md#general).

## Activity updates

| Option | Needs | Update shown |
|---|---|---|
| Post job activity | Nothing extra | "Member posted a job: Job title", linked to the job |
| Post resume activity | Resume Manager | "Member published a resume", linked to the resume |

- The update is created when the job or resume becomes published. A job that waits for approval creates its update when it is approved, in the author's name.
- Hiding a job or resume and publishing it again does not add a second update.
- Editing a published job updates the title in its existing update. If the job had no update, because the option was off when it was published, editing does not create one.
- Moving a job or resume to the trash or deleting it removes its update.
- Updates are not created for jobs or resumes published before you turned the option on.
- Entries appear in the activity filter as **Jobs** and **Resumes**, on the site-wide stream and on member profiles. They display in each reader's language.

## Notifications

With **Application notifications** on, the Applications add-on active and the BuddyPress Notifications component active:

| Who | When | Text | Opens |
|---|---|---|---|
| Job author | Someone applies | "Member applied for Job title." | The job's applications in My Jobs |
| Candidate | The employer changes the application's status | "Your application for Job title is now: Status." | Applied Jobs |

- Archiving an application does not notify the candidate.
- Opening the job's applications marks the employer's notifications for that job as read. Opening Applied Jobs marks the candidate's status notifications as read.
- There is one setting for both notifications. You cannot turn one off without the other.

The employer text can be changed with the `_bprwg_notification_text` filter. See the [Developer Guide](../developer-guide/index.md#filters).
