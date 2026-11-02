'use strict';
module.exports = function ( grunt ) {

	// load all grunt tasks matching the `grunt-*` pattern
	// Ref. https://npmjs.org/package/load-grunt-tasks
	require( 'load-grunt-tasks' )( grunt );
	grunt.initConfig(
		{
			pkg: grunt.file.readJSON( 'package.json' ),

				// Check text domain
			checktextdomain: {
				options: {
					text_domain: [ 'bp-job-manager', 'buddypress' ,'wp-job-manager-resumes'], // Specify allowed domain(s)
					keywords: [ // List keyword specifications
						'__:1,2d',
						'_e:1,2d',
						'_x:1,2c,3d',
						'esc_html__:1,2d',
						'esc_html_e:1,2d',
						'esc_html_x:1,2c,3d',
						'esc_attr__:1,2d',
						'esc_attr_e:1,2d',
						'esc_attr_x:1,2c,3d',
						'_ex:1,2c,3d',
						'_n:1,2,4d',
						'_nx:1,2,4c,5d',
						'_n_noop:1,2,3d',
						'_nx_noop:1,2,3c,4d'
					]
				},
				target: {
					files: [ {
						src: [
						'*.php',
						'**/*.php',
						'!node_modules/**',
						'!options/framework/**',
						'!tests/**'
							], // all php
						expand: true
					} ]
				}
			},
				// make po files
			makepot: {
				target: {
					options: {
						cwd: '.', // Directory of files to internationalize.
						domainPath: 'languages/', // Where to save the POT file.
						exclude: [ 'node_modules/*', 'options/framework/*' ], // List of files or directories to ignore.
						mainFile: 'index.php', // Main project file.
						potFilename: 'bp-job-manager.pot', // Name of the POT file.
						potHeaders: { // Headers to add to the generated POT file.
							poedit: true, // Includes common Poedit headers.
							'Last-Translator': 'Varun Dubey',
							'Language-Team': 'Wbcom Designs',
							'report-msgid-bugs-to': '',
							'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
						},
						type: 'wp-plugin', // Type of project (wp-plugin or wp-theme).
						updateTimestamp: true // Whether the POT-Creation-Date should be updated without other changes.
					}
				}
			},
				// Clean dist folder
			clean: {
				dist: ['dist']
			},
				// Copy files to dist folder
			copy: {
				dist: {
					files: [{
						expand: true,
						src: [
							'**',
							'!node_modules/**',
							'!dist/**',
							'!.git/**',
							'!.github/**',
							'!.wordpress-org/**',
							'!.gitignore',
							'!.gitattributes',
							// Dev tooling. `vendor/` is Composer's DEV tree - PHPStan and
							// PHPCS - and nothing in it is loaded at runtime. Without
							// this line the zip ships 2699 files and 19MB of it.
							'!vendor/**',
							'!composer.json',
							'!composer.lock',
							'!package-lock.json',
							'!package.json',
							'!gruntfile.js',
							'!Gruntfile.js',
							'!phpcs.xml',
							'!phpcs.xml.dist',
							'!phpstan.neon',
							'!phpstan-bootstrap.php',
							'!phpunit.xml',
							'!tests/**',
							'!*.md',
							'!audit/**',
							// Docs live in the repo for contributors, not in the zip.
							'!docs/**',
							'!.wbcom-i18n.json',
							'!*.zip'
						],
						dest: 'dist/bp-job-manager/'
					}]
				}
			},
				// Create zip file
			compress: {
				dist: {
					options: {
						// Versioned, like every other plugin in the free series. An
					// unversioned asset is ambiguous on a release page and makes it
					// impossible to tell two downloads apart. Keep package.json's
					// version in step with the plugin header.
					archive: 'dist/bp-job-manager-<%= pkg.version %>.zip'
					},
					files: [{
						expand: true,
						cwd: 'dist/',
						src: ['bp-job-manager/**'],
						dest: ''
					}]
				}
			}
		}
	);

	// register task  'checktextdomain', 'makepot',
	grunt.registerTask( 'default', [ 'checktextdomain','makepot' ] );

	// Build task - creates clean dist folder and zip file
	grunt.registerTask( 'build', [ 'clean:dist', 'copy:dist' ] );

	// Dist task - creates distribution zip file
	grunt.registerTask( 'dist', [ 'clean:dist', 'copy:dist', 'compress:dist' ] );
};
