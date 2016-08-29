import React, { PropTypes } from 'react';
import { Nav } from 'react-bootstrap';

import Header from './Header';
import Dialog from '../containers/dialogs/KeyDialog';
import NavItem from '../../app/components/NavItem';
import Project from '../../models/Project';

const ProjectDetails = (props) => {
  const {
    project,
    children,
  } = props;

  const strings = {
    deployments: Lang.get('deployments.label'),
    servers: Lang.get('servers.label'),
    commands: Lang.get('commands.label'),
    files: Lang.get('sharedFiles.label'),
    notifications: Lang.get('notifications.label'),
    health: Lang.get('heartbeats.label'),
  };

  const navItems = [
    { name: 'deployments', fa: 'hdd-o', primary: true },
    { name: 'servers', fa: 'tasks' },
    { name: 'commands', fa: 'terminal' },
    { name: 'files', fa: 'file-code-o' },
    { name: 'notifications', fa: 'bullhorn' },
    { name: 'health', fa: 'heartbeat' },
  ];

  return (
    <div>
      <Header project={project} />

      <div className="row project-status">
        <div className="col-md-12">
          <div className="nav-tabs-custom">
            <Nav bsStyle="tabs">
              {
                navItems.map((nav) => {
                  let link = `/projects/${project.id}`;

                  if (!nav.primary) {
                    link = `${link}/${nav.name}`;
                  }

                  return (
                    <NavItem
                      key={nav.name}
                      to={link}
                      id={nav.name}
                      fa={nav.fa}
                      primary={nav.primary || false}
                    >{strings[nav.name]}</NavItem>
                  );
                })
              }
            </Nav>
            <div className="tab-content">
              <div className="tab-pane active">{children}</div>
            </div>
          </div>
        </div>
      </div>

      <Dialog project={project} />
    </div>
  );
};

ProjectDetails.propTypes = {
  project: Project.isRequired,
  children: PropTypes.oneOfType([
    PropTypes.arrayOf(PropTypes.node),
    PropTypes.node,
  ]).isRequired,
};

export default ProjectDetails;
