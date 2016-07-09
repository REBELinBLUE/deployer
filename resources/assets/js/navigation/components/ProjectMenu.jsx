import React, { PropTypes } from 'react';

import ProjectMenuItem from './ProjectMenuItem';
import Icon from '../../app/components/Icon';

const ProjectMenu = (props) => {
  const {
    projects,
    type,
  } = props;

  let icon = 'clock-o';
  let colour = 'label label-info';
  let id = 'pending_menu';
  let translation = 'dashboard.pending';

  if (type === 'running') {
    icon = 'spinner';
    colour = 'label label-warning';
    id = 'deploying_menu';
    translation = 'dashboard.running';
  }

  const label = Lang.choice(translation, projects.length, { count: projects.length });

  if (!projects.length) {
    return null;
  }

  return (
    <li className="dropdown messages-menu" id={id}>
      <a href="#" className="dropdown-toggle" data-toggle="dropdown">
        <Icon fa={icon} />
        <span className={colour}>{projects.length}</span>
      </a>
      <ul className="dropdown-menu">
        <li className="header">{label}</li>
        <li>
          <ul className="menu">
            {
              projects.map((project, index) => (
                <ProjectMenuItem key={index} project={project} />
              ))
            }
          </ul>
        </li>
      </ul>
    </li>
  );
};

ProjectMenu.propTypes = {
  type: PropTypes.oneOf(['pending', 'running']).isRequired,
  projects: PropTypes.array.isRequired,
};

export default ProjectMenu;