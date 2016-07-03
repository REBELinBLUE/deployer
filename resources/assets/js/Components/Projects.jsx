import React, { PropTypes } from 'react';

import Loading from './Loading';

const Projects = (props) => {
  const {
    projects,
    fetching,
  } = props;

  const strings = {
    none: Lang.get('dashboard.no_projects'),
    title: Lang.get('dashboard.projects'),
  };

  if (!projects.length) {
    return (
      <div className="box">
        <div className="box-header">
          <h3 className="box-title">{strings.title}</h3>
        </div>

        <div className="box-body">
          <p>{fetching ? 'Loading...' : strings.none}</p>
        </div>

        <Loading visible={fetching} />
      </div>
    );
  }

  let groups = [];
  projects.forEach((group, index) => {
    groups.push(
      <div className="box" key={index}>
        <div className="box-header">
          <h3 className="box-title">{group.group}</h3>
        </div>

        <div className="box-body">
          <pre>{JSON.stringify(group.projects)}</pre>
        </div>
      </div>
    );
  });

  return (<div>{groups}</div>);
};

Projects.propTypes = {
  fetching: PropTypes.bool.isRequired,
  projects: PropTypes.array.isRequired,
};

export default Projects;
