import React, { PropTypes } from 'react';
import { Link } from 'react-router';

const ProjectMenu = (props) => {
  const {
    projects,
    type,
  } = props;

  let icon = 'fa fa-clock-o';
  let colour = 'label label-info';
  let id = 'pending_menu';
  let translation = 'dashboard.pending';

  if (type === 'running') {
    icon = 'fa fa-spinner';
    colour = 'label label-warning';
    id = 'deploying_menu';
    translation = 'dashboard.running';
  }

  const label = Lang.choice(translation, projects.length, { count: projects.length });

  return (
    <li className="dropdown messages-menu" id={id}>
      <a href="#" className="dropdown-toggle" data-toggle="dropdown">
        <i className={icon}></i>
        <span className={colour}>{projects.length}</span>
      </a>
      <ul className="dropdown-menu">
        <li className="header">{label}</li>
        <li>
          <ul className="menu">
            {
              projects.map((project, index) => (
                <li id="deployment_info_id" key={index}>
                  <Link to="url">
                    <h4>project_name <small className="pull-right">started: time</small></h4>
                    <p>branch: branch</p>
                  </Link>
                </li>
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
