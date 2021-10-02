import GroupsCollection from './Groups';
import UsersCollection from './Users';
import TemplatesCollection from './Templates';
import ProjectsCollection from './Projects';
import ServersCollection from './Servers';
import VariablesCollection from './Variables';
import SharedFilesCollection from './SharedFiles';
import ConfigFilesCollection from './ConfigFiles';
import NotificationsCollection from './Notifications';
import HeartbeatsCollection from './Heartbeats';
import CheckUrlsCollection from './CheckUrls';
import CommandsCollection from './Commands';
import LogsCollection from './Logs';

export default {
  Groups: GroupsCollection,
  Users: UsersCollection,
  Templates: TemplatesCollection,
  Projects: ProjectsCollection,
  Servers: ServersCollection,
  Variables: VariablesCollection,
  SharedFiles: SharedFilesCollection,
  ConfigFiles: ConfigFilesCollection,
  Notifications: NotificationsCollection,
  Heartbeats: HeartbeatsCollection,
  CheckUrls: CheckUrlsCollection,
  Commands: CommandsCollection,
  Logs: LogsCollection,
};
