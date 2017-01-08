# Change Log

## [0.0.40](https://github.com/REBELinBLUE/deployer/tree/0.0.40) (2016-11-24)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.39...0.0.40)

**Implemented enhancements:**

- Add support to webhook for Gogs & Gitea services [\#289](https://github.com/REBELinBLUE/deployer/pull/289) ([axeloz](https://github.com/axeloz))

**Fixed bugs:**

- Undefined index: DB\_TYPE [\#278](https://github.com/REBELinBLUE/deployer/issues/278)
- Projects using git submodules are not supported  [\#264](https://github.com/REBELinBLUE/deployer/issues/264)
- Allow the beanstalkd port to be supplied [\#281](https://github.com/REBELinBLUE/deployer/pull/281) ([uLow](https://github.com/uLow))

## [0.0.39](https://github.com/REBELinBLUE/deployer/tree/0.0.39) (2016-10-23)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.38...0.0.39)

**Fixed bugs:**

- node socket.js doesn't work with SSL when key has a passphrase [\#276](https://github.com/REBELinBLUE/deployer/issues/276)
- Install dev dependencies does not work with older versions of composer due to using a 1.2+ flag [\#275](https://github.com/REBELinBLUE/deployer/issues/275)
- Persistent Folder creating multiple folders [\#273](https://github.com/REBELinBLUE/deployer/issues/273)
- Fixed missing reason of rollback [\#270](https://github.com/REBELinBLUE/deployer/pull/270) ([lianguan](https://github.com/lianguan))

**Merged pull requests:**

- Cleaning up polymorphic relationships \(projects/templates\) [\#265](https://github.com/REBELinBLUE/deployer/pull/265) ([REBELinBLUE](https://github.com/REBELinBLUE))
- Added target trait [\#269](https://github.com/REBELinBLUE/deployer/pull/269) ([lianguan](https://github.com/lianguan))

## [0.0.38](https://github.com/REBELinBLUE/deployer/tree/0.0.38) (2016-08-21)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.37...0.0.38)

**Fixed bugs:**

- usort: Array was modified by the user comparison function [\#258](https://github.com/REBELinBLUE/deployer/issues/258)

## [0.0.37](https://github.com/REBELinBLUE/deployer/tree/0.0.37) (2016-08-06)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.36...0.0.37)

**Implemented enhancements:**

- Update the deployment log whilst viewing it [\#232](https://github.com/REBELinBLUE/deployer/issues/232)
- Ignore suggestions when installing Composer deps [\#252](https://github.com/REBELinBLUE/deployer/pull/252) ([jbrooksuk](https://github.com/jbrooksuk))
- Prioritise WS for optimal transport [\#250](https://github.com/REBELinBLUE/deployer/pull/250) ([denji](https://github.com/denji))

**Fixed bugs:**

- --warning option don't exist on all versions of tar [\#257](https://github.com/REBELinBLUE/deployer/issues/257)
- Fatal error if git tags contain a string which is not a valid version [\#256](https://github.com/REBELinBLUE/deployer/issues/256)
- Installer should check for node or nodejs [\#230](https://github.com/REBELinBLUE/deployer/issues/230)
- Installer gets into an infinite loop if there are PDO drivers loaded before MySQL [\#254](https://github.com/REBELinBLUE/deployer/pull/254) ([moxx](https://github.com/moxx))
- Incorrect Lang variables [\#239](https://github.com/REBELinBLUE/deployer/pull/239) ([uLow](https://github.com/uLow))

## [0.0.36](https://github.com/REBELinBLUE/deployer/tree/0.0.36) (2016-05-30)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.35...0.0.36)

**Fixed bugs:**

- Update command broken [\#229](https://github.com/REBELinBLUE/deployer/issues/229)

## [0.0.35](https://github.com/REBELinBLUE/deployer/tree/0.0.35) (2016-05-29)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.34...0.0.35)

**Implemented enhancements:**

- "Run as" user on the before/after command hooks should be optional [\#223](https://github.com/REBELinBLUE/deployer/issues/223)
- Ignore deployment if already deploying [\#145](https://github.com/REBELinBLUE/deployer/issues/145)
- Support for webhooks from Github, Gitlab & Bitbucket [\#66](https://github.com/REBELinBLUE/deployer/issues/66)

**Fixed bugs:**

- Resetting to gravatar causes a 500 error [\#228](https://github.com/REBELinBLUE/deployer/issues/228)
- Deploy fails when composer doesn't exist, instead of downloading it [\#226](https://github.com/REBELinBLUE/deployer/issues/226)
- If the deploy key is invalid, the deployment will get stuck at "Running" and never abort [\#217](https://github.com/REBELinBLUE/deployer/issues/217)
- Heartbeat notifications can queue up, if Slack is unavailable. [\#212](https://github.com/REBELinBLUE/deployer/issues/212)
- Check redis and beanstalk are running [\#208](https://github.com/REBELinBLUE/deployer/issues/208)
- Cleanup command not cleaning up "aborting" deploy [\#207](https://github.com/REBELinBLUE/deployer/issues/207)
- Bitbucket links incorrect [\#205](https://github.com/REBELinBLUE/deployer/issues/205)
- Check link firing too often [\#162](https://github.com/REBELinBLUE/deployer/issues/162)
- Socket should also connect to redis specified in .env [\#213](https://github.com/REBELinBLUE/deployer/pull/213) ([pavankumarkatakam](https://github.com/pavankumarkatakam))

**Closed issues:**

- Process Output don't show full log [\#219](https://github.com/REBELinBLUE/deployer/issues/219)
- Split Vagrant machine into a seperate repository [\#214](https://github.com/REBELinBLUE/deployer/issues/214)

## [0.0.34](https://github.com/REBELinBLUE/deployer/tree/0.0.34) (2016-04-26)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.33...0.0.34)

**Implemented enhancements:**

- Support for TrustedProxies package [\#209](https://github.com/REBELinBLUE/deployer/issues/209)

**Fixed bugs:**

- Old builds not being deleted [\#211](https://github.com/REBELinBLUE/deployer/issues/211)

## [0.0.33](https://github.com/REBELinBLUE/deployer/tree/0.0.33) (2016-03-31)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.32...0.0.33)

**Fixed bugs:**

- SSH key not being created [\#201](https://github.com/REBELinBLUE/deployer/issues/201)
- Abort button not disappearing when deployment finishes [\#193](https://github.com/REBELinBLUE/deployer/issues/193)
- Deployment fails at installing Composer dependencies [\#191](https://github.com/REBELinBLUE/deployer/issues/191)

## [0.0.32](https://github.com/REBELinBLUE/deployer/tree/0.0.32) (2016-03-28)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.31...0.0.32)

**Fixed bugs:**

- Project page fails after successful deploy due to there being no optional commands [\#197](https://github.com/REBELinBLUE/deployer/issues/197)

## [0.0.31](https://github.com/REBELinBLUE/deployer/tree/0.0.31) (2016-03-21)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.30...0.0.31)

**Fixed bugs:**

- Can't go through installation process with PostgreSQL [\#194](https://github.com/REBELinBLUE/deployer/issues/194)
- Exception when project has deployments but none are 'completed' [\#188](https://github.com/REBELinBLUE/deployer/issues/188)
- Throttling interferes with heartbeart URLS [\#153](https://github.com/REBELinBLUE/deployer/issues/153)

**Merged pull requests:**

- Added HTTP and Node instructions to Install section [\#185](https://github.com/REBELinBLUE/deployer/pull/185) ([Patabugen](https://github.com/Patabugen))

## [0.0.30](https://github.com/REBELinBLUE/deployer/tree/0.0.30) (2016-03-02)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.29...0.0.30)

**Implemented enhancements:**

- Remove need for mcrypt [\#183](https://github.com/REBELinBLUE/deployer/issues/183)
- Have the update process backup the DB [\#177](https://github.com/REBELinBLUE/deployer/issues/177)
- Re-ordering groups [\#176](https://github.com/REBELinBLUE/deployer/issues/176)
- Checking if composer exists [\#103](https://github.com/REBELinBLUE/deployer/issues/103)
- Mirroring repositories locally to deployer [\#129](https://github.com/REBELinBLUE/deployer/pull/129) ([REBELinBLUE](https://github.com/REBELinBLUE))
- Checking if composer is installed [\#104](https://github.com/REBELinBLUE/deployer/pull/104) ([REBELinBLUE](https://github.com/REBELinBLUE))

**Fixed bugs:**

- Refresh JWT when it expires [\#180](https://github.com/REBELinBLUE/deployer/issues/180)
- SQLite installation no longer works [\#169](https://github.com/REBELinBLUE/deployer/issues/169)

## [0.0.29](https://github.com/REBELinBLUE/deployer/tree/0.0.29) (2016-02-05)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.28...0.0.29)

## [0.0.28](https://github.com/REBELinBLUE/deployer/tree/0.0.28) (2016-02-04)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.27...0.0.28)

**Implemented enhancements:**

- Server testing should check for read + write permission [\#166](https://github.com/REBELinBLUE/deployer/issues/166)
- Socket server should be able to run on HTTPS [\#165](https://github.com/REBELinBLUE/deployer/issues/165)
- Variables for templates [\#157](https://github.com/REBELinBLUE/deployer/issues/157)

**Fixed bugs:**

- Cancelling a deployment sends a success message to Slack [\#163](https://github.com/REBELinBLUE/deployer/issues/163)
- Minor UI issues in modals, deploy button [\#161](https://github.com/REBELinBLUE/deployer/issues/161)
- Sidebar is not expanding to the active group [\#160](https://github.com/REBELinBLUE/deployer/issues/160)
- Reload after adding a project [\#74](https://github.com/REBELinBLUE/deployer/issues/74)

## [0.0.27](https://github.com/REBELinBLUE/deployer/tree/0.0.27) (2016-01-28)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.26...0.0.27)

**Implemented enhancements:**

- Add a console command to create a user [\#155](https://github.com/REBELinBLUE/deployer/issues/155)
- Add option to deploy only default branch [\#146](https://github.com/REBELinBLUE/deployer/issues/146)
- Option to include require-dev packages in composer install [\#143](https://github.com/REBELinBLUE/deployer/issues/143)
- Heartbeat should notify on recovery [\#130](https://github.com/REBELinBLUE/deployer/issues/130)
- Add an "Update available" prompt [\#148](https://github.com/REBELinBLUE/deployer/pull/148) ([REBELinBLUE](https://github.com/REBELinBLUE))

**Fixed bugs:**

- Optional deployment steps no longer work [\#151](https://github.com/REBELinBLUE/deployer/issues/151)

## [0.0.26](https://github.com/REBELinBLUE/deployer/tree/0.0.26) (2016-01-12)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.25...0.0.26)

**Fixed bugs:**

- 0.0.25 breaks on composer install [\#147](https://github.com/REBELinBLUE/deployer/issues/147)

## [0.0.25](https://github.com/REBELinBLUE/deployer/tree/0.0.25) (2016-01-12)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.24...0.0.25)

## [0.0.24](https://github.com/REBELinBLUE/deployer/tree/0.0.24) (2016-01-09)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.23...0.0.24)

## [0.0.23](https://github.com/REBELinBLUE/deployer/tree/0.0.23) (2016-01-08)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.22...0.0.23)

**Implemented enhancements:**

- Remove references to gitlab in the SSH key dialog [\#131](https://github.com/REBELinBLUE/deployer/issues/131)
- Add tokens for deployer and committer users [\#123](https://github.com/REBELinBLUE/deployer/issues/123)
- Update command should prompt for confirmation before taking the site offline [\#113](https://github.com/REBELinBLUE/deployer/issues/113)
- Cancel deployment [\#142](https://github.com/REBELinBLUE/deployer/pull/142) ([REBELinBLUE](https://github.com/REBELinBLUE))
- Ability to supply ENV variables [\#141](https://github.com/REBELinBLUE/deployer/pull/141) ([REBELinBLUE](https://github.com/REBELinBLUE))

**Fixed bugs:**

- Installer should determine if running nginx [\#137](https://github.com/REBELinBLUE/deployer/issues/137)
- Security issue with socket.io coding [\#135](https://github.com/REBELinBLUE/deployer/issues/135)
- How to make a Symfony application deploy [\#133](https://github.com/REBELinBLUE/deployer/issues/133)
- Fatal error when deploy starts [\#91](https://github.com/REBELinBLUE/deployer/issues/91)

## [0.0.22](https://github.com/REBELinBLUE/deployer/tree/0.0.22) (2015-11-15)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.21...0.0.22)

**Implemented enhancements:**

- Updating webhook to receive branch and update\_only [\#127](https://github.com/REBELinBLUE/deployer/issues/127)
- Access branch name in the commands [\#119](https://github.com/REBELinBLUE/deployer/issues/119)
- Added toast alerts [\#118](https://github.com/REBELinBLUE/deployer/pull/118) ([REBELinBLUE](https://github.com/REBELinBLUE))

**Fixed bugs:**

- Error 500 after redirect from / to /auth/login [\#126](https://github.com/REBELinBLUE/deployer/issues/126)
- Production CSS is broken [\#111](https://github.com/REBELinBLUE/deployer/issues/111)
- Fix database timeout issue with worker daemon. [\#116](https://github.com/REBELinBLUE/deployer/pull/116) ([dancryer](https://github.com/dancryer))

**Closed issues:**

- Socket.io not working when changing from apache2 to ngnix. [\#120](https://github.com/REBELinBLUE/deployer/issues/120)

## [0.0.21](https://github.com/REBELinBLUE/deployer/tree/0.0.21) (2015-10-10)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.20...0.0.21)

## [0.0.20](https://github.com/REBELinBLUE/deployer/tree/0.0.20) (2015-10-09)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.19...0.0.20)

**Fixed bugs:**

- Installer does not work [\#112](https://github.com/REBELinBLUE/deployer/issues/112)
- Issue with webhook/manually trigger deploy [\#107](https://github.com/REBELinBLUE/deployer/issues/107)

## [0.0.19](https://github.com/REBELinBLUE/deployer/tree/0.0.19) (2015-10-05)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.18...0.0.19)

**Fixed bugs:**

- Stuck in Pending... [\#110](https://github.com/REBELinBLUE/deployer/issues/110)

## [0.0.18](https://github.com/REBELinBLUE/deployer/tree/0.0.18) (2015-10-04)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.17...0.0.18)

**Fixed bugs:**

- Checkboxes not clearing [\#89](https://github.com/REBELinBLUE/deployer/issues/89)
- Tmp files are getting left in storage/app [\#86](https://github.com/REBELinBLUE/deployer/issues/86)
- JS dates use a different format to the PHP dates [\#70](https://github.com/REBELinBLUE/deployer/issues/70)

## [0.0.17](https://github.com/REBELinBLUE/deployer/tree/0.0.17) (2015-10-03)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.16...0.0.17)

**Implemented enhancements:**

- Creating an installer [\#109](https://github.com/REBELinBLUE/deployer/pull/109) ([REBELinBLUE](https://github.com/REBELinBLUE))

**Fixed bugs:**

- 'is\_template' column not found in the where clause. [\#100](https://github.com/REBELinBLUE/deployer/issues/100)
- Got a exception when the queue is running as daemon [\#98](https://github.com/REBELinBLUE/deployer/issues/98)
- Support for AWS codecommit. [\#96](https://github.com/REBELinBLUE/deployer/issues/96)
- Reconnect the database when queue is running as a daemon [\#99](https://github.com/REBELinBLUE/deployer/pull/99) ([iflamed](https://github.com/iflamed))

## [0.0.16](https://github.com/REBELinBLUE/deployer/tree/0.0.16) (2015-09-17)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.15...0.0.16)

**Implemented enhancements:**

- Failed after activate new release is confusing [\#95](https://github.com/REBELinBLUE/deployer/issues/95)
- Make composer install command as optional [\#94](https://github.com/REBELinBLUE/deployer/issues/94)
- Add a cronjob to delete orphaned user avatars [\#80](https://github.com/REBELinBLUE/deployer/issues/80)
- Allow for choosing default state of optional commands [\#69](https://github.com/REBELinBLUE/deployer/issues/69)
- Allow optional commands to be specified in webhooks [\#65](https://github.com/REBELinBLUE/deployer/issues/65)
- User profile [\#10](https://github.com/REBELinBLUE/deployer/issues/10)
- Finish with errors status [\#97](https://github.com/REBELinBLUE/deployer/pull/97) ([REBELinBLUE](https://github.com/REBELinBLUE))
- Profile [\#79](https://github.com/REBELinBLUE/deployer/pull/79) ([REBELinBLUE](https://github.com/REBELinBLUE))
- Default to remember the user login [\#64](https://github.com/REBELinBLUE/deployer/pull/64) ([iflamed](https://github.com/iflamed))

**Fixed bugs:**

- Failed after activate new release is confusing [\#95](https://github.com/REBELinBLUE/deployer/issues/95)
- Fatal error in historical deploys if command is deleted [\#90](https://github.com/REBELinBLUE/deployer/issues/90)
- Help text on commands dialog is misleading [\#88](https://github.com/REBELinBLUE/deployer/issues/88)
- Shouldn't use the "builds to keep" as the number of deployments to show per page [\#87](https://github.com/REBELinBLUE/deployer/issues/87)
- Error if GD is missing [\#85](https://github.com/REBELinBLUE/deployer/issues/85)
- Deleting a user causes the project listing to die [\#71](https://github.com/REBELinBLUE/deployer/issues/71)
- SendFile in DeployProject not using custom port [\#67](https://github.com/REBELinBLUE/deployer/issues/67)
- Fix the navtab overflow [\#63](https://github.com/REBELinBLUE/deployer/pull/63) ([iflamed](https://github.com/iflamed))

**Closed issues:**

- Use StyleCI for code standards [\#75](https://github.com/REBELinBLUE/deployer/issues/75)

**Merged pull requests:**

- Fix PSR2 [\#83](https://github.com/REBELinBLUE/deployer/pull/83) ([jbrooksuk](https://github.com/jbrooksuk))
- Changing App namespace to REBELinBLUE\Deployer [\#81](https://github.com/REBELinBLUE/deployer/pull/81) ([REBELinBLUE](https://github.com/REBELinBLUE))

## [0.0.15](https://github.com/REBELinBLUE/deployer/tree/0.0.15) (2015-06-19)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.14...0.0.15)

**Implemented enhancements:**

- Added the ability to notify a channel only on failure [\#61](https://github.com/REBELinBLUE/deployer/pull/61) ([REBELinBLUE](https://github.com/REBELinBLUE))
- Ordering servers [\#60](https://github.com/REBELinBLUE/deployer/pull/60) ([REBELinBLUE](https://github.com/REBELinBLUE))
- Deployment templates [\#58](https://github.com/REBELinBLUE/deployer/pull/58) ([REBELinBLUE](https://github.com/REBELinBLUE))
- Event broadcasting [\#56](https://github.com/REBELinBLUE/deployer/pull/56) ([REBELinBLUE](https://github.com/REBELinBLUE))

**Fixed bugs:**

- Set access right to 664 for the project global file [\#57](https://github.com/REBELinBLUE/deployer/pull/57) ([iflamed](https://github.com/iflamed))

## [0.0.14](https://github.com/REBELinBLUE/deployer/tree/0.0.14) (2015-06-12)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.13...0.0.14)

**Implemented enhancements:**

- Add a health check block to project details [\#53](https://github.com/REBELinBLUE/deployer/pull/53) ([iflamed](https://github.com/iflamed))

**Fixed bugs:**

- Remove Queue::push use and fix active\_url rule failed. [\#54](https://github.com/REBELinBLUE/deployer/pull/54) ([iflamed](https://github.com/iflamed))
- Check link dialog has the wrong image [\#52](https://github.com/REBELinBLUE/deployer/pull/52) ([iflamed](https://github.com/iflamed))

**Merged pull requests:**

- Reduce the sql statement in the deployments page [\#55](https://github.com/REBELinBLUE/deployer/pull/55) ([iflamed](https://github.com/iflamed))
- Laravel 5.1 [\#50](https://github.com/REBELinBLUE/deployer/pull/50) ([REBELinBLUE](https://github.com/REBELinBLUE))

## [0.0.13](https://github.com/REBELinBLUE/deployer/tree/0.0.13) (2015-05-31)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.12...0.0.13)

**Implemented enhancements:**

- Application health check [\#44](https://github.com/REBELinBLUE/deployer/pull/44) ([iflamed](https://github.com/iflamed))
- CC tray [\#43](https://github.com/REBELinBLUE/deployer/pull/43) ([REBELinBLUE](https://github.com/REBELinBLUE))
- Email notifications [\#37](https://github.com/REBELinBLUE/deployer/pull/37) ([iflamed](https://github.com/iflamed))

## [0.0.12](https://github.com/REBELinBLUE/deployer/tree/0.0.12) (2015-05-25)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.11...0.0.12)

**Implemented enhancements:**

- Split the log file for cli and php-fpm [\#40](https://github.com/REBELinBLUE/deployer/pull/40) ([iflamed](https://github.com/iflamed))

## [0.0.11](https://github.com/REBELinBLUE/deployer/tree/0.0.11) (2015-05-24)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.10...0.0.11)

**Implemented enhancements:**

- Add the ability to view the script [\#24](https://github.com/REBELinBLUE/deployer/issues/24)
- Support write environment file such like .env, .local.env [\#35](https://github.com/REBELinBLUE/deployer/pull/35) ([iflamed](https://github.com/iflamed))

**Fixed bugs:**

- TestServerConnection is still using SSH [\#36](https://github.com/REBELinBLUE/deployer/issues/36)
- Slack notification icon [\#34](https://github.com/REBELinBLUE/deployer/pull/34) ([iflamed](https://github.com/iflamed))

## [0.0.10](https://github.com/REBELinBLUE/deployer/tree/0.0.10) (2015-05-23)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.9...0.0.10)

**Implemented enhancements:**

- Server changes [\#17](https://github.com/REBELinBLUE/deployer/issues/17)
- Releases share some folders or files [\#33](https://github.com/REBELinBLUE/deployer/pull/33) ([iflamed](https://github.com/iflamed))

**Fixed bugs:**

- Clean up failed deployment [\#27](https://github.com/REBELinBLUE/deployer/issues/27)
- Always running after a deploy, when ssh to server failed! [\#32](https://github.com/REBELinBLUE/deployer/pull/32) ([iflamed](https://github.com/iflamed))

## [0.0.9](https://github.com/REBELinBLUE/deployer/tree/0.0.9) (2015-05-15)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.8...0.0.9)

**Implemented enhancements:**

- Allow different branches and tags to be deployed [\#18](https://github.com/REBELinBLUE/deployer/issues/18)
- Add ace editor for syntax highlighting in script editor [\#26](https://github.com/REBELinBLUE/deployer/pull/26) ([REBELinBLUE](https://github.com/REBELinBLUE))

**Fixed bugs:**

- 2015\_05\_13\_121650\_set\_deployment\_branch is not setting the branch [\#23](https://github.com/REBELinBLUE/deployer/issues/23)

## [0.0.8](https://github.com/REBELinBLUE/deployer/tree/0.0.8) (2015-05-13)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.7...0.0.8)

**Implemented enhancements:**

- Allowing deploying specific tags and branches [\#20](https://github.com/REBELinBLUE/deployer/pull/20) ([REBELinBLUE](https://github.com/REBELinBLUE))

**Fixed bugs:**

- Problem when SSH details are incorrect [\#22](https://github.com/REBELinBLUE/deployer/issues/22)

## [0.0.7](https://github.com/REBELinBLUE/deployer/tree/0.0.7) (2015-05-09)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.6...0.0.7)

## [0.0.6](https://github.com/REBELinBLUE/deployer/tree/0.0.6) (2015-05-07)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.5...0.0.6)

**Implemented enhancements:**

- Timezone [\#16](https://github.com/REBELinBLUE/deployer/issues/16)
- Heartbeats [\#21](https://github.com/REBELinBLUE/deployer/pull/21) ([REBELinBLUE](https://github.com/REBELinBLUE))

## [0.0.5](https://github.com/REBELinBLUE/deployer/tree/0.0.5) (2015-05-05)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.4...0.0.5)

**Implemented enhancements:**

- Deployment templates [\#9](https://github.com/REBELinBLUE/deployer/issues/9)
- Allows the port of the server to be modified [\#19](https://github.com/REBELinBLUE/deployer/pull/19) ([REBELinBLUE](https://github.com/REBELinBLUE))

## [0.0.4](https://github.com/REBELinBLUE/deployer/tree/0.0.4) (2015-04-15)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.3...0.0.4)

**Implemented enhancements:**

- Optional steps [\#5](https://github.com/REBELinBLUE/deployer/pull/5) ([REBELinBLUE](https://github.com/REBELinBLUE))

**Fixed bugs:**

- Sorting projects [\#3](https://github.com/REBELinBLUE/deployer/issues/3)

## [0.0.3](https://github.com/REBELinBLUE/deployer/tree/0.0.3) (2015-04-15)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.2...0.0.3)

**Fixed bugs:**

- Fixed HTML being escaped in ssh key Dialog.  [\#2](https://github.com/REBELinBLUE/deployer/pull/2) ([mikehhhhhhh](https://github.com/mikehhhhhhh))

## [0.0.2](https://github.com/REBELinBLUE/deployer/tree/0.0.2) (2015-04-08)
[Full Changelog](https://github.com/REBELinBLUE/deployer/compare/0.0.1...0.0.2)

## [0.0.1](https://github.com/REBELinBLUE/deployer/tree/0.0.1) (2015-04-08)


\* *This Change Log was automatically generated by [github_changelog_generator](https://github.com/skywinder/Github-Changelog-Generator)*