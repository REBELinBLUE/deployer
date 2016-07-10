import React, { PropTypes } from 'react';
import { Nav } from 'react-bootstrap';

import Header from './Header';
import Dialog from '../containers/KeyDialog';
import NavItem from '../../app/components/NavItem';

const ProjectDetails = (props) => {
  const {
    project,
    children,
  } = props;

  return (
    <div>
      <Header project={project} />

      <div className="row project-status">
        <div className="col-md-12">
          <div className="nav-tabs-custom">
            <Nav bsStyle="tabs">
              <NavItem to={`/projects/${project.id}`} id="deployments" fa="hdd-o" primary>Deployments</NavItem>
              <NavItem to={`/projects/${project.id}/servers`} id="servers" fa="tasks">Servers</NavItem>
              <NavItem to={`/projects/${project.id}/commands`} id="commands" fa="terminal">Commands</NavItem>
              <NavItem to={`/projects/${project.id}/files`} id="files" fa="file-code-o">Files</NavItem>
              <NavItem to={`/projects/${project.id}/notifications`} id="notifications" fa="bullhorn">Notifications</NavItem>
              <NavItem to={`/projects/${project.id}/health`} id="health" fa="heartbeat">Health Checks</NavItem>
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
  project: PropTypes.object.isRequired,
  children: PropTypes.oneOfType([
    PropTypes.arrayOf(PropTypes.node),
    PropTypes.node,
  ]).isRequired,
};

export default ProjectDetails;
