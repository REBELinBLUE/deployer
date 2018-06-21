---
name: Bug report
about: Create a report to help us improve

---

Before submitting your issue, please make sure that you've checked all of the checkboxes below.

- [ ] You're running the [latest release](https://github.com/REBElinBLUE/deployer/releases/latest) version of Deployer.
- [ ] Ensure that you're running at least PHP 7.0.8, you can check this by running `php -v`
- [ ] You've ran `composer install --no-dev` from the root of your installation.
- [ ] You've ran `npm install --production` from the root of your installation.
- [ ] You've ran `rm -rf bootstrap/cache/*` from the root of your installation.
- [ ] You have restarted the queue listener and node socket server.

### Describe the bug

*A clear and concise description of what the bug is.*

### Expected behaviour

*Please describe what you're expecting to see happen.*

### Actual behaviour

*Please describe what you're actually seeing happen.*

### Steps to reproduce

*If your issue requires any specific steps to reproduce, please outline them here.*

### Screenshots
*If applicable, add screenshots to help explain your problem.*

### Environment info

Visit `/admin/sysinfo` on your install and click the "Get System Report" button. Paste the report here, if you can't
please provide the following instead.

- Operating System:
- PHP Version:
- Node Version:
- Database System:
- Database Version:

### Logs (see storage/logs/) or other output that would be helpful
(If logs are large, please upload as attachment).
