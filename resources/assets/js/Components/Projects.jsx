import React from 'react';

const Projects = () => {
  const strings = {
    none: Lang.get('dashboard.no_projects'),
    title: Lang.get('dashboard.projects'),
  };

  return (
    <div className="box">
      <div className="box-header">
        <h3 className="box-title">{strings.title}</h3>
      </div>
      <div className="box-body">
        <p>{strings.none}</p>
      </div>
    </div>
  );
};

export default Projects;
