import checkUrlTests from './CheckUrl.spec';
import commandTests from './Command.spec';
import deploymentTests from './Deployment.spec';
import heartbeatTests from './Heartbeat.spec';
import notificationTests from './Notification.spec';
import projectTests from './Project.spec';
import serverTests from './Server.spec';

describe('Models', () => {
  checkUrlTests();
  commandTests();
  deploymentTests();
  heartbeatTests();
  notificationTests();
  projectTests();
  serverTests();
});
