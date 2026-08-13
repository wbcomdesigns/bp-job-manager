=== Wbcom Designs - BuddyPress Job Manager ===
Contributors: wbcomdesigns, vapvarun
Donate link: https://wbcomdesigns.com/donate/
Tags: buddypress, wp-job-manager, job-manager, resume-manager, jobs
Requires at least: 6.5
Tested up to: 7.0
Stable tag: 3.0.0
Requires PHP: 8.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: bp-job-manager
Domain Path: /languages

Brings WP Job Manager into BuddyPress member profiles: post and manage jobs, list resumes, track applications, and publish job activity to the stream.

== Description ==

**BuddyPress Job Manager** connects WP Job Manager to your BuddyPress community. Instead of sending members to a separate job board page, it puts a Jobs tab (and, with the Resume Manager add-on, a Resumes tab) right on the member profile, so posting a job, managing listings, and browsing resumes all happen inside the community.

It needs two plugins: BuddyPress and WP Job Manager. That is the whole hard requirement. The WP Job Manager add-ons (Resume Manager, Applications, Bookmarks, Job Alerts) are entirely optional, and the plugin is built to run without them: each one simply unlocks an extra tab or setting when you install it.

= What you get =

**Jobs on the member profile**
* A Jobs tab on member profiles with a My Jobs dashboard to post, edit, and track listings.
* A Post Job tab that keeps job submission inside the profile instead of sending members to a standalone page.
* Load-more / infinite scroll on the My Jobs dashboard so long listing histories stay fast.
* Choose which member roles can post jobs and which can apply, from one settings screen.
* Members whose role cannot post jobs get a read-only listing view, with no Add Job button.

**Resumes on the member profile (needs the Resume Manager add-on)**
* A Resumes tab with My Resumes and Add Resume, so candidates can keep several resumes.
* Members pick which of their resumes shows on their BuddyPress profile from a dropdown on the profile settings. If they never pick one, the most recently updated resume is shown as a fallback.
* Choose which resume fields are visible on the profile, per member.
* A "Send Private Message" link on candidate contact details, using BuddyPress Messages.

**Applications (needs the Applications add-on)**
* An Applied Jobs tab on the profile, paginated for members with long application histories.
* A BuddyPress notification to the job author when someone applies, with its own on/off setting.

**Bookmarks and Job Alerts (need their matching add-ons)**
* A My Bookmarks tab on the profile for jobs the member saved.
* A Job Alerts tab so members manage their alerts without leaving the community.

**Activity stream**
* Publish new jobs and new resumes to the BuddyPress activity stream, each with its own admin toggle.
* Activity is removed automatically when a job or resume is trashed or deleted, so the stream never keeps dead links.

**Quick access and guests**
* Jobs and Resumes menus in the WordPress admin bar with live counts and submenus for My Jobs, Post Job, My Resumes, Applied Jobs, and Add Resume.
* An optional "Guest" role for job posting and applying, so people who are not logged in can take part when you allow it.

= Perfect For =

* Community sites that want a careers area without a separate job board
* Professional and alumni networks matching members to openings
* Niche industry communities where employers and candidates are already members
* Membership sites adding hiring on top of an existing BuddyPress profile

= Premium Support =

Our dedicated support team is ready to help you get the most out of this plugin. Whether you need help with setup, customization, or troubleshooting, we're here to assist.

= Documentation =

* **[Documentation and Support](https://docs.wbcomdesigns.com/)** - Setup walkthrough, settings reference, and usage guides
* The plugin's Help & Support tab covers setup and common questions inside the admin itself

= Translations =

* English (default)
* Ready for translation in your language with included POT file

= Links =

* [Plugin Homepage](https://wbcomdesigns.com/downloads/buddypress-job-manager/)
* [Documentation](https://docs.wbcomdesigns.com/)
* [Support](https://wbcomdesigns.com/support/)
* [Request Features](https://wbcomdesigns.com/contact/)

= Compatibility =

* WordPress 6.5 and higher
* PHP 8.0 and higher
* BuddyPress 12.0+ - required
* WP Job Manager - required
* WP Job Manager add-ons (Resume Manager, Applications, Bookmarks, Job Alerts) - all optional, each unlocks its own tab
* Works with BP Rewrites
* Tested with popular themes including BuddyX, Reign and Youzify

= What's New in 2.8.5 =

The admin settings screen is rebuilt on the modern Wbcom card-panel layout with sidebar navigation, and your saved settings are unchanged. The Applied Jobs tab now uses real pagination, profile nav counts are cached, and the Add Job button reliably opens the in-profile Post Job tab. Job and resume activity is now cleaned up automatically when the listing is trashed or deleted, so the stream never keeps dead links. Dark mode rendering for WP Job Manager surfaces is fixed across BuddyX, BuddyX Pro and Reign.

== More Free Tools from Wbcom Designs ==

BuddyPress Job Manager turns member profiles into a hiring hub for jobs, resumes, and applications. A careers area works best inside a community members already want to spend time in, so these other free Wbcom Designs tools build out the space around it, from the theme and social network itself to forums, media, events, gamification, directories, and courses.

* **[BuddyX](https://wbcomdesigns.com/downloads/buddyx-theme/)** - A free, fast community theme for BuddyPress, BuddyBoss and PeepSo with a modern layout and dark mode.
* **[BuddyNext](https://wbcomdesigns.com/downloads/buddynext/)** - Stand up a complete WordPress community with activity streams, member spaces, profiles, direct messaging, and built-in moderation.
* **[Jetonomy](https://wbcomdesigns.com/downloads/jetonomy/)** - Add forums, question-and-answer boards, and idea spaces that stay tidy through trust-based auto-moderation even past 100,000 topics.
* **[Mediaverse](https://wbcomdesigns.com/downloads/mediaverse/)** - Let members build photo and video albums, react, follow each other, and message privately while AI moderation keeps things clean.
* **[Eventonomy](https://wbcomdesigns.com/downloads/eventonomy/)** - Run community events with RSVPs, calendars, and front-end submissions.
* **[WB Gamification](https://wbcomdesigns.com/downloads/wordpress-gamification-plugin/)** - Reward members with points, badges, and leaderboards to keep engagement high.
* **[Listora](https://wbcomdesigns.com/downloads/listora/)** - Publish searchable directories across ten listing types with reviews, maps, and member-submitted entries from the front end.
* **[WP Career Board](https://wbcomdesigns.com/downloads/wp-career-board/)** - Add a job board with front-end listings, applications, and employer profiles.
* **[Learnomy](https://wbcomdesigns.com/downloads/learnomy/)** - Build and sell online courses, auto-grade quizzes, collect payments, and award certificates when learners finish.

== Installation ==

= Automatic Installation (Recommended) =

1. Make sure BuddyPress and WP Job Manager are installed and active first
2. Log in to your WordPress admin dashboard
3. Navigate to Plugins > Add New
4. Search for "BuddyPress Job Manager"
5. Click "Install Now" on the Wbcom Designs - BuddyPress Job Manager plugin
6. After installation, click "Activate"
7. Go to WB Plugins > BuddyPress Job Manager > General to choose which roles can post and apply for jobs

= Manual Installation =

1. Upload the entire `bp-job-manager` folder to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure settings at WB Plugins > BuddyPress Job Manager

= Post-Installation Setup =

After activating the plugin, we recommend:

1. Visit **WB Plugins > BuddyPress Job Manager > General** and pick the roles allowed to post jobs and to apply for jobs
2. Decide whether new jobs and new resumes should publish to the BuddyPress activity stream
3. Install any WP Job Manager add-ons you want (Resume Manager, Applications, Bookmarks, Job Alerts) to unlock their profile tabs
4. Visit a member profile and confirm the Jobs tab appears for a member in an allowed role
5. See the [documentation](https://docs.wbcomdesigns.com/) for detailed instructions

= Requirements =

* WordPress 6.5 or higher
* PHP 8.0 or higher
* BuddyPress 12.0 or higher - required
* WP Job Manager - required
* WP Job Manager add-ons - all optional

== Frequently Asked Questions ==

= What are the plugin requirements? =

Two plugins, and only two:

1. [BuddyPress](https://buddypress.org/download/)
2. [WP Job Manager](https://wpjobmanager.com/)

If either is missing, the plugin shows an admin notice and stays inactive.

= Which WP Job Manager add-ons do I need? =

None of them. Every add-on is optional, and each one unlocks an extra part of the plugin when you install it:

* [Resume Manager](https://wpjobmanager.com/add-ons/resume-manager/) - unlocks the Resumes tab (My Resumes and Add Resume), resume display on the profile, and the resume field visibility controls.
* [Applications](https://wpjobmanager.com/add-ons/applications/) - unlocks the Applied Jobs tab and the BuddyPress notification to the job author when someone applies.
* [Bookmarks](https://wpjobmanager.com/add-ons/bookmarks/) - unlocks the My Bookmarks tab on the profile.
* [Job Alerts](https://wpjobmanager.com/add-ons/job-alerts/) - unlocks the Job Alerts tab on the profile.

Without any add-ons, the plugin still gives you the Jobs tab, the My Jobs dashboard, in-profile job posting, role controls, admin bar menus, and job activity in the stream. The settings screen tells you which options are still locked and which add-on unlocks them.

= What does the "Resume on profile" setting actually control? =

Exactly what its label says. When you turn off "Show the latest resume on the member's BuddyPress profile", the resume is no longer displayed on the profile - and nothing else changes. The Resumes tab and its **My Resumes**, **Add Resume** and **Applied Jobs** subnavs stay available, so members can still manage their resumes and see their applications. Those subnavs depend only on the Resume Manager add-on being active.

= Which resume shows on a member's profile? =

The member chooses. On their profile settings they get a "Select resume to display at profile" dropdown listing their resumes, and whichever they pick is the one shown. If a member never picks one, the plugin falls back to their most recently updated resume.

= How do I control who can post jobs and who can apply? =

Go to WB Plugins > BuddyPress Job Manager > General. Both are role pickers, so you select any roles that exist on your site. There is also a "Guest (not logged in)" option in each picker if you want people who are not logged in to be able to post or apply.

= Do I need "employer" and "candidate" roles? =

No. The plugin ships `employer` and `candidate` as default selections because WP Job Manager and Resume Manager can create roles with those names, but this plugin never creates them itself. On a stock site those roles may not exist at all, in which case they simply match nobody. Pick whichever roles your site actually uses, for example Subscriber, and the gating follows your choice.

= Does the job author get notified when someone applies? =

Yes, when the Applications add-on is active. The job author receives a BuddyPress notification for each application, and you can turn it off under Application notifications in the General settings.

= Are jobs and resumes added to the activity stream? =

Yes, if you enable it. New jobs and new resumes each have their own toggle in the settings. When a job or resume is later trashed or deleted, its activity entry is removed automatically, so members never click through to a listing that no longer exists.

= Can members message a candidate? =

Yes. When the BuddyPress Messages component is active, candidate contact details include a "Send Private Message" link that opens a BuddyPress private message to that candidate.

= Will this work with my theme? =

The plugin has been tested with popular BuddyPress themes including BuddyX, Reign, and Youzify, and it follows BuddyPress template standards, so it should work with any properly coded BuddyPress-compatible theme. WP Job Manager surfaces are styled for dark mode in BuddyX, BuddyX Pro and Reign. BP Rewrites is supported.

= Is the plugin translation ready? =

Yes. The plugin is fully translation ready with an included POT file.

= Where can I get support? =

We offer comprehensive support through multiple channels:
* [Documentation](https://docs.wbcomdesigns.com/) - Free guides for every Wbcom plugin
* [Support](https://wbcomdesigns.com/support/) - Get help from our team
* [Contact us](https://wbcomdesigns.com/contact/) - Report bugs and request features

= How often is the plugin updated? =

We regularly update the plugin to ensure compatibility with the latest versions of WordPress, BuddyPress, and WP Job Manager. Security patches are released promptly when needed.

== Screenshots ==

1. My Jobs dashboard on a member profile - post, manage, and track job listings with an Add Job button.
2. Read-only Jobs tab for a member whose role cannot post jobs - listings only, no Add Job button.
3. Admin settings - choose which member roles can post jobs and apply, plus activity options.
4. Discover more free Wbcom Designs community plugins from the admin screen.

== Changelog ==

= 3.0.0 - August 2026 =

A major simplification. This release removes settings and stored data rather than adding
features: the plugin now defers to WP Job Manager for permissions, renders its add-ons'
own screens instead of copies, and stores nothing but its settings. It also fixes a
front-end crash and completes translation readiness.

Please read the upgrade notice below before updating.

Fixes a front-end crash on sites missing a required plugin, corrects add-on detection on multisite, and makes the Jobs and Resumes tabs consistent. Translation corrections across all five locales.

* New      - The two role fields on the General tab are now searchable and show each selected role as a removable chip, with a clear-all control. With JavaScript disabled the original multi-select still works.
* Improve  - The Jobs and Resumes profile tabs now use the same wording and the same layout. Each shows a count on the tab and the owner's name on the screen below it, where the Jobs tab previously showed only a generic label.
* Fix      - Every visitor except administrators got a blank page on the whole front end when BuddyPress or WP Job Manager was removed from the site's files while this plugin stayed active, for example after a failed update or an interrupted upload. The plugin now stays dormant instead of loading without what it needs.
* Fix      - On multisite, the Resume Manager, Applications, Bookmarks and Alerts add-ons were always reported as inactive on subsites when they had been activated for the whole network, which hid the resume and application features.
* Fix      - The Overview tab reported three roles allowed to post jobs on a new install when only two were actually allowed.
* Fix      - Application notifications attached themselves to a retired WP Job Manager hook whenever another plugin happened to use it, which could stop job authors being notified.
* Fix      - Logged-out visitors on the job submission page were shown a "not authorized" notice with no sign-in or registration box, leaving no way to continue. The sign-in box is no longer suppressed.
* Fix      - The Roles allowed to apply for jobs field offered a Guest option that the plugin never acted on, so it appeared to enable something that did nothing. The option has been removed from that field; it remains available for posting jobs.
* Fix      - Removed the Send Feedback link on the Help & Support tab, which pointed at a page that no longer exists.
* Fix      - Plugin styles and scripts did not load on the Job Alerts and My Bookmarks profile tabs, and scripts also missed Job Alerts. The four separate lists of owned profile screens are now one list, so every tab loads the same assets.
* Fix      - The has_wpjm_shortcode filter discarded the incoming value, which could suppress WP Job Manager's own frontend stylesheet on pages outside a member profile.
* Fix      - German: corrected the typo "Lebenslaufffelder" to "Lebenslauffelder" on the resume field options.
* Fix      - German: corrected "Stellenautor erhalten" to "Stellenautoren erhalten" in the application notification description.
* Fix      - German: added the missing hyphen in nine proper-noun compounds, such as "BuddyPress-Benachrichtigung".
* Fix      - Spanish, French, Italian and Portuguese (Brazil): the Discover tab description was flagged fuzzy, so it was missing from the compiled .mo file while present in the .l10n.php file. Both compiled files now agree.
* Fix      - The WP Job Manager add-on names "Resume Manager" and "Applications" were translated in some locales, so site owners were told to install add-ons under names that do not exist. They are now treated as product names, matching how BuddyPress and WP Job Manager are already handled.
* Fix      - French: one string used vouvoiement while the rest of the file uses the informal register declared for this locale.
* Fix      - French: "Display video link" was translated as "Display the video", which told members they were showing the whole video rather than a link to it.
* Fix      - French: the Jobs and Resumes tab counters used different words for jobs and resumes than the rest of the interface.
* Fix      - Portuguese (Brazil): the On and Off labels used the wording for a physical switch instead of the wording Brazilian software uses for a setting.
* Fix      - Spanish: the heading on another member's jobs screen reused the translation of the tab counter, so the two were indistinguishable.
* Fix      - seconds_to_words() returned null instead of a string when a duration landed exactly on zero.
* Fix      - get_posts_by_author_sql() received an empty string where a boolean was expected.
* Fix      - remove_filter() was called with a fourth argument it does not accept.
* Fix      - Bp_Job_Manager_Globals was constructed with two arguments its constructor does not accept; both were silently discarded.
* Fix      - The settings and overview views could pass null to array_key_exists() if included outside the admin page renderer.
* Security - New installs no longer allow logged-out visitors to post job listings. The Guest pseudo-role was included in the default posting roles, which opened anonymous job submission on a fresh install. Existing sites keep whatever they have saved.
* Improve  - Removed 147 obsolete entries from the French translation and 13 from each other locale, and corrected the Language header in four locale files.
* Dev      - Added phpstan.neon, phpcs.xml.dist and composer.json. PHPStan level 5 now reports zero errors; it previously reported 27 and could not run reproducibly without a config.
* Dev      - New filter bpjm_owned_profile_actions to adjust which profile screens load this plugin's assets.
* Dev      - New filter bpjm_jobs_tab_name for the Jobs subnav label, matching the existing bpjm_resume_tab_name.
* Dev      - Application notifications now always bind to job_manager_applications_new_job_application. The deprecated new_job_application hook is no longer used; anything that removed our callback from it must target the current hook.
* Dev      - New helper bpjm_is_plugin_active() replaces direct active_plugins lookups so add-on detection also covers network activation.
* Dev      - The strings "%s Jobs" and "%s's Jobs" and "%s's Resumes" were replaced by "Jobs by %s" and "Resumes by %s" so translators can place their own preposition. Update any custom translation that overrode them.
* Dev      - The POT build now runs WP-CLI's i18n audit instead of skipping it, so unwrapped strings and inconsistent translator comments fail the build.

= 2.8.5 - July 2026 =

* New      - Added German, Spanish, French, Italian and Portuguese (Brazil) translations.
* New      - Job and resume activity is now removed automatically when the listing is trashed or deleted, so the activity stream no longer keeps links to listings that are gone.
* Improve  - Rebuilt the admin settings screen on the modern Wbcom card-panel layout (sidebar navigation, Overview, General, Help & Support, and Discover); saved settings and data are unchanged.
* Improve  - Applied Jobs tab now uses real LIMIT/OFFSET pagination with batched post-cache priming, removing the per-row query on large lists.
* Improve  - Profile Jobs and Resumes nav counts are cached in per-user transients and invalidated on job status change or deletion.
* Improve  - The settings screen now states which WP Job Manager add-ons unlock the remaining options, instead of hiding them without explanation.
* Fix      - The bundled French translation could never load: the text domain was not registered. It now loads on init.
* Fix      - The Personal Information, URL(s) and Education profile headings had no translatable source.
* Fix      - A double-escaped heading rendered HTML entities instead of accented characters in translated languages.
* Fix      - Add Job button on the profile dashboard now opens the in-profile Post Job tab instead of an empty link or a page outside the profile.
* Fix      - Plugin and WP Job Manager frontend styles now load on the profile Jobs tab for owners whose role cannot post jobs.
* Fix      - WP Job Manager dark surfaces (dashboard buttons, notices, actions popover, submit preview) now render correctly in BuddyX, BuddyX Pro and Reign dark mode.
* Fix      - Resolved a fatal ArgumentCountError in the load-more job rows on WP Job Manager 2.3 and later.
* Fix      - Public CSS and JS now enqueue with the correct plugin version for cache busting.
* Security - Restricted the load-more jobs AJAX response to the profile owner's published jobs and rejected requests with no resolvable member.
* Dev      - Fixed all WordPress Coding Standards violations and cleared all Plugin Check errors; removed leftover debug logging and localized the loading string.
* Compat   - Aligned with WP Job Manager 2.3 and later dashboard action hooks.

= 2.8.2 =
* Fix: Explicitly cast float to int in `seconds_to_words` function to prevent deprecated warnings about implicit conversion.
* Fix: Resolved issue with the resume-manager tab not displaying correctly on single profile pages.
* Fix: Improved UI for the "My Jobs" listing tab on single profile pages.
* Fix: Prevented guest users from posting jobs.
* Fix: Addressed database query error in `bpjm_resume_settings_before_submit` function.
* Fix: Corrected an incorrect feedback form URL.
* Fix: Resolved issue with resume fields not hiding on the profile page.
* Fix: Resolved issue with profile visibility settings.
* Enhancement: Added an admin notice to alert users when no resumes are created in Youzify.
* Enhancement: Added a filter to modify resume field text in the profile menu.
* Enhancement: Introduced functionality to hide or display resume fields selectively within Youzify.

= 2.8.1 =
* Added "Guest" role option to Job Posting and Application settings.
* Enabled non-logged-in users to submit job postings by adding the "Guest" role to allowed job posting roles.

= 2.8.0 =
* Added job menu to the admin bar with consistent use of existing count functions.
* Implemented WPML support for job and resume count functions.
* Improved UI for job and resume tabs and job dashboard for better user experience.
* Managed "Apply Job" button compatibility with Reign theme and displayed only published jobs.
* Enhanced language clarity in General and Welcome Settings for user guidance.
* Added nonce verification to secure form data processing.
* Updated database queries to use `$wpdb->prepare()` for security.
* Fixed mismatched text domains to improve translation consistency.
* Resolved deprecated function warnings and used `job_manager_applications_new_job_application` hook.
* Added role validation and error handling for undefined variables.
* Fixed pagination, undefined capability variables, and removed duplicate files.
* Cleaned up code by removing unnecessary variables and grouping `define` functions.
* Fixed PHP deprecated warnings and improved compatibility with logged-out users.
* Added transient flushing for new installs and simplified settings notices.

= 2.7.1 =
* Fix- Compatibility check with WordPress 6.5.0

= 2.7.0 =
* Fix- updated screenshot
* Fix- Fixed page loading notification issue
* Fix- PHPCS Fixes

= 2.6.6 =
* Fix - phpcs fixes

= 2.6.5 =
* Fix - Readme file updated
* Fix - (#136) Fixed application redirects to wrong url

= 2.6.4 =
* Fix - update Backend UI

= 2.6.3 =
* Fix - (#123)Fixed continue submission link issue
* Fix - phpcs fixes

= 2.6.2 =
* Fix - PHPCS fixes

= 2.6.1 =
* Fix - Removed install Free plugin from WordPress Wrapper

= 2.6.0 =
* Fix - (#127) Fixed job and resume tab redirection to login user profile
* Fix - (#122) Fixed unable to select a role on admin general tab setting page
* Fix - (#102) Fixed resume tab translation issue
* Fix - Deactivate plugin if BP and WP job Manager Plugin is not activated

= 2.5.0 =
* Fix - Update plugin backend UI
* Fix - (#109)Fixed job expired relist button not working
* Fix - (#108) Fixed Expired Resumes - shows "Relistsa" instead of "Relist"
* Fix - (#104) Fixed Job expired is not showing for the user
* Fix - (#84)Fixed set default enable resume visibility settings
* Fix - (#103) Fixed job counter issue

= 2.4.0 =
* Fix - translation fixes
* Fix - (#77) Fixed designing glitch with buddyboss theme
* Fix - (#66)Fixed apply button for displayed user's job
* Fix - (#64) Added class exist condition
* Fix - (#73)enqueued scripts on only plugin pages
* Fix - (#70)Removed "add resume" link for displayed user
* Fix - (#65) Remove action coloum from job dashbord page.

= 2.3.0 =
* Enhancement - Resume table and table data content dynamic
* Fix - (#62) Responsive Table issue fixed
* Enhancement - (#57) Added admin notice to review the plugin
* Enhancement - (#58) Hide apply button on marked filled job
* Enhancement - (#58) Remove Applications column
* Fix - (#55) Pagination Issue

= 2.2.1 =
* Fix : updated Job Tab condition logic to display on selected user roles.

= 2.2.0 =
* Fix: Job Listing at displayed user profile

= 2.1.0 =
* Enhancement - Added plugin in-dependency from WP Job Manager - Application Manager plugin. #14
* Enhancement - Added plugin in-dependency from WP Job Manager - Resume Manager plugin. #26
* Enhancement - A sitewide buddypress activity will be created on posting job application. #42
* Enhancement - A sitewide buddypress activity will be created on posting a resume. #44
* Enhancement - Admin option to enable/disable bp activity creation on posting job and resume. #45
* Enhancement - Added bp notification for job author when an application is submitted on his job. #27

= 2.0.0 =
* Enhancement - Improve Backend UI where you can all manage wbcom plugin's settings at one place. #38

= 1.0.7 =
* Fix - Fixed profile page error.

= 1.0.6 =
* Fix - Fixed issues with WP Job Manager listing page.
* Fix - Fixed issues with Edit, Duplicate and Delete functionality on BuddyPress Page.
* Enhancement - Added 'Private message' on candidate contact button.

= 1.0.5 =
* Fix - Translation fixes

= 1.0.4 =
* Enhancement - Setting to show recently updated or desired resume at BuddyPress profile view.
* Enhancement - Resume listing at BuddyPress profile.

= 1.0.3 =
* Enhancement - Code Quality enhancement with WPCS

= 1.0.2 =
* Fix - Post a Job for employer is not working when member slug is different
* Fix - Fixed 404 errors on certain tabs
* Fix - PHP warning and errors
* Tweak - Tab name changed to Post a New Job for Employer
* Enhancement - Add tab for Applied Job list
* Enhancement - Added options to display Job and Resume menu inside BuddyPress admin bar
* Enhancement - Added Support for WordPress Job Manager - Bookmarks
* Enhancement - Added Support for WordPress Job Manager - Alerts

= 1.0.1 =
* Fix - Warning display on the page.
* Fix - When the user submits listing it blocks page and display warning.
* Fix - After plugin install error generated on backend and frontend.
* Fix - Error in plugin settings page
* Fix - HTML display with applied jobs in Jobs page
* Fix - Error in user profile job tab
* Fix - Settings are not working.
* Fix - Saved jobs display incorrect count number and "save job" button is missing.

= 1.0.0 =
* first version.

== Upgrade Notice ==

= 3.0.0 =
Major release. Your jobs, resumes and applications are untouched - this plugin has never stored any of them. What changes is configuration. The "Roles allowed to post jobs" and "Roles allowed to apply for jobs" settings are retired in favour of WP Job Manager's own permission, and existing values are honoured automatically. Members no longer need to opt in to show a resume on their profile; a published resume now appears there by default. My Resumes and Applied Jobs are rendered by Resume Manager and Applications directly, so they gain application status and every translation those plugins ship - if you styled either table with custom CSS, check it after updating.

= 2.8.5 =
Modernized admin settings screen, real pagination on Applied Jobs, cached profile nav counts, automatic activity cleanup for deleted jobs and resumes, and dark mode fixes for WP Job Manager surfaces. Settings and data are unchanged.
</content>
</invoke>
